# Laravel Filament Boilerplate

A modern Laravel boilerplate with Filament admin panel, role-based permissions, and essential packages pre-configured for rapid application development.

## Features

- **Laravel 12** - Latest Laravel framework
- **Filament 4.1** - Beautiful admin panel with modern UI
- **Filament Shield** - Role and permission management
- **Laravel Trend** - Data trend analysis
- **Local File Storage** - Images stored locally with public access
- **TailwindCSS** - Utility-first CSS framework
- **Vite** - Fast build tool
- **Concurrently** - Run multiple dev servers simultaneously

## Requirements

### For Docker (Recommended)
- Docker & Docker Compose
- At least 4GB RAM allocated to Docker

### For Local Development
- PHP 8.3 or higher (with extensions: intl, bcmath, gd, zip, pdo_mysql)
- Composer
- Node.js & NPM
- MySQL/PostgreSQL/SQLite database

## Installation

### 1. Clone the Repository

```bash
git clone <your-repo-url>
cd back-end
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

Create your `.env` file:

```bash
cp .env.example .env
```

Configure your database and other settings in `.env`:

```env
APP_NAME="Your App Name"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Create Filament Admin User

Create your first admin user:

```bash
php artisan make:filament-user
```

You'll be prompted to enter:
- Name
- Email
- Password

### 7. Setup Filament Shield

Install Shield permissions and roles:

```bash
php artisan shield:install
```

Then generate permissions for your resources:

```bash
php artisan shield:generate --all
```

This will create:
- Super Admin role with all permissions
- Permissions for all Filament resources
- Policy classes for authorization

### 8. Create Storage Symlink

Create a symbolic link for public file access:

```bash
php artisan storage:link
```

This links `public/storage` to `storage/app/public` for serving uploaded images.

### 9. Assign Super Admin Role

Assign the super_admin role to your user via Tinker:

```bash
php artisan tinker
```

Then run:

```php
$user = \App\Models\User::find(1); // Replace 1 with your user ID
$user->assignRole('super_admin');
```

Or create a seeder to automate this process.

### 10. Build Assets

```bash
npm run build
```

## Running the Application

### Option 1: Docker (Recommended for Production)

**Quick Setup:**
```bash
./docker-setup.sh
```

**Manual Setup:**
```bash
# 1. Setup environment
cp docker/env.example .env

# 2. Generate SSL certificates
cd docker/nginx/ssl && ./generate-ssl.sh && cd ../../..

# 3. Start services
docker-compose up -d

# 4. Initialize database
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan make:filament-user
```

**Access:**
- HTTP: `http://localhost:8080` (redirects to HTTPS)
- HTTPS: `https://localhost:8443`
- Admin: `https://localhost:8443/admin`
- API: `https://localhost:8443/api/v1/`

**Note:** Self-signed SSL certificates are used for development. Your browser will show a security warning that you need to accept.

See [DOCKER_QUICK_START.md](./DOCKER_QUICK_START.md) and [DOCKER_SETUP_GUIDE.md](./DOCKER_SETUP_GUIDE.md) for details.

#### Docker Troubleshooting

**Permission Errors (Production Servers)**

If you see errors like:
```
The stream or file "/var/www/html/storage/logs/laravel.log" could not be opened
The /var/www/html/bootstrap/cache directory must be present and writable
```

Fix with:
```bash
# Stop containers
docker-compose down

# Fix permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache  
chmod -R 777 storage/logs storage/framework

# Create directories
mkdir -p storage/framework/{cache,sessions,testing,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Rebuild and restart
docker-compose build --no-cache
docker-compose up -d

# Run initialization
docker-compose exec app sh /var/www/html/docker/init-app.sh
```

**Using Standard Ports (80/443)**

Create a `.env` file with:
```env
HTTP_PORT=80
HTTPS_PORT=443
```

Then restart:
```bash
docker-compose down
docker-compose up -d
```

### Option 2: Local Development

**Quick Setup (Automated):**

```bash
composer setup
```

This will:
- Install Composer dependencies
- Copy `.env.example` to `.env`
- Generate application key
- Run migrations
- Install NPM dependencies
- Build assets

**Development Mode:**

Start all development servers (Laravel, Queue, Logs, Vite):

```bash
composer dev
```

This runs:
- Laravel development server on `http://localhost:8000`
- Queue worker
- Log viewer (Laravel Pail)
- Vite dev server for hot module replacement

