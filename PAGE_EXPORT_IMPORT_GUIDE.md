# Page Export/Import Guide

## Overview

The Page Export/Import feature allows you to easily transfer pages between environments (development, staging, production). Pages are exported as JSON files that preserve all content including complex structures like FAQs and galleries.

## Features

✅ **Export single or all pages** to JSON files  
✅ **Import from file or directory** with validation  
✅ **Update existing pages** or skip them  
✅ **Preserves all content** including nested structures  
✅ **Validation** ensures data integrity  
✅ **Safe operations** with confirmation prompts  
✅ **Full test coverage** (9 tests, 42 assertions)

---

## Commands

### 1. List Pages

View all pages in your database:

```bash
php artisan pages:list
```

**Output:**
```
Found 3 page(s):

+----+---------------+---------------+-----------+-------+--------+-----------------------------+-------------------+
| ID | Title         | Slug          | Status    | Order | Blocks | Block Types                 | Updated           |
+----+---------------+---------------+-----------+-------+--------+-----------------------------+-------------------+
| 1  | Home          | home          | published | 1     | 5      | hero, text_section, faq     | 2025-10-25 18:30  |
| 2  | About Us      | about         | published | 2     | 3      | hero, detailed_gallery      | 2025-10-25 17:15  |
| 3  | Contact       | contact       | draft     | 3     | 2      | text_section, faq           | 2025-10-25 16:45  |
+----+---------------+---------------+-----------+-------+--------+-----------------------------+-------------------+
```

---

### 2. Export Pages

#### Export a Single Page

```bash
php artisan pages:export --slug=home
```

**Output:**
```
Created output directory: storage/exports/pages
✓ Exported: Home → home.json

Successfully exported 1 page(s) to: storage/exports/pages

To import these pages in another environment, run:
php artisan pages:import --file=<filename> or --directory=<directory>
```

#### Export All Pages

```bash
php artisan pages:export --all
```

**Output:**
```
✓ Exported: Home → home.json
✓ Exported: About Us → about.json
✓ Exported: Contact → contact.json

Successfully exported 3 page(s) to: storage/exports/pages
```

#### Custom Output Directory

```bash
php artisan pages:export --all --output=/path/to/exports
```

---

### 3. Import Pages

#### Import a Single File

```bash
php artisan pages:import --file=storage/exports/pages/home.json
```

**Output:**
```
Found 1 file(s) to import.

Do you want to proceed with the import? (yes/no) [yes]:
> yes

✓ Imported: Home (home)

Import completed:
  ✓ Imported: 1
```

#### Import from Directory

```bash
php artisan pages:import --directory=storage/exports/pages
```

**Output:**
```
Found 3 file(s) to import.

Do you want to proceed with the import? (yes/no) [yes]:
> yes

✓ Imported: Home (home)
✓ Imported: About Us (about)
⊗ Skipped (already exists): Contact (contact)

Import completed:
  ✓ Imported: 2
  ⊗ Skipped: 1
```

#### Update Existing Pages

```bash
php artisan pages:import --directory=storage/exports/pages --update
```

This will update pages that already exist instead of skipping them.

#### Skip Confirmation

```bash
php artisan pages:import --file=home.json --force
```

---

## Complete Workflow

### Dev → Production Migration

#### 1. On Development Server

```bash
# List pages to see what you have
php artisan pages:list

# Export all pages
php artisan pages:export --all

# Files are saved to: storage/exports/pages/
```

#### 2. Transfer Files

Copy the exported JSON files to your production server:

```bash
# Using SCP
scp storage/exports/pages/*.json user@production:/path/to/app/storage/exports/pages/

# Or using rsync
rsync -avz storage/exports/pages/ user@production:/path/to/app/storage/exports/pages/

# Or download and re-upload via FTP/SFTP
```

#### 3. On Production Server

```bash
# Import all pages
php artisan pages:import --directory=storage/exports/pages --force

# Or import specific page
php artisan pages:import --file=storage/exports/pages/home.json --force

# If you want to update existing pages
php artisan pages:import --directory=storage/exports/pages --update --force
```

---

## Export File Format

### JSON Structure

