# 📦 Panduan Deployment Inovasi-Boyolali

Panduan lengkap untuk deploy aplikasi **Portal Inovasi Daerah** ke server production.

---

## 📋 Daftar Isi
1. [Prasyarat](#prasyarat)
2. [Struktur Project](#struktur-project)
3. [Deployment di Shared Hosting](#deployment-di-shared-hosting)
4. [Deployment di VPS/Cloud](#deployment-di-vpscloud)
5. [Konfigurasi Database](#konfigurasi-database)
6. [Troubleshooting](#troubleshooting)

---

## Prasyarat

### Untuk Server
- PHP 8.1+ (minimal 8.2)
- MySQL/MariaDB 5.7+
- Composer (untuk PHP dependencies)
- Node.js 18+ & npm (untuk build frontend)
- Git (untuk pull dari repository)

### Untuk Development (Local)
- PHP 8.1+
- Composer
- Node.js 18+
- npm atau yarn

---

## Struktur Project

```
Inovasi-Boyolali/
├── backend/          (Laravel API - berjalan di 8000 atau custom port)
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── public/       (Root untuk backend)
│   ├── routes/
│   └── storage/
├── frontend/         (Vue 3 - di-deploy sebagai static files)
│   ├── src/
│   ├── dist/        (Output build - digunakan saat production)
│   └── public/
└── postman_guide.md
```

**Catatan Penting:**
- Backend adalah REST API (tidak render HTML)
- Frontend adalah SPA (Single Page Application) yang di-serve terpisah
- Keduanya bisa di-deploy di server yang sama atau berbeda

---

## Deployment di Shared Hosting

### Opsi 1: Shared Hosting Tradisional (cPanel/Plesk)

#### Step 1: Setup Backend di Folder Berbeda

```bash
# 1. Login via FTP/File Manager
# 2. Upload backend ke folder, contoh: /backend atau /api

# Struktur folder di server:
/public_html/
├── index.php (frontend)
├── ... (frontend static files)
└── /backend/        ← Backend API
    ├── public/
    ├── app/
    ├── ...
```

#### Step 2: Setup Addon Domain / Sub-domain untuk Backend

**Via cPanel:**
1. Addon Domains atau Sub-domains
2. Buat domain baru: `api.yourdomain.com` → `/backend`
3. Set Document Root ke `/backend/public`

#### Step 3: Konfigurasi Backend

```bash
# 1. SSH ke server atau gunakan terminal cPanel
cd /home/username/public_html/backend

# 2. Install dependencies
composer install --optimize-autoloader --no-dev

# 3. Copy .env file
cp .env.example .env

# 4. Generate APP_KEY
php artisan key:generate

# 5. Setup database
php artisan migrate --force
php artisan db:seed  # opsional

# 6. Clear cache
php artisan config:clear
php artisan cache:clear
```

#### Step 4: Konfigurasi .env untuk Backend

```env
APP_NAME=InovasiDaerah
APP_ENV=production
APP_DEBUG=false          # PENTING: false di production!
APP_URL=https://api.yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost       # atau hostname yang disediakan hosting
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=db_username
DB_PASSWORD=db_password

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=file

# App Settings
QUEUE_CONNECTION=database

# CORS - izinkan frontend domain
APP_CORS_ALLOWED_ORIGINS=https://yourdomain.com
```

#### Step 5: Setup Frontend

```bash
# 1. Di folder lokal/dev, build frontend
cd frontend
npm install
npm run build

# 2. Upload folder dist/ ke public_html via FTP/File Manager
# Hasil: /public_html/index.html, /public_html/assets/, dll
```

#### Step 6: Update Frontend API Base URL

**File:** `frontend/src/services/api.js` atau file konfigurasi API

```javascript
// Untuk production
const API_BASE = 'https://api.yourdomain.com/api'
// atau bisa dari env variable
const API_BASE = process.env.VUE_APP_API_URL || 'https://api.yourdomain.com/api'
```

#### Step 7: Setup htaccess untuk Frontend (SPA Routing)

**File:** `/public_html/.htaccess`

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

## Deployment di VPS/Cloud

### Opsi 2: VPS dengan Nginx + PM2

#### Step 1: Setup Server

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install dependencies
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
  php8.2-xml php8.2-curl nginx git curl

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install PM2 (untuk manage Node/frontend)
sudo npm install -g pm2
```

#### Step 2: Clone Repository

```bash
cd /var/www
sudo git clone <repository-url> inovasi-boyolali
cd inovasi-boyolali
sudo chown -R $USER:$USER .
```

#### Step 3: Setup Backend (Laravel)

```bash
cd /var/www/inovasi-boyolali/backend

# Install dependencies
composer install --optimize-autoloader --no-dev

# Setup .env
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --force
php artisan db:seed

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache
```

#### Step 4: Konfigurasi .env Backend

```env
APP_NAME=InovasiDaerah
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inovasi_db
DB_USERNAME=inovasi_user
DB_PASSWORD=strong_password_here

QUEUE_CONNECTION=database
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

#### Step 5: Setup Frontend (Vue 3)

```bash
cd /var/www/inovasi-boyolali/frontend

# Install & build
npm install
npm run build

# Output ada di folder 'dist/'
```

#### Step 6: Konfigurasi Nginx

**File:** `/etc/nginx/sites-available/inovasi-boyolali`

```nginx
# Backend API
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name api.yourdomain.com;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    root /var/www/inovasi-boyolali/backend/public;
    index index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ /\.ht {
        deny all;
    }

    # Redirect to https
    if ($scheme != "https") {
        return 301 https://$server_name$request_uri;
    }
}

# Frontend SPA
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    root /var/www/inovasi-boyolali/frontend/dist;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # CORS headers untuk frontend
    location ~ ^/api/ {
        proxy_pass https://api.yourdomain.com;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # Redirect to https
    if ($scheme != "https") {
        return 301 https://$server_name$request_uri;
    }
}

# Redirect http to https
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com api.yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```

Enable Nginx config:
```bash
sudo ln -s /etc/nginx/sites-available/inovasi-boyolali /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### Step 7: SSL Certificate (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot certonly --nginx -d yourdomain.com -d api.yourdomain.com
```

---

## Konfigurasi Database

### Setup Database MySQL

```bash
# Login ke MySQL
mysql -u root -p

# Create database
CREATE DATABASE inovasi_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create user
CREATE USER 'inovasi_user'@'localhost' IDENTIFIED BY 'strong_password_here';

# Grant privileges
GRANT ALL PRIVILEGES ON inovasi_db.* TO 'inovasi_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Run Migrations

```bash
cd /path/to/backend
php artisan migrate --force
php artisan db:seed --force  # opsional
```

---

## Environment Variables Penting

### Backend (.env)

| Variable | Contoh | Keterangan |
|----------|--------|-----------|
| `APP_ENV` | `production` | Harus production di live |
| `APP_DEBUG` | `false` | Jangan true di production! |
| `APP_URL` | `https://api.yourdomain.com` | URL backend |
| `DB_DATABASE` | `inovasi_db` | Nama database |
| `DB_USERNAME` | `inovasi_user` | Username database |
| `DB_PASSWORD` | `password123` | Password database |
| `SESSION_DRIVER` | `database` atau `file` | Penyimpanan session |
| `CACHE_STORE` | `file` atau `redis` | Penyimpanan cache |

### Frontend (.env.production)

```
VITE_API_URL=https://api.yourdomain.com/api
VITE_APP_NAME=Portal Inovasi Daerah
```

---

## Deployment Workflow (Otomatis)

### GitHub Actions untuk Auto-Deploy

**File:** `.github/workflows/deploy.yml`

```yaml
name: Deploy Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Deploy Backend
        run: |
          ssh user@server.com << 'EOF'
          cd /var/www/inovasi-boyolali/backend
          git pull origin main
          composer install --optimize-autoloader --no-dev
          php artisan migrate --force
          php artisan config:cache
          php artisan cache:clear
          EOF

      - name: Deploy Frontend
        run: |
          ssh user@server.com << 'EOF'
          cd /var/www/inovasi-boyolali/frontend
          git pull origin main
          npm install
          npm run build
          EOF
```

---

## Troubleshooting

### 1. Error: "500 Internal Server Error"

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Clear cache & config
php artisan cache:clear
php artisan config:clear
```

### 2. Error: "CORS Policy: No 'Access-Control-Allow-Origin'"

**Update Backend** (`.env`):
```env
APP_CORS_ALLOWED_ORIGINS=https://yourdomain.com
```

**Atau di Nginx** (tambahkan headers):
```nginx
add_header 'Access-Control-Allow-Origin' 'https://yourdomain.com';
add_header 'Access-Control-Allow-Methods' 'GET, POST, PUT, DELETE, OPTIONS';
```

### 3. Error: "Database Connection Refused"

```bash
# Check MySQL running
sudo systemctl status mysql

# Verify DB credentials di .env
php artisan migrate --force

# Check file permissions
sudo chown -R www-data:www-data storage bootstrap/cache
```

### 4. Frontend Blank / 404 Error

```bash
# Pastikan .htaccess atau Nginx config untuk SPA routing benar
# Check build output
ls -la frontend/dist/

# Rebuild jika perlu
cd frontend
npm run build
```

### 5. Static Files (CSS/JS) Tidak Load

```nginx
# Di Nginx, pastikan location ini ada
location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}

# Atau di .htaccess
<FilesMatch "\.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$">
    Header set Cache-Control "max-age=2592000, public"
</FilesMatch>
```

---

## Checklist Pre-Production

- [ ] `.env` sudah dikonfigurasi dengan nilai production
- [ ] `APP_DEBUG=false`
- [ ] Database sudah di-setup dan teruji
- [ ] Migrations sudah jalan
- [ ] SSL Certificate sudah installed (HTTPS)
- [ ] Frontend sudah di-build (`npm run build`)
- [ ] API URL di frontend sudah benar
- [ ] CORS sudah dikonfigurasi dengan benar
- [ ] Storage permissions sudah benar
- [ ] Backup database dijadwalkan
- [ ] Monitoring/logging sudah setup
- [ ] Email configuration sudah tested (jika ada)

---

## File Penting yang Perlu Dikonfigurasi

```
backend/
├── .env                    ← CRITICAL: Database & app config
├── config/cors.php         ← CORS settings
├── config/app.php          ← App configuration
└── storage/                ← Must be writable

frontend/
├── src/services/api.js     ← API base URL
├── .env.production         ← Production variables
└── dist/                   ← Output build (di-upload ke server)
```

---

## Perintah Berguna di Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Optimize class loader
composer install --optimize-autoloader --no-dev

# Check queue jobs (jika pakai queue)
php artisan queue:work

# Monitor logs
tail -f storage/logs/laravel.log

# Database backup
mysqldump -u user -p database_name > backup.sql

# Frontend rebuild
npm run build
```

---

## Support & Referensi

- [Laravel Documentation](https://laravel.com/docs)
- [Vue 3 Documentation](https://vuejs.org/)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

**Last Updated:** 2026-06-25
