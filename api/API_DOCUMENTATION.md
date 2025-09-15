# CKO Framework API - Documentação Técnica

## 📋 Visão Geral

A API do CKO Framework é construída com PHP 8.2 + Slim Framework, oferecendo endpoints para gestão financeira e integração com sistemas de AI.

## 🏗️ Arquitetura da API

```
┌─────────────────┐
│   Frontend      │
│   (Vue 3)       │
└─────────┬───────┘
          │ HTTP/HTTPS
          ▼
┌─────────────────┐
│   Slim Router   │
│   (api.php)     │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  Controllers    │
│  (Business)     │
└─────────┬───────┘
          │
    ┌─────┴─────┐
    │           │
    ▼           ▼
┌──────┐  ┌─────────┐
│ AI   │  │ Models  │
│ Core │  │(Eloquent)│
└──────┘  └─────────┘
```

## 🔧 Configuração

### Variáveis de Ambiente

```env
# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# AI Configuration
AI_PROVIDER=openai
AI_API_KEY=sua_chave_api
AI_MODEL=gpt-4

# JWT
JWT_SECRET=sua_chave_secreta
JWT_EXPIRY=3600

# Server
APP_ENV=development
APP_DEBUG=true
```

### Dependências Principais

```json
{
  "require": {
    "php": "^8.1",
    "slim/slim": "^4.11",
    "illuminate/database": "^10.0",
    "inspector-apm/neuron-ai": "dev-main",
    "mcp/sdk": "dev-main",
    "firebase/php-jwt": "^6.8"
  }
}
```

## 🛠️ Endpoints da API

### Autenticação

#### POST /api/auth/login
Autentica um usuário e retorna JWT token.

**Request:**
```json
{
  "email": "usuario@exemplo.com",
  "password": "senha123"
}
```

**Response:**
```json
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": 1,
    "name": "João Silva",
    "email": "usuario@exemplo.com"
  }
}
```

#### POST /api/auth/register
Registra um novo usuário.

**Request:**
```json
{
  "name": "João Silva",
  "email": "usuario@exemplo.com",
  "password": "senha123"
}
```

### Dados Financeiros

#### GET /api/cashflow
Lista transações de fluxo de caixa.

**Query Parameters:**
- `type`: `income` | `expense` | `all` (default: `all`)
- `category_id`: ID da categoria
- `start_date`: Data inicial (YYYY-MM-DD)
- `end_date`: Data final (YYYY-MM-DD)
- `limit`: Número de registros (default: 50)
- `offset`: Offset para paginação (default: 0)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "income",
      "amount": 5000.00,
      "description": "Salário",
      "category": "Trabalho",
      "occurred_at": "2024-01-15T10:30:00Z"
    }
  ],
  "pagination": {
    "total": 100,
    "limit": 50,
    "offset": 0,
    "pages": 2
  }
}
```

#### POST /api/cashflow
Cria nova transação de fluxo de caixa.

**Request:**
```json
{
  "type": "income",
  "amount": 5000.00,
  "description": "Salário",
  "category_id": 1,
  "occurred_at": "2024-01-15T10:30:00Z"
}
```

#### GET /api/trades
Lista trades realizados.

**Query Parameters:**
- `asset`: Nome do ativo
- `start_date`: Data inicial
- `end_date`: Data final
- `limit`: Número de registros

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "asset": "PETR4",
      "type": "buy",
      "quantity": 100,
      "price": 25.50,
      "pnl": 150.00,
      "executed_at": "2024-01-15T14:30:00Z"
    }
  ]
}
```

#### GET /api/holdings
Lista holdings do portfólio.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "asset": "PETR4",
      "quantity": 100,
      "average_price": 25.50,
      "current_price": 27.00,
      "current_value": 2700.00,
      "total_cost": 2550.00,
      "pnl": 150.00,
      "pnl_percent": 5.88
    }
  ]
}
```

### Sistema de AI

#### POST /api/ai/analyze
Executa análise financeira com AI.

**Request:**
```json
{
  "query": "Analise minha situação financeira e me dê recomendações"
}
```

**Response:**
```json
{
  "success": true,
  "query": "Analise minha situação financeira e me dê recomendações",
  "response": "Com base na análise dos seus dados financeiros...",
  "data": {
    "cashflow": {
      "total_income": 67961.16,
      "total_expenses": 55082.09,
      "balance": 12879.07
    },
    "trades": {
      "total_trades": 25,
      "total_pnl": 2500.00,
      "win_rate": 68.0
    },
    "holdings": {
      "total_holdings": 8,
      "total_value": 45000.00,
      "total_pnl_percent": 12.5
    }
  },
  "timestamp": "2024-01-15T15:30:00Z",
  "llm_provider": "openai",
  "llm_model": "gpt-4"
}
```

#### GET /api/ai/status
Verifica status do sistema de AI.

**Response:**
```json
{
  "success": true,
  "status": "active",
  "provider": "openai",
  "model": "gpt-4",
  "test_result": {
    "query": "Test connection",
    "response": "Sistema AI funcionando corretamente"
  }
}
```

#### POST /api/ai/chat
Interface de chat com AI.

**Request:**
```json
{
  "message": "Quais são minhas melhores categorias de receita?",
  "context": "financial_analysis"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Quais são minhas melhores categorias de receita?",
  "response": "Com base na análise dos seus dados...",
  "timestamp": "2024-01-15T15:30:00Z"
}
```

## 🧠 Sistema de AI - Arquitetura Técnica

### Estrutura de Diretórios

```
src/AI/
├── Agents/           # Agentes Neuron AI
│   └── FinanceAgent.php
├── Tools/            # Tools unificadas
│   ├── FinancialTool.php
│   ├── DatabaseTool.php
│   └── AnalysisTool.php
├── Adapters/         # Adaptadores para sistemas
│   ├── NeuronToolAdapter.php
│   └── MCPToolAdapter.php
├── Core/            # Interfaces e registros
│   ├── ToolInterface.php
│   └── ToolRegistry.php
└── Servers/         # Servidores MCP
    └── MCPServer.php
