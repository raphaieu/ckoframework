# CKO Framework AI System - Documentação Técnica

## 🧠 Visão Geral do Sistema de AI

O sistema de AI do CKO Framework utiliza uma arquitetura unificada que combina **Neuron AI** (para agentes internos) e **MCP** (Model Context Protocol para comunicação externa), permitindo que as mesmas tools sejam utilizadas em diferentes contextos.

## 🏗️ Arquitetura Unificada

```
┌─────────────────┐    ┌─────────────────┐
│   Neuron AI     │    │      MCP        │
│   (Interno)     │    │   (Externo)     │
└─────────┬───────┘    └─────────┬───────┘
          │                      │
          └──────────┬───────────┘
                     │
            ┌────────▼────────┐
            │  Shared Tools   │
            │  (Core Layer)   │
            └─────────────────┘
```

### Componentes Principais

1. **ToolInterface**: Interface unificada para todas as tools
2. **ToolRegistry**: Gerenciamento centralizado de tools
3. **Adapters**: Conexão com diferentes sistemas AI
4. **Agents**: Agentes Neuron AI especializados
5. **Servers**: Servidores MCP para acesso externo

## 🛠️ Estrutura de Diretórios

```
src/AI/
├── Core/                    # Interfaces e registros
│   ├── ToolInterface.php    # Interface unificada
│   └── ToolRegistry.php     # Registry de tools
├── Tools/                   # Tools implementadas
│   ├── FinancialTool.php    # Análise financeira
│   ├── DatabaseTool.php     # Acesso a dados
│   └── AnalysisTool.php     # Cálculos e métricas
├── Adapters/                # Adaptadores para sistemas
│   ├── NeuronToolAdapter.php # Adapter para Neuron AI
│   └── MCPToolAdapter.php   # Adapter para MCP
├── Agents/                  # Agentes Neuron AI
│   └── FinanceAgent.php     # Agente financeiro
└── Servers/                 # Servidores MCP
    └── MCPServer.php        # Servidor MCP
```

## 🔧 Interface Unificada

### ToolInterface

```php
interface ToolInterface
{
    /**
     * Nome da tool
     */
    public function getName(): string;

    /**
     * Descrição da tool
     */
    public function getDescription(): string;

    /**
     * Parâmetros aceitos pela tool
     */
    public function getParameters(): array;

    /**
     * Executa a tool com parâmetros
     */
    public function execute(array $parameters): array;

    /**
     * Metadados da tool
     */
    public function getMetadata(): array;
}
```

### Exemplo de Implementação

```php
class FinancialTool implements ToolInterface
{
    public function getName(): string
    {
        return 'financial_analysis';
    }

    public function getDescription(): string
    {
        return 'Provides comprehensive financial analysis including cashflow, trades, and portfolio data with insights and recommendations.';
    }

    public function getParameters(): array
    {
        return [
            'analysis_type' => [
                'type' => 'string',
                'description' => 'Type of financial analysis to perform',
                'required' => true,
                'enum' => ['cashflow', 'trades', 'portfolio', 'comprehensive'],
                'default' => 'comprehensive'
            ],
            'period' => [
                'type' => 'string',
                'description' => 'Time period for analysis',
                'required' => false,
                'enum' => ['week', 'month', 'quarter', 'year', 'all'],
                'default' => 'month'
            ],
            'filters' => [
                'type' => 'object',
                'description' => 'Optional filters for the analysis',
                'required' => false,
                'default' => []
            ]
        ];
    }

    public function execute(array $parameters): array
    {
        $analysisType = $parameters['analysis_type'] ?? 'comprehensive';
        $period = $parameters['period'] ?? 'month';
        $filters = $parameters['filters'] ?? [];

        return $this->analyze($analysisType, $period, $filters);
    }

    public function getMetadata(): array
    {
        return [
            'version' => '1.0.0',
            'author' => 'CKO Framework',
            'category' => 'financial',
            'tags' => ['finance', 'analysis', 'cashflow', 'trades', 'portfolio']
        ];
    }
}
```

## 🔄 Tool Registry

### Gerenciamento Centralizado

```php
class ToolRegistry
{
    private array $tools = [];
    private array $neuronAdapters = [];
    private array $mcpAdapters = [];

    public function registerTool(ToolInterface $tool): self
    {
        $this->tools[$tool->getName()] = $tool;
        
        // Criar adapters automaticamente
        $this->neuronAdapters[$tool->getName()] = new NeuronToolAdapter($tool);
        $this->mcpAdapters[$tool->getName()] = new MCPToolAdapter($tool, $this->mcpClient);

        return $this;
    }

    public function executeTool(string $name, array $parameters): array
    {
        $tool = $this->getTool($name);
        if (!$tool) {
            return ['success' => false, 'error' => "Tool '{$name}' not found"];
        }

        return $tool->execute($parameters);
    }
}
```

