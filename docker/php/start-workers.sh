#!/bin/bash
# Start the Symfony Messenger workers to consume messages from RabbitMQ

set -e

until nc -z rabbitmq 5672; do
  echo "Waiting on RabbitMQ..."
  sleep 2
done

echo "Starting Symfony Messenger workers..."

php /var/www/html/bin/console messenger:consume task_deletion --quiet --limit=10 --time-limit=3600 &

tail -f /dev/null