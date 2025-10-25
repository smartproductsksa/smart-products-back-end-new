# Quick Start: Export/Import Pages

## 🚀 Quick Commands

### List Pages
```bash
php artisan pages:list
```

### Export
```bash
# Export all pages
php artisan pages:export --all

# Export one page
php artisan pages:export --slug=home
```

### Import
```bash
# Import one file
php artisan pages:import --file=storage/exports/pages/home.json --force

# Import all files from directory
php artisan pages:import --directory=storage/exports/pages --force

# Update existing pages
php artisan pages:import --directory=storage/exports/pages --update --force
```

---

## 📦 Dev → Production Workflow

### On Development:
```bash
# 1. Export pages
php artisan pages:export --all
# Files saved to: storage/exports/pages/

# 2. Transfer files to production
scp storage/exports/pages/*.json user@production:/app/storage/exports/pages/

# 3. Transfer images
rsync -avz storage/app/public/ user@production:/app/storage/app/public/
```

### On Production:
```bash
# 1. Import pages
php artisan pages:import --directory=storage/exports/pages --force

# 2. Fix permissions
php artisan storage:link
php artisan storage:fix-permissions
```

---

## 💡 Common Scenarios

### Scenario 1: Add New Page to Production
```bash
# Dev
php artisan pages:export --slug=new-page
scp storage/exports/pages/new-page.json prod:/app/storage/exports/pages/

# Production
php artisan pages:import --file=storage/exports/pages/new-page.json --force
```

### Scenario 2: Update Existing Page
```bash
# Dev
php artisan pages:export --slug=home

# Production
php artisan pages:import --file=storage/exports/pages/home.json --update --force
```

### Scenario 3: Sync All Pages
```bash
# Dev
php artisan pages:export --all

# Production
php artisan pages:import --directory=storage/exports/pages --update --force
```

### Scenario 4: Backup Before Changes
```bash
# Before making changes
php artisan pages:export --all --output=backups/pages/$(date +%Y%m%d)

# If needed to restore
php artisan pages:import --directory=backups/pages/20251025 --update --force
```

---

## ⚠️ Important Notes

### 1. Images Are Not Included
Export only includes image **paths**, not the actual files.

**You must transfer images separately:**
```bash
rsync -avz storage/app/public/ prod:/app/storage/app/public/
```

### 2. Existing Pages
By default, existing pages are **skipped**. Use `--update` to overwrite:
```bash
php artisan pages:import --file=home.json --update --force
```

### 3. Slug Must Be Unique
Can't import if slug already exists (unless using `--update`).

### 4. Validation
All imports are validated:
- Title required
- Slug required & unique
- Status must be 'draft' or 'published'
- Order must be integer

---

## 📁 File Locations

### Default Export Directory
```
storage/exports/pages/
```

### Exported Files
```
storage/exports/pages/
├── home.json
├── about.json
└── contact.json
```

### Example File
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
        "text": "<p>Welcome text</p>"
      }
    }
  ]
}
```

---

## 🔧 Troubleshooting

### Problem: Import does nothing
```bash
# Check file exists
ls -la storage/exports/pages/*.json

# Use --force flag
php artisan pages:import --file=home.json --force
```

### Problem: Images not showing
```bash
# Transfer images
rsync -avz source:/path/storage/app/public/ storage/app/public/

# Fix permissions
php artisan storage:link
php artisan storage:fix-permissions
```

### Problem: Already exists error
```bash
# Use --update to overwrite
php artisan pages:import --file=home.json --update --force
```

---

## 📖 Full Documentation

See [PAGE_EXPORT_IMPORT_GUIDE.md](./PAGE_EXPORT_IMPORT_GUIDE.md) for:
- Detailed command reference
- Advanced usage
- Automation examples
- Best practices
- Complete troubleshooting guide

---

## ✅ Testing

```bash
php artisan test tests/Feature/PageExportImportTest.php
```

**Result:** 9 tests, 42 assertions, all passing ✅

---

**That's it!** You're ready to export and import pages. 🎉

