#!/bin/bash
# Jalankan di server hosting via SSH / Terminal cPanel
# cd /home/evaluas4/evaluasiobe && bash deploy-hosting.sh

set -e

APP_DIR="/home/evaluas4/evaluasiobe"
cd "$APP_DIR"

echo "==> Copy .env production (jika belum ada)"
if [ ! -f .env ]; then
  cp .env.production .env
  echo "    .env dibuat dari .env.production"
else
  echo "    .env sudah ada, lewati copy"
fi

echo "==> Composer install (production)"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Storage link"
php artisan storage:link 2>/dev/null || true

echo "==> Migrate database"
php artisan migrate --force

echo "==> Clear & cache config"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Set permission storage & cache"
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "==> Deploy selesai!"
echo "    Cek: https://evaluasiobe.com"
