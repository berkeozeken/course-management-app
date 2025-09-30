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

# Gerekli build bağımlılıkları + PostgreSQL header'ları
RUN apk add --no-cache \
      git unzip libpng-dev libjpeg-turbo-dev libwebp-dev libzip-dev oniguruma-dev icu-dev postgresql-dev \
  && docker-php-ext-configure gd --with-jpeg --with-webp \
  && docker-php-ext-configure pdo_pgsql --with-pdo-pgsql=/usr \
  && docker-php-ext-install -j"$(nproc)" gd pdo pdo_mysql pdo_pgsql mbstring zip intl

# Uygulama dosyaları ve composer
# (Build çıktılarını da içerdiği için tüm proje kopyalanıyor)
COPY --from=assets /app /var/www/html
COPY composer.json composer.lock* ./

# Composer kur & bağımlılıkları indir
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
  && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ---------- 3) RUNTIME: Nginx + PHP 8.3 (apk) + Supervisor ----------
FROM alpine:3.20
WORKDIR /var/www/html

# Runtime paketleri
RUN apk add --no-cache \
    nginx php83 php83-fpm php83-opcache php83-session php83-pdo php83-pdo_pgsql php83-pdo_mysql \
    php83-mbstring php83-xml php83-xmlreader php83-xmlwriter php83-dom php83-curl php83-zip \
    php83-gd php83-intl php83-fileinfo \
    supervisor bash curl

# php cli kısayolu (artisan için)
RUN [ -e /usr/bin/php ] || ln -s /usr/bin/php83 /usr/bin/php

# php-fpm ayarları (TCP 9000; socket KULLANMIYORUZ)
RUN sed -i 's|;daemonize = yes|daemonize = no|g' /etc/php83/php-fpm.conf \
 && sed -i 's|^user = nobody|user = nginx|g' /etc/php83/php-fpm.d/www.conf \
 && sed -i 's|^group = nobody|group = nginx|g' /etc/php83/php-fpm.d/www.conf

# nginx & supervisor config
COPY .deploy/nginx.conf /etc/nginx/http.d/default.conf
COPY .deploy/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# App dosyaları (phpdeps aşamasından)
COPY --from=phpdeps /var/www/html /var/www/html

# storage/cache klasörleri ve izinler
RUN mkdir -p \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/bootstrap/cache \
    /run/nginx \
 && chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache \
 && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# ENV (loglar stderr'e)
ENV APP_ENV=production \
    APP_DEBUG=true \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=debug

EXPOSE 80

# Boot sırasında artisan komutları
# (DB ilk anda hazır değilse servis düşmesin diye migrate'e || true)
CMD ["bash","-lc","\
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache && \
chmod -R 777 storage bootstrap/cache && \
php artisan config:clear && \
php artisan route:clear && \
php artisan view:clear && \
php artisan cache:clear && \
php artisan clear-compiled && \
php artisan migrate --force || true && \
php artisan storage:link || true && \
chmod -R 777 storage bootstrap/cache && \
supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
