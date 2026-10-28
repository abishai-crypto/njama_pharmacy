#!/bin/bash
set -e

echo "==> Starting NJAMA Pharmacy Application..."

# Configure Apache Port dynamically for Render/Cloud (default 80 or $PORT)
PORT="${PORT:-80}"
echo "==> Configuring Apache to listen on port ${PORT}..."
sed -i "s/Listen [0-9]*/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Ensure .env file exists so Laravel commands don't crash
if [ ! -f /var/www/html/.env ]; then
    echo "==> Preparing .env file..."
    if [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    else
        touch /var/www/html/.env
    fi
fi

# Setup SQLite if selected as DB_CONNECTION
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    echo "==> Using SQLite database..."
    export DB_CONNECTION="sqlite"
    export DB_DATABASE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    mkdir -p /var/www/html/database
    if [ ! -f "$DB_DATABASE" ]; then
        echo "==> Creating SQLite file at $DB_DATABASE..."
        touch "$DB_DATABASE"
    fi
    chown -R www-data:www-data /var/www/html/database
    chmod -R 777 /var/www/html/database
    chmod 666 "$DB_DATABASE"
fi

# Set proper permissions for storage & cache
mkdir -p /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate Application Key if not present
if [ -z "$APP_KEY" ]; then
    echo "==> Generating application key..."
    php artisan key:generate --force || true
    KEY_VAL=$(grep '^APP_KEY=' /var/www/html/.env 2>/dev/null | cut -d '=' -f2-)
    if [ -n "$KEY_VAL" ]; then
        export APP_KEY="$KEY_VAL"
    fi
fi

# Storage symlink
php artisan storage:link --force || true

# Run database migrations automatically
echo "==> Running database migrations..."
php artisan migrate --force || true

# Seed ONLY if database is brand new (no users exist)
echo "==> Checking if initial database seeding is required..."
USER_COUNT=$(php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); try { echo App\Models\User::count(); } catch (\Throwable \$e) { echo '0'; }" 2>/dev/null || echo "0")

if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "==> Fresh database detected. Seeding initial users and medicines catalog..."
    php artisan db:seed --force || true
else
    echo "==> Existing database detected (${USER_COUNT} users found). Seeding skipped to preserve sales and inventory!"
fi

# Clear and optimize Laravel caches
echo "==> Caching configuration, routes, and views..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> NJAMA PHARMACY is ready! Launching Apache on port ${PORT}..."
exec apache2-foreground
