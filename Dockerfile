FROM php:7.4-cli AS php-cli

RUN apt-get update && apt-get install -y --no-install-recommends libonig-dev libzip-dev libicu-dev git wget unzip \
    && docker-php-ext-install zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /usr/src/gilded-rose
