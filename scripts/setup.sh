#!/bin/bash

echo "🚀 Configurando CKO Framework..."

# Verificar se Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker não está instalado. Por favor, instale o Docker primeiro."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose não está instalado. Por favor, instale o Docker Compose primeiro."
    exit 1
fi

echo "✅ Docker e Docker Compose encontrados"

# Criar diretórios necessários
echo "📁 Criando diretórios..."
mkdir -p api/database
mkdir -p frontend/dist

# Copiar arquivo de ambiente
if [ ! -f api/.env ]; then
    echo "📝 Copiando arquivo de ambiente..."
    cp api/env.example api/.env
    echo "⚠️  Configure o arquivo api/.env com suas credenciais"
fi

# Dar permissões
echo "🔐 Configurando permissões..."
chmod +x scripts/*.sh

# Build dos containers
echo "🔨 Fazendo build dos containers..."
docker-compose build

# Iniciar serviços
echo "🚀 Iniciando serviços..."
docker-compose up -d

echo ""
echo "🎉 CKO Framework configurado com sucesso!"
echo ""
echo "📱 Acesse:"
echo "   - Frontend: http://localhost:3000"
echo "   - API: http://localhost:8000"
echo "   - Health Check: http://localhost:8000/health"
echo ""
echo "🔧 Comandos úteis:"
echo "   - Ver logs: docker-compose logs -f"
echo "   - Parar: docker-compose down"
echo "   - Rebuild: docker-compose up -d --build"
echo ""
echo "📚 Para alternar para MySQL:"
echo "   docker-compose -f docker-compose.yml -f docker-compose.mysql.yml up -d"
