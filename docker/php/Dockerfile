FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    librabbitmq-dev \
    libssh-dev \
    netcat-openbsd \
    cron \
    && docker-php-ext-install \
    pdo_mysql \
    zip \
    intl \
    sockets \
    && pecl install amqp \
    && docker-php-ext-enable amqp

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/php/start-workers.sh /usr/local/bin/start-workers
RUN chmod +x /usr/local/bin/start-workers

RUN echo "0 2 * * * cd /var/www/html && php bin/console app:schedule-task-deletion >> /var/log/cron.log 2>&1" > /etc/cron.d/task-deletion-cron \
    && chmod 0644 /etc/cron.d/task-deletion-cron \
    && crontab /etc/cron.d/task-deletion-cron \
    && touch /var/log/cron.log

RUN echo '#!/bin/sh\n\
set -e\n\
git config --global --add safe.directory /var/www/html\n\
composer install --prefer-dist --no-progress --no-interaction\n\
until php bin/console doctrine:query:sql "SELECT 1" >/dev/null 2>&1; do\n\
  echo "Waiting on database..."\n\
  sleep 1\n\
done\n\
php bin/console doctrine:migrations:migrate --no-interaction\n\
service cron start\n\
/usr/local/bin/start-workers &\n\
exec php-fpm' > /usr/local/bin/docker-entrypoint \
    && chmod +x /usr/local/bin/docker-entrypoint

WORKDIR /var/www/html

ENTRYPOINT ["/usr/local/bin/docker-entrypoint"]