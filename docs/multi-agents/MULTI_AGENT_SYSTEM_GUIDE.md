# 🤖 Guia Completo: Sistema Multi-Agêntico com NeuronAI

## 📋 Índice
1. [Conceitos Fundamentais](#conceitos-fundamentais)
2. [Arquitetura do Sistema](#arquitetura-do-sistema)
3. [Estrutura de Arquivos](#estrutura-de-arquivos)
4. [Implementação Passo a Passo](#implementação-passo-a-passo)
5. [Exemplos Práticos](#exemplos-práticos)
6. [Boas Práticas](#boas-práticas)
7. [Troubleshooting](#troubleshooting)

## 🎯 Conceitos Fundamentais

### O que é um Sistema Multi-Agêntico?
Um sistema multi-agêntico é uma arquitetura onde **múltiplos agentes especializados** trabalham em conjunto para resolver problemas complexos. Cada agente tem uma responsabilidade específica e se comunica através de eventos e estado compartilhado.

### Componentes Principais
- **Workflow**: Coordenador principal que define a sequência de agentes
- **Nodes**: Agentes especializados que executam tarefas específicas
- **Events**: Mensagens que os agentes trocam entre si
- **State**: Estado compartilhado onde os dados são armazenados
- **Tools**: Ferramentas externas que os agentes podem usar

## 🏗️ Arquitetura do Sistema

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Workflow      │    │     Nodes       │    │     Events      │
│   (Coordenador) │───▶│  (Agentes)      │───▶│  (Comunicação)  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   State         │    │     Tools       │    │   Prompts       │
│ (Estado Comp.)  │    │ (Ferramentas)   │    │  (Templates)    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## 📁 Estrutura de Arquivos

```
app/
├── Neuron/
│   ├── [NomeDoSistema]Agent.php          # Workflow principal
│   ├── Prompts.php                        # Templates de prompts
│   ├── Agents/                           # Agentes especializados
│   │   ├── [Nome]Agent.php
│   │   └── [EstruturaDados].php
│   ├── Nodes/                            # Nós do workflow
│   │   ├── [Nome]Node.php
│   │   └── [Delegator]Node.php
│   ├── Events/                           # Eventos do sistema
│   │   ├── [Nome]Event.php
│   │   └── ProgressEvent.php
│   └── Tools/                            # Ferramentas externas
│       ├── [API]/
│       │   └── [Nome]Tool.php
│       └── [OutraAPI]/
│           └── [Nome]Tool.php
```

## 🚀 Implementação Passo a Passo

### Passo 1: Definir o Workflow Principal

```php
<?php
// app/Neuron/[NomeDoSistema]Agent.php

namespace App\Neuron;

use App\Neuron\Nodes\Receptionist;
use App\Neuron\Nodes\Delegator;
use App\Neuron\Nodes\[Agente1];
use App\Neuron\Nodes\[Agente2];
use App\Neuron\Nodes\GenerateResult;
use NeuronAI\Chat\History\ChatHistoryInterface;
use NeuronAI\Workflow\Persistence\PersistenceInterface;
use NeuronAI\Workflow\Workflow;
use NeuronAI\Workflow\WorkflowState;

class [NomeDoSistema]Agent extends Workflow
{
    public function __construct(
        protected ChatHistoryInterface $history,
        ?WorkflowState $state = null,
        ?PersistenceInterface $persistence = null,
        ?string $workflowId = null
    ) {
        parent::__construct($state, $persistence, $workflowId);
    }

    protected function nodes(): array
    {
        return [
            new Receptionist($this->history),
            new Delegator(),
            new [Agente1](),
            new [Agente2](),
            new GenerateResult($this->history)
        ];
    }
}
```

### Passo 2: Criar Estruturas de Dados

```php
<?php
// app/Neuron/Agents/[EstruturaDados].php

namespace App\Neuron\Agents;

use NeuronAI\StructuredOutput\SchemaProperty;
use NeuronAI\StructuredOutput\Validation\Rules\Length;

class [EstruturaDados]
{
    #[SchemaProperty(
        description: 'Descrição do campo',
        required: true
    )]
    #[Length(exactly: 10)] // Se necessário
    public string $campo1;

    #[SchemaProperty(
        description: 'Outro campo importante',
        required: false
    )]
    public string $campo2;

    public function isComplete(): bool
    {
        return isset($this->campo1) && isset($this->campo2);
    }
}
```

### Passo 3: Implementar o Nó Receptionist

```php
<?php
// app/Neuron/Nodes/Receptionist.php

namespace App\Neuron\Nodes;

use App\Neuron\Agents\[EstruturaDados];
use App\Neuron\Agents\ResearchAgent;
use App\Neuron\Events\Retrieve;
use App\Neuron\Prompts;
use NeuronAI\Chat\History\ChatHistoryInterface;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Exceptions\AgentException;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StartEvent;
use NeuronAI\Workflow\WorkflowInterrupt;
use NeuronAI\Workflow\WorkflowState;

class Receptionist extends Node
{
    public function __construct(protected ChatHistoryInterface $history)
    {
    }

    public function __invoke(StartEvent $event, WorkflowState $state): Retrieve
    {
        $query = $this->consumeInterruptFeedback();

        if ($query === null) {
            $query = \str_replace('{query}', $state->get('query'), Prompts::[NOME_DO_PROMPT]);
        }

        /** @var [EstruturaDados] $info */
        $info = ResearchAgent::make()
            ->withChatHistory($this->history)
            ->structured(
                new UserMessage($query),
                [EstruturaDados]::class
            );

        if (!$info->isComplete()) {
            $this->interrupt(['question' => $info->description]);
        }

        return new Retrieve($info);
    }
}
```

### Passo 4: Implementar o Nó Delegator

```php
<?php
// app/Neuron/Nodes/Delegator.php

namespace App\Neuron\Nodes;

use App\Neuron\Events\CreateResult;
use App\Neuron\Events\ProgressEvent;
use App\Neuron\Events\Retrieve;
use App\Neuron\Events\Retrieve[Agente1];
use App\Neuron\Events\Retrieve[Agente2];
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;

class Delegator extends Node
{
    public function __invoke(
        Retrieve $event, 
        WorkflowState $state
    ): \Generator|Retrieve[Agente1]|Retrieve[Agente2]|CreateResult {

        if (!$state->has('[dados_agente1]')) {
            yield new ProgressEvent("\n- Coletando [dados_agente1]...");
            return new Retrieve[Agente1]($event->data);
        }

        if (!$state->has('[dados_agente2]')) {
            yield new ProgressEvent("\n- Coletando [dados_agente2]...");
            return new Retrieve[Agente2]($event->data);
        }

        return new CreateResult($event->data);
    }
}
```

### Passo 5: Implementar Agentes Especializados

```php
<?php
// app/Neuron/Nodes/[Agente1].php

namespace App\Neuron\Nodes;

use App\Neuron\Agents\ResearchAgent;
use App\Neuron\Events\Retrieve;
use App\Neuron\Events\Retrieve[Agente1];
use App\Neuron\Tools\[API]\[Nome]Tool;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;

class [Agente1] extends Node
{
    public function __invoke(Retrieve[Agente1] $event, WorkflowState $state): Retrieve
    {
        $response = ResearchAgent::make()
            ->addTool(
                [Nome]Tool::make($_ENV['[API_KEY]'])
            )
            ->chat(
                new UserMessage(
                    "Descrição da tarefa específica: " . $event->data->campo1 . 
                    " com parâmetro: " . $event->data->campo2
                )
            );

        $state->set('[dados_agente1]', $response->getContent());

        return new Retrieve($event->data);
    }
}
```

### Passo 6: Implementar Ferramentas Externas

```php
<?php
// app/Neuron/Tools/[API]/[Nome]Tool.php

namespace App\Neuron\Tools\[API];

use GuzzleHttp\Client;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;

class [Nome]Tool extends Tool
{
    protected Client $client;

    public function __construct(
        protected string $key,
        protected string $param1 = 'default',
        protected int $param2 = 1,
    ) {
        parent::__construct(
            '[nome_da_funcao]',
            'Descrição do que a ferramenta faz.',
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
            new ToolProperty(
                name: 'parametro2',
                type: PropertyType::STRING,
                description: 'Descrição do outro parâmetro',
                required: false,
            ),
        ];
    }

    public function __invoke(
        string $parametro1,
        string $parametro2 = null,
    ): array {
        $result = $this->getClient()->get('endpoint', [
            'query' => [
                "param1" => $parametro1,
                "param2" => $parametro2,
                "api_key" => $this->key,
            ]
        ])->getBody()->getContents();

        return \json_decode($result, true);
    }

    protected function getClient(): Client
    {
        return $this->client ?? $this->client = new Client([
            'base_uri' => 'https://api.exemplo.com/',
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
    }
}
```

### Passo 7: Implementar Eventos

```php
<?php
// app/Neuron/Events/Retrieve[Agente1].php

namespace App\Neuron\Events;

use NeuronAI\Workflow\Event;

class Retrieve[Agente1] implements Event
{
    public function __construct(public [EstruturaDados] $data)
    {
    }
}
```

```php
<?php
// app/Neuron/Events/ProgressEvent.php

namespace App\Neuron\Events;

use NeuronAI\Workflow\Event;

class ProgressEvent implements Event
{
    public function __construct(public string $message)
    {
    }
}
```

### Passo 8: Implementar Prompts

```php
<?php
// app/Neuron/Prompts.php

namespace App\Neuron;

class Prompts
{
    const [NOME_DO_PROMPT] = <<<EOT
Descrição do que o agente deve fazer com base na solicitação do usuário.

Solicitação do usuário: {query}

Instruções específicas sobre como processar a solicitação.
Se alguma informação estiver faltando, peça ao usuário de forma amigável.
EOT;

    const [OUTRO_PROMPT] = <<<EOT
Template para outro agente baseado nos dados coletados.

---
{dados_agente1}
---
{dados_agente2}
---

Instruções para processar e formatar o resultado final.
EOT;
}
```

### Passo 9: Implementar Nó de Geração de Resultado

```php
<?php
// app/Neuron/Nodes/GenerateResult.php

namespace App\Neuron\Nodes;

use App\Neuron\Agents\ResearchAgent;
use App\Neuron\Events\CreateResult;
use App\Neuron\Events\ProgressEvent;
use App\Neuron\Prompts;
use NeuronAI\Chat\History\ChatHistoryInterface;
use NeuronAI\Chat\Messages\ToolCallMessage;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Tools\ToolInterface;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StopEvent;
use NeuronAI\Workflow\WorkflowState;

class GenerateResult extends Node
{
    public function __construct(protected ChatHistoryInterface $history)
    {
    }

    public function __invoke(CreateResult $event, WorkflowState $state): \Generator|StopEvent
    {
        $message = \str_replace('{dados_agente1}', $state->get('[dados_agente1]'), Prompts::[OUTRO_PROMPT]);
        $message = \str_replace('{dados_agente2}', $state->get('[dados_agente2]'), $message);

        $result = ResearchAgent::make()
            ->withChatHistory($this->history)
            ->stream(
                new UserMessage($message)
            );

        foreach ($result as $item) {
            if ($item instanceof ToolCallMessage) {
                yield new ProgressEvent(
                    \array_reduce($item->getTools(), function (string $carry, ToolInterface $tool): string {
                        $carry .= "\n- Chamando ferramenta: " . $tool->getName();
                        return $carry;
                    }, '') . "\n"
                );
            } else {
                yield new ProgressEvent($item);
            }
        }

        $state->set('resultado_final', $result->getReturn()->getContent());

        return new StopEvent();
    }
}
```

### Passo 10: Integrar com Laravel

```php
<?php
// app/Livewire/[NomeDoComponente].php

namespace App\Livewire;

use App\Neuron\[NomeDoSistema]Agent;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use NeuronAI\Chat\History\ChatHistoryInterface;
use NeuronAI\Chat\History\FileChatHistory;
use NeuronAI\Workflow\Persistence\FilePersistence;
use NeuronAI\Workflow\Persistence\PersistenceInterface;
use NeuronAI\Workflow\WorkflowInterrupt;
use NeuronAI\Workflow\WorkflowState;

class [NomeDoComponente] extends Component
{
    public string $input;
    public array $messages = [];
    public bool $thinking = false;
    public bool $interrupted = false;

    protected ChatHistoryInterface $history;
    protected PersistenceInterface $persistence;

    public function __construct()
    {
        $this->history = new FileChatHistory(storage_path('ai'), '[nome_do_historico]');
        $this->persistence = new FilePersistence(storage_path('ai'), '[nome_da_persistencia]');
    }

    public function render()
    {
        return view('livewire.[nome-do-componente]');
    }

    public function [metodoPrincipal]()
    {
        $this->messages[] = [
            'who' => 'user',
            'content' => $this->input,
        ];

        $this->thinking = true;
        $this->dispatch('scroll-bottom');
        $this->dispatch('[evento_ai_response]', $this->input);
        $this->input = '';
    }

    #[On('[evento_ai_response]')]
    public function [metodoAIResponse]($input): void
    {
        $workflow = new [NomeDoSistema]Agent(
            $this->history,
            new WorkflowState(['query' => \array_first($this->messages)['content']]),
            $this->persistence,
            '[nome_do_workflow]'
        );

        try {
            if ($this->interrupted) {
                $handler = $workflow->wakeup($input);
            } else {
                $handler = $workflow->start();
            }

            $message = '';
            foreach ($handler->streamEvents() as $event) {
                if ($event instanceof \App\Neuron\Events\ProgressEvent) {
                    $message .= $event->message;
                    $this->stream('response', Str::markdown($message, ['html_input' => 'strip']), true);
                }
            }

            $this->messages[] = [
                'who' => 'ai',
                'content' => $handler->getResult()->get('resultado_final'),
            ];
            $this->thinking = false;
            $this->interrupted = false;
            $this->dispatch('scroll-bottom');

        } catch (WorkflowInterrupt $interrupt) {
            $this->interrupted = true;
            $this->messages[] = [
                'who' => 'ai',
                'content' => $interrupt->getData()['question'],
            ];
            $this->thinking = false;
        }
    }
}
```

## 📝 Exemplos Práticos

### Exemplo 1: Sistema de Análise de Sentimentos
```php
// Agentes: Coletor, Analisador, Gerador de Relatório
// Ferramentas: API de análise de texto, API de dados demográficos
// Fluxo: Coleta → Análise → Relatório
```

### Exemplo 2: Sistema de Recomendação de Produtos
```php
// Agentes: Perfil do Usuário, Analisador de Preferências, Gerador de Recomendações
// Ferramentas: API de produtos, API de reviews, API de preços
// Fluxo: Perfil → Análise → Recomendações
```

### Exemplo 3: Sistema de Monitoramento de Redes Sociais
```php
// Agentes: Coletor, Filtrador, Analisador, Notificador
// Ferramentas: APIs do Twitter, Facebook, Instagram
// Fluxo: Coleta → Filtro → Análise → Notificação
```

## ✅ Boas Práticas

### 1. Nomenclatura
- Use nomes descritivos para agentes e eventos
- Mantenha consistência na nomenclatura
- Use PascalCase para classes e camelCase para métodos

### 2. Estrutura de Dados
- Sempre valide dados de entrada
- Use atributos de validação do NeuronAI
- Implemente métodos `isComplete()` para verificar completude

### 3. Tratamento de Erros
- Sempre trate exceções adequadamente
- Use interrupções para coletar informações faltantes
- Implemente logs para debugging

### 4. Performance
- Use streaming para respostas longas
- Implemente cache quando apropriado
- Otimize chamadas de API

### 5. Manutenibilidade
- Mantenha agentes focados em uma responsabilidade
- Use interfaces para ferramentas
- Documente prompts e comportamentos

## 🔧 Troubleshooting

### Problemas Comuns

#### 1. Erro de Chave de API
```bash
# Verifique se as variáveis de ambiente estão configuradas
echo $SERPAPI_KEY
```

#### 2. Erro de Estrutura de Dados
```php
// Verifique se todos os campos obrigatórios estão definidos
public function isComplete(): bool
{
    return isset($this->campo1) && isset($this->campo2);
}
```

#### 3. Erro de Workflow Interrupt
```php
// Verifique se o feedback está sendo consumido corretamente
$query = $this->consumeInterruptFeedback();
```

#### 4. Erro de Estado Compartilhado
```php
// Verifique se o estado está sendo definido corretamente
$state->set('chave', $valor);
```

### Debugging
```php
// Adicione logs para debugging
\Log::info('Estado atual', $state->all());
\Log::info('Evento recebido', $event);
```

## 🚀 Próximos Passos

1. **Implemente seu primeiro agente** seguindo este guia
2. **Teste com dados reais** para validar o funcionamento
3. **Adicione novos agentes** conforme necessário
4. **Otimize performance** baseado no uso real
5. **Documente comportamentos** específicos do seu domínio

## 📚 Recursos Adicionais

- [Documentação NeuronAI](https://github.com/inspector-apm/neuron-ai)
- [Laravel Livewire](https://livewire.laravel.com/)
- [Guzzle HTTP Client](https://docs.guzzlephp.org/)

---

**💡 Dica**: Comece com um sistema simples e vá adicionando complexidade gradualmente. A arquitetura multi-agêntica é poderosa, mas requer planejamento cuidadoso para ser eficaz.
