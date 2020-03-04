FROM php:7.4-cli AS php-cli

RUN apt-get update && apt-get install -y --no-install-recommends libonig-dev libzip-dev libicu-dev git wget unzip \
    && docker-php-ext-install zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN wget https://get.symfony.com/cli/installer -O - | bash \
    && mv /root/.symfony/bin/symfony /usr/local/bin/symfony

WORKDIR /usr/src/gilded-rose
