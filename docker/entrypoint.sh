#!/bin/bash
set -e

echo "==> Starting NJAMA Pharmacy Application..."

# Configure Apache Port dynamically for Render/Cloud (default 80 or $PORT)
PORT="${PORT:-80}"
echo "==> Configuring Apache to listen on port ${PORT}..."
sed -i "s/Listen [0-9]*/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Setup SQLite if selected as DB_CONNECTION
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    echo "==> Using SQLite database..."
    export DB_CONNECTION="sqlite"
    export DB_DATABASE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    if [ ! -f "$DB_DATABASE" ]; then
        echo "==> Creating SQLite file at $DB_DATABASE..."
        touch "$DB_DATABASE"
    fi
    chown -R www-data:www-data /var/www/html/database
    chmod -R 775 /var/www/html/database
fi

# Set proper permissions for storage & cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate Application Key if not present
if [ -z "$APP_KEY" ]; then
    echo "==> Generating application key..."
    php artisan key:generate --force
fi

# Storage symlink
php artisan storage:link --force || true

# Run database migrations and seeders automatically
echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Seeding initial data (if needed)..."
php artisan db:seed --force || true

# Clear and optimize Laravel caches
echo "==> Caching configuration, routes, and views..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> NJAMA PHARMACY is ready! Launching Apache..."
exec apache2-foreground
