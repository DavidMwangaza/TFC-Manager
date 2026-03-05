#!/bin/sh
set -e

cd /var/www/html

# Générer le cache de config/routes/views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Exécuter les migrations automatiquement
php artisan migrate --force

# Lien symbolique storage
php artisan storage:link 2>/dev/null || true

# Lancer Supervisor (nginx + php-fpm + queue)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
