# CKO Framework - Documentação Docker

## 🐳 Visão Geral

O CKO Framework utiliza Docker para containerização, facilitando o desenvolvimento, teste e deploy em diferentes ambientes.

## 🏗️ Arquitetura Docker

```
┌─────────────────────────────────────────────────────────────────┐
│                        Docker Compose                          │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐            │
│  │   Frontend  │  │    API      │  │  Database   │            │
│  │   (Vue 3)   │  │   (PHP)     │  │ (MySQL)     │            │
│  │   Port 3002 │  │   Port 8000 │  │   Port 3306 │            │
│  └─────────────┘  └─────────────┘  └─────────────┘            │
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐            │
│  │    Nginx    │  │   Redis     │  │   Mobile    │            │
│  │  (Proxy)    │  │   (Cache)   │  │ (Flutter)   │            │
│  │   Port 80   │  │   Port 6379 │  │   Port 8080 │            │
│  └─────────────┘  └─────────────┘  └─────────────┘            │
└─────────────────────────────────────────────────────────────────┘
```

## 📁 Estrutura Docker

```
docker/
├── docker-compose.yml          # Desenvolvimento
├── docker-compose.prod.yml     # Produção
├── Dockerfile.api              # Backend PHP
├── Dockerfile.frontend         # Frontend Vue 3
├── Dockerfile.mobile           # Mobile Flutter
├── nginx/
│   ├── nginx.conf              # Configuração Nginx
│   └── default.conf            # Virtual host
└── scripts/
    ├── setup.sh                # Script de setup
    └── deploy.sh               # Script de deploy
```

## 🔧 Configuração

### Docker Compose - Desenvolvimento

```yaml
# docker-compose.yml
version: '3.8'

services:
  # Database
  mysql:
    image: mysql:8.0
    container_name: cko_mysql
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: cko_framework
      MYSQL_USER: cko_user
      MYSQL_PASSWORD: cko_password
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
      - ./database/init.sql:/docker-entrypoint-initdb.d/init.sql

  # Redis Cache
  redis:
    image: redis:7-alpine
    container_name: cko_redis
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data

  # Backend API
  api:
    build:
      context: ../api
      dockerfile: ../docker/Dockerfile.api
    container_name: cko_api
    ports:
      - "8000:8000"
    environment:
      - DB_CONNECTION=mysql
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_DATABASE=cko_framework
      - DB_USERNAME=cko_user
      - DB_PASSWORD=cko_password
      - REDIS_HOST=redis
      - REDIS_PORT=6379
    depends_on:
      - mysql
      - redis
    volumes:
      - ../api:/var/www/html
      - ./logs/api:/var/www/html/logs

  # Frontend
  frontend:
    build:
      context: ../frontend
      dockerfile: ../docker/Dockerfile.frontend
    container_name: cko_frontend
    ports:
      - "3002:3002"
    environment:
      - VITE_API_URL=http://localhost:8000/api
    volumes:
      - ../frontend:/app
      - /app/node_modules

  # Mobile (Flutter Web)
  mobile:
    build:
      context: ../mobile
      dockerfile: ../docker/Dockerfile.mobile
    container_name: cko_mobile
    ports:
      - "8080:8080"
    environment:
      - API_URL=http://localhost:8000/api
    volumes:
      - ../mobile:/app

  # Nginx (Proxy)
  nginx:
    image: nginx:alpine
    container_name: cko_nginx
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx/nginx.conf:/etc/nginx/nginx.conf
      - ./nginx/default.conf:/etc/nginx/conf.d/default.conf
      - ../frontend/dist:/usr/share/nginx/html
    depends_on:
      - frontend
      - api

volumes:
  mysql_data:
  redis_data:
```

### Docker Compose - Produção