```

### Interface Unificada

```php
interface ToolInterface
{
    public function getName(): string;
    public function getDescription(): string;
    public function getParameters(): array;
    public function execute(array $parameters): array;
    public function getMetadata(): array;
}
```

### Tool Registry

```php
$registry = new ToolRegistry();
$registry->registerTool(new FinancialTool());

// Execução direta
$result = $registry->executeTool('financial_analysis', [
    'analysis_type' => 'cashflow',
    'period' => 'month'
]);

// Adapters para diferentes sistemas
$neuronAdapter = $registry->getNeuronAdapter('financial_analysis');
$mcpAdapter = $registry->getMCPAdapter('financial_analysis');
```

### Agente Financeiro

```php
class FinanceAgent extends Agent
{
    public function __construct()
    {
        $this->toolRegistry = new ToolRegistry();
        $this->toolRegistry->registerTool(new FinancialTool());
        
        // Adiciona tool ao Neuron AI
        $this->addTool($this->toolRegistry->getNeuronAdapter('financial_analysis'));
    }
    
    public function analyzeFinance(string $query): array
    {
        // Neuron AI decide quando chamar tools automaticamente
        $response = $this->chat([
            new UserMessage($query)
        ]);
        
        return [
            'query' => $query,
            'response' => $response->getContent(),
            'data' => $this->getFinancialData()
        ];
    }
}
```

## 🔒 Autenticação e Autorização

### JWT Token

```php
// Geração de token
$payload = [
    'user_id' => $user->id,
    'email' => $user->email,
    'exp' => time() + 3600
];
$token = JWT::encode($payload, $secret, 'HS256');
```

### Middleware de Autenticação

```php
$authMiddleware = function ($request, $handler) {
    $token = $request->getHeaderLine('Authorization');
    $token = str_replace('Bearer ', '', $token);
    
    try {
        $payload = JWT::decode($token, $secret, ['HS256']);
        $request = $request->withAttribute('user_id', $payload->user_id);
    } catch (Exception $e) {
        return new Response(401, ['error' => 'Token inválido']);
    }
    
    return $handler->handle($request);
};
```

## 📊 Banco de Dados

### Estrutura das Tabelas

```sql
-- Usuários
CREATE TABLE users (
    id INTEGER PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categorias
CREATE TABLE categories (
    id INTEGER PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Transações de Fluxo de Caixa
CREATE TABLE cashflow_transactions (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    category_id INTEGER,
    occurred_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Trades
CREATE TABLE trades (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    asset VARCHAR(50) NOT NULL,
    type ENUM('buy', 'sell') NOT NULL,
    quantity INTEGER NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    pnl DECIMAL(10,2) NOT NULL,
    executed_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Holdings
CREATE TABLE holdings (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    asset VARCHAR(50) NOT NULL,
    quantity INTEGER NOT NULL,
    average_price DECIMAL(10,2) NOT NULL,
    current_price DECIMAL(10,2) NOT NULL,
    current_value DECIMAL(10,2) NOT NULL,
    total_cost DECIMAL(10,2) NOT NULL,
    pnl DECIMAL(10,2) NOT NULL,
    pnl_percent DECIMAL(5,2) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Migrações

```php
// Executar migrações
php setup_database.php

// Ou via Eloquent
php artisan migrate
```

## 🧪 Testes

### Teste da API

```bash
# Teste básico
curl -X GET http://localhost:8000/api/ai/status

# Teste de análise
curl -X POST http://localhost:8000/api/ai/analyze \
  -H "Content-Type: application/json" \
  -d '{"query":"Analise minha situação financeira"}'
```

### Teste de Tools

```bash
# Executar testes de tools
php test_unified_tools.php

# Executar testes de AI
php test_ai.php
```

## 🚀 Deploy

### Desenvolvimento

```bash
# Iniciar servidor de desenvolvimento
php -S localhost:8000 -t public
```

### Produção

1. **Configurar servidor web (Nginx/Apache)**
2. **Configurar PHP-FPM**
3. **Configurar SSL/HTTPS**
4. **Configurar backup do banco**
5. **Configurar monitoramento**

### Docker (Opcional)

```dockerfile
FROM php:8.2-fpm
RUN docker-php-ext-install pdo pdo_sqlite
COPY . /var/www/html
WORKDIR /var/www/html
RUN composer install
EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
```

## 📈 Monitoramento

### Logs

```php
// Configurar logs
$logger = new Logger('api');
$logger->pushHandler(new StreamHandler('logs/api.log', Logger::INFO));
```

### Métricas

- **Response Time**: Tempo de resposta da API
- **Error Rate**: Taxa de erros
- **AI Usage**: Uso do sistema de AI
- **Database Performance**: Performance do banco

## 🔧 Troubleshooting

### Problemas Comuns

1. **Erro de Conexão com Banco**
   - Verificar se o arquivo SQLite existe
   - Verificar permissões de escrita

2. **Erro de AI**
   - Verificar chave API
   - Verificar conectividade com internet

3. **Erro de JWT**
   - Verificar JWT_SECRET
   - Verificar formato do token

### Logs de Debug

```bash
# Habilitar debug
export APP_DEBUG=true

# Ver logs
tail -f logs/api.log
```

---

**Documentação técnica da API CKO Framework v1.0**