### Uso do Registry

```php
// Registrar tools
$registry = new ToolRegistry();
$registry->registerTool(new FinancialTool());
$registry->registerTool(new DatabaseTool());
$registry->registerTool(new AnalysisTool());

// Executar tool diretamente
$result = $registry->executeTool('financial_analysis', [
    'analysis_type' => 'cashflow',
    'period' => 'month'
]);

// Obter adapters para diferentes sistemas
$neuronAdapter = $registry->getNeuronAdapter('financial_analysis');
$mcpAdapter = $registry->getMCPAdapter('financial_analysis');
```

## 🤖 Agentes Neuron AI

### FinanceAgent

```php
class FinanceAgent extends Agent
{
    private ToolRegistry $toolRegistry;

    public function __construct()
    {
        // Inicializar registry
        $this->toolRegistry = new ToolRegistry();
        
        // Registrar tools
        $this->toolRegistry->registerTool(new FinancialTool());
        
        // Adicionar tools ao Neuron AI
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

### Casos de Uso

**Single Agent:**
```php
$agent = new FinanceAgent();
$response = $agent->analyzeFinance("trazer meu saldo");
```

**Multi-Agent:**
```php
$financeAgent = new FinanceAgent();
$tradingAgent = new TradingAgent();
// Ambos usam as mesmas tools através do registry
```

## 🌐 Sistema MCP

### MCP Server

```php
class MCPServer
{
    private ToolRegistry $toolRegistry;

    public function __construct()
    {
        $this->toolRegistry = new ToolRegistry();
        $this->registerTools();
    }

    private function registerTools(): void
    {
        $this->toolRegistry->registerTool(new FinancialTool());
        $this->toolRegistry->registerTool(new DatabaseTool());
    }

    public function executeTool(string $toolName, array $parameters): array
    {
        return $this->toolRegistry->executeTool($toolName, $parameters);
    }
}
```

### Casos de Uso MCP

**Aplicação Externa:**
```php
$mcpServer = new MCPServer();
$result = $mcpServer->executeTool('financial_analysis', [
    'analysis_type' => 'portfolio',
    'period' => 'quarter'
]);
```

**Comunicação entre Serviços:**
```php
// Serviço A chama Serviço B via MCP
$mcpClient = new MCPClient('servico-b', 8080);
$result = $mcpClient->callTool('financial_analysis', $parameters);
```

## 🔧 Adapters

### NeuronToolAdapter

```php
class NeuronToolAdapter implements NeuronToolInterface
{
    private ToolInterface $tool;

    public function __construct(ToolInterface $tool)
    {
        $this->tool = $tool;
    }

    public function call(ToolCall $toolCall): ToolResult
    {
        $parameters = $toolCall->getParameters();
        $result = $this->tool->execute($parameters);

        return new ToolResult(
            content: $result['summary'] ?? $result['response'] ?? 'Analysis completed',
            data: $result['data'] ?? [],
            metadata: array_merge(
                $result['metadata'] ?? [],
                ['tool_name' => $this->tool->getName()]
            )
        );
    }
}
```

### MCPToolAdapter

```php
class MCPToolAdapter
{
    private ToolInterface $tool;
    private Client $mcpClient;

    public function execute(array $parameters): array
    {
        // Executar tool localmente
        $result = $this->tool->execute($parameters);

        // Enviar resultado para MCP server se necessário
        $mcpResult = $this->mcpClient->sendToolResult([
            'tool_name' => $this->tool->getName(),
            'parameters' => $parameters,
            'result' => $result
        ]);

        return array_merge($result, [
            'mcp_response' => $mcpResult,
            'execution_method' => 'mcp_enhanced'
        ]);
    }
}
```

## 📊 Tools Disponíveis

### FinancialTool

**Funcionalidades:**
- Análise de fluxo de caixa
- Análise de trades
- Análise de portfólio
- Análise abrangente

**Parâmetros:**
- `analysis_type`: Tipo de análise
- `period`: Período de análise
- `filters`: Filtros opcionais

**Exemplo:**
```php
$tool = new FinancialTool();
$result = $tool->execute([
    'analysis_type' => 'cashflow',
    'period' => 'month',
    'filters' => ['category_id' => 1]
]);
```

### DatabaseTool

**Funcionalidades:**
- Acesso a dados de fluxo de caixa
- Acesso a dados de trades
- Acesso a dados de holdings
- Cálculos de métricas

**Exemplo:**
```php
$tool = new DatabaseTool();
$cashflowData = $tool->getCashflowData();
$tradesData = $tool->getTradesData();
$holdingsData = $tool->getHoldingsData();
```

### AnalysisTool

**Funcionalidades:**
- Cálculos de performance
- Análise de tendências
- Métricas de risco
- Recomendações

**Exemplo:**
```php
$tool = new AnalysisTool();
$analysis = $tool->analyzePerformance($data);
$recommendations = $tool->generateRecommendations($data);
```

## 🧪 Testes

### Teste de Tools

```bash
# Executar testes de tools
php test_unified_tools.php
```

**Saída esperada:**
```
🧪 Testing Unified Tools Architecture