```json
{
  "title": "Home Page",
  "slug": "home",
  "status": "published",
  "order": 1,
  "content": [
    {
      "type": "hero",
      "data": {
        "title": "Welcome",
        "text": "<p>Welcome to our site</p>",
        "image": "pages/hero/hero-image.jpg"
      }
    },
    {
      "type": "faq",
      "data": {
        "section_title": "Frequently Asked Questions",
        "items": [
          {
            "question": "What are your hours?",
            "answer": "<p>9 AM - 5 PM</p>"
          }
        ]
      }
    }
  ],
  "created_at": "2025-10-25T10:30:00.000000Z",
  "updated_at": "2025-10-25T18:45:00.000000Z",
  "exported_at": "2025-10-25T20:00:00.000000Z",
  "export_version": "1.0"
}
```

### What Gets Exported

✅ **Page Metadata**: Title, slug, status, order  
✅ **All Content Blocks**: Hero, FAQ, galleries, text sections, etc.  
✅ **Nested Data**: Complex structures preserved  
✅ **Timestamps**: Created and updated dates  
✅ **Export Info**: Export time and version  

### What Doesn't Get Exported

❌ **Files**: Images referenced in content (see below)  
❌ **IDs**: Database IDs (auto-generated on import)

---

## Handling Images and Files

### Important Notes

⚠️ **Image files are NOT included** in JSON exports. Only file paths are stored.

When migrating pages with images, you must also transfer the actual image files:

### Transfer Images

```bash
# From development to production
rsync -avz storage/app/public/ user@production:/path/to/app/storage/app/public/

# Or specific directories
rsync -avz storage/app/public/pages/ user@production:/path/to/app/storage/app/public/pages/
rsync -avz storage/app/public/articles/ user@production:/path/to/app/storage/app/public/articles/
```

### Image Paths in Content

Image paths in content are relative, e.g.:
- `pages/hero/image.jpg`
- `pages/detailed-gallery/client-logo.jpg`
- `articles/article-image.jpg`

These work as long as files exist in `storage/app/public/`.

### On Production After Transfer

```bash
# Ensure storage link exists
php artisan storage:link

# Fix permissions
php artisan storage:fix-permissions

# Set directory permissions
chmod -R 755 storage/app/public
```

---

## Advanced Usage

### Automating Export

Create a scheduled task to automatically export pages:

```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // Export all pages daily at 2 AM
    $schedule->command('pages:export --all --output=storage/exports/backup')
             ->daily()
             ->at('02:00');
}
```

### Backup Script

```bash
#!/bin/bash
# backup-pages.sh

echo "Backing up pages..."

# Export pages
php artisan pages:export --all --output=storage/exports/backup

# Add timestamp
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p backups/pages
cp -r storage/exports/backup backups/pages/pages_${DATE}

# Keep only last 7 days of backups
find backups/pages -type d -mtime +7 -exec rm -rf {} +

echo "Backup completed: backups/pages/pages_${DATE}"
```

### Git Integration

You can version control your pages:

```bash
# Export to version-controlled directory
php artisan pages:export --all --output=resources/pages

# Commit
git add resources/pages/*.json
git commit -m "Update pages"
git push

# On another environment
git pull
php artisan pages:import --directory=resources/pages --update --force
```

---

## Use Cases

### 1. Development → Staging

```bash
# Dev
php artisan pages:export --all
scp storage/exports/pages/*.json staging:/app/storage/exports/pages/

# Staging
php artisan pages:import --directory=storage/exports/pages --update --force
```

### 2. Create Page Templates

```bash
# Export a template page
php artisan pages:export --slug=landing-page-template

# Edit the JSON file, change slug and title
# Import as new page
php artisan pages:import --file=modified-template.json --force
```

### 3. Disaster Recovery

```bash
# Regular backups
php artisan pages:export --all --output=backups/pages/$(date +%Y%m%d)

# Restore from backup
php artisan pages:import --directory=backups/pages/20251025 --update --force
```

### 4. Multi-Environment Setup

```yaml
# .github/workflows/deploy.yml
- name: Export pages from dev
  run: php artisan pages:export --all --output=artifacts/pages
  
- name: Upload artifact
  uses: actions/upload-artifact@v2
  with:
    name: pages
    path: artifacts/pages
    
- name: Download and import on production
  run: |
    php artisan pages:import --directory=artifacts/pages --update --force
```

---

## Validation and Error Handling

### Validation Rules

Imported pages must meet these requirements:

- ✅ **title**: Required, max 255 characters
- ✅ **slug**: Required, unique, max 255 characters
- ✅ **status**: Required, must be 'draft' or 'published'
- ✅ **order**: Required, integer
- ✅ **content**: Optional, must be valid array/JSON

### Common Errors

