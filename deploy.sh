#!/usr/bin/env bash
set -euo pipefail

# Deploy-Skript fuer den Netcup-Webserver (klassisches Shared-/VPS-LAMP-Hosting, kein
# Docker/PaaS). Voraussetzung: .env liegt bereits produktiv auf dem Server (APP_ENV=production,
# APP_DEBUG=false, echte DB-/Mail-Zugangsdaten, ADMIN_EMAIL/ADMIN_PASSWORD_HASH) und wird von
# diesem Skript nicht angelegt oder veraendert. Apache/Nginx-DocumentRoot zeigt auf public/.
# Cron (einmalig einzurichten, nicht Teil dieses Skripts): * * * * * php artisan schedule:run
# - fuehrt intern auch appointments:send-reminders, invoices:generate-recurring,
# reports:weekly-summary UND das minuetliche queue:work (siehe routes/console.php) aus.

echo "==> git pull"
git pull --ff-only

echo "==> composer install (production, ohne dev-Abhaengigkeiten)"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> npm install & Asset-Build"
npm ci --no-audit --no-fund
npm run build

echo "==> Datenbank-Migrationen"
php artisan migrate --force

echo "==> Storage-Symlink (public/storage -> storage/app/public, fuer Avatare/Belege)"
php artisan storage:link || true

echo "==> Caches neu aufbauen"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Fertig."