1️⃣ Testing Direct Tool Usage:
✅ Direct execution: SUCCESS
📊 Summary: Análise de Fluxo de Caixa - Período: month

2️⃣ Testing Tool Registry:
✅ Tools registered: 1
✅ Tool names: financial_analysis

3️⃣ Testing Neuron AI Adapter:
✅ Adapter name: financial_analysis
✅ Adapter description: Provides comprehensive financial analysis...

4️⃣ Testing MCP Server:
✅ MCP Server initialized
✅ Available tools: 1

5️⃣ Testing Tool Execution via Registry:
✅ Registry execution: SUCCESS
📊 Data keys: total_trades, total_pnl, winning_trades...

🎉 All tests completed!
```

### Teste de AI

```bash
# Executar testes de AI
php test_ai.php
```

## 🔧 Configuração

### Variáveis de Ambiente

```env
# AI Configuration
AI_PROVIDER=openai
AI_API_KEY=sua_chave_api
AI_MODEL=gpt-4

# MCP Configuration
MCP_HOST=localhost
MCP_PORT=8080
MCP_PATH=/mcp
```

### Dependências

```json
{
  "require": {
    "inspector-apm/neuron-ai": "dev-main",
    "mcp/sdk": "dev-main"
  }
}
```

## 🚀 Casos de Uso Avançados

### 1. Multi-Agent Workflow

```php
// Agente de análise
$analysisAgent = new FinanceAgent();

// Agente de trading
$tradingAgent = new TradingAgent();

// Workflow coordenado
$analysis = $analysisAgent->analyzeFinance("Analise meu portfólio");
$trading = $tradingAgent->analyzeTrading("Sugira trades baseados na análise");
```

### 2. MCP para Microserviços

```php
// Serviço de análise
$analysisService = new MCPServer();

// Serviço de notificações
$notificationService = new MCPServer();

// Comunicação entre serviços
$analysis = $analysisService->executeTool('financial_analysis', $params);
$notificationService->executeTool('send_notification', [
    'message' => $analysis['summary']
]);
```

### 3. Tool Calling Automático

```php
// Neuron AI decide automaticamente quando chamar tools
$agent = new FinanceAgent();
$response = $agent->chat([
    new UserMessage("trazer meu saldo")
]);
// AI automaticamente chama FinancialTool com parâmetros apropriados
```

## 📈 Monitoramento

### Logs de AI

```php
// Configurar logs específicos para AI
$aiLogger = new Logger('ai');
$aiLogger->pushHandler(new StreamHandler('logs/ai.log', Logger::INFO));

// Log de execução de tools
$aiLogger->info('Tool executed', [
    'tool_name' => 'financial_analysis',
    'parameters' => $parameters,
    'execution_time' => $executionTime
]);
```

### Métricas

- **Tool Usage**: Uso de cada tool
- **Execution Time**: Tempo de execução
- **Success Rate**: Taxa de sucesso
- **Error Rate**: Taxa de erros

## 🔒 Segurança

### Validação de Parâmetros

```php
public function execute(array $parameters): array
{
    // Validar parâmetros obrigatórios
    if (!isset($parameters['analysis_type'])) {
        throw new InvalidArgumentException('analysis_type is required');
    }

    // Validar enum values
    $validTypes = ['cashflow', 'trades', 'portfolio', 'comprehensive'];
    if (!in_array($parameters['analysis_type'], $validTypes)) {
        throw new InvalidArgumentException('Invalid analysis_type');
    }

    // Executar tool
    return $this->analyze($parameters);
}
```

### Sanitização de Dados

```php
// Sanitizar inputs
$analysisType = filter_var($parameters['analysis_type'], FILTER_SANITIZE_STRING);
$period = filter_var($parameters['period'], FILTER_SANITIZE_STRING);
```

## 🎯 Roadmap

### Próximas Funcionalidades

- [ ] **Tool Caching**: Cache de resultados de tools
- [ ] **Tool Chaining**: Encadeamento de tools
- [ ] **Tool Versioning**: Versionamento de tools
- [ ] **Tool Metrics**: Métricas detalhadas
- [ ] **Tool Testing**: Testes automatizados
- [ ] **Tool Documentation**: Documentação automática

### Melhorias Planejadas

- [ ] **Performance**: Otimização de execução
- [ ] **Scalability**: Suporte a mais tools
- [ ] **Reliability**: Melhor tratamento de erros
- [ ] **Monitoring**: Monitoramento avançado

---

**Documentação do Sistema de AI CKO Framework v1.0**
