# CKO Framework AI System

## 🧠 Visão Geral

Sistema de inteligência artificial integrado ao CKO Framework, utilizando **Neuron AI** e **MCP (Model Context Protocol)** para análise financeira inteligente com arquitetura unificada.

## ✨ Funcionalidades

- **Análise Financeira**: Fluxo de caixa, trades e portfólio
- **Chat Inteligente**: Interface conversacional com AI
- **Tool Calling**: Execução automática de ferramentas
- **Arquitetura Unificada**: Tools compartilhadas entre sistemas
- **Multi-Provider**: OpenAI, Anthropic, Gemini

## 🏗️ Arquitetura

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

## 📁 Estrutura

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

## 🚀 Uso Rápido

### Single Agent (Neuron AI)
```php
$agent = new FinanceAgent();
$response = $agent->analyzeFinance("trazer meu saldo");
```

### Multi-Agent
```php
$financeAgent = new FinanceAgent();
$tradingAgent = new TradingAgent();
// Ambos usam as mesmas tools
```

### MCP (Aplicações Externas)
```php
$mcpServer = new MCPServer();
$result = $mcpServer->executeTool('financial_analysis', [
    'analysis_type' => 'cashflow',
    'period' => 'month'
]);
```

### Acesso Direto
```php
$tool = new FinancialTool();
$result = $tool->execute(['analysis_type' => 'portfolio']);
```

## 🔧 Configuração

### Variáveis de Ambiente
```env
AI_PROVIDER=openai
AI_API_KEY=sua_chave_api
AI_MODEL=gpt-4
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

## 🧪 Testes

```bash
# Teste de tools
php test_unified_tools.php

# Teste de AI
php test_ai.php
```

## 📚 Documentação

- [AI System Documentation](AI_SYSTEM_DOCUMENTATION.md)
- [API Documentation](../API_DOCUMENTATION.md)
- [Development Guide](../../DEVELOPMENT_GUIDE.md)

## 🎯 Casos de Uso

1. **Análise Automática**: AI decide quando chamar tools
2. **Chat Financeiro**: Interface conversacional
3. **Microserviços**: Comunicação via MCP
4. **Multi-Agent**: Workflows coordenados
5. **Tools Compartilhadas**: Mesma lógica, múltiplos usos

## 🔒 Segurança

- Validação de parâmetros
- Sanitização de inputs
- Logs de auditoria
- Controle de acesso

## 📈 Performance

- Cache de resultados
- Execução assíncrona
- Pool de conexões
- Monitoramento de métricas

---

**Sistema de AI CKO Framework v1.0**