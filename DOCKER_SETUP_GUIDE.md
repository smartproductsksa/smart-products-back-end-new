# Docker Setup Guide with Nginx & SSL

## Overview

Complete Docker setup for Laravel application with:
- ✅ **Nginx** web server with SSL/TLS
- ✅ **PHP-FPM** 8.2
- ✅ **MySQL 8.0** with persistent data
- ✅ **Redis** for caching and queues
- ✅ **Queue Worker** for background jobs
- ✅ **Scheduler** for cron jobs
- ✅ **Data Persistence** - Database survives container restarts
- ✅ **SSL/HTTPS** support
- ✅ **Production-ready** configuration

---

## Quick Start

### 1. Prerequisites

Install Docker and Docker Compose:
- **Docker**: https://docs.docker.com/get-docker/
- **Docker Compose**: Included with Docker Desktop

Verify installation:
```bash
docker --version
docker-compose --version
```

### 2. Setup Environment

```bash
# Copy environment file
cp docker/env.example .env

# Edit .env with your settings
nano .env

# Generate application key
php artisan key:generate
```

### 3. Generate SSL Certificates

For **development** (self-signed):
```bash
cd docker/nginx/ssl
./generate-ssl.sh
cd ../../..
```

For **production**, see [Production SSL Setup](#production-ssl-setup) section.

### 4. Build and Start

```bash
# Build images
docker-compose build

# Start all services
docker-compose up -d

# Check status
docker-compose ps
```

### 5. Initialize Database

```bash
# Run migrations
docker-compose exec app php artisan migrate --force

# (Optional) Seed database
docker-compose exec app php artisan db:seed

# Create admin user
docker-compose exec app php artisan make:filament-user
```

### 6. Access Application

- **HTTPS**: https://localhost
- **HTTP**: http://localhost (redirects to HTTPS)
- **Admin Panel**: https://localhost/admin

---

## Services

### Application Services

| Service | Container | Port | Description |
|---------|-----------|------|-------------|
| **app** | laravel_app | 9000 | PHP-FPM application |
| **nginx** | laravel_nginx | 80, 443 | Web server with SSL |
| **mysql** | laravel_mysql | 3306 | MySQL database |
| **redis** | laravel_redis | 6379 | Cache & queues |
| **queue** | laravel_queue | - | Queue worker |
| **scheduler** | laravel_scheduler | - | Cron jobs |

---

## Data Persistence

### Persistent Volumes

All important data is stored in Docker volumes:

```
mysql-data       → /var/lib/mysql (MySQL database)
redis-data       → /data (Redis persistence)
nginx-logs       → /var/log/nginx (Nginx logs)
php-socket       → /var/run/php (PHP-FPM socket)
```

### Volume Management

```bash
# List volumes
docker volume ls

# Inspect a volume
docker volume inspect laravel_mysql-data

# Backup database volume
docker run --rm \
  -v laravel_mysql-data:/data \
  -v $(pwd):/backup \
  alpine tar czf /backup/mysql-backup.tar.gz /data

# Restore database volume
docker run --rm \
  -v laravel_mysql-data:/data \
  -v $(pwd):/backup \
  alpine tar xzf /backup/mysql-backup.tar.gz -C /
```

### Why Data Persists

Even if containers are stopped, removed, or rebuilt:
- ✅ MySQL data remains in `mysql-data` volume
- ✅ Redis data remains in `redis-data` volume
- ✅ Application files in `./` directory

**Data is only lost if you explicitly delete volumes:**
```bash
# This WILL delete data (careful!)
docker-compose down -v
```

---

## Common Commands

### Container Management

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose stop

# Restart services
docker-compose restart

# Stop and remove containers (data persists)
docker-compose down

# Stop, remove containers AND volumes (data is deleted!)
docker-compose down -v

# View logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f mysql
```

### Application Commands

```bash
# Run artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear

# Access container shell
docker-compose exec app bash

# Run composer
docker-compose exec app composer install
docker-compose exec app composer update

# Run NPM
docker-compose exec app npm install
docker-compose exec app npm run build

# File permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Database Commands

```bash
# MySQL shell
docker-compose exec mysql mysql -u laravel -p

# Database backup
docker-compose exec mysql mysqldump \
  -u root -p${DB_ROOT_PASSWORD} \
  ${DB_DATABASE} > backup.sql

# Database restore
docker-compose exec -T mysql mysql \
  -u root -p${DB_ROOT_PASSWORD} \
  ${DB_DATABASE} < backup.sql

# View database logs
docker-compose exec mysql tail -f /var/lib/mysql/error.log
```

---

## SSL Configuration

### Development (Self-Signed Certificates)

Already generated in Quick Start:

```bash
cd docker/nginx/ssl
./generate-ssl.sh
```

**Browser Warning:** You'll see a security warning because the certificate is self-signed. This is normal for development.

### Production SSL Setup

#### Option 1: Let's Encrypt (Recommended)

1. **Install Certbot**:
```bash
# On your server
sudo apt-get update
sudo apt-get install certbot
```

2. **Generate Certificates**:
```bash
sudo certbot certonly --standalone \
  -d yourdomain.com \
  -d www.yourdomain.com \
  --agree-tos \
  -m your@email.com
```

3. **Copy Certificates**:
```bash
sudo cp /etc/letsencrypt/live/yourdomain.com/fullchain.pem docker/nginx/ssl/cert.pem
sudo cp /etc/letsencrypt/live/yourdomain.com/privkey.pem docker/nginx/ssl/key.pem
```

4. **Auto-Renewal Setup**:
```bash
# Add to crontab
sudo crontab -e

# Add this line
0 0 1 * * certbot renew --quiet && docker-compose restart nginx
```

#### Option 2: Commercial SSL Certificate

1. Purchase SSL from provider (GoDaddy, Namecheap, etc.)
2. Download certificate files
3. Copy to `docker/nginx/ssl/`:
   - Certificate: `cert.pem`
   - Private Key: `key.pem`

#### Update Nginx Config for Your Domain

Edit `docker/nginx/conf.d/default.conf`:

```nginx
server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;  # Update this
    # ... rest of config
}
```

---

## Environment Variables

### Important Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `APP_URL` | Application URL | `https://yourdomain.com` |
| `DB_HOST` | Database host | `mysql` (container name) |
| `DB_DATABASE` | Database name | `laravel` |
| `DB_USERNAME` | Database user | `laravel` |
| `DB_PASSWORD` | Database password | Strong password |
| `DB_ROOT_PASSWORD` | MySQL root password | Strong password |
| `REDIS_HOST` | Redis host | `redis` (container name) |

### Generate Secure Passwords

```bash
# Generate random password
openssl rand -base64 32
```

---

## Production Deployment

### 1. Server Requirements

- Docker 20.10+
- Docker Compose 2.0+
- 2GB+ RAM
- 20GB+ Disk Space

### 2. Security Checklist

```bash
# ✅ Set strong passwords in .env
# ✅ Set APP_DEBUG=false
# ✅ Set APP_ENV=production
# ✅ Use real SSL certificates
# ✅ Configure firewall (UFW/iptables)
# ✅ Enable automatic updates
# ✅ Setup backups
```

### 3. Firewall Configuration

```bash
# Allow HTTP/HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Allow SSH
sudo ufw allow 22/tcp

# Enable firewall
sudo ufw enable
```

### 4. Deployment Steps

```bash
# 1. Clone repository
git clone your-repo-url
cd back-end

# 2. Setup environment
cp docker/env.example .env
nano .env  # Edit with production values

# 3. Generate app key
docker-compose exec app php artisan key:generate

# 4. Setup SSL certificates
# (See Production SSL Setup section)

# 5. Build and start
docker-compose build
docker-compose up -d

# 6. Initialize database
docker-compose exec app php artisan migrate --force

# 7. Cache config
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# 8. Create admin user
docker-compose exec app php artisan make:filament-user

# 9. Verify services
docker-compose ps
curl -k https://localhost/health
```

---

## Monitoring

### Health Checks

```bash
# Application health
curl -k https://localhost/health

# Container health
docker-compose ps

# Individual service health
docker inspect laravel_mysql --format='{{.State.Health.Status}}'
```

### Logs

```bash
# All services
docker-compose logs -f --tail=100

# Application logs
docker-compose logs -f app

# Nginx access logs
docker-compose exec nginx tail -f /var/log/nginx/access.log

# Nginx error logs
docker-compose exec nginx tail -f /var/log/nginx/error.log

# MySQL logs
docker-compose exec mysql tail -f /var/lib/mysql/error.log

# Laravel logs (inside container)
docker-compose exec app tail -f storage/logs/laravel.log
```

---

## Backup and Restore

### Full System Backup

```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="backups/$DATE"

mkdir -p $BACKUP_DIR

# Backup database
docker-compose exec -T mysql mysqldump \
  -u root -p${DB_ROOT_PASSWORD} --all-databases \
  > $BACKUP_DIR/database.sql

# Backup application files
tar -czf $BACKUP_DIR/application.tar.gz \
  --exclude='./vendor' \
  --exclude='./node_modules' \
  --exclude='./storage/logs' \
  ./

# Backup volumes
docker run --rm \
  -v laravel_mysql-data:/data \
  -v $(pwd)/$BACKUP_DIR:/backup \
  alpine tar czf /backup/mysql-volume.tar.gz /data

docker run --rm \
  -v laravel_redis-data:/data \
  -v $(pwd)/$BACKUP_DIR:/backup \
  alpine tar czf /backup/redis-volume.tar.gz /data

echo "Backup completed: $BACKUP_DIR"
```

### Automated Backups

Add to crontab:
```bash
0 2 * * * cd /path/to/app && ./backup.sh
```

---

## Troubleshooting

### Container Won't Start

```bash
# Check logs
docker-compose logs app

# Check if ports are in use
sudo netstat -tulpn | grep :80
sudo netstat -tulpn | grep :443
sudo netstat -tulpn | grep :3306

# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Database Connection Failed

```bash
# Check MySQL is running
docker-compose ps mysql

# Check MySQL logs
docker-compose logs mysql

# Test connection
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo();

# Verify credentials in .env
cat .env | grep DB_
```

### Permission Denied Errors

```bash
# Fix permissions
docker-compose exec app chown -R www-data:www-data /var/www/html
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### SSL Certificate Errors

```bash
# Verify certificates exist
ls -la docker/nginx/ssl/

# Regenerate self-signed certificates
cd docker/nginx/ssl
rm cert.pem key.pem
./generate-ssl.sh
cd ../../..
docker-compose restart nginx
```

### Out of Memory

```bash
# Check memory usage
docker stats

# Increase Docker memory limit in Docker Desktop settings
# Or add to docker-compose.yml:
services:
  app:
    deploy:
      resources:
        limits:
          memory: 1G
```

### Data Loss Prevention

**IMPORTANT**: To ensure database data persists:

✅ **DO**:
```bash
docker-compose down        # Stops containers, keeps volumes
docker-compose stop        # Stops containers
docker-compose restart     # Restarts containers
```

❌ **DON'T** (unless you want to delete data):
```bash
docker-compose down -v     # Deletes volumes!
docker volume rm laravel_mysql-data  # Deletes database!
```

---

## Performance Optimization

### PHP-FPM Tuning

Edit Dockerfile and add:
```dockerfile
RUN echo "pm.max_children = 50" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.start_servers = 10" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.min_spare_servers = 5" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.max_spare_servers = 15" >> /usr/local/etc/php-fpm.d/www.conf
```

### MySQL Tuning

Edit `docker/mysql/my.cnf`:
```ini
innodb_buffer_pool_size=1G  # 50-70% of available RAM
max_connections=300
```

### Redis Memory Limit

Edit docker-compose.yml:
```yaml
redis:
  command: redis-server --maxmemory 512mb --maxmemory-policy allkeys-lru
```

---

## Updates and Maintenance

### Update Application

```bash
# Pull latest code
git pull origin main

# Rebuild containers
docker-compose build

# Restart services
docker-compose down
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate --force

# Clear caches
docker-compose exec app php artisan optimize:clear
docker-compose exec app php artisan optimize
```

### Update Docker Images

```bash
# Pull latest images
docker-compose pull

# Rebuild
docker-compose build --no-cache

# Restart
docker-compose down
docker-compose up -d
```

---

## Architecture Diagram

```
┌─────────────────────────────────────────┐
│          Internet / Users               │
└────────────────┬────────────────────────┘
                 │
                 ▼
         ┌───────────────┐
         │  Nginx (443)  │ SSL/TLS Termination
         │  Web Server   │
         └───────┬───────┘
                 │
                 ▼
         ┌───────────────┐
         │  PHP-FPM (app)│ Laravel Application
         └───┬───────┬───┘
             │       │
     ┌───────┘       └───────┐
     ▼                       ▼
┌─────────┐           ┌─────────┐
│  MySQL  │           │  Redis  │
│Database │           │  Cache  │
└─────────┘           └─────────┘
     │                       │
     ▼                       ▼
[mysql-data]          [redis-data]
 (Persistent           (Persistent
   Volume)              Volume)
```

---

## Summary

✅ **Complete Docker Setup** with all services  
✅ **Data Persistence** - Database survives restarts  
✅ **SSL/HTTPS Support** - Secure connections  
✅ **Production-Ready** - Optimized configuration  
✅ **Easy Backup** - Simple backup/restore procedures  
✅ **Well-Documented** - Complete guide  

**You're ready to deploy!** 🚀

For questions or issues, refer to the Troubleshooting section.


