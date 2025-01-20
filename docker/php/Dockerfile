FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libicu-dev libonig-dev \
    && docker-php-ext-install pdo_mysql zip intl opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.* symfony.* ./
COPY bin bin/

RUN mkdir -p var/cache var/log \
    && chmod 777 var/cache var/log \
    && chmod +x bin/console

ENV APP_ENV=dev
ENV APP_DEBUG=1

RUN composer install --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload --optimize \
    && composer run-script post-install-cmd