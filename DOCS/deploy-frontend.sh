#!/bin/bash

# Deploy script untuk Frontend (Vue 3)
# Usage: ./deploy-frontend.sh

set -e  # Exit on error

echo "========================================="
echo "🚀 Frontend Deployment Script"
echo "========================================="

# Configuration
FRONTEND_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/frontend" && pwd)"
DIST_DIR="$FRONTEND_DIR/dist"

echo "📁 Frontend Directory: $FRONTEND_DIR"
echo "📦 Build Output: $DIST_DIR"

# Step 1: Install dependencies
echo ""
echo "📥 Installing npm dependencies..."
cd "$FRONTEND_DIR"
npm install

# Step 2: Build for production
echo ""
echo "🔨 Building for production..."
npm run build

# Step 3: Check build output
if [ -d "$DIST_DIR" ]; then
    echo ""
    echo "✅ Build completed successfully!"
    echo ""
    echo "📊 Build size:"
    du -sh "$DIST_DIR"
    echo ""
    echo "📋 Files in dist:"
    ls -la "$DIST_DIR" | head -20
else
    echo ""
    echo "❌ Build failed! dist folder not found."
    exit 1
fi

# Step 4: Provide deployment instructions
echo ""
echo "========================================="
echo "✅ Frontend build completed!"
echo "========================================="
echo ""
echo "🚀 Next: Upload dist/ folder to server"
echo ""
echo "For Shared Hosting:"
echo "  - Upload dist/* to /public_html"
echo "  - Create .htaccess for SPA routing"
echo ""
echo "For VPS/Cloud:"
echo "  - Copy to /var/www/inovasi-boyolali/frontend/dist"
echo "  - Configure Nginx"
echo ""
echo "Preview locally:"
echo "  npm run preview"