#### 1. Invalid JSON

```
✗ Invalid JSON in file: home.json
```

**Fix**: Check JSON syntax, use a JSON validator

#### 2. Validation Failed

```
✗ Validation failed for: home.json
  - The title field is required.
  - The status must be one of: draft, published.
```

**Fix**: Ensure all required fields are present and valid

#### 3. Duplicate Slug

```
⊗ Skipped (already exists): Home (home)
```

**Fix**: Use `--update` flag to update instead of skip, or change the slug

#### 4. File Not Found

```
File not found: /path/to/file.json
```

**Fix**: Check file path, ensure file exists

---

## Best Practices

### 1. Version Control Pages

Store exported pages in version control:

```bash
php artisan pages:export --all --output=database/seeds/pages
git add database/seeds/pages
git commit -m "Add page seeds"
```

### 2. Document Changes

Add comments in commit messages when exporting:

```bash
# After exporting
git diff database/seeds/pages/home.json
git commit -m "Update home page: Add new FAQ section"
```

### 3. Test Imports

Always test imports in staging before production:

```bash
# Staging
php artisan pages:import --file=home.json --force
# Test thoroughly
# Then apply to production
```

### 4. Backup Before Import

```bash
# Export current state before importing
php artisan pages:export --all --output=backups/before-import

# Then import
php artisan pages:import --directory=new-pages --update --force

# If something goes wrong, restore
php artisan pages:import --directory=backups/before-import --update --force
```

### 5. Transfer Files Separately

Remember to transfer images:

```bash
# Pages
php artisan pages:export --all
scp storage/exports/pages/*.json prod:/app/storage/exports/pages/

# Images
rsync -avz storage/app/public/ prod:/app/storage/app/public/
```

---

## Troubleshooting

### Import Does Nothing

**Issue**: Files aren't being imported

**Check**:
```bash
# Verify file exists
ls -la storage/exports/pages/*.json

# Check JSON is valid
cat storage/exports/pages/home.json | jq .

# Try with force flag
php artisan pages:import --file=storage/exports/pages/home.json --force
```

### Images Not Showing After Import

**Issue**: Pages imported but images don't display

**Fix**:
```bash
# Transfer image files
rsync -avz source:/app/storage/app/public/ storage/app/public/

# Recreate symlink
php artisan storage:link

# Fix permissions
php artisan storage:fix-permissions
chmod -R 755 storage/app/public
```

### Slug Conflicts

**Issue**: Can't import because slug already exists

**Options**:
1. Use `--update` to overwrite
2. Edit JSON file and change the slug
3. Delete existing page first

### JSON Encoding Issues

**Issue**: Special characters not displaying correctly

**Fix**: Ensure your editor saves as UTF-8:
```bash
# Check file encoding
file -I storage/exports/pages/home.json

# Should show: charset=utf-8
```

---

## Testing

Run the test suite:

```bash
php artisan test tests/Feature/PageExportImportTest.php
```

**Tests cover:**
- ✅ Export single page
- ✅ Export all pages
- ✅ Import single page
- ✅ Import multiple pages
- ✅ Skip existing pages
- ✅ Update existing pages
- ✅ Validation
- ✅ Complex content preservation
- ✅ Full roundtrip (export → import)

**Result:** 9 tests, 42 assertions, all passing ✅

---

## API Reference

### Export Command

```bash
php artisan pages:export [options]
```

**Options:**
- `--slug=SLUG` - Export specific page by slug
- `--all` - Export all pages
- `--output=PATH` - Output directory (default: storage/exports/pages)

### Import Command

```bash
php artisan pages:import [options]
```

**Options:**
- `--file=PATH` - Import from specific file
- `--directory=PATH` - Import all JSON files from directory
- `--update` - Update existing pages instead of skipping
- `--force` - Skip confirmation prompt

### List Command

```bash
php artisan pages:list
```

No options required.

---

## Summary

The Page Export/Import feature provides a reliable way to migrate content between environments:

✅ **Simple**: Easy-to-use commands  
✅ **Safe**: Validation and confirmations  
✅ **Flexible**: Export single or all pages  
✅ **Complete**: Preserves all content structures  
✅ **Tested**: Full test coverage  

**Perfect for:**
- 🚀 Deploying new pages to production
- 📦 Backing up your content
- 🔄 Syncing between environments
- 🎨 Creating page templates
- 💾 Version controlling your pages

---

**Ready to use!** Start exporting your pages now. 🎉

