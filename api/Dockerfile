FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

COPY . ./

RUN composer dump-autoload --optimize --no-dev --no-scripts --classmap-authoritative

FROM php:8.2-fpm-alpine AS image-builder

WORKDIR /var/www/html

RUN apk add --no-cache postgresql-dev nginx supervisor \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && docker-php-ext-install pdo_pgsql bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

COPY --from=composer /app /var/www/html

COPY docker/nginx.conf /etc/nginx/nginx.conf

COPY docker/default.conf /etc/nginx/http.d/default.conf

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]