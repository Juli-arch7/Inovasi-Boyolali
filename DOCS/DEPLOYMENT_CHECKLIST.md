# ✅ Deployment Checklist

Checklist lengkap sebelum, saat, dan sesudah deployment.

---

## 📋 Pre-Deployment (Development)

### Code Quality
- [ ] Semua features sudah tested
- [ ] Tidak ada console.log atau debug code
- [ ] Tidak ada hard-coded credentials
- [ ] Git history clean (tidak ada secret files)
- [ ] README.md updated

### Backend
- [ ] `.env.example` up-to-date dengan semua variables
- [ ] Database migrations tested di local
- [ ] Routes sudah tested
- [ ] Error handling implemented
- [ ] Logging properly configured
- [ ] CORS configuration ready

### Frontend  
- [ ] Build berhasil tanpa warning/error: `npm run build`
- [ ] API endpoints sudah correct
- [ ] Environment variables di `.env.production`
- [ ] Images sudah optimized
- [ ] No console errors
- [ ] Responsive design tested

### Documentation
- [ ] API documentation complete (di Postman atau Swagger)
- [ ] Setup instructions documented
- [ ] Database schema documented

---

## 🔧 During Deployment

### Server Preparation

- [ ] SSH access tested
- [ ] Git access verified
- [ ] PHP 8.2+ installed
- [ ] MySQL/MariaDB running
- [ ] Composer installed
- [ ] Node.js 18+ installed
- [ ] npm installed
- [ ] Disk space sufficient

### Backend Setup

```bash
[ ] cd backend
[ ] composer install --optimize-autoloader --no-dev
[ ] cp .env.example .env
[ ] php artisan key:generate
[ ] Update .env with production values
[ ] php artisan migrate --force
[ ] php artisan db:seed (if needed)
[ ] php artisan config:cache
[ ] php artisan route:cache
[ ] php artisan view:cache
[ ] chmod -R 755 storage bootstrap/cache
[ ] chown -R www-data:www-data storage bootstrap/cache
```

### Frontend Setup

```bash
[ ] cd frontend
[ ] npm install
[ ] Update .env.production or vite.config.js
[ ] npm run build
[ ] Copy dist/ to production location
[ ] Create .htaccess or configure Nginx for SPA routing
```

### Web Server Configuration

**For Shared Hosting (cPanel):**
- [ ] Create addon domain/subdomain for backend
- [ ] Set document root to `/backend/public`
- [ ] .htaccess properly configured
- [ ] Upload frontend files to public_html

**For VPS/Cloud (Nginx):**
- [ ] Configure Nginx vhost for backend
- [ ] Configure Nginx vhost for frontend
- [ ] SSL certificate installed
- [ ] Proxy settings correct

### Database

- [ ] Database created
- [ ] Database user created with proper permissions
- [ ] Backup sebelum migration
- [ ] Migrations ran successfully
- [ ] Test data seeded (if needed)

### SSL/HTTPS

- [ ] SSL certificate obtained
- [ ] Certificate properly configured
- [ ] HTTP to HTTPS redirect setup
- [ ] Mixed content issues checked

---

## 🧪 Post-Deployment Testing

### Immediate Tests

```bash
[ ] API endpoint responds: curl https://api.yourdomain.com/api/health
[ ] Frontend loads: https://yourdomain.com
[ ] No 500 errors in logs
[ ] Database connection working
[ ] Static files loading (CSS, JS, images)
```

### Functional Tests

- [ ] Login works
- [ ] Create product works
- [ ] Submit product works
- [ ] Admin verification works
- [ ] Public portal displays products
- [ ] File uploads working
- [ ] Email notifications working (if applicable)

### Performance Tests

- [ ] Page load time acceptable
- [ ] API response time < 500ms
- [ ] Database queries optimized
- [ ] No N+1 queries
- [ ] Images properly optimized
- [ ] Bundle size reasonable

### Security Tests

- [ ] CORS properly configured
- [ ] No sensitive data in responses
- [ ] XSS protection active
- [ ] CSRF tokens implemented
- [ ] Input validation working
- [ ] SQL injection protected
- [ ] No debug information exposed

### Browser Compatibility

- [ ] Chrome/Edge latest
- [ ] Firefox latest
- [ ] Safari latest
- [ ] Mobile browsers tested

---

## 🚨 Monitoring Setup

- [ ] Error logging configured
- [ ] Uptime monitoring setup
- [ ] Database backup automated
- [ ] Log rotation configured
- [ ] Alert notifications setup

---

## 📊 Performance Optimization

### Backend
- [ ] Query optimization done
- [ ] Indexes created on database
- [ ] Cache properly configured
- [ ] Assets minified

### Frontend
- [ ] Code splitting implemented
- [ ] Lazy loading for routes
- [ ] Images compressed
- [ ] CSS/JS minified
- [ ] Assets cached properly

---

## 🔐 Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Database credentials NOT in git
- [ ] API keys secured
- [ ] SSL/HTTPS enforced
- [ ] Strong database passwords
- [ ] Regular backups scheduled
- [ ] Rate limiting implemented
- [ ] Input validation everywhere
- [ ] Authentication tested
- [ ] Authorization tested

---

## 📝 Documentation & Handover

- [ ] Production URL documented
- [ ] API documentation accessible
- [ ] Admin credentials stored securely
- [ ] Database credentials stored securely
- [ ] Deployment procedures documented
- [ ] Emergency procedures documented
- [ ] Team trained on deployment
- [ ] Rollback procedure documented

---

## 🆘 Emergency Procedures

### If Something Goes Wrong

```bash
[ ] Check error logs: tail -f storage/logs/laravel.log
[ ] Check server logs: journalctl -xe
[ ] Check database: php artisan tinker
[ ] Clear cache: php artisan cache:clear
[ ] Rollback code if needed
[ ] Restore database from backup if needed
```

### Keep Handy

- [ ] Hosting provider support number
- [ ] Database backup location
- [ ] SSH access details
- [ ] Git repository URL
- [ ] Emergency contact numbers

---

## 📅 Post-Deployment Monitoring (First Week)

- [ ] Check error logs daily
- [ ] Monitor performance metrics
- [ ] Test all user workflows
- [ ] Verify email notifications
- [ ] Check database performance
- [ ] Verify backups working
- [ ] Collect user feedback
- [ ] Monitor server resources

---

## 🎉 Sign-Off

- [ ] All tests passed
- [ ] Team lead approval
- [ ] Client/PO confirmation
- [ ] Documentation complete
- [ ] Team briefed

---

**Deployment Date:** _____________  
**Deployed By:** _____________  
**Approved By:** _____________  

**Notes:**
```
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
```

---

**Last Updated:** 2026-06-25
