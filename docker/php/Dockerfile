FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install \
    pdo_mysql \
    zip \
    intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN echo '#!/bin/sh\n\
php bin/console doctrine:migrations:migrate --no-interaction' > /usr/local/bin/run-migrations \
    && chmod +x /usr/local/bin/run-migrations

WORKDIR /var/www/html