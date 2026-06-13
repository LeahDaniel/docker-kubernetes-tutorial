#!/bin/sh
set -e

cd /var/www/html

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

# Discover packages and warm caches when APP_KEY is available.
if [ -n "$APP_KEY" ]; then
    php artisan package:discover --ansi
    php artisan config:cache
    php artisan route:cache
fi

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
