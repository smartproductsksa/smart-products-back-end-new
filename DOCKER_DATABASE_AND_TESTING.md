# Docker: Database Management & Testing Guide

Complete guide for managing database migrations, seeders, and running tests in Docker.

## Table of Contents
- [Initial Setup](#initial-setup)
- [Database Migrations](#database-migrations)
- [Database Seeders](#database-seeders)
- [Running Tests](#running-tests)
- [Automated Initialization](#automated-initialization)
- [Common Workflows](#common-workflows)

---

## Initial Setup

### First Time Setup

When you first set up the application, run the initialization script:

```bash
docker-compose exec app sh docker/init-app.sh
```

This will:
- ✅ Wait for database to be ready
- ✅ Run all migrations
- ✅ Create storage symlink
- ✅ Setup Filament Shield permissions
- ✅ Clear and cache configuration
- ✅ Fix storage permissions

### Create Admin User

After initialization, create your admin user:

```bash
docker-compose exec app php artisan make:filament-user
```

Follow the prompts to enter:
- Name
- Email
- Password

---

## Database Migrations

### Run Migrations

```bash
# Run all pending migrations
docker-compose exec app php artisan migrate

# Force run in production (no confirmation)
docker-compose exec app php artisan migrate --force

# Run a specific migration
docker-compose exec app php artisan migrate --path=/database/migrations/2025_10_09_155727_create_articles_table.php
```

### Rollback Migrations

```bash
# Rollback the last batch
docker-compose exec app php artisan migrate:rollback

# Rollback all migrations
docker-compose exec app php artisan migrate:reset

# Rollback and re-run all migrations
docker-compose exec app php artisan migrate:refresh

# Rollback and re-run with seeding
docker-compose exec app php artisan migrate:refresh --seed
```

### Check Migration Status

```bash
# View migration status
docker-compose exec app php artisan migrate:status

# Show database information
docker-compose exec app php artisan db:show

# Show specific table structure
docker-compose exec app php artisan db:table users
```

### Create New Migration

```bash
# Create a new migration
docker-compose exec app php artisan make:migration create_products_table

# Create migration for modifying table
docker-compose exec app php artisan make:migration add_status_to_products_table
```

---

## Database Seeders

### Current Seeders

Your `DatabaseSeeder` creates a test user:
- Email: `test@example.com`
- Name: Test User

### Run Seeders

```bash
# Run all seeders
docker-compose exec app php artisan db:seed

# Run in production (force)
docker-compose exec app php artisan db:seed --force

# Run a specific seeder
docker-compose exec app php artisan db:seed --class=UserSeeder
```

### Create New Seeder

```bash
# Create a new seeder
docker-compose exec app php artisan make:seeder ProductSeeder
```

### Fresh Database with Seeding

```bash
# Drop all tables, re-migrate, and seed
docker-compose exec app php artisan migrate:fresh --seed
```

⚠️ **Warning**: `migrate:fresh` will destroy ALL data in your database!

---

## Running Tests

### Quick Test Run

Use the test runner script:

```bash
# Run all tests
docker-compose exec app sh docker/run-tests.sh

# Run specific test file
docker-compose exec app sh docker/run-tests.sh tests/Feature/ArticleResourceTest.php

# Run specific test method
docker-compose exec app sh docker/run-tests.sh --filter testCanCreateArticle
```

### Manual PHPUnit Commands

```bash
# Run all tests
docker-compose exec app vendor/bin/phpunit

# Run tests with coverage
docker-compose exec app vendor/bin/phpunit --coverage-html coverage

# Run only feature tests
docker-compose exec app vendor/bin/phpunit --testsuite Feature

# Run only unit tests
docker-compose exec app vendor/bin/phpunit --testsuite Unit

# Run tests in parallel (faster)
docker-compose exec app php artisan test --parallel

# Run with verbose output
docker-compose exec app vendor/bin/phpunit --verbose
```

### Your Available Tests

**Unit Tests:**
- `tests/Unit/ExampleTest.php`
- `tests/Unit/ArticleTest.php`
- `tests/Unit/ContactSubmissionModelTest.php`
- `tests/Unit/StorageConfigurationTest.php`

**Feature Tests:**
- `tests/Feature/ArticleResourceTest.php`
- `tests/Feature/PageExportImportTest.php`
- `tests/Feature/FaqSectionTest.php`
- `tests/Feature/ContactSubmissionTest.php`
- `tests/Feature/ImageStorageIntegrationTest.php`
- `tests/Feature/DetailedGalleryTest.php`
- `tests/Feature/FixStoragePermissionsTest.php`
- `tests/Feature/MailingListTest.php`

### Run Specific Test Categories

```bash
# Test articles
docker-compose exec app vendor/bin/phpunit --filter Article

# Test storage/uploads
docker-compose exec app vendor/bin/phpunit --filter Storage

# Test API endpoints
docker-compose exec app vendor/bin/phpunit --filter Api
```

### Testing Database

By default, tests use the **same database** as development. To use a separate test database:

1. Update `.env.testing`:
```env
DB_DATABASE=laravel_test
DB_CONNECTION=mysql
```

2. Create test database:
```bash
docker-compose exec mysql mysql -u root -p -e "CREATE DATABASE laravel_test;"
```

3. Run migrations for test database:
```bash
docker-compose exec app php artisan migrate --env=testing
```

---

## Automated Initialization

### The `init-app.sh` Script

Located at `docker/init-app.sh`, this script handles complete application initialization:

```bash
docker-compose exec app sh docker/init-app.sh
```

**What it does:**

1. **Database Check**: Waits for MySQL to be ready
2. **Migrations**: Runs all pending migrations
3. **Storage Link**: Creates symbolic link for public storage
4. **Shield Setup**: Generates Filament Shield permissions
5. **Cache Management**: Clears and rebuilds all caches
6. **Permissions**: Sets correct storage permissions
7. **Storage Fix**: Runs custom storage permissions command

### When to Use

- ✅ First time setup
- ✅ After pulling new migrations
- ✅ After deployment
- ✅ When rebuilding containers
- ✅ After database restoration

### Customizing Initialization

To enable automatic seeding, edit `docker/init-app.sh`:

```bash
# Uncomment this line:
php artisan db:seed --force
```

---

## Common Workflows

### 1. Fresh Start (Development)

Complete reset and rebuild:

```bash
# Stop containers
docker-compose down

# Remove database volume (⚠️ destroys data!)
docker volume rm back-end_mysql-data

# Start fresh
docker-compose up -d

# Wait for MySQL
sleep 15

# Initialize
docker-compose exec app sh docker/init-app.sh

# Seed database
docker-compose exec app php artisan db:seed

# Create admin user
docker-compose exec app php artisan make:filament-user
```

### 2. Pull Changes & Update Database

When teammates add new migrations:

```bash
# Pull latest code
git pull

# Rebuild containers (if Dockerfile changed)
docker-compose up -d --build

# Run new migrations
docker-compose exec app php artisan migrate

# Clear caches
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
```

### 3. Before Committing Code

Run tests to ensure nothing broke:

```bash
# Run all tests
docker-compose exec app sh docker/run-tests.sh

# Or with Laravel's test command
docker-compose exec app php artisan test

# Check code style (if you have Pint)
docker-compose exec app vendor/bin/pint --test
```

### 4. Backup Database

```bash
# Create backup
docker-compose exec mysql mysqldump -u laravel -p laravel > backup.sql

# Or with root access
docker-compose exec mysql mysqldump -u root -p laravel > backup_$(date +%Y%m%d).sql
```

### 5. Restore Database

```bash
# From backup file
docker-compose exec -T mysql mysql -u root -p laravel < backup.sql

# After restore, clear cache
docker-compose exec app php artisan config:clear
```

### 6. Reset Development Database

```bash
# Drop, recreate, migrate, and seed
docker-compose exec app php artisan migrate:fresh --seed

# Then run tests
docker-compose exec app sh docker/run-tests.sh
```

### 7. Deploy to Production

```bash
# On production server:

# Pull latest code
git pull

# Rebuild containers
docker-compose up -d --build

# Run migrations (no rollback!)
docker-compose exec app php artisan migrate --force

# Clear and cache
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Restart queue workers
docker-compose restart queue
```

---

## Filament Shield Setup

### Initial Setup

After migrations, setup Shield permissions:

```bash
# Install Shield (creates roles & permissions tables)
docker-compose exec app php artisan shield:install

# Generate permissions for all resources
docker-compose exec app php artisan shield:generate --all
```

This creates:
- `super_admin` role
- Permissions for all Filament resources
- Policy classes

### Assign Super Admin Role

```bash
docker-compose exec app php artisan tinker
```

Then in Tinker:
```php
$user = \App\Models\User::where('email', 'your@email.com')->first();
$user->assignRole('super_admin');
exit
```

Or create a seeder for this:

```php
// database/seeders/AdminUserSeeder.php
public function run()
{
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
    ]);
    
    $admin->assignRole('super_admin');
}
```

---

## Troubleshooting

### "Database connection refused"

Wait for MySQL to be healthy:
```bash
docker-compose ps
# Wait until mysql shows "healthy"
```

### "SQLSTATE[HY000] [2002] Connection refused"

Container can't reach MySQL. Check:
```bash
# Verify MySQL is running
docker-compose ps mysql

# Check network
docker network ls | grep laravel

# Restart services
docker-compose restart app mysql
```

### Tests Failing Due to Database

```bash
# Reset test database
docker-compose exec app php artisan migrate:fresh --env=testing

# Clear test cache
docker-compose exec app php artisan config:clear --env=testing
```

### Migration Already Ran Error

```bash
# Check migration table
docker-compose exec mysql mysql -u laravel -p -e "SELECT * FROM laravel.migrations;"

# If needed, manually remove entry
docker-compose exec mysql mysql -u laravel -p -e "DELETE FROM laravel.migrations WHERE migration = 'migration_name';"
```

### Storage Permission Errors

```bash
# Fix permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache

# Or run the custom command
docker-compose exec app php artisan storage:fix-permissions
```

---

## Best Practices

### Development

✅ **DO:**
- Run tests before committing
- Use `migrate:fresh --seed` for clean slate during development
- Keep seeders updated with realistic test data
- Use factories for test data generation

❌ **DON'T:**
- Edit old migrations (create new ones instead)
- Commit without running tests
- Use production data in development database

### Production

✅ **DO:**
- Always backup before migrations
- Run migrations in maintenance mode
- Test migrations on staging first
- Use `--force` flag for non-interactive mode
- Keep track of migration history

❌ **DON'T:**
- Use `migrate:fresh` in production (destroys data!)
- Run seeders in production (unless intentional)
- Skip testing migrations on staging

---

## Quick Reference

```bash
# Initialization
docker-compose exec app sh docker/init-app.sh

# Database
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan migrate:fresh --seed

# Tests
docker-compose exec app sh docker/run-tests.sh
docker-compose exec app php artisan test

# Cache
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose exec app php artisan config:clear

# Tinker (interactive shell)
docker-compose exec app php artisan tinker

# Database access
docker-compose exec mysql mysql -u laravel -p

# Logs
docker-compose logs app
docker-compose logs mysql
docker-compose logs -f  # Follow mode
```

---

## Additional Resources

- [Laravel Migrations Documentation](https://laravel.com/docs/migrations)
- [Laravel Seeding Documentation](https://laravel.com/docs/seeding)
- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Filament Shield Documentation](https://github.com/bezhanSalleh/filament-shield)

---

**Need Help?**

Check the logs:
```bash
docker-compose logs app --tail 50
docker-compose logs mysql --tail 50
```

Or access the container:
```bash
docker-compose exec app sh
```

