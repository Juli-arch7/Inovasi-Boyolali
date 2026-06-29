# 🐳 Docker Setup (Optional)

Panduan deployment menggunakan Docker untuk production environment yang konsisten.

## Keuntungan Docker

✅ Environment yang sama di local, staging, dan production  
✅ Easy scaling dan deployment  
✅ Isolated dependencies  
✅ Version control untuk infrastruktur  

---

## File: `Dockerfile` (Backend)

```dockerfile
# Use official PHP image
FROM php:8.2-fpm

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    git \
    zip \
    unzip \
    libmysqlclient-dev \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
COPY backend/ .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port
EXPOSE 9000

# Run PHP-FPM
CMD ["php-fpm"]
```

---

## File: `Dockerfile` (Frontend)

```dockerfile
# Build stage
FROM node:18 AS builder

WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci

COPY frontend/ .
RUN npm run build

# Production stage
FROM nginx:alpine

COPY --from=builder /app/dist /usr/share/nginx/html

# Copy nginx config
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
```

---

## File: `docker-compose.yml`

```yaml
version: '3.8'

services:
  # Backend (Laravel)
  backend:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: inovasi-backend
    restart: always
    working_dir: /app
    environment:
      APP_ENV: production
      APP_DEBUG: "false"
      DB_HOST: db
      DB_PORT: 3306
      DB_DATABASE: inovasi_db
      DB_USERNAME: inovasi_user
      DB_PASSWORD: ${DB_PASSWORD}
    volumes:
      - ./backend:/app
      - ./backend/storage:/app/storage
    ports:
      - "9000:9000"
    depends_on:
      - db
    networks:
      - inovasi-network

  # Frontend (Vue 3)
  frontend:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: inovasi-frontend
    restart: always
    environment:
      VITE_API_URL: http://localhost:8000/api
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./frontend/dist:/usr/share/nginx/html
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - backend
    networks:
      - inovasi-network

  # MySQL Database
  db:
    image: mysql:8.0
    container_name: inovasi-db
    restart: always
    environment:
      MYSQL_DATABASE: inovasi_db
      MYSQL_USER: inovasi_user
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
    volumes:
      - db_data:/var/lib/mysql
    ports:
      - "3306:3306"
    networks:
      - inovasi-network

  # Redis (optional - untuk cache/session)
  redis:
    image: redis:7-alpine
    container_name: inovasi-redis
    restart: always
    ports:
      - "6379:6379"
    networks:
      - inovasi-network

volumes:
  db_data:

networks:
  inovasi-network:
    driver: bridge
```

---

## File: `.env.docker`

```env
APP_NAME="Portal Inovasi Daerah"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=inovasi_db
DB_USERNAME=inovasi_user
DB_PASSWORD=change_me_strong_password

SESSION_DRIVER=redis
CACHE_STORE=redis
REDIS_HOST=redis
```

---

## File: `nginx.conf`

```nginx
server {
    listen 80;
    server_name _;
    client_max_body_size 20M;

    root /usr/share/nginx/html;
    index index.html;

    # Frontend SPA routing
    location / {
        try_files $uri $uri/ /index.html;
    }

    # Static files caching
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # API proxy to backend
    location /api/ {
        proxy_pass http://backend:9000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

---

## Usage Commands

### Build Images

```bash
docker-compose build
```

### Start Services

```bash
# Start in background
docker-compose up -d

# View logs
docker-compose logs -f backend
docker-compose logs -f frontend
docker-compose logs -f db
```

### Stop Services

```bash
docker-compose stop
docker-compose down  # Stop & remove containers
```

### Database Migration

```bash
docker-compose exec backend php artisan migrate --force
docker-compose exec backend php artisan db:seed
```

### Debug

```bash
# Access backend shell
docker-compose exec backend bash

# Check MySQL
docker-compose exec db mysql -u inovasi_user -p inovasi_db

# View logs
docker-compose logs backend --tail=100
```

---

## Production Deployment

### Option 1: Docker Hub

```bash
# Login to Docker Hub
docker login

# Build & tag image
docker build -t your-username/inovasi-backend:1.0 -f Dockerfile .
docker build -t your-username/inovasi-frontend:1.0 -f Dockerfile .

# Push to Docker Hub
docker push your-username/inovasi-backend:1.0
docker push your-username/inovasi-frontend:1.0
```

### Option 2: Docker Swarm / Kubernetes

```bash
# Initialize Swarm (jika belum)
docker swarm init

# Deploy stack
docker stack deploy -c docker-compose.yml inovasi
```

### Option 3: AWS ECS, Google Cloud Run, atau Azure Container Instances

Lihat dokumentasi masing-masing layanan untuk deployment container.

---

## Useful Docker Commands

```bash
# Clean up everything
docker system prune -a

# View container stats
docker stats

# Access running container
docker exec -it inovasi-backend bash

# View container logs
docker logs -f inovasi-backend

# Rebuild specific service
docker-compose build --no-cache backend
```

---

## SSL/HTTPS dengan Docker

Gunakan **Let's Encrypt + Certbot** dalam container:

```bash
# Modify docker-compose.yml untuk volume SSL
volumes:
  - ./certs:/etc/letsencrypt

# Run certbot
docker-compose exec frontend certbot certonly --standalone -d yourdomain.com
```

---

**Note:** Docker option ini lebih cocok untuk production deployment yang scalable dan professional.

Last Updated: 2026-06-25
