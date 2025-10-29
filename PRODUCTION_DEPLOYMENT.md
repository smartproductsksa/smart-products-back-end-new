# Production Deployment Guide

## 🚀 Quick Start

### Initial Deployment

```bash
# 1. Clone the repository
git clone <your-repo-url> /var/www/your-app
cd /var/www/your-app

# 2. Set up environment
cp docker/env.example .env
nano .env  # Update with production values

# 3. Fix permissions BEFORE starting Docker
chown -R www-data:www-data .
chmod -R 775 storage bootstrap/cache  
chmod -R 777 storage/logs storage/framework

# 4. Create required directories
mkdir -p storage/framework/{cache,sessions,testing,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# 5. Generate SSL certificates
cd docker/nginx/ssl
./generate-ssl.sh
cd ../../..

# 6. Build and start containers
docker-compose build --no-cache
docker-compose up -d

# 7. Wait for containers to be ready
sleep 10
docker-compose ps

# 8. Initialize application
docker-compose exec app php artisan key:generate
docker-compose exec app sh /var/www/html/docker/init-app.sh

# 9. Create admin user
docker-compose exec app php artisan make:filament-user

# 10. Verify it's working
curl -k https://localhost/health
```

---

## 🔧 Common Issues & Solutions

### Issue 1: Permission Denied Errors

**Symptoms:**
```
The stream or file "/var/www/html/storage/logs/laravel.log" could not be opened
The /var/www/html/bootstrap/cache directory must be present and writable
```

**Solution:**
```bash
# Stop containers
docker-compose down

# Fix permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache  
chmod -R 777 storage/logs storage/framework

# Recreate directories
mkdir -p storage/framework/{cache,sessions,testing,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Restart
docker-compose up -d

# Check logs
docker-compose logs app --tail=50
```

---

### Issue 2: Port 80/443 Already in Use

**Check what's using the ports:**
```bash
sudo lsof -i :80
sudo lsof -i :443
netstat -tulpn | grep -E ':80|:443'
```

**Option A: Stop conflicting service**
```bash
# Apache
sudo systemctl stop apache2
sudo systemctl disable apache2

# Nginx (system-level)
sudo systemctl stop nginx
sudo systemctl disable nginx
```

**Option B: Use different ports**

Edit `.env`:
```env
HTTP_PORT=8080
HTTPS_PORT=8443
```

Then:
```bash
docker-compose down
docker-compose up -d
```

Access via: `https://your-server-ip:8443`

---

### Issue 3: Database Connection Errors

**Symptoms:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Solution:**
```bash
# Check if MySQL container is running
docker-compose ps

# Check MySQL logs
docker-compose logs mysql

# Verify MySQL is healthy
docker-compose exec mysql mysql -u root -p laravel -e "SELECT 1;"

# If needed, recreate database
docker-compose down -v  # WARNING: This deletes all data
docker-compose up -d
docker-compose exec app php artisan migrate --force
```

---

### Issue 4: App Container Keeps Restarting

**Check logs:**
```bash
# Real-time logs
docker-compose logs -f app

# Last 100 lines
docker-compose logs app --tail=100

# All containers
docker-compose logs --tail=50
```

**Common causes:**
1. **Permission errors** → See Issue 1
2. **Missing .env** → `cp docker/env.example .env`
3. **Invalid PHP syntax** → Check recent code changes
4. **Missing dependencies** → Rebuild: `docker-compose build --no-cache`

---

### Issue 5: 502 Bad Gateway

**Symptoms:**
Nginx shows "502 Bad Gateway"

**Causes:**
1. **App container is down** → Check `docker-compose ps`
2. **PHP-FPM not responding** → Check app logs
3. **Network issue** → Restart containers

**Solution:**
```bash
# Check container health
docker-compose ps

# Restart app service
docker-compose restart app

# If that doesn't work, full restart
docker-compose down
docker-compose up -d

# Check Nginx can reach PHP-FPM
docker-compose exec nginx ping -c 3 app
```

---

## 📊 Monitoring

### Check Application Status

```bash
# Container status
docker-compose ps

# Resource usage
docker stats

# Logs (all services)
docker-compose logs -f

# Logs (specific service)
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f mysql
```

### Health Checks

```bash
# Application health
curl -k https://localhost/health

# Nginx status
docker-compose exec nginx nginx -t

# MySQL status
docker-compose exec mysql mysqladmin -u root -p ping

# Redis status
docker-compose exec redis redis-cli ping

# Queue worker status
docker-compose exec queue php artisan queue:work --once
```

---

## 🔄 Updating the Application

```bash
# 1. Pull latest code
git pull origin main

# 2. Rebuild containers
docker-compose build --no-cache

# 3. Restart services (zero downtime)
docker-compose up -d

# 4. Run migrations
docker-compose exec app php artisan migrate --force

# 5. Clear caches
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan route:clear

# 6. Rebuild caches
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# 7. Restart queue workers (to pick up new code)
docker-compose restart queue
```

---

## 🗄️ Database Backup & Restore

### Backup

```bash
# Automated backup with timestamp
docker-compose exec mysql mysqldump -u root -proot laravel > backup-$(date +%Y%m%d-%H%M%S).sql

# Or without password in command (more secure)
docker-compose exec -T mysql mysqldump -u root -p laravel > backup.sql
# Enter password when prompted
```

### Restore

```bash
# Restore from backup
docker-compose exec -T mysql mysql -u root -p laravel < backup.sql
# Enter password when prompted
```

---

## 🔐 Security Checklist

- [ ] Change default database passwords in `.env`
- [ ] Set `APP_DEBUG=false` in production
- [ ] Set `APP_ENV=production`
- [ ] Use proper SSL certificates (not self-signed) in production
- [ ] Set up firewall rules (allow only 80, 443, 22)
- [ ] Disable unnecessary ports (3306, 6379, etc.) from external access
- [ ] Set up regular database backups
- [ ] Configure log rotation
- [ ] Set up monitoring and alerts
- [ ] Enable Redis password authentication

---

## 📝 Environment Variables

**Required `.env` variables for production:**

```env
APP_NAME="Your App Name"
APP_ENV=production
APP_KEY=base64:... # Generated by artisan key:generate
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=mysql  # Docker service name
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=your_secure_password_here

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis  # Docker service name
REDIS_PASSWORD=null
REDIS_PORT=6379

# Ports (optional, defaults to 80/443)
HTTP_PORT=80
HTTPS_PORT=443
```

---

## 🆘 Emergency Procedures

### Complete Reset (⚠️ DELETES ALL DATA)

```bash
# Stop and remove everything
docker-compose down -v

# Remove images
docker rmi $(docker images 'smart-products*' -q)

# Start fresh
./docker-setup.sh
```

### Restart Services

```bash
# Graceful restart (recommended)
docker-compose restart

# Force restart
docker-compose down
docker-compose up -d

# Restart specific service
docker-compose restart app
docker-compose restart nginx
docker-compose restart mysql
```

---

## 📞 Need Help?

If you encounter issues not covered here:

1. Check logs: `docker-compose logs -f`
2. Check container status: `docker-compose ps`
3. Verify environment: `docker-compose exec app php artisan about`
4. Test database: `docker-compose exec app php artisan migrate:status`
5. Review this guide and [DOCKER_SETUP_GUIDE.md](./DOCKER_SETUP_GUIDE.md)

