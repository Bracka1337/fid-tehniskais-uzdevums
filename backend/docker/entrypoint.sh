#!/bin/sh
set -e

mkdir -p /data
if [ ! -f /data/database.sqlite ]; then
  touch /data/database.sqlite
fi
chown -R www-data:www-data /data

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
  if [ -f /data/app_key ]; then
    APP_KEY="$(cat /data/app_key)"
  else
    APP_KEY="$(php -r 'echo "base64:".base64_encode(random_bytes(32));')"
    printf '%s' "$APP_KEY" > /data/app_key
  fi
  export APP_KEY
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache storage/app/remote
chown -R www-data:www-data storage bootstrap/cache

php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction || true

if [ ! -f storage/app/remote/documents.xml ]; then
  php artisan documents:generate-sample --count=25 --no-interaction 2>/dev/null || true
fi

php artisan documents:sync --local --no-interaction 2>/dev/null || true

exec php-fpm -F
