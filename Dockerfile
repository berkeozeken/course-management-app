# ---------- 1) ASSETS BUILD (Node) ----------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- 2) COMPOSER DEPENDENCIES ----------
FROM php:8.3-fpm-alpine AS phpdeps
WORKDIR /var/www/html
RUN apk add --no-cache git unzip libpng-dev libjpeg-turbo-dev libwebp-dev libzip-dev oniguruma-dev icu-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql mbstring zip intl
COPY --from=assets /app /var/www/html
COPY composer.json composer.lock* ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ---------- 3) RUNTIME: Nginx + PHP-FPM + Supervisor ----------
FROM alpine:3.20
WORKDIR /var/www/html

RUN apk add --no-cache nginx php83 php83-fpm php83-opcache php83-session php83-pdo php83-pdo_pgsql php83-pdo_mysql \
    php83-mbstring php83-xml php83-curl php83-zip php83-gd php83-intl php83-fileinfo supervisor bash curl

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

# app dosyaları (composer & assets’ten)
COPY --from=phpdeps /var/www/html /var/www/html

# storage izinleri
RUN adduser -D -g 'www' nginx \
 && chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
