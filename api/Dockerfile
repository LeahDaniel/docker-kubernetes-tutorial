FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

COPY . ./

RUN composer dump-autoload --optimize --no-dev --no-scripts --classmap-authoritative

FROM php:8.2-fpm-alpine AS image-builder

WORKDIR /var/www/html

RUN apk add postgresql-dev nginx supervisor

RUN docker-php-ext-install pdo_pgsql bcmath

COPY --from=composer /app /var/www/html


