# CKO Framework

Framework full-stack para desenvolvimento rápido de MVPs e micro-projetos.

## 🚀 Stack

- **Backend**: PHP 8.2 + Slim Framework + Eloquent ORM
- **Frontend**: Vue 3 + Shadcn/ui
- **Banco**: SQLite (desenvolvimento) / MySQL (produção)
- **Infra**: Docker Compose
- **CI/CD**: GitHub Actions

## 🏗️ Estrutura

```
ckoframework/
├── api/                    # Backend PHP
├── frontend/               # Frontend Vue
├── docker/                 # Configurações Docker
├── docker-compose.yml      # Orquestração dos serviços
└── .github/                # GitHub Actions
```

## 🚀 Quick Start

### Desenvolvimento Local

```bash
# Clonar e entrar no projeto
git clone <repo>
cd ckoframework

# Iniciar todos os serviços
docker-compose up -d

# Acessar
# API: http://localhost:8000
# Frontend: http://localhost:3000
# MySQL: localhost:3306
```

### Alternar Banco de Dados

```bash
# SQLite (padrão)
docker-compose up -d

# MySQL
docker-compose -f docker-compose.yml -f docker-compose.mysql.yml up -d
```

## 🔧 Comandos Úteis

```bash
# Ver logs
docker-compose logs -f

# Parar serviços
docker-compose down

# Rebuild containers
docker-compose up -d --build

# Acessar container da API
docker-compose exec api bash

# Acessar container do frontend
docker-compose exec frontend bash
```

## 📁 Desenvolvimento

### Backend (PHP)
- Controllers em `api/src/Controllers/`
- Models em `api/src/Models/`
- Rotas em `api/src/Routes/`
- Middleware em `api/src/Middleware/`

### Frontend (Vue)
- Componentes em `frontend/src/components/`
- Views em `frontend/src/views/`
- Stores em `frontend/src/stores/`

## 🔒 Segurança

- Containers isolados e não-privilegiados
- Volumes limitados aos diretórios necessários
- Network isolation entre serviços
- Secrets via arquivos .env

## 📝 Licença

MIT
