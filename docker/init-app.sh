#!/bin/bash
set -e

echo "🚀 Initializing Laravel Application..."

# Wait for database to be ready
echo "⏳ Waiting for database..."
until php artisan db:show 2>/dev/null; do
    echo "Database is unavailable - sleeping"
    sleep 2
done

echo "✅ Database is ready!"

# Run migrations
echo "📦 Running migrations..."
php artisan migrate --force

# Create storage symlink if it doesn't exist
echo "🔗 Creating storage symlink..."
php artisan storage:link || true

# Setup Filament Shield permissions
echo "🛡️  Setting up Filament Shield..."
php artisan shield:generate --all --force || echo "Shield generation skipped (resources may not exist yet)"

# Seed database (optional - uncomment if needed)
# echo "🌱 Seeding database..."
# php artisan db:seed --force

# Clear and cache configuration
echo "🗑️  Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "📝 Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix storage permissions
echo "🔐 Setting storage permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Run custom storage permissions fix
echo "🖼️  Fixing storage permissions for uploads..."
php artisan storage:fix-permissions || echo "Storage fix command not available"

echo ""
echo "✨ Application initialization complete!"
echo "🌐 Access your application at: https://localhost:8443"
echo "🔐 Admin panel: https://localhost:8443/admin"
echo "📡 API: https://localhost:8443/api/v1/"
echo ""
echo "📝 Next steps:"
echo "   1. Create admin user: docker-compose exec app php artisan make:filament-user"
echo "   2. Assign super_admin role manually if needed"
echo ""