```yaml
# docker-compose.prod.yml
version: '3.8'

services:
  # Database
  mysql:
    image: mysql:8.0
    container_name: cko_mysql_prod
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
      MYSQL_DATABASE: ${MYSQL_DATABASE}
      MYSQL_USER: ${MYSQL_USER}
      MYSQL_PASSWORD: ${MYSQL_PASSWORD}
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - cko_network
    restart: unless-stopped

  # Redis Cache
  redis:
    image: redis:7-alpine
    container_name: cko_redis_prod
    volumes:
      - redis_data:/data
    networks:
      - cko_network
    restart: unless-stopped

  # Backend API
  api:
    build:
      context: ../api
      dockerfile: ../docker/Dockerfile.api
    container_name: cko_api_prod
    environment:
      - APP_ENV=production
      - DB_CONNECTION=mysql
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_DATABASE=${MYSQL_DATABASE}
      - DB_USERNAME=${MYSQL_USER}
      - DB_PASSWORD=${MYSQL_PASSWORD}
      - REDIS_HOST=redis
      - REDIS_PORT=6379
      - JWT_SECRET=${JWT_SECRET}
      - AI_API_KEY=${AI_API_KEY}
    depends_on:
      - mysql
      - redis
    networks:
      - cko_network
    restart: unless-stopped

  # Frontend
  frontend:
    build:
      context: ../frontend
      dockerfile: ../docker/Dockerfile.frontend
    container_name: cko_frontend_prod
    environment:
      - NODE_ENV=production
      - VITE_API_URL=${API_URL}
    networks:
      - cko_network
    restart: unless-stopped

  # Nginx (Proxy)
  nginx:
    image: nginx:alpine
    container_name: cko_nginx_prod
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx/nginx.prod.conf:/etc/nginx/nginx.conf
      - ./nginx/default.prod.conf:/etc/nginx/conf.d/default.conf
      - ../frontend/dist:/usr/share/nginx/html
      - ./ssl:/etc/nginx/ssl
    depends_on:
      - frontend
      - api
    networks:
      - cko_network
    restart: unless-stopped

volumes:
  mysql_data:
  redis_data:

networks:
  cko_network:
    driver: bridge
```

## 🐳 Dockerfiles

### Dockerfile.api (Backend PHP)

```dockerfile
# Dockerfile.api
FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libsqlite3-dev

# Instalar extensões PHP
RUN docker-php-ext-install \
    pdo_mysql \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos
COPY . .

# Instalar dependências
RUN composer install --no-dev --optimize-autoloader

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Expor porta
EXPOSE 8000

# Comando de inicialização
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
```

### Dockerfile.frontend (Frontend Vue 3)

```dockerfile
# Dockerfile.frontend
FROM node:18-alpine

# Configurar diretório de trabalho
WORKDIR /app

# Copiar package.json e package-lock.json
COPY package*.json ./

# Instalar dependências
RUN npm ci --only=production

# Copiar código fonte
COPY . .

# Build da aplicação
RUN npm run build

# Instalar servidor estático
RUN npm install -g serve

# Expor porta
EXPOSE 3002

# Comando de inicialização
CMD ["serve", "-s", "dist", "-l", "3002"]
```

### Dockerfile.mobile (Mobile Flutter)

```dockerfile
# Dockerfile.mobile
FROM ubuntu:20.04

# Instalar dependências
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    xz-utils \
    zip \
    libglu1-mesa

# Instalar Flutter
RUN git clone https://github.com/flutter/flutter.git -b stable /flutter
ENV PATH="/flutter/bin:${PATH}"

# Verificar instalação
RUN flutter doctor

# Configurar diretório de trabalho
WORKDIR /app

# Copiar código fonte
COPY . .

# Instalar dependências
RUN flutter pub get

# Build para web
RUN flutter build web

# Instalar servidor estático
RUN apt-get install -y nginx

# Configurar Nginx
COPY nginx.conf /etc/nginx/sites-available/default

# Expor porta
EXPOSE 8080

# Comando de inicialização
CMD ["nginx", "-g", "daemon off;"]
```

## 🚀 Comandos Docker

### Desenvolvimento

```bash
# Iniciar todos os serviços
docker-compose up -d

# Ver logs
docker-compose logs -f

# Parar serviços
docker-compose down

# Rebuild de um serviço
docker-compose up --build api

# Executar comando em container
docker-compose exec api php artisan migrate
docker-compose exec frontend npm run build
docker-compose exec mobile flutter pub get
```

### Produção

```bash
# Build das imagens
docker-compose -f docker-compose.prod.yml build

# Deploy
docker-compose -f docker-compose.prod.yml up -d

# Ver logs
docker-compose -f docker-compose.prod.yml logs -f

# Backup do banco
docker-compose exec mysql mysqldump -u root -p cko_framework > backup.sql

# Restore do banco
docker-compose exec -T mysql mysql -u root -p cko_framework < backup.sql
```

### Manutenção

```bash
# Limpar containers parados
docker-compose down --remove-orphans

# Limpar volumes não utilizados
docker volume prune

# Limpar imagens não utilizadas
docker image prune

# Ver uso de recursos
docker stats

# Ver logs de um serviço específico
docker-compose logs -f api
```

## 🔧 Configuração Nginx

### nginx.conf

