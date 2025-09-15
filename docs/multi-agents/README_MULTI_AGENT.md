# 🤖 Sistema Multi-Agêntico com NeuronAI

## 🎯 Visão Geral

Este projeto demonstra como implementar sistemas multi-agênticos usando a arquitetura NeuronAI em Laravel. O sistema atual é um **planejador de viagens** que usa múltiplos agentes especializados para criar itinerários completos.

## 📁 Estrutura do Projeto

```
app/Neuron/
├── TravelPlannerAgent.php          # Workflow principal
├── Prompts.php                     # Templates de prompts
├── Agents/                         # Estruturas de dados
│   ├── ExtractedInfo.php
│   ├── ResearchAgent.php
│   └── TourInfo.php
├── Nodes/                          # Agentes especializados
│   ├── Receptionist.php           # Coleta informações
│   ├── Delegator.php              # Coordena agentes
│   ├── Flights.php                # Busca voos
│   ├── Hotels.php                 # Busca hotéis
│   ├── Places.php                 # Busca pontos turísticos
│   └── GenerateItinerary.php      # Gera roteiro final
├── Events/                         # Eventos do sistema
│   ├── ProgressEvent.php
│   ├── Retrieve.php
│   ├── RetrieveFlights.php
│   ├── RetrieveHotels.php
│   ├── RetrievePlaces.php
│   └── CreateItinerary.php
└── Tools/                          # Ferramentas externas
    └── SerpAPI/
        ├── SerpAPIFlight.php
        ├── SerpAPIHotel.php
        ├── SerpAPIPlace.php
        └── SerpAPIToolkit.php
```

## 🚀 Como Usar

### 1. Configuração Inicial

```bash
# Instalar dependências
composer install

# Configurar variáveis de ambiente
cp .env.example .env

# Adicionar chaves de API no .env
SERPAPI_KEY=sua_chave_aqui
```

### 2. Executar o Sistema

```bash
# Iniciar servidor
php artisan serve

# Acessar no navegador
http://localhost:8000/dashboard
```

### 3. Testar o Sistema

1. Faça login na aplicação
2. Acesse o dashboard
3. Digite uma solicitação de viagem (ex: "Quero viajar para Paris em dezembro")
4. O sistema irá:
   - Coletar informações necessárias
   - Buscar voos, hotéis e pontos turísticos
   - Gerar um roteiro completo

## 🛠️ Criando Novos Sistemas Multi-Agênticos

### Método 1: Script Automatizado

```bash
# Criar um novo sistema
php scripts/create_multi_agent_system.php "NomeDoSistema" "Agente1" "Agente2" "Agente3"

# Exemplo: Sistema de análise de sentimentos
php scripts/create_multi_agent_system.php "AnaliseSentimentos" "Coletor" "Analisador" "Relatorio"
```

### Método 2: Manual

1. Siga o guia em `docs/MULTI_AGENT_SYSTEM_GUIDE.md`
2. Use o template em `docs/TEMPLATE_MULTI_AGENT.md`
3. Implemente os agentes específicos do seu domínio

## 📚 Documentação

- **[Guia Completo](docs/MULTI_AGENT_SYSTEM_GUIDE.md)** - Documentação detalhada
- **[Template](docs/TEMPLATE_MULTI_AGENT.md)** - Template para novos sistemas
- **[Exemplos](docs/EXAMPLES.md)** - Exemplos práticos (em breve)

## 🔧 Configuração Avançada

### Variáveis de Ambiente

```bash
# APIs Externas
SERPAPI_KEY=sua_chave_serpapi

# Configurações do Sistema
APP_ENV=local
APP_DEBUG=true

# NeuronAI
NEURON_AI_API_KEY=sua_chave_neuron
```

### Personalização de Agentes

```php
// Exemplo: Personalizar agente de voos
class Flights extends Node
{
    public function __invoke(RetrieveFlights $event, WorkflowState $state): Retrieve
    {
        // Sua lógica personalizada aqui
        $response = ResearchAgent::make()
            ->addTool(SerpAPIFlight::make($_ENV['SERPAPI_KEY']))
            ->chat(new UserMessage("Sua mensagem personalizada"));
        
        $state->set('flights', $response->getContent());
        return new Retrieve($event->tour);
    }
}
```

## 🎯 Exemplos de Uso

### 1. Sistema de Análise de Sentimentos
```php
// Agentes: Coletor, Analisador, Relatório
// Fluxo: Texto → Análise → Relatório de Sentimentos
```

### 2. Sistema de Recomendação de Produtos
```php
// Agentes: Perfil, Preferências, Recomendador
// Fluxo: Preferências → Análise → Recomendações
```

### 3. Sistema de Monitoramento de Preços
```php
// Agentes: Coletor, Comparador, Notificador
// Fluxo: Produto → Preços → Comparação → Alerta
```

## 🐛 Troubleshooting

### Problemas Comuns

#### 1. Erro de Chave de API
```bash
# Verificar se as variáveis estão configuradas
echo $SERPAPI_KEY
```

#### 2. Erro de Workflow Interrupt
```php
// Verificar se o feedback está sendo consumido
$query = $this->consumeInterruptFeedback();
```

#### 3. Erro de Estado Compartilhado
```php
// Verificar se o estado está sendo definido
$state->set('chave', $valor);
```

### Debugging

```php
// Adicionar logs para debugging
\Log::info('Estado atual', $state->all());
\Log::info('Evento recebido', $event);
```

## 📈 Performance

### Otimizações Recomendadas

1. **Cache de Respostas**: Implemente cache para respostas frequentes
2. **Streaming**: Use streaming para respostas longas
3. **Rate Limiting**: Implemente limitação de taxa para APIs externas
4. **Queue Jobs**: Use filas para processamento assíncrono

### Monitoramento

```php
// Adicionar métricas de performance
$startTime = microtime(true);
// ... processamento ...
$executionTime = microtime(true) - $startTime;
\Log::info("Tempo de execução: {$executionTime}s");
```

## 🔒 Segurança

### Boas Práticas

1. **Validação de Entrada**: Sempre valide dados de entrada
2. **Sanitização**: Sanitize dados antes de processar
3. **Rate Limiting**: Implemente limitação de taxa
4. **Logs de Segurança**: Monitore tentativas de acesso

### Configuração de Segurança

```php
// Exemplo: Validação de entrada
public function __invoke(StartEvent $event, WorkflowState $state): Retrieve
{
    $query = $this->consumeInterruptFeedback();
    
    // Validar entrada
    if (empty($query) || strlen($query) > 1000) {
        throw new \InvalidArgumentException('Query inválida');
    }
    
    // ... resto da lógica
}
```

## 🚀 Próximos Passos

1. **Implemente seu primeiro sistema** usando o script automatizado
2. **Teste com dados reais** para validar o funcionamento
3. **Adicione novos agentes** conforme necessário
4. **Otimize performance** baseado no uso real
5. **Documente comportamentos** específicos do seu domínio

## 📞 Suporte

- **Documentação**: Consulte os arquivos em `docs/`
- **Exemplos**: Veja o sistema de planejamento de viagens
- **Issues**: Reporte problemas no repositório
- **Discussões**: Participe das discussões da comunidade

## 📄 Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

---

**💡 Dica**: Comece com sistemas simples e vá adicionando complexidade gradualmente. A arquitetura multi-agêntica é poderosa, mas requer planejamento cuidadoso para ser eficaz.
