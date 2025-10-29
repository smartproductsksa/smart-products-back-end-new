#!/bin/bash

# Docker Setup Script for Laravel Application
# This script automates the initial setup process

set -e

echo "========================================="
echo "  Laravel Docker Setup"
echo "========================================="
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    echo "   Visit: https://docs.docker.com/get-docker/"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

echo "✅ Docker and Docker Compose are installed"
echo ""

# Step 1: Environment Setup
echo "Step 1/6: Setting up environment..."
if [ ! -f .env ]; then
    if [ -f docker/env.example ]; then
        cp docker/env.example .env
        echo "✅ Created .env file from docker/env.example"
    else
        echo "❌ docker/env.example not found"
        exit 1
    fi
else
    echo "⚠️  .env file already exists, skipping..."
fi
echo ""

# Step 2: Generate SSL Certificates
echo "Step 2/6: Generating SSL certificates..."
if [ ! -f docker/nginx/ssl/cert.pem ] || [ ! -f docker/nginx/ssl/key.pem ]; then
    cd docker/nginx/ssl
    chmod +x generate-ssl.sh
    ./generate-ssl.sh
    cd ../../..
    echo "✅ SSL certificates generated"
else
    echo "⚠️  SSL certificates already exist, skipping..."
fi
echo ""

# Step 3: Create storage directories
echo "Step 3/6: Setting up storage directories..."
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
echo "✅ Storage directories created"
echo ""

# Step 4: Build Docker images
echo "Step 4/6: Building Docker images..."
docker-compose build
echo "✅ Docker images built successfully"
echo ""

# Step 5: Start services
echo "Step 5/6: Starting Docker services..."
docker-compose up -d
echo "✅ Docker services started"
echo ""

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
sleep 15

# Step 6: Initialize application
echo "Step 6/6: Initializing application..."

# Generate app key
echo "Generating application key..."
docker-compose exec -T app php artisan key:generate
echo "✅ Application key generated"

# Run initialization script
echo "Running initialization script..."
docker-compose exec -T app sh /var/www/html/docker/init-app.sh
echo "✅ Application initialized"

echo ""
echo "========================================="
echo "  ✅ Setup Complete!"
echo "========================================="
echo ""
echo "Your application is now running:"
echo "  • HTTPS: https://localhost:8443"
echo "  • HTTP:  http://localhost:8080 (redirects to HTTPS)"
echo "  • Admin: https://localhost:8443/admin"
echo "  • API:   https://localhost:8443/api/v1/"
echo ""
echo "⚠️  Note: Self-signed SSL certificate - your browser will show a warning"
echo ""
echo "Container status:"
docker-compose ps
echo ""
echo "Next steps:"
echo "  1. Create an admin user:"
echo "     docker-compose exec app php artisan make:filament-user"
echo ""
echo "  2. Run tests:"
echo "     docker-compose exec app sh docker/run-tests.sh"
echo ""
echo "  3. View logs:"
echo "     docker-compose logs -f"
echo ""
echo "  4. Stop services:"
echo "     docker-compose down"
echo ""
echo "For more information, see DOCKER_SETUP_GUIDE.md"
echo ""


