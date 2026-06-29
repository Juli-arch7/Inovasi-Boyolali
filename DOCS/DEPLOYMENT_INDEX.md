# 📚 Deployment Documentation Index

Panduan lengkap untuk deployment project **Inovasi-Boyolali** dari development ke production.

---

## 🚀 Quick Start

Pilih sesuai environment Anda:

### **→ Shared Hosting (cPanel/Plesk)?**
👉 Baca: [QUICK_DEPLOY.md](QUICK_DEPLOY.md) → [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md#deployment-di-shared-hosting)

**Script untuk Windows:**
```powershell
.\deploy-backend.ps1
.\deploy-frontend.ps1
```

### **→ VPS/Cloud (Ubuntu/Debian)?**
👉 Baca: [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md#deployment-di-vpscloud)

**Script untuk Linux/Mac:**
```bash
chmod +x deploy-backend.sh deploy-frontend.sh
./deploy-backend.sh
./deploy-frontend.sh
```

### **→ Docker?**
👉 Baca: [DOCKER_SETUP.md](DOCKER_SETUP.md)

```bash
docker-compose up -d
```

---

## 📖 Dokumentasi Lengkap

| File | Konten |
|------|--------|
| [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) | **Panduan deployment lengkap** - Shared Hosting, VPS, Environment, Troubleshooting |
| [QUICK_DEPLOY.md](QUICK_DEPLOY.md) | **Quick reference** - 5 langkah deployment cepat |
| [ENV_PRODUCTION.md](ENV_PRODUCTION.md) | **Konfigurasi environment** - .env untuk production, security checklist |
| [DOCKER_SETUP.md](DOCKER_SETUP.md) | **Docker deployment** - Dockerfile, docker-compose, container setup |
| [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) | **Pre/During/Post deployment checklist** - Testing, security, monitoring |
| [postman_guide.md](postman_guide.md) | **API testing** - Postman collection setup |

---

## 📋 Checklist Deployment

Sebelum deploy ke production, pastikan sudah cek:

```
PRE-DEPLOYMENT:
[ ] Kode sudah tested di local
[ ] Database migrations tested
[ ] Frontend build berhasil (npm run build)
[ ] Environment variables siap
[ ] SSL certificate ready (untuk HTTPS)

DEPLOYMENT:
[ ] Server/hosting sudah siap
[ ] Dependencies installed (composer, npm)
[ ] Database migrasi berjalan
[ ] Backend running
[ ] Frontend build uploaded
[ ] SSL/HTTPS configured

POST-DEPLOYMENT:
[ ] API endpoint accessible
[ ] Frontend loading correctly
[ ] Database connection OK
[ ] All features working
[ ] Error logs checked
[ ] Monitoring setup
```

👉 Lihat lengkap: [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)

---

## 🔧 Scripts Tersedia

### Windows (PowerShell)

```powershell
# Backend deployment
.\deploy-backend.ps1
  ├─ Install composer dependencies
  ├─ Generate APP_KEY
  ├─ Run migrations
  └─ Cache configuration

# Frontend deployment  
.\deploy-frontend.ps1
  ├─ Install npm packages
  ├─ Build for production
  └─ Display build size
```

### Linux/Mac (Bash)

```bash
# Backend deployment
./deploy-backend.sh
  ├─ Install composer dependencies
  ├─ Generate APP_KEY
  ├─ Run migrations
  ├─ Set permissions
  └─ Cache configuration

# Frontend deployment
./deploy-frontend.sh
  ├─ Install npm packages
  ├─ Build for production
  └─ Show deployment instructions
```

---

## 🌐 Architecture

```
┌─────────────────────────────────────────────────────┐
│                   Production Server                  │
├─────────────────────────────────────────────────────┤
│                                                     │
│  ┌──────────────────────────────────────────────┐  │
│  │  Frontend (Vue 3 - Static SPA)               │  │
│  │  ├─ Domain: yourdomain.com                   │  │
│  │  ├─ Location: /var/www/frontend/dist        │  │
│  │  ├─ Server: Nginx                           │  │
│  │  └─ Port: 443 (HTTPS)                       │  │
│  └──────────────────────────────────────────────┘  │
│           ↓ API Calls (CORS OK)                    │
│  ┌──────────────────────────────────────────────┐  │
│  │  Backend (Laravel REST API)                  │  │
│  ├─ Domain: api.yourdomain.com                  │  │
│  ├─ Location: /var/www/backend                 │  │
│  ├─ PHP-FPM: 9000                              │  │
│  └─ Port: 443 (HTTPS)                          │  │
│  └──────────────────────────────────────────────┘  │
│           ↓ Database Queries                       │
│  ┌──────────────────────────────────────────────┐  │
│  │  MySQL Database                              │  │
│  ├─ Host: localhost                            │  │
│  ├─ Port: 3306                                 │  │
│  └─ Database: inovasi_db                       │  │
│  └──────────────────────────────────────────────┘  │
│                                                     │
└─────────────────────────────────────────────────────┘
```

---

## 🎯 Opsi Deployment

### 1️⃣ Shared Hosting (Termurah)

**Cocok untuk:**
- Small to medium projects
- Limited budget
- Tidak perlu custom infrastructure

**Tools:**
- cPanel/Plesk
- FTP/SFTP
- PHP + MySQL built-in

**Setup time:** ~30 menit  
**Maintenance:** Minimal

👉 [Lihat panduan Shared Hosting](DEPLOYMENT_GUIDE.md#deployment-di-shared-hosting)

---

### 2️⃣ VPS (Scalable)

**Cocok untuk:**
- Medium to large projects
- Custom requirements
- More control needed

**Tools:**
- Nginx/Apache
- Linux (Ubuntu/Debian)
- Custom domains

**Setup time:** ~1-2 jam  
**Maintenance:** Moderate

👉 [Lihat panduan VPS](DEPLOYMENT_GUIDE.md#deployment-di-vpscloud)

---

### 3️⃣ Docker (Professional)

**Cocok untuk:**
- Large scale projects
- Team deployment
- CI/CD pipelines
- Easy scaling

**Tools:**
- Docker
- Docker Compose
- Container orchestration

**Setup time:** ~2-3 jam  
**Maintenance:** Advanced

👉 [Lihat panduan Docker](DOCKER_SETUP.md)

---

## 🔑 Key Environment Variables

| Variable | Dev Value | Prod Value | Keterangan |
|----------|-----------|-----------|-----------|
| `APP_ENV` | `local` | `production` | ⚠️ Critical |
| `APP_DEBUG` | `true` | `false` | ⚠️ Critical |
| `DB_HOST` | `localhost` | `hostname` | Database server |
| `CACHE_STORE` | `file` | `redis`/`file` | Performance |
| `SESSION_DRIVER` | `file` | `database`/`redis` | Session storage |
| `MAIL_MAILER` | `log` | `smtp` | Email sending |

👉 Template lengkap: [ENV_PRODUCTION.md](ENV_PRODUCTION.md)

---

## 🧪 Testing Setelah Deploy

```bash
# 1. Test Backend API
curl -I https://api.yourdomain.com/api/health

# 2. Test Frontend
open https://yourdomain.com

# 3. Test Database
php artisan tinker
> User::count()

# 4. Check Logs
tail -f storage/logs/laravel.log

# 5. Monitor Performance
php artisan migrate:status
```

---

## 🆘 Troubleshooting

| Error | Solusi |
|-------|--------|
| 500 Internal Server Error | Check `storage/logs/laravel.log` |
| CORS Policy Error | Update `APP_CORS_ALLOWED_ORIGINS` in .env |
| Database Connection Refused | Verify DB credentials in .env |
| Frontend Blank Page | Check `.htaccess` or Nginx SPA routing |
| Static Files 404 | Check file permissions, verify nginx config |

👉 Lengkap: [DEPLOYMENT_GUIDE.md - Troubleshooting](DEPLOYMENT_GUIDE.md#troubleshooting)

---

## 📞 Support Resources

- 📖 [Laravel Documentation](https://laravel.com/docs)
- 📖 [Vue 3 Documentation](https://vuejs.org/)  
- 📖 [Nginx Documentation](https://nginx.org/en/docs/)
- 📖 [Docker Documentation](https://docs.docker.com/)
- 🐛 [Stack Overflow - Laravel](https://stackoverflow.com/questions/tagged/laravel)

---

## 💡 Best Practices

✅ **SELALU:**
- Use HTTPS/SSL
- Backup database secara regular
- Monitor error logs
- Keep dependencies updated
- Use environment variables untuk secrets
- Test di staging sebelum production
- Document configuration changes
- Setup monitoring & alerts

❌ **JANGAN:**
- Set `APP_DEBUG=true` di production
- Commit `.env` file ke git
- Use weak passwords untuk database
- Ignore error logs
- Deploy tanpa testing
- Put secrets di source code

---

## 🎓 Learning Path

```
1. Baca QUICK_DEPLOY.md (5 menit)
2. Pilih deployment option
3. Ikuti step-by-step DEPLOYMENT_GUIDE.md
4. Gunakan DEPLOYMENT_CHECKLIST.md sebagai reference
5. Setup monitoring & backup
6. Test thoroughly
7. Deploy!
```

---

## 📝 Version & Updates

- **Last Updated:** 2026-06-25
- **Project:** Inovasi-Boyolali
- **Backend:** Laravel 11+
- **Frontend:** Vue 3 + Vite
- **PHP:** 8.2+
- **Node:** 18+

---

## 🆘 Butuh Bantuan?

1. Cek [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)
2. Lihat [Troubleshooting section](DEPLOYMENT_GUIDE.md#troubleshooting)
3. Check logs: `tail -f storage/logs/laravel.log`
4. Konsultasi team lead atau DevOps

---

**Mari deploy! 🚀**

Pilih dokumentasi yang sesuai dengan lingkungan Anda dan ikuti step-by-step.
