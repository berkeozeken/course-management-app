# ---------- 1) ASSETS BUILD (Node) ----------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- 2) COMPOSER + PHP EXT (php-fpm-alpine) ----------
FROM php:8.3-fpm-alpine AS phpdeps
WORKDIR /var/www/html

RUN apk add --no-cache \
      git unzip libpng-dev libjpeg-turbo-dev libwebp-dev libzip-dev oniguruma-dev icu-dev postgresql-dev \
  && docker-php-ext-configure gd --with-jpeg --with-webp \
  && docker-php-ext-configure pdo_pgsql --with-pdo-pgsql=/usr \
  && docker-php-ext-install -j"$(nproc)" gd pdo pdo_mysql pdo_pgsql mbstring zip intl

# App + Composer
COPY --from=assets /app /var/www/html
COPY composer.json composer.lock* ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
  && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ---------- 3) RUNTIME: Nginx + PHP-FPM + Supervisor ----------
FROM alpine:3.20
WORKDIR /var/www/html

RUN apk add --no-cache \
    nginx php83 php83-fpm php83-opcache php83-session php83-pdo php83-pdo_pgsql php83-pdo_mysql \
    php83-mbstring php83-xml php83-xmlreader php83-xmlwriter php83-dom php83-curl php83-zip \
    php83-gd php83-intl php83-fileinfo \
    supervisor bash curl

# php cli
RUN [ -e /usr/bin/php ] || ln -s /usr/bin/php83 /usr/bin/php

# php-fpm: 127.0.0.1:9000'a ZORLA (tüm pool dosyalarında)
RUN sed -i 's|;daemonize = yes|daemonize = no|g' /etc/php83/php-fpm.conf \
 && find /etc/php83/php-fpm.d -type f -name "*.conf" -exec sed -i 's|^user = .*|user = nginx|g;s|^group = .*|group = nginx|g;s|^listen = .*|listen = 127.0.0.1:9000|g' {} \;

# Nginx & Supervisor config
COPY .deploy/nginx.conf /etc/nginx/http.d/default.conf
COPY .deploy/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# App dosyaları
COPY --from=phpdeps /var/www/html /var/www/html

# storage/cache + izinler
RUN mkdir -p \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/bootstrap/cache \
    /run/nginx \
 && chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache \
 && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Basit sağlık sayfası (opsiyonel ama debug için iyi)
RUN printf "<?php echo 'OK';" > /var/www/html/public/health.php

ENV APP_ENV=production APP_DEBUG=false LOG_CHANNEL=stderr LOG_LEVEL=debug
EXPOSE 80

# Boot komutları
CMD ["bash","-lc","\
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache && \
chmod -R 777 storage bootstrap/cache && \
php artisan config:clear || true && \
php artisan route:clear  || true && \
php artisan view:clear   || true && \
php artisan cache:clear  || true && \
php artisan clear-compiled || true && \
php artisan migrate --force || true && \
php artisan storage:link || true && \
chmod -R 777 storage bootstrap/cache && \
supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
