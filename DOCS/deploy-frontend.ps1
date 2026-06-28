# Deploy script untuk Frontend (Vue 3) - Windows PowerShell
# Usage: .\deploy-frontend.ps1

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "🚀 Frontend Deployment Script (Windows)" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$FrontendDir = Join-Path $ScriptDir "frontend"
$DistDir = Join-Path $FrontendDir "dist"

Write-Host "📁 Frontend Directory: $FrontendDir" -ForegroundColor Yellow
Write-Host "📦 Build Output: $DistDir" -ForegroundColor Yellow

# Step 1: Check if node_modules exists
Write-Host ""
$NodeModules = Join-Path $FrontendDir "node_modules"
if (-not (Test-Path $NodeModules)) {
    Write-Host "📥 Installing npm dependencies..." -ForegroundColor Yellow
    Push-Location $FrontendDir
    npm install
    if ($LASTEXITCODE -ne 0) {
        Write-Host "❌ npm install failed!" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "✅ node_modules already exists, skipping install" -ForegroundColor Green
}

# Step 2: Build for production
Write-Host ""
Write-Host "🔨 Building for production..." -ForegroundColor Yellow
npm run build

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Build failed!" -ForegroundColor Red
    exit 1
}

# Step 3: Check build output
Write-Host ""
if (Test-Path $DistDir) {
    Write-Host "✅ Build completed successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "📊 Build artifacts:" -ForegroundColor Yellow
    Get-ChildItem $DistDir | Select-Object Name, Length | Format-Table -AutoSize
    
    $DistSize = (Get-ChildItem $DistDir -Recurse | Measure-Object -Property Length -Sum).Sum / 1MB
    Write-Host "Total size: $([Math]::Round($DistSize, 2)) MB" -ForegroundColor Cyan
} else {
    Write-Host "❌ Build failed! dist folder not found." -ForegroundColor Red
    exit 1
}

# Step 4: Provide deployment instructions
Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "✅ Frontend build completed!" -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "📂 Build output location:" -ForegroundColor Cyan
Write-Host "   $DistDir" -ForegroundColor White
Write-Host ""
Write-Host "🚀 Next: Upload dist folder to your server" -ForegroundColor Cyan
Write-Host ""
Write-Host "For Shared Hosting (via FTP):" -ForegroundColor Yellow
Write-Host "   1. Open FTP client" -ForegroundColor White
Write-Host "   2. Navigate to /public_html" -ForegroundColor White
Write-Host "   3. Upload contents of dist/ folder" -ForegroundColor White
Write-Host "   4. Create .htaccess for SPA routing" -ForegroundColor White
Write-Host ""
Write-Host "For VPS/Cloud (via SSH):" -ForegroundColor Yellow
Write-Host "   scp -r $DistDir/* user@server:/var/www/inovasi-boyolali/frontend/dist/" -ForegroundColor White
Write-Host ""
Write-Host "🖥️  Preview locally:" -ForegroundColor Yellow
Write-Host "   npm run preview" -ForegroundColor White
Write-Host ""

Pop-Location
