#!/usr/bin/env bash
# Mise à jour du site sur un serveur avec accès SSH
set -e
php artisan down --retry=30 || true
composer install --no-dev --optimize-autoloader --no-interaction
if command -v npm >/dev/null 2>&1; then npm ci && npm run build; fi
php artisan migrate --force
php artisan storage:link --force || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
echo "Site à jour."
