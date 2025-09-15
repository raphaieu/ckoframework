# CKO Framework - Sistema Financeiro com AI

## 🚀 Visão Geral

O **CKO Framework** é uma solução completa de gestão financeira pessoal integrada com inteligência artificial, oferecendo análise inteligente de fluxo de caixa, trades e portfólio de investimentos.

### ✨ Características Principais

- **Frontend Moderno**: Vue 3 + Vite + TailwindCSS + shadcn-vue
- **Backend Robusto**: PHP 8.2 + Slim Framework + Eloquent ORM
- **AI Integrada**: Neuron AI + MCP (Model Context Protocol)
- **Arquitetura Unificada**: Tools compartilhadas entre sistemas
- **Interface Responsiva**: Design moderno e intuitivo
- **Containerização**: Docker para desenvolvimento e produção
- **Mobile App**: Flutter para iOS e Android

## 🏗️ Arquitetura do Sistema

```
┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend API   │
│   (Vue 3)       │◄──►│   (PHP/Slim)    │
└─────────────────┘    └─────────┬───────┘
                                 │
                    ┌────────────┼────────────┐
                    │            │            │
            ┌───────▼──────┐ ┌───▼────┐ ┌────▼────┐
            │  Neuron AI   │ │  MCP   │ │ Database│
            │  (Agents)    │ │(Tools) │ │(SQLite) │
            └──────────────┘ └────────┘ └─────────┘
```

## 🛠️ Tecnologias Utilizadas

### Frontend
- **Vue 3** - Framework JavaScript reativo
- **Vite** - Build tool e dev server
- **TailwindCSS** - Framework CSS utilitário
- **shadcn-vue** - Componentes UI modernos
- **Pinia** - Gerenciamento de estado
- **Vue Router** - Roteamento SPA

### Backend
- **PHP 8.2** - Linguagem de programação
- **Slim Framework** - Micro-framework web
- **Eloquent ORM** - ORM para banco de dados
- **SQLite** - Banco de dados (desenvolvimento)
- **JWT** - Autenticação de usuários

### AI & Machine Learning
- **Neuron AI** - Framework PHP para agentes AI
- **MCP (Model Context Protocol)** - Protocolo para tools externas
- **OpenAI GPT-4** - Modelo de linguagem
- **Anthropic Claude** - Modelo alternativo
- **Google Gemini** - Modelo alternativo

### Mobile
- **Flutter** - Framework para desenvolvimento mobile
- **Dart** - Linguagem de programação
- **Material Design** - Design system
- **Cupertino** - Design system iOS

### DevOps & Infraestrutura
- **Docker** - Containerização
- **Docker Compose** - Orquestração de containers
- **Nginx** - Servidor web (produção)
- **MySQL/PostgreSQL** - Banco de dados (produção)

## 📁 Estrutura do Projeto

```
ckoframework/
├── frontend/                 # Aplicação Vue 3
│   ├── src/
│   │   ├── components/      # Componentes reutilizáveis
│   │   ├── views/          # Páginas da aplicação
│   │   ├── router/         # Configuração de rotas
│   │   ├── stores/         # Estado global (Pinia)
│   │   └── lib/           # Utilitários e helpers
│   ├── package.json
│   └── vite.config.js
├── api/                     # Backend PHP
│   ├── src/
│   │   ├── AI/            # Sistema de AI
│   │   │   ├── Agents/    # Agentes Neuron AI
│   │   │   ├── Tools/     # Tools unificadas
│   │   │   ├── Adapters/  # Adaptadores para sistemas
│   │   │   ├── Core/      # Interfaces e registros
│   │   │   └── Servers/   # Servidores MCP
│   │   ├── Controllers/   # Controladores da API
│   │   ├── Models/        # Modelos de dados
│   │   └── Routes/        # Definição de rotas
│   ├── database/          # Migrações e seeders
│   ├── public/           # Ponto de entrada da API
│   └── composer.json
├── mobile/                  # Aplicação Flutter
│   ├── lib/
│   │   ├── screens/       # Telas da aplicação
│   │   ├── widgets/       # Widgets reutilizáveis
│   │   ├── services/      # Serviços e APIs
│   │   ├── models/        # Modelos de dados
│   │   └── utils/         # Utilitários
│   ├── android/           # Configuração Android
│   ├── ios/              # Configuração iOS
│   └── pubspec.yaml
├── docker/                 # Configurações Docker
│   ├── docker-compose.yml
│   ├── Dockerfile.api
│   ├── Dockerfile.frontend
│   └── Dockerfile.mobile
└── README.md
```

## 🚀 Instalação e Configuração

### Pré-requisitos

- **Node.js** 18+ e npm
- **PHP** 8.2+ com extensões: sqlite, json, curl
- **Composer** para dependências PHP
- **Docker** e Docker Compose
- **Flutter** 3.0+ (para desenvolvimento mobile)
- **Chave API** do OpenAI, Anthropic ou Gemini

### 1. Clone o Repositório

```bash
git clone https://github.com/seu-usuario/ckoframework.git
cd ckoframework
```

### 2. Configurar Backend

```bash
cd api
composer install
cp env.example .env
```

Edite o arquivo `.env`:
```env
# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# AI Configuration
AI_PROVIDER=openai
AI_API_KEY=sua_chave_api_aqui
AI_MODEL=gpt-4

# JWT
JWT_SECRET=sua_chave_secreta_jwt
```

### 3. Configurar Banco de Dados

```bash
php setup_database.php
```

### 4. Configurar Frontend

```bash
cd ../frontend
npm install
```

### 5. Iniciar com Docker (Recomendado)

