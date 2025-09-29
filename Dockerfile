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
COPY --from=assets /app /var/www/html
COPY composer.json composer.lock* ./

# Composer kur & bağımlılıkları indir
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
  && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ---------- 3) RUNTIME: Nginx + PHP 8.3 (apk) + Supervisor ----------
FROM alpine:3.20
WORKDIR /var/www/html

# Runtime paketleri (php83 ve gerekli ext'ler)
RUN apk add --no-cache \
    nginx php83 php83-fpm php83-opcache php83-session php83-pdo php83-pdo_pgsql php83-pdo_mysql \
    php83-mbstring php83-xml php83-curl php83-zip php83-gd php83-intl php83-fileinfo \
    supervisor bash curl

# php cli kısayolu (artisan için)
RUN ln -s /usr/bin/php83 /usr/bin/php

# php-fpm socket ayarı
RUN sed -i 's|;daemonize = yes|daemonize = no|g' /etc/php83/php-fpm.conf \
 && sed -i 's|^user = nobody|user = nginx|g' /etc/php83/php-fpm.d/www.conf \
 && sed -i 's|^group = nobody|group = nginx|g' /etc/php83/php-fpm.d/www.conf \
 && sed -i 's|^;listen.owner = nobody|listen.owner = nginx|g' /etc/php83/php-fpm.d/www.conf \
 && sed -i 's|^;listen.group = nobody|listen.group = nginx|g' /etc/php83/php-fpm.d/www.conf \
 && sed -i 's|^listen = 127.0.0.1:9000|listen = /run/php-fpm.sock|g' /etc/php83/php-fpm.d/www.conf

# nginx & supervisor config
COPY .deploy/nginx.conf /etc/nginx/http.d/default.conf
COPY .deploy/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# app dosyaları (phpdeps & assets’ten)
COPY --from=phpdeps /var/www/html /var/www/html

# storage izinleri
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
 && chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache \
 && chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache

# Production env vars (Render tarafında da tanımlanacak)
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

EXPOSE 80

# ====== ÖNEMLİ ======
# Free planda Shell yok; artisan komutlarını runtime’da çalıştır:
# - cache temizliği
# - migrate --force
# - storage:link
# - izinler
# sonra supervisor ile nginx+php-fpm başlat
CMD ["bash","-lc","php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear \
 && php artisan optimize:clear \
 && php artisan migrate --force \
 && php artisan storage:link || true \
 && chmod -R ug+rwx storage bootstrap/cache \
 && supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
