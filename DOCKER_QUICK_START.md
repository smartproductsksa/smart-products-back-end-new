# Docker Quick Start

## ⚡ Super Quick Setup

```bash
# 1. Run automated setup
./docker-setup.sh

# 2. Create admin user
docker-compose exec app php artisan make:filament-user

# 3. Access application
open https://localhost
```

That's it! 🎉

---

## 🚀 Manual Setup (Step by Step)

### 1. Setup Environment
```bash
cp docker/env.example .env
# Edit .env with your settings
```

### 2. Generate SSL Certificates
```bash
cd docker/nginx/ssl && ./generate-ssl.sh && cd ../../..
```

### 3. Start Services
```bash
docker-compose up -d
```

### 4. Initialize Database
```bash
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan make:filament-user
```

### 5. Access Application
- HTTPS: https://localhost
- Admin: https://localhost/admin

---

## 📦 Common Commands

### Start/Stop
```bash
docker-compose up -d          # Start
docker-compose stop           # Stop
docker-compose restart        # Restart
docker-compose down           # Stop & remove containers
```

### Logs
```bash
docker-compose logs -f        # All logs
docker-compose logs -f app    # App logs only
```

### Run Commands
```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan pages:export --all
```

### Database
```bash
# MySQL shell
docker-compose exec mysql mysql -u laravel -p

# Backup
docker-compose exec mysql mysqldump -u root -p laravel > backup.sql

# Restore
docker-compose exec -T mysql mysql -u root -p laravel < backup.sql
```

---

## 🔧 Troubleshooting

### Containers won't start
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Permission errors
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Database connection failed
```bash
# Check if MySQL is ready
docker-compose ps mysql
docker-compose logs mysql
```

### Port already in use
```bash
# Check what's using port 80/443
sudo lsof -i :80
sudo lsof -i :443

# Change ports in docker-compose.yml or .env
HTTP_PORT=8080
HTTPS_PORT=8443
```

---

## 📊 Services

| Service | Container | Port | URL |
|---------|-----------|------|-----|
| App | laravel_app | 9000 | - |
| Nginx | laravel_nginx | 80, 443 | https://localhost |
| MySQL | laravel_mysql | 3306 | localhost:3306 |
| Redis | laravel_redis | 6379 | localhost:6379 |

---

## 💾 Data Persistence

**Your data is safe!** Even if containers stop/restart:
- ✅ Database data persists in `mysql-data` volume
- ✅ Redis data persists in `redis-data` volume  
- ✅ Application files in `./` directory

**Only deleted if you run:**
```bash
docker-compose down -v  # DON'T run this unless you want to lose data!
```

---

## 🔒 Production Deployment

### 1. Update Environment
```bash
# Edit .env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_PASSWORD=strong_password_here
```

### 2. Get Real SSL Certificates
```bash
# Using Let's Encrypt
sudo certbot certonly --standalone -d yourdomain.com
sudo cp /etc/letsencrypt/live/yourdomain.com/fullchain.pem docker/nginx/ssl/cert.pem
sudo cp /etc/letsencrypt/live/yourdomain.com/privkey.pem docker/nginx/ssl/key.pem
```

### 3. Deploy
```bash
docker-compose build
docker-compose up -d
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan optimize
```

---

## 📚 Full Documentation

See [DOCKER_SETUP_GUIDE.md](./DOCKER_SETUP_GUIDE.md) for:
- Complete setup instructions
- SSL configuration
- Backup & restore
- Monitoring
- Performance optimization
- Troubleshooting

---

## ✅ Health Check

```bash
# Check all services
docker-compose ps

# Test application
curl -k https://localhost/health

# View logs
docker-compose logs -f --tail=50
```

---

**Need help?** See [DOCKER_SETUP_GUIDE.md](./DOCKER_SETUP_GUIDE.md)


