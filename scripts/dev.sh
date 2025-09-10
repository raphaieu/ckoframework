#!/bin/bash

echo "🛠️  Iniciando ambiente de desenvolvimento..."

# Verificar se os containers estão rodando
if ! docker-compose ps | grep -q "Up"; then
    echo "⚠️  Containers não estão rodando. Iniciando..."
    docker-compose up -d
fi

echo "✅ Containers rodando"

# Instalar dependências do frontend (se necessário)
if [ ! -d "frontend/node_modules" ]; then
    echo "📦 Instalando dependências do frontend..."
    docker-compose exec frontend sh -c "cd /var/www/html && npm install"
fi

# Instalar dependências da API (se necessário)
if [ ! -d "api/vendor" ]; then
    echo "📦 Instalando dependências da API..."
    docker-compose exec api sh -c "cd /var/www/html && composer install"
fi

echo ""
echo "🎯 Ambiente de desenvolvimento pronto!"
echo ""
echo "📱 URLs:"
echo "   - Frontend: http://localhost:3000"
echo "   - API: http://localhost:8000"
echo ""
echo "🔧 Comandos úteis:"
echo "   - Logs da API: docker-compose logs -f api"
echo "   - Logs do Frontend: docker-compose logs -f frontend"
echo "   - Acessar API: docker-compose exec api bash"
echo "   - Acessar Frontend: docker-compose exec frontend sh"
echo ""
echo "💡 Para desenvolvimento:"
echo "   - Edite os arquivos em api/ e frontend/"
echo "   - Os containers recarregarão automaticamente"
echo "   - Use docker-compose restart api para recarregar PHP"
