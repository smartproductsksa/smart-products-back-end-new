# Storage Migration: S3 to Local Storage

## Overview

This document describes the migration from AWS S3 cloud storage to local storage for image uploads in the smart-products back-end application.

## Date of Migration

October 11, 2025

## Changes Made

### 1. Updated Form Configurations

The following Filament form schemas were updated to use the `public` disk instead of `s3`:

#### PageForm.php
- **Location**: `app/Filament/Resources/Pages/Schemas/PageForm.php`
- **Changes**:
  - Hero images: `disk('s3')` → `disk('public')`, directory: `pages/hero`
  - Gallery images: `disk('s3')` → `disk('public')`, directory: `pages/gallery`
  - Section images: `disk('s3')` → `disk('public')`, directory: `pages/sections`

#### ArticleForm.php
- **Location**: `app/Filament/Resources/Articles/Schemas/ArticleForm.php`
- **Changes**:
  - Main images: `disk('s3')` → `disk('public')`, directory: `articles`
  - Rich editor attachments: `fileAttachmentsDisk('s3')` → `fileAttachmentsDisk('public')`, directory: `article-attachments`

#### NewsForm.php
- **Location**: `app/Filament/Resources/News/Schemas/NewsForm.php`
- **Changes**:
  - Main images: `disk('s3')` → `disk('public')`, directory: `news`
  - Rich editor attachments: `fileAttachmentsDisk('s3')` → `fileAttachmentsDisk('public')`, directory: `news-attachments`

### 2. Storage Symlink

The symbolic link between `public/storage` and `storage/app/public` was created/verified using:

```bash
php artisan storage:link
```

This allows public access to uploaded images via the web server.

### 3. Storage Structure

Images are now stored in the following directories under `storage/app/public/`:

```
storage/app/public/
├── articles/              # Article main images
├── news/                  # News main images
├── pages/
│   ├── hero/             # Page hero images
│   ├── gallery/          # Page gallery images
│   └── sections/         # Page section images
├── article-attachments/  # Rich editor attachments for articles
└── news-attachments/     # Rich editor attachments for news
```

### 4. Public Access

All uploaded images are accessible via:
- URL pattern: `{APP_URL}/storage/{directory}/{filename}`
- Example: `http://localhost/storage/articles/image.jpg`

## Configuration

### Filesystem Configuration
The `public` disk configuration in `config/filesystems.php`:

```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
    'throw' => false,
    'report' => false,
],
```

### S3 Configuration
The S3 disk configuration remains in place but is no longer used for images. It can be removed if not needed for other purposes.

## Testing

### Unit Tests
- **File**: `tests/Unit/StorageConfigurationTest.php`
- **Tests**: 10 tests covering storage configuration and operations
- **Status**: ✅ All passing

### Integration Tests
- **File**: `tests/Feature/ImageStorageIntegrationTest.php`
- **Tests**: 10 tests covering image storage for all resource types
- **Status**: ✅ All passing

Run tests with:
```bash
php artisan test tests/Unit/StorageConfigurationTest.php
php artisan test tests/Feature/ImageStorageIntegrationTest.php
```

## Benefits of Local Storage

1. **Cost Reduction**: No AWS S3 charges for storage or data transfer
2. **Simplicity**: Easier setup and configuration
3. **Performance**: Direct file system access is faster than cloud storage
4. **Development**: Simpler local development environment
5. **Portability**: Easier to backup and migrate with the application

## Considerations

1. **Backup**: Ensure `storage/app/public` is included in backup strategies
2. **Scaling**: For high-traffic applications, consider CDN integration
3. **Docker Volumes**: When using Docker, mount `storage/app/public` as a volume
4. **Disk Space**: Monitor disk usage as images accumulate
5. **Permissions**: Ensure proper file permissions (755 for directories, 644 for files)

## Docker Setup

When using Docker, ensure the storage directory is mounted as a volume in `docker-compose.yml`:

```yaml
services:
  backend:
    volumes:
      - ./storage/app/public:/var/www/html/storage/app/public
```

## Migration from Existing S3 Data

If you have existing images in S3, you'll need to:

1. Download all images from S3
2. Organize them into the appropriate directories
3. Update database records if they contain full S3 URLs
4. Run a migration script to copy files to local storage

## Reverting to S3

To revert back to S3 storage:

1. Update all form files to use `disk('s3')` instead of `disk('public')`
2. Ensure S3 credentials are configured in `.env`
3. Upload existing local images to S3
4. Update database records if needed

## Troubleshooting

### Issue: Images not showing after upload

**Symptom**: After uploading an image, it keeps loading but doesn't display when you return to edit.

**Solution**: 
1. Ensure the `visibility('public')` setting is added to all FileUpload components (already fixed in this migration)
2. Run the fix permissions command to update existing files:
   ```bash
   php artisan storage:fix-permissions
   ```
3. Clear caches:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

### Issue: Storage symlink not working

**Symptom**: 404 errors when accessing images via `/storage/` URL

**Solution**:
```bash
php artisan storage:link
```

If it says the link already exists but still not working:
```bash
rm public/storage
php artisan storage:link
```

### Issue: Permission denied errors

**Symptom**: Cannot write to storage directories

**Solution**:
```bash
chmod -R 775 storage/app/public
chmod -R 775 bootstrap/cache
```

## Commands

### Fix Storage Permissions
Automatically sets the correct visibility for all uploaded files:
```bash
php artisan storage:fix-permissions
```

This command:
- Creates missing directories
- Sets all files to public visibility
- Reports the number of files processed

## Support

For issues or questions about the storage system, refer to:
- Laravel Storage Documentation: https://laravel.com/docs/filesystem
- Filament FileUpload Documentation: https://filamentphp.com/docs/forms/fields/file-upload
