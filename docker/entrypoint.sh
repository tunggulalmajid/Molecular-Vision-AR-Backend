#!/bin/sh
set -e

# Pastikan folder storage dan bootstrap/cache memiliki permission yang benar
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Jalankan storage link jika belum ada
if [ ! -L /var/www/html/public/storage ]; then
    php /var/www/html/artisan storage:link || true
fi

# Jalankan perintah utama (supervisord)
exec "$@"
