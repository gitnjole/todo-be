#!/bin/sh
set -e

until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
  echo "Waiting for database to be ready..."
  sleep 2
done

echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

exec "$@"