```bash
# Iniciar todos os serviços
docker-compose up -d

# Verificar status
docker-compose ps
```

### 6. Iniciar Manualmente (Desenvolvimento)

**Terminal 1 - Backend:**
```bash
cd api
php -S localhost:8000 -t public
```

**Terminal 2 - Frontend:**
```bash
cd frontend
npm run dev
```

**Terminal 3 - Mobile (Opcional):**
```bash
cd mobile
flutter run
```

## 🎯 Funcionalidades

### 💰 Gestão Financeira

- **Fluxo de Caixa**: Controle de receitas e despesas
- **Categorização**: Organização por categorias
- **Relatórios**: Análises e insights automáticos
- **Tendências**: Identificação de padrões temporais

### 📈 Trading e Investimentos

- **Trades**: Registro de operações
- **Performance**: Métricas de rentabilidade
- **Análise de Risco**: Avaliação de perdas
- **Portfólio**: Acompanhamento de holdings

### 🤖 Inteligência Artificial

- **Chat Financeiro**: Interface conversacional
- **Análise Inteligente**: Insights baseados em dados
- **Recomendações**: Sugestões personalizadas
- **Tool Calling**: Execução automática de análises

## 🔧 API Endpoints

### Autenticação
- `POST /api/auth/login` - Login do usuário
- `POST /api/auth/register` - Registro de usuário
- `POST /api/auth/refresh` - Renovar token

### Dados Financeiros
- `GET /api/cashflow` - Listar transações
- `POST /api/cashflow` - Criar transação
- `GET /api/trades` - Listar trades
- `GET /api/holdings` - Listar holdings

### AI
- `POST /api/ai/analyze` - Análise financeira
- `GET /api/ai/status` - Status do sistema AI
- `POST /api/ai/chat` - Chat com AI

## 🧠 Sistema de AI

### Arquitetura Unificada

O sistema utiliza uma arquitetura unificada que permite:

1. **Tools Compartilhadas**: Mesma lógica para Neuron AI e MCP
2. **Adapters Específicos**: Conexão com diferentes sistemas
3. **Registry Central**: Gerenciamento centralizado de tools
4. **Execução Flexível**: Múltiplas formas de uso

### Tools Disponíveis

- **FinancialTool**: Análise financeira completa
- **DatabaseTool**: Acesso a dados do banco
- **AnalysisTool**: Cálculos e métricas

### Casos de Uso

**Single Agent (Neuron AI):**
```php
$agent = new FinanceAgent();
$response = $agent->analyzeFinance("trazer meu saldo");
```

**Multi-Agent (Neuron AI):**
```php
$agent1 = new FinanceAgent();
$agent2 = new TradingAgent();
// Ambos usam as mesmas tools
```

**MCP (Aplicações Externas):**
```php
$mcpServer = new MCPServer();
$result = $mcpServer->executeTool('financial_analysis', [
    'analysis_type' => 'cashflow',
    'period' => 'month'
]);
```

## 🧪 Testes

### Executar Testes da API

```bash
cd api
php test_ai.php
```

### Executar Testes de Tools

```bash
cd api
php test_unified_tools.php
```

### Testar Frontend

```bash
cd frontend
npm run test
```

## 📊 Exemplos de Uso

### 1. Análise de Fluxo de Caixa

```javascript
// Frontend
const response = await fetch('/api/ai/analyze', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    query: "Analise meu fluxo de caixa do último mês"
  })
});
```

### 2. Análise de Trades

```php
// Backend
$tool = new FinancialTool();
$result = $tool->execute([
    'analysis_type' => 'trades',
    'period' => 'quarter'
]);
```

### 3. Chat com AI

```javascript
// Frontend
const chatResponse = await fetch('/api/ai/chat', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    message: "Quais são minhas melhores categorias de receita?"
  })
});
```

## 🔒 Segurança

- **JWT Authentication**: Tokens seguros para autenticação
- **Input Validation**: Validação de dados de entrada
- **SQL Injection Protection**: Uso de ORM para queries seguras
- **CORS Configuration**: Configuração adequada de CORS
- **Environment Variables**: Chaves sensíveis em variáveis de ambiente

## 🚀 Deploy

### Desenvolvimento
- Frontend: `http://localhost:3002`
- Backend: `http://localhost:8000`
- Mobile: `flutter run` (iOS/Android)

### Produção com Docker
```bash
# Build das imagens
docker-compose -f docker-compose.prod.yml build

# Deploy
docker-compose -f docker-compose.prod.yml up -d
```

### Produção Manual
1. Configure variáveis de ambiente
2. Execute migrações do banco
3. Configure servidor web (Nginx/Apache)
4. Configure SSL/HTTPS
5. Configure backup do banco de dados
6. Build e deploy do mobile (iOS/Android)

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

- **Issues**: [GitHub Issues](https://github.com/seu-usuario/ckoframework/issues)
- **Documentação**: [Wiki do Projeto](https://github.com/seu-usuario/ckoframework/wiki)
- **Discord**: [Servidor da Comunidade](https://discord.gg/ckoframework)

## 🎯 Roadmap

- [ ] Multi-tenant support
- [ ] Integração com corretoras
- [ ] Relatórios avançados
- [x] Mobile app (Flutter) - Em desenvolvimento
- [ ] Integração com APIs financeiras
- [ ] Machine Learning para previsões
- [ ] Notificações em tempo real
- [ ] Docker para produção
- [ ] CI/CD com GitHub Actions
- [ ] Monitoramento com Prometheus

---

**Desenvolvido com ❤️ pela equipe CKO Framework**