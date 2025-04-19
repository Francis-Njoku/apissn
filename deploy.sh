#!/bin/bash
cd ~/test-server.ftm.ng
git pull origin staging
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 755 .
chmod -R 777 storage bootstrap/cache
