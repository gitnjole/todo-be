#!/bin/sh
set -e

composer install --prefer-dist --no-progress --no-interaction

until php bin/console doctrine:query:sql "SELECT 1" >/dev/null 2>&1; do
  sleep 1
done

php bin/console doctrine:migrations:migrate --no-interaction

exec "$@"