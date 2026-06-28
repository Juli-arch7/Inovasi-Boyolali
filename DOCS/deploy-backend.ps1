# Deploy script untuk Backend (Laravel) - Windows PowerShell
# Usage: .\deploy-backend.ps1

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "🚀 Backend Deployment Script (Windows)" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$BackendDir = Join-Path $ScriptDir "backend"
$EnvFile = Join-Path $BackendDir ".env"

Write-Host "📁 Backend Directory: $BackendDir" -ForegroundColor Yellow

# Step 1: Check if .env exists
if (-not (Test-Path $EnvFile)) {
    Write-Host "❌ .env file not found!" -ForegroundColor Red
    Write-Host "📋 Creating .env from .env.example..." -ForegroundColor Yellow
    
    $EnvExample = Join-Path $BackendDir ".env.example"
    if (Test-Path $EnvExample) {
        Copy-Item $EnvExample $EnvFile
        Write-Host "✅ .env created. Please edit it with your configuration." -ForegroundColor Green
        Write-Host "📝 Edit: $EnvFile" -ForegroundColor Cyan
    } else {
        Write-Host "❌ .env.example not found!" -ForegroundColor Red
    }
    exit 1
}

# Step 2: Install dependencies
Write-Host ""
Write-Host "📦 Installing composer dependencies..." -ForegroundColor Yellow
Push-Location $BackendDir
composer install --optimize-autoloader --no-dev

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Composer install failed!" -ForegroundColor Red
    exit 1
}

# Step 3: Generate app key if not exists
Write-Host ""
Write-Host "🔑 Checking APP_KEY..." -ForegroundColor Yellow
$EnvContent = Get-Content $EnvFile
if ($EnvContent -match "APP_KEY=$") {
    Write-Host "🔑 Generating APP_KEY..." -ForegroundColor Yellow
    php artisan key:generate
}

# Step 4: Run migrations
Write-Host ""
Write-Host "🗄️  Running database migrations..." -ForegroundColor Yellow
php artisan migrate --force

if ($LASTEXITCODE -ne 0) {
    Write-Host "⚠️  Migration had issues. Check database connection in .env" -ForegroundColor Yellow
}

# Step 5: Seed database (optional)
Write-Host ""
$Response = Read-Host "Do you want to seed the database? (y/n)"
if ($Response -eq 'y' -or $Response -eq 'Y') {
    Write-Host "🌱 Seeding database..." -ForegroundColor Yellow
    php artisan db:seed --force
}

# Step 6: Cache configuration
Write-Host ""
Write-Host "⚙️  Caching configuration..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 7: Summary
Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "✅ Backend deployment completed!" -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Build frontend: .\deploy-frontend.ps1" -ForegroundColor White
Write-Host "2. Start server: cd backend && php artisan serve" -ForegroundColor White
Write-Host "3. Check logs: Get-Content storage/logs/laravel.log -Tail 50 -Wait" -ForegroundColor White
Write-Host ""

Pop-Location