Or run them separately:

```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Queue Worker
php artisan queue:listen

# Terminal 3 - Vite Dev Server
npm run dev
```

### Access Filament Admin Panel

**Docker:**
```
https://localhost/admin
```

**Local:**
```
http://localhost:8000/admin
```

Login with the credentials you created earlier.

## Filament Shield Usage

### Managing Roles & Permissions

1. Navigate to **Admin Panel** → **Shield** → **Roles**
2. Create new roles or edit existing ones
3. Assign permissions to roles
4. Assign roles to users

### Protecting Resources

Filament Shield automatically generates policies for your resources. To use them:

```php
// In your Filament Resource
protected static ?string $model = YourModel::class;

// Policies are automatically applied
```

### Custom Permissions

Add custom permissions in your resource:

```php
public static function getPermissionPrefixes(): array
{
    return [
        'view',
        'view_any',
        'create',
        'update',
        'delete',
        'delete_any',
        'publish', // Custom permission
    ];
}
```

Then regenerate permissions:

```bash
php artisan shield:generate --resource=YourResource
```

## Testing

Run tests:

```bash
composer test
```

Or directly:

```bash
php artisan test
```

## Artisan Commands

### Fix Storage Permissions

If uploaded images are not displaying properly, run this command to fix file visibility:

```bash
php artisan storage:fix-permissions
```

This command will:
- Create missing storage directories
- Set all uploaded files to public visibility
- Process all image directories (articles, news, pages, etc.)

### Page Export/Import

Transfer pages between environments (dev → staging → production):

```bash
# List all pages
php artisan pages:list

# Export all pages
php artisan pages:export --all

# Export specific page
php artisan pages:export --slug=home

# Import from file
php artisan pages:import --file=storage/exports/pages/home.json

# Import from directory
php artisan pages:import --directory=storage/exports/pages --update
```

See [PAGE_EXPORT_IMPORT_GUIDE.md](./PAGE_EXPORT_IMPORT_GUIDE.md) for detailed usage.

## Code Style

Format code with Laravel Pint:

```bash
./vendor/bin/pint
```

## Additional Configuration

### File Storage

Images are stored locally in `storage/app/public/` with the following structure:

```
storage/app/public/
├── articles/              # Article images
├── news/                  # News images
├── pages/
│   ├── hero/             # Page hero images
│   ├── gallery/          # Simple gallery images
│   ├── detailed-gallery/ # Detailed gallery images (clients, team, etc.)
│   └── sections/         # Page section images
├── article-attachments/  # Rich editor attachments
└── news-attachments/     # Rich editor attachments
```

Images are accessible via: `{APP_URL}/storage/{directory}/{filename}`

For details on the storage system, see [STORAGE_MIGRATION.md](./STORAGE_MIGRATION.md).

### Page Builder Content Blocks

The page builder supports the following content blocks:

- **Hero Section** - Large header with title, text, and image
- **Text Section** - Rich text content with formatting
- **Image Gallery (Simple)** - Multiple images with one title (for photo galleries)
- **Gallery with Details** - Each item has title, image, and description (for clients, team, partners)
- **Text with Image** - Combination of text and image side-by-side
- **FAQ Section** - Frequently Asked Questions with expandable Q&A items
- **Model List** - Dynamic content from articles, news, or categories

See detailed guides:
- [DETAILED_GALLERY_GUIDE.md](./DETAILED_GALLERY_GUIDE.md) - Gallery features
- [FAQ_SECTION_GUIDE.md](./FAQ_SECTION_GUIDE.md) - FAQ section implementation

### AWS S3 Setup (Optional)

If you need S3 storage for other purposes, configure it in `.env`:

```env
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
AWS_USE_PATH_STYLE_ENDPOINT=false
```

**Note**: Images for pages, articles, and news are currently configured to use local storage, not S3.

### Queue Configuration

For production, use a proper queue driver:

```env
QUEUE_CONNECTION=redis
```

## Project Structure

```
app/
├── Filament/
│   ├── Pages/          # Custom Filament pages
│   ├── Resources/      # Filament resources (CRUD)
│   └── Widgets/        # Dashboard widgets
├── Models/             # Eloquent models
├── Policies/           # Authorization policies
└── Providers/          # Service providers

database/
├── migrations/         # Database migrations
└── seeders/           # Database seeders
```

## License

This boilerplate is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