```nginx
# nginx.conf
user nginx;
worker_processes auto;

events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    # Logs
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    # Gzip
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # Rate limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;

    # Upstreams
    upstream api {
        server api:8000;
    }

    upstream frontend {
        server frontend:3002;
    }

    # Servidor principal
    server {
        listen 80;
        server_name localhost;

        # Frontend
        location / {
            proxy_pass http://frontend;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
        }

        # API
        location /api {
            limit_req zone=api burst=20 nodelay;
            proxy_pass http://api;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
        }

        # Mobile
        location /mobile {
            proxy_pass http://mobile:8080;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
        }
    }
}
```

## 🔒 Segurança

### Variáveis de Ambiente

```bash
# .env.docker
# Database
MYSQL_ROOT_PASSWORD=secure_root_password
MYSQL_DATABASE=cko_framework
MYSQL_USER=cko_user
MYSQL_PASSWORD=secure_user_password

# API
JWT_SECRET=your_jwt_secret_here
AI_API_KEY=your_ai_api_key_here

# URLs
API_URL=https://api.ckoframework.com
FRONTEND_URL=https://ckoframework.com
```

### SSL/TLS

```bash
# Gerar certificados SSL
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout ./ssl/private.key \
  -out ./ssl/certificate.crt

# Configurar Nginx com SSL
# Adicionar ao nginx.conf:
# listen 443 ssl;
# ssl_certificate /etc/nginx/ssl/certificate.crt;
# ssl_certificate_key /etc/nginx/ssl/private.key;
```

## 📊 Monitoramento

### Health Checks

```yaml
# Adicionar aos serviços
healthcheck:
  test: ["CMD", "curl", "-f", "http://localhost:8000/api/health"]
  interval: 30s
  timeout: 10s
  retries: 3
  start_period: 40s
```

### Logs

```bash
# Configurar logs
logging:
  driver: "json-file"
  options:
    max-size: "10m"
    max-file: "3"
```

## 🧪 Testes

### Testes de Integração

```bash
# Executar testes em containers
docker-compose exec api composer test
docker-compose exec frontend npm run test
docker-compose exec mobile flutter test
```

### Testes de Performance

```bash
# Teste de carga
docker run --rm -it --network cko_network \
  williamyeh/wrk -t12 -c400 -d30s http://nginx/api/health
```

## 🚀 Deploy

### Script de Deploy

```bash
#!/bin/bash
# scripts/deploy.sh

echo "🚀 Iniciando deploy do CKO Framework..."

# Build das imagens
echo "📦 Building images..."
docker-compose -f docker-compose.prod.yml build

# Parar serviços antigos
echo "🛑 Stopping old services..."
docker-compose -f docker-compose.prod.yml down

# Iniciar novos serviços
echo "▶️ Starting new services..."
docker-compose -f docker-compose.prod.yml up -d

# Aguardar serviços ficarem prontos
echo "⏳ Waiting for services to be ready..."
sleep 30

# Verificar saúde dos serviços
echo "🔍 Checking service health..."
docker-compose -f docker-compose.prod.yml ps

echo "✅ Deploy completed!"
```

### CI/CD

```yaml
# .github/workflows/docker.yml
name: Docker Build and Deploy

on:
  push:
    branches: [main]

jobs:
  build:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Build and push images
      run: |
        docker-compose -f docker-compose.prod.yml build
        docker-compose -f docker-compose.prod.yml push
    
    - name: Deploy to production
      run: |
        ssh user@server 'cd /path/to/app && ./scripts/deploy.sh'
```

## 🔧 Troubleshooting

### Problemas Comuns

1. **Porta já em uso**
   ```bash
   # Verificar portas em uso
   netstat -tulpn | grep :8000
   
   # Parar processo
   sudo kill -9 <PID>
   ```

2. **Container não inicia**
   ```bash
   # Ver logs
   docker-compose logs api
   
   # Verificar configuração
   docker-compose config
   ```

3. **Banco de dados não conecta**
   ```bash
   # Verificar se MySQL está rodando
   docker-compose exec mysql mysql -u root -p
   
   # Verificar variáveis de ambiente
   docker-compose exec api env | grep DB_
   ```

### Comandos Úteis

```bash
# Entrar em container
docker-compose exec api bash
docker-compose exec frontend sh
docker-compose exec mobile bash

# Ver logs em tempo real
docker-compose logs -f --tail=100

# Reiniciar serviço
docker-compose restart api

# Ver uso de recursos
docker stats

# Limpar tudo
docker-compose down -v --remove-orphans
docker system prune -a
```

---

**Documentação Docker CKO Framework v1.0**
