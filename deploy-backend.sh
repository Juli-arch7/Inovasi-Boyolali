#!/bin/bash

# Deploy script untuk Backend (Laravel)
# Usage: ./deploy-backend.sh

set -e  # Exit on error

echo "========================================="
echo "🚀 Backend Deployment Script"
echo "========================================="

# Configuration
BACKEND_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/backend" && pwd)"
ENV_FILE="$BACKEND_DIR/.env"

echo "📁 Backend Directory: $BACKEND_DIR"

# Step 1: Check if .env exists
if [ ! -f "$ENV_FILE" ]; then
    echo "❌ .env file not found!"
    echo "📋 Creating .env from .env.example..."
    cp "$BACKEND_DIR/.env.example" "$ENV_FILE"
    echo "✅ .env created. Please edit it with your configuration."
    exit 1
fi

# Step 2: Install dependencies
echo ""
echo "📦 Installing composer dependencies..."
cd "$BACKEND_DIR"
composer install --optimize-autoloader --no-dev

# Step 3: Generate app key if not exists
if grep -q "APP_KEY=$" "$ENV_FILE"; then
    echo ""
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate
fi

# Step 4: Run migrations
echo ""
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Step 5: Seed database (optional)
read -p "Do you want to seed the database? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force
fi

# Step 6: Cache configuration
echo ""
echo "⚙️  Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 7: Fix permissions
echo ""
echo "🔐 Setting permissions..."
if [[ "$OSTYPE" != "msys" && "$OSTYPE" != "cygwin" ]]; then
    sudo chown -R www-data:www-data "$BACKEND_DIR/storage" "$BACKEND_DIR/bootstrap/cache"
    sudo chmod -R 755 "$BACKEND_DIR/storage" "$BACKEND_DIR/bootstrap/cache"
    echo "✅ Permissions set for Linux/Unix"
else
    echo "⚠️  Skipping chmod for Windows"
fi

echo ""
echo "========================================="
echo "✅ Backend deployment completed!"
echo "========================================="
echo ""
echo "Next steps:"
echo "1. Build frontend: ./deploy-frontend.sh"
echo "2. Check logs: tail -f $BACKEND_DIR/storage/logs/laravel.log"
echo "3. Test API: curl http://localhost:8000/api/health"
