#!/bin/sh
set -e

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

php artisan package:discover --ansi

# Defaults for Phase 1 (no .env in the image). Override via docker run -e when needed.
export SESSION_DRIVER="${SESSION_DRIVER:-file}"
export CACHE_STORE="${CACHE_STORE:-file}"
export QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"

if [ -n "$APP_KEY" ]; then
    php artisan migrate --force
    php artisan config:cache
    php artisan route:cache
fi

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
