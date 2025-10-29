#!/bin/bash
set -e

echo "🧪 Running Laravel Tests in Docker..."

# Set testing environment
export APP_ENV=testing

# Clear test caches
echo "🗑️  Clearing test caches..."
php artisan config:clear
php artisan cache:clear

# Run database migrations for testing
echo "📦 Preparing test database..."
php artisan migrate --env=testing --force

# Run PHPUnit tests
echo "🔬 Running PHPUnit tests..."
vendor/bin/phpunit "$@"

# Show summary
echo ""
echo "✅ Tests completed!"

