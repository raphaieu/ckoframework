# 🤖 Sistema Multi-Agêntico com NeuronAI

## 🎯 Visão Geral

Este projeto demonstra como implementar **sistemas multi-agênticos** usando a arquitetura NeuronAI em Laravel. O sistema atual é um **planejador de viagens** que usa múltiplos agentes especializados para criar itinerários completos.

## 🚀 Funcionalidades

### ✅ Sistema Atual (Planejamento de Viagens)
- **Receptionist**: Coleta informações do usuário
- **Delegator**: Coordena os agentes especializados
- **Flights**: Busca voos usando SerpAPI
- **Hotels**: Busca hotéis usando SerpAPI
- **Places**: Busca pontos turísticos usando SerpAPI
- **GenerateItinerary**: Gera roteiro completo em Markdown

### 🛠️ Ferramentas de Desenvolvimento
- **Comando Artisan**: `php artisan make:multi-agent-simple`
- **Templates Pré-configurados**: 6 templates diferentes
- **Scripts Automatizados**: Geração de código automática
- **Documentação Completa**: Guias e exemplos práticos

## 📚 Documentação

| Arquivo | Descrição |
|---------|-----------|
| `docs/README_MULTI_AGENT.md` | Visão geral do sistema |
| `docs/MULTI_AGENT_SYSTEM_GUIDE.md` | Guia completo de implementação |
| `docs/TEMPLATE_MULTI_AGENT.md` | Template para novos sistemas |
| `docs/EXAMPLES.md` | Exemplos práticos |
| `docs/QUICK_START.md` | Guia de início rápido |

## 🎯 Templates Disponíveis

| Template | Descrição | Agentes |
|----------|-----------|---------|
| `analise_sentimentos` | Análise de sentimentos em textos | Coletor, Analisador, Relatorio |
| `recomendacao_produtos` | Recomendação de produtos | Perfil, Preferencias, Recomendador |
| `monitoramento_precos` | Monitoramento de preços | Coletor, Comparador, Notificador |
| `analise_redes_sociais` | Análise de redes sociais | Coletor, Filtrador, Analisador, Relatorio |
| `geracao_conteudo` | Geração de conteúdo | Pesquisador, Escritor, Editor, Publicador |
| `planejamento_viagem` | Planejamento de viagens | Receptionist, Delegator, Flights, Hotels, Places, GenerateItinerary |

## 🚀 Como Usar

### 1. Usar o Sistema Atual

```bash
# Instalar dependências
composer install

# Configurar variáveis de ambiente
cp .env.example .env
# Adicionar: SERPAPI_KEY=sua_chave_aqui

# Executar
php artisan serve
# Acesse: http://localhost:8000/dashboard
```

### 2. Criar Novos Sistemas

```bash
# Listar templates
php artisan make:multi-agent-simple --help

# Criar com template
php artisan make:multi-agent-simple "MeuSistema" --template=analise_sentimentos

# Criar personalizado
php artisan make:multi-agent-simple "MeuSistema" --agents=Agente1,Agente2,Agente3
```

### 3. Usar Script PHP

```bash
# Criar sistema usando script
php scripts/create_multi_agent_system.php "MeuSistema" "Agente1" "Agente2" "Agente3"
```

## 🏗️ Arquitetura

### Componentes Principais
- **Workflow**: Coordenador principal que define a sequência de agentes
- **Nodes**: Agentes especializados que executam tarefas específicas
- **Events**: Mensagens que os agentes trocam entre si
- **State**: Estado compartilhado onde os dados são armazenados
- **Tools**: Ferramentas externas que os agentes podem usar

### Fluxo de Execução
1. **Receptionist** coleta informações do usuário
2. **Delegator** decide qual agente especializado chamar
3. **Agentes especializados** executam suas tarefas específicas
4. **GenerateResult** gera o resultado final

## 🔧 Configuração

### Variáveis de Ambiente

```bash
# APIs Externas
SERPAPI_KEY=sua_chave_serpapi
OPENAI_API_KEY=sua_chave_openai

# Configurações do Sistema
APP_ENV=local
APP_DEBUG=true
```

### Estrutura de Arquivos

```
app/Neuron/
├── TravelPlannerAgent.php          # Sistema atual
├── Prompts.php                     # Templates de prompts
├── Agents/                         # Estruturas de dados
├── Nodes/                          # Agentes especializados
├── Events/                         # Eventos do sistema
└── Tools/                          # Ferramentas externas
    └── SerpAPI/
        ├── SerpAPIFlight.php
        ├── SerpAPIHotel.php
        └── SerpAPIPlace.php
```

## 📊 Exemplos de Uso

### Sistema de Análise de Sentimentos
```bash
php artisan make:multi-agent-simple "AnaliseSentimentos" --template=analise_sentimentos
```

### Sistema de Recomendação de Produtos
```bash
php artisan make:multi-agent-simple "RecomendacaoProdutos" --template=recomendacao_produtos
```

### Sistema Personalizado
```bash
php artisan make:multi-agent-simple "MeuSistema" --agents=Coletor,Processador,Gerador
```

## 🐛 Troubleshooting

### Problemas Comuns

1. **Erro de Chave de API**
   ```bash
   echo $SERPAPI_KEY
   ```

2. **Erro de Workflow Interrupt**
   ```php
   $query = $this->consumeInterruptFeedback();
   ```

3. **Erro de Estado Compartilhado**
   ```php
   $state->set('chave', $valor);
   ```

### Debugging

```php
// Adicionar logs para debugging
\Log::info('Estado atual', $state->all());
\Log::info('Evento recebido', $event);
```

## 🚀 Próximos Passos

1. **Explore o sistema atual** de planejamento de viagens
2. **Crie seu primeiro sistema** usando os templates
3. **Personalize os agentes** para seu domínio específico
4. **Implemente ferramentas externas** conforme necessário
5. **Teste com dados reais** e ajuste conforme necessário

## 📞 Suporte

- **Documentação**: Consulte os arquivos em `docs/`
- **Exemplos**: Veja o sistema de planejamento de viagens
- **Templates**: Use os templates pré-configurados
- **Comandos**: Use `php artisan make:multi-agent-simple --help`

## 📄 Licença

Este projeto está licenciado sob a licença MIT.

---

**💡 Dica**: Comece com sistemas simples e vá adicionando complexidade gradualmente. A arquitetura multi-agêntica é poderosa, mas requer planejamento cuidadoso para ser eficaz!

**🎯 Objetivo**: Este projeto fornece tudo que você precisa para criar sistemas multi-agênticos poderosos e escaláveis usando NeuronAI e Laravel.
