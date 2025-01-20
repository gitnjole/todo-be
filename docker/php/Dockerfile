FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libicu-dev libonig-dev \
    && docker-php-ext-install pdo_mysql zip intl opcache \
    && rm -rf /var/lib/apt/lists/* 

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ENV APP_ENV=dev
ENV APP_DEBUG=1
ENV APP_SECRET="$(php -r 'echo bin2hex(random_bytes(16));')}"

COPY . .

RUN composer install --optimize-autoloader \
    && chown -R www-data:www-data .

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]