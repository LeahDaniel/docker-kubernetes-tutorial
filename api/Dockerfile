# -----------------------------------------------------------------------------
# Stage 1: Install PHP dependencies
# -----------------------------------------------------------------------------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative --no-scripts

# -----------------------------------------------------------------------------
# Stage 2: Runtime (nginx + PHP-FPM)
# -----------------------------------------------------------------------------
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
        nginx \
        supervisor \
        icu-dev \
        libpq-dev \
        libxml2-dev \
        oniguruma-dev \
    && docker-php-ext-install \
        bcmath \
        opcache \
        pdo \
        pdo_pgsql \
    && rm -rf /var/cache/apk/* /tmp/*

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

COPY --from=vendor /app /var/www/html

RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
