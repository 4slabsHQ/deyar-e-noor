#!/usr/bin/env bash

set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP_DIR"

echo "==> Deploying Deyar-e-Noor from ${APP_DIR}"

echo "==> Pulling latest code..."
git fetch origin main
git pull origin main

echo "==> Installing PHP dependencies..."
if command -v composer >/dev/null 2>&1; then
    composer install --no-dev --optimize-autoloader --no-interaction
elif [ -f "${HOME}/composer.phar" ]; then
    php "${HOME}/composer.phar" install --no-dev --optimize-autoloader --no-interaction
else
    echo "Composer not found. Install it or place composer.phar at ~/composer.phar"
    exit 1
fi

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Syncing permissions..."
php artisan permissions:sync

echo "==> Resetting permission cache..."
php artisan permission:cache-reset

if command -v npm >/dev/null 2>&1; then
    echo "==> Building frontend assets..."
    npm ci
    npm run build
else
    echo "==> Skipping npm build (npm not found)."
fi

echo "==> Optimizing Laravel..."
php artisan storage:link --force 2>/dev/null || true
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:cache
php artisan event:cache

if php artisan list --raw 2>/dev/null | grep -q '^queue:restart$'; then
    echo "==> Restarting queue workers..."
    php artisan queue:restart || true
fi

echo "==> Deploy complete."
