# 🎯 Exemplos Práticos de Sistemas Multi-Agênticos

## 📋 Índice
1. [Sistema de Análise de Sentimentos](#sistema-de-análise-de-sentimentos)
2. [Sistema de Recomendação de Produtos](#sistema-de-recomendação-de-produtos)
3. [Sistema de Monitoramento de Preços](#sistema-de-monitoramento-de-preços)
4. [Sistema de Análise de Redes Sociais](#sistema-de-análise-de-redes-sociais)
5. [Sistema de Geração de Conteúdo](#sistema-de-geração-de-conteúdo)

## 🎭 Sistema de Análise de Sentimentos

### Descrição
Sistema que analisa sentimentos em textos, reviews, comentários e gera relatórios detalhados.

### Agentes
- **Coletor**: Coleta textos de diferentes fontes
- **Analisador**: Analisa sentimentos usando IA
- **Relatorio**: Gera relatórios visuais e estatísticas

### Implementação

```bash
# Criar o sistema
php scripts/create_multi_agent_system.php "AnaliseSentimentos" "Coletor" "Analisador" "Relatorio"
```

### Estrutura de Dados
```php
class SentimentInfo
{
    public string $texto;
    public string $fonte;
    public string $data;
    public string $sentimento; // positivo, negativo, neutro
    public float $confianca;
    public array $palavras_chave;
}
```

### Fluxo
1. **Coletor**: Recebe texto → Valida → Armazena
2. **Analisador**: Analisa sentimento → Calcula confiança → Extrai palavras-chave
3. **Relatorio**: Gera gráficos → Estatísticas → Insights

### Ferramentas Externas
- API de análise de sentimentos (OpenAI, Google Cloud)
- API de visualização de dados (Chart.js, D3.js)
- API de processamento de linguagem natural

---

## 🛍️ Sistema de Recomendação de Produtos

### Descrição
Sistema que analisa preferências do usuário e recomenda produtos personalizados.

### Agentes
- **Perfil**: Cria perfil do usuário
- **Preferencias**: Analisa histórico e preferências
- **Recomendador**: Gera recomendações personalizadas

### Implementação

```bash
# Criar o sistema
php scripts/create_multi_agent_system.php "RecomendacaoProdutos" "Perfil" "Preferencias" "Recomendador"
```

### Estrutura de Dados
```php
class UserProfile
{
    public string $user_id;
    public array $preferencias;
    public array $historico_compras;
    public array $categorias_interesse;
    public string $faixa_etaria;
    public string $genero;
}
```

### Fluxo
1. **Perfil**: Coleta dados → Cria perfil → Armazena
2. **Preferencias**: Analisa histórico → Identifica padrões → Atualiza perfil
3. **Recomendador**: Busca produtos → Aplica filtros → Gera recomendações

### Ferramentas Externas
- API de produtos (Amazon, Mercado Livre)
- API de reviews e avaliações
- API de preços e disponibilidade
- Sistema de machine learning

---

## 💰 Sistema de Monitoramento de Preços

### Descrição
Sistema que monitora preços de produtos e alerta sobre mudanças e oportunidades.

### Agentes
- **Coletor**: Coleta preços de diferentes fontes
- **Comparador**: Compara preços e identifica oportunidades
- **Notificador**: Envia alertas e relatórios

### Implementação

```bash
# Criar o sistema
php scripts/create_multi_agent_system.php "MonitoramentoPrecos" "Coletor" "Comparador" "Notificador"
```

### Estrutura de Dados
```php
class PriceInfo
{
    public string $produto_id;
    public string $nome_produto;
    public float $preco_atual;
    public float $preco_anterior;
    public string $loja;
    public string $url;
    public string $data_coleta;
    public float $variacao_percentual;
}
```

### Fluxo
1. **Coletor**: Busca preços → Valida dados → Armazena
2. **Comparador**: Compara preços → Calcula variações → Identifica oportunidades
3. **Notificador**: Gera alertas → Envia notificações → Atualiza relatórios

### Ferramentas Externas
- APIs de e-commerce (Amazon, eBay, Mercado Livre)
- APIs de preços (PriceAPI, ScrapingBee)
- APIs de notificação (Email, SMS, Push)
- Sistema de alertas

---

## 📱 Sistema de Análise de Redes Sociais

### Descrição
Sistema que monitora menções, hashtags e sentimentos em redes sociais.

### Agentes
- **Coletor**: Coleta posts e menções
- **Filtrador**: Filtra conteúdo relevante
- **Analisador**: Analisa sentimentos e tendências
- **Relatorio**: Gera insights e relatórios

### Implementação

```bash
# Criar o sistema
php scripts/create_multi_agent_system.php "AnaliseRedesSociais" "Coletor" "Filtrador" "Analisador" "Relatorio"
```

### Estrutura de Dados
```php
class SocialMediaPost
{
    public string $post_id;
    public string $plataforma;
    public string $usuario;
    public string $conteudo;
    public array $hashtags;
    public int $likes;
    public int $compartilhamentos;
    public string $data;
    public string $sentimento;
    public array $mencoes;
}
```

### Fluxo
1. **Coletor**: Busca posts → Filtra por critérios → Armazena
2. **Filtrador**: Remove spam → Valida conteúdo → Categoriza
3. **Analisador**: Analisa sentimentos → Identifica tendências → Calcula métricas
4. **Relatorio**: Gera insights → Cria visualizações → Envia relatórios

### Ferramentas Externas
- APIs de redes sociais (Twitter, Facebook, Instagram)
- APIs de análise de sentimentos
- APIs de visualização de dados
- Sistema de alertas em tempo real

---

## ✍️ Sistema de Geração de Conteúdo

### Descrição
Sistema que gera conteúdo personalizado baseado em tópicos, público-alvo e objetivos.

### Agentes
- **Pesquisador**: Pesquisa tópicos e tendências
- **Escritor**: Gera conteúdo baseado em templates
- **Editor**: Revisa e otimiza conteúdo
- **Publicador**: Formata e publica conteúdo

### Implementação

```bash
# Criar o sistema
php scripts/create_multi_agent_system.php "GeracaoConteudo" "Pesquisador" "Escritor" "Editor" "Publicador"
```

### Estrutura de Dados
```php
class ContentRequest
{
    public string $topico;
    public string $publico_alvo;
    public string $objetivo;
    public string $formato; // blog, social, email
    public int $tamanho;
    public array $palavras_chave;
    public string $tom; // formal, informal, técnico
}
```

### Fluxo
1. **Pesquisador**: Pesquisa tópico → Identifica tendências → Coleta referências
2. **Escritor**: Gera conteúdo → Aplica templates → Personaliza
3. **Editor**: Revisa texto → Corrige erros → Otimiza SEO
4. **Publicador**: Formata conteúdo → Aplica estilos → Publica

### Ferramentas Externas
- APIs de pesquisa (Google, Bing)
- APIs de IA para escrita (OpenAI, GPT)
- APIs de SEO (Yoast, SEMrush)
- APIs de publicação (WordPress, Medium)

---

## 🛠️ Implementação Passo a Passo

### 1. Planejamento
```bash
# Definir o domínio do problema
# Identificar agentes necessários
# Mapear fluxo de dados
# Definir APIs externas necessárias
```

### 2. Criação da Estrutura
```bash
# Usar o script automatizado
php scripts/create_multi_agent_system.php "NomeDoSistema" "Agente1" "Agente2" "Agente3"

# Ou criar manualmente seguindo o template
```

### 3. Personalização
```php
// Ajustar prompts para seu domínio
const COLETA_INFORMACOES = <<<EOT
Com base na solicitação do usuário, extraia as seguintes informações:

Solicitação: {query}

Informações necessárias:
- [Campo específico do seu domínio]
- [Outro campo específico]

Se alguma informação estiver faltando, peça ao usuário de forma amigável.
EOT;
```

### 4. Implementação de Ferramentas
```php
// Criar ferramentas específicas para seu domínio
class MinhaFerramenta extends Tool
{
    public function __construct(protected string $apiKey)
    {
        parent::__construct(
            'minha_funcao',
            'Descrição do que a ferramenta faz'
        );
    }

    protected function properties(): array
    {
        return [
            new ToolProperty(
                name: 'parametro1',
                type: PropertyType::STRING,
                description: 'Descrição do parâmetro',
                required: true,
            ),
        ];
    }

    public function __invoke(string $parametro1): array
    {
        // Implementar lógica da ferramenta
        return $this->chamarAPI($parametro1);
    }
}
```

### 5. Teste e Validação
```php
// Testar com dados reais
$workflow = new MeuSistemaAgent($history, $state, $persistence);
$result = $workflow->start();

foreach ($result->streamEvents() as $event) {
    if ($event instanceof ProgressEvent) {
        echo $event->message;
    }
}
```

## 📊 Métricas e Monitoramento

### Métricas Importantes
- **Tempo de execução** por agente
- **Taxa de sucesso** das operações
- **Qualidade das respostas** geradas
- **Uso de recursos** (APIs, memória)

### Implementação de Logs
```php
// Adicionar logs detalhados
\Log::info('Agente iniciado', [
    'agente' => get_class($this),
    'dados_entrada' => $event,
    'timestamp' => now()
]);

\Log::info('Agente finalizado', [
    'agente' => get_class($this),
    'resultado' => $resultado,
    'tempo_execucao' => $tempoExecucao
]);
```

## 🔒 Considerações de Segurança

### Validação de Entrada
```php
// Sempre validar dados de entrada
public function __invoke(StartEvent $event, WorkflowState $state): Retrieve
{
    $query = $this->consumeInterruptFeedback();
    
    // Validar entrada
    if (empty($query) || strlen($query) > 1000) {
        throw new \InvalidArgumentException('Query inválida');
    }
    
    // Sanitizar entrada
    $query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');
    
    // ... resto da lógica
}
```

### Rate Limiting
```php
// Implementar limitação de taxa
class RateLimiter
{
    public function checkLimit(string $userId): bool
    {
        $key = "rate_limit:{$userId}";
        $requests = Cache::get($key, 0);
        
        if ($requests >= 10) { // 10 requests por minuto
            return false;
        }
        
        Cache::put($key, $requests + 1, 60);
        return true;
    }
}
```

## 🚀 Próximos Passos

1. **Escolha um exemplo** que se alinha com suas necessidades
2. **Implemente o sistema** usando o script automatizado
3. **Personalize os agentes** para seu domínio específico
4. **Teste com dados reais** e ajuste conforme necessário
5. **Monitore performance** e otimize continuamente

---

**💡 Dica**: Comece com sistemas simples e vá adicionando complexidade gradualmente. Cada exemplo pode ser adaptado e combinado para criar soluções mais complexas e poderosas!
