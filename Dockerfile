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

# Build aşamasında DB probunu kapat (AppServiceProvider bunu görüp DB'ye dokunmaz)
ENV SKIP_DB_PROBE=1

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

# ⚠️ TLS için gerekli paketler: openssl + ca-certificates (+ libpq için postgresql-libs)
RUN apk add --no-cache \
    nginx php83 php83-fpm php83-opcache php83-session php83-pdo php83-pdo_pgsql php83-pdo_mysql \
    php83-mbstring php83-xml php83-xmlreader php83-xmlwriter php83-dom php83-curl php83-zip \
    php83-gd php83-intl php83-fileinfo php83-tokenizer \
    supervisor bash curl \
    openssl ca-certificates postgresql-libs \
 && update-ca-certificates

# php cli
RUN [ -e /usr/bin/php ] || ln -s /usr/bin/php83 /usr/bin/php

# php-fpm ayarları (TCP 9000 + ENV'leri PHP'ye geçir)
RUN sed -i 's|;daemonize = yes|daemonize = no|g' /etc/php83/php-fpm.conf \
 && sed -i 's|^user = .*|user = nginx|g' /etc/php83/php-fpm.d/www.conf \
 && sed -i 's|^group = .*|group = nginx|g' /etc/php83/php-fpm.d/www.conf \
 && sed -i 's|^listen = .*|listen = 127.0.0.1:9000|g' /etc/php83/php-fpm.d/www.conf \
 && echo "clear_env = no" >> /etc/php83/php-fpm.d/www.conf \
 && echo "listen = 127.0.0.1:9000" >> /etc/php83/php-fpm.d/www.conf

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

# Basit sağlık dosyası
RUN printf "<?php echo 'OK';" > /var/www/html/public/health.php

# Runtime ENV — DB probu artık açık
ENV SKIP_DB_PROBE=0 \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=debug

EXPOSE 80

# Boot komutları
CMD ["bash","-lc","\
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache && \
chmod -R 777 storage bootstrap/cache && \
php artisan optimize:clear || true && \
php artisan storage:link || true && \
php artisan config:cache && php artisan route:cache && php artisan view:cache && \
chmod -R 777 storage bootstrap/cache && \
supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
