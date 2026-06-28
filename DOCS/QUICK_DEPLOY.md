# 🚀 Quick Deployment Guide

Panduan cepat deployment dalam 5 langkah.

## Opsi 1: Shared Hosting (cPanel/Plesk)

### Backend Setup (Via SSH/Terminal)

```bash
cd /home/username/public_html/backend
composer install --optimize-autoloader --no-dev
cp .env.example .env
php artisan key:generate
```

**Edit .env:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.yourdomain.com
DB_HOST=localhost
DB_DATABASE=your_db
DB_USERNAME=db_user
DB_PASSWORD=db_pass
SESSION_DRIVER=database
```

**Run Migration:**
```bash
php artisan migrate --force
```

### Frontend Setup (Via FTP)

**Lokal:**
```bash
cd frontend
npm install
npm run build
```

**Upload `dist/` folder ke `/public_html`**

**Buat `.htaccess` di `/public_html`:**
```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /
  RewriteRule ^index\.html$ - [L]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule . /index.html [L]
</IfModule>
```

---

## Opsi 2: VPS/Cloud (Ubuntu)

```bash
# 1. Setup Server
sudo apt update && sudo apt install -y php8.2 php8.2-fpm php8.2-mysql \
  nginx git nodejs npm composer

# 2. Clone & Setup Backend
cd /var/www
git clone <repo-url> inovasi-boyolali
cd inovasi-boyolali/backend
composer install --optimize-autoloader --no-dev
cp .env.example .env
php artisan key:generate
php artisan migrate --force

# 3. Build Frontend
cd ../frontend
npm install
npm run build

# 4. Setup Nginx (copy config dari DEPLOYMENT_GUIDE.md)
# 5. Setup SSL dengan certbot
sudo certbot certonly --nginx -d yourdomain.com -d api.yourdomain.com
```

---

## Post-Deployment Checklist

- [ ] Backend API berjalan: `curl https://api.yourdomain.com/health`
- [ ] Frontend loaded: `https://yourdomain.com`
- [ ] Database terhubung: `php artisan tinker` → `DB::connection()->getPdo()`
- [ ] Logs bersih: `tail storage/logs/laravel.log`
- [ ] CORS OK: Frontend bisa call API

---

## Emergency Commands

```bash
# Clear everything
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Fix permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache

# Check status
php artisan tinker
> User::count()
```

**Lihat [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) untuk setup lengkap.**
