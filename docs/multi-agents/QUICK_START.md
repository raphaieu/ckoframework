# 🚀 Quick Start: Sistemas Multi-Agênticos

## 📋 Resumo

Este projeto fornece uma **documentação completa** e **ferramentas automatizadas** para criar sistemas multi-agênticos usando NeuronAI em Laravel.

## 🎯 O que você pode fazer

### 1. **Usar o Sistema Existente**
- Sistema de planejamento de viagens já implementado
- Múltiplos agentes especializados (voos, hotéis, lugares)
- Interface web interativa com Livewire

### 2. **Criar Novos Sistemas**
- Use templates pré-configurados
- Crie sistemas personalizados
- Automatize a geração de código

## 🛠️ Como Usar

### Método 1: Comando Artisan (Recomendado)

```bash
# Listar templates disponíveis
php artisan make:multi-agent-simple --help

# Criar sistema com template
php artisan make:multi-agent-simple "MeuSistema" --template=analise_sentimentos

# Criar sistema personalizado
php artisan make:multi-agent-simple "MeuSistema" --agents=Agente1,Agente2,Agente3
```

### Método 2: Script PHP

```bash
# Criar sistema usando script
php scripts/create_multi_agent_system.php "MeuSistema" "Agente1" "Agente2" "Agente3"
```

### Método 3: Manual

1. Siga o guia em `docs/MULTI_AGENT_SYSTEM_GUIDE.md`
2. Use o template em `docs/TEMPLATE_MULTI_AGENT.md`

## 📚 Documentação Disponível

| Arquivo | Descrição |
|---------|-----------|
| `docs/README_MULTI_AGENT.md` | Visão geral do sistema |
| `docs/MULTI_AGENT_SYSTEM_GUIDE.md` | Guia completo de implementação |
| `docs/TEMPLATE_MULTI_AGENT.md` | Template para novos sistemas |
| `docs/EXAMPLES.md` | Exemplos práticos |
| `docs/QUICK_START.md` | Este arquivo |

## 🎯 Templates Disponíveis

| Template | Descrição | Agentes |
|----------|-----------|---------|
| `analise_sentimentos` | Análise de sentimentos em textos | Coletor, Analisador, Relatorio |
| `recomendacao_produtos` | Recomendação de produtos | Perfil, Preferencias, Recomendador |
| `monitoramento_precos` | Monitoramento de preços | Coletor, Comparador, Notificador |
| `analise_redes_sociais` | Análise de redes sociais | Coletor, Filtrador, Analisador, Relatorio |
| `geracao_conteudo` | Geração de conteúdo | Pesquisador, Escritor, Editor, Publicador |
| `planejamento_viagem` | Planejamento de viagens | Receptionist, Delegator, Flights, Hotels, Places, GenerateItinerary |

## 🚀 Exemplo Rápido

### 1. Criar Sistema de Análise de Sentimentos

```bash
# Criar o sistema
php artisan make:multi-agent-simple "AnaliseSentimentos" --template=analise_sentimentos

# Adicionar rota (opcional)
echo "require_once 'analise-sentimentos.php';" >> routes/web.php

# Testar
php artisan serve
# Acesse: http://localhost:8000/analise-sentimentos
```

### 2. Personalizar o Sistema

```php
// Editar prompts em app/Neuron/AnaliseSentimentos/Prompts.php
const COLETA_INFORMACOES = <<<EOT
Analise o seguinte texto e extraia informações sobre sentimento:

Texto: {query}

Informações necessárias:
- Sentimento: positivo, negativo ou neutro
- Confiança: 0.0 a 1.0
- Palavras-chave: lista de palavras importantes

Se alguma informação estiver faltando, peça ao usuário.
EOT;
```

### 3. Implementar Ferramentas Externas

```php
// Criar ferramenta em app/Neuron/AnaliseSentimentos/Tools/
class SentimentAnalysisTool extends Tool
{
    public function __invoke(string $text): array
    {
        // Implementar análise de sentimentos
        return [
            'sentiment' => 'positive',
            'confidence' => 0.85,
            'keywords' => ['feliz', 'ótimo', 'excelente']
        ];
    }
}
```

## 🔧 Configuração

### Variáveis de Ambiente

```bash
# APIs Externas (opcional)
SERPAPI_KEY=sua_chave_serpapi
OPENAI_API_KEY=sua_chave_openai

# Configurações do Sistema
APP_ENV=local
APP_DEBUG=true
```

### Estrutura de Arquivos

```
app/Neuron/[SeuSistema]/
├── [SeuSistema]Agent.php          # Workflow principal
├── Prompts.php                     # Templates de prompts
├── Agents/                         # Estruturas de dados
│   └── ExtractedInfo.php
├── Nodes/                          # Agentes especializados
│   ├── Receptionist.php
│   ├── Delegator.php
│   └── [SeusAgentes].php
├── Events/                         # Eventos do sistema
│   ├── ProgressEvent.php
│   ├── Retrieve.php
│   └── [SeusEventos].php
└── Tools/                          # Ferramentas externas
    └── [SuasFerramentas].php
```

## 📊 Monitoramento e Debug

### Logs

```php
// Adicionar logs para debugging
\Log::info('Agente iniciado', [
    'agente' => get_class($this),
    'dados_entrada' => $event
]);
```

### Métricas

```php
// Medir performance
$startTime = microtime(true);
// ... processamento ...
$executionTime = microtime(true) - $startTime;
\Log::info("Tempo de execução: {$executionTime}s");
```

## 🐛 Troubleshooting

### Problemas Comuns

1. **Erro de Chave de API**
   ```bash
   # Verificar variáveis de ambiente
   echo $SERPAPI_KEY
   ```

2. **Erro de Workflow Interrupt**
   ```php
   // Verificar se o feedback está sendo consumido
   $query = $this->consumeInterruptFeedback();
   ```

3. **Erro de Estado Compartilhado**
   ```php
   // Verificar se o estado está sendo definido
   $state->set('chave', $valor);
   ```

### Debugging

```php
// Adicionar logs detalhados
\Log::info('Estado atual', $state->all());
\Log::info('Evento recebido', $event);
```

## 🚀 Próximos Passos

1. **Escolha um template** que se alinha com suas necessidades
2. **Crie seu sistema** usando o comando Artisan
3. **Personalize os agentes** para seu domínio específico
4. **Implemente ferramentas externas** conforme necessário
5. **Teste com dados reais** e ajuste conforme necessário
6. **Monitore performance** e otimize continuamente

## 📞 Suporte

- **Documentação**: Consulte os arquivos em `docs/`
- **Exemplos**: Veja o sistema de planejamento de viagens
- **Templates**: Use os templates pré-configurados
- **Comandos**: Use `php artisan make:multi-agent-simple --help`

## 📄 Licença

Este projeto está licenciado sob a licença MIT.

---

**💡 Dica**: Comece com sistemas simples e vá adicionando complexidade gradualmente. A arquitetura multi-agêntica é poderosa, mas requer planejamento cuidadoso para ser eficaz!

**🎯 Objetivo**: Este guia fornece tudo que você precisa para criar sistemas multi-agênticos poderosos e escaláveis usando NeuronAI e Laravel.
