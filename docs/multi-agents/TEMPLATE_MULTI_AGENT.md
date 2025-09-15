# 🚀 Template para Sistema Multi-Agêntico

## 📋 Checklist de Implementação

### ✅ Fase 1: Planejamento
- [ ] Definir o domínio do problema
- [ ] Identificar agentes necessários
- [ ] Mapear fluxo de dados
- [ ] Definir APIs externas necessárias
- [ ] Criar estrutura de dados

### ✅ Fase 2: Estrutura Base
- [ ] Criar pasta `app/Neuron/[NomeDoSistema]/`
- [ ] Implementar `[NomeDoSistema]Agent.php`
- [ ] Criar `Prompts.php`
- [ ] Definir estruturas de dados em `Agents/`

### ✅ Fase 3: Nós do Workflow
- [ ] Implementar `Receptionist.php`
- [ ] Implementar `Delegator.php`
- [ ] Implementar agentes especializados
- [ ] Implementar `GenerateResult.php`

### ✅ Fase 4: Eventos e Ferramentas
- [ ] Criar eventos necessários em `Events/`
- [ ] Implementar ferramentas externas em `Tools/`
- [ ] Configurar variáveis de ambiente

### ✅ Fase 5: Integração Laravel
- [ ] Criar componente Livewire
- [ ] Implementar view Blade
- [ ] Configurar rotas
- [ ] Testar integração

## 🎯 Template de Código

### 1. Workflow Principal
```php
<?php
// app/Neuron/[NomeDoSistema]/[NomeDoSistema]Agent.php

namespace App\Neuron\[NomeDoSistema];

use App\Neuron\[NomeDoSistema]\Nodes\Receptionist;
use App\Neuron\[NomeDoSistema]\Nodes\Delegator;
use App\Neuron\[NomeDoSistema]\Nodes\[Agente1];
use App\Neuron\[NomeDoSistema]\Nodes\[Agente2];
use App\Neuron\[NomeDoSistema]\Nodes\GenerateResult;
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

### 2. Estrutura de Dados
```php
<?php
// app/Neuron/[NomeDoSistema]/Agents/[EstruturaDados].php

namespace App\Neuron\[NomeDoSistema]\Agents;

use NeuronAI\StructuredOutput\SchemaProperty;
use NeuronAI\StructuredOutput\Validation\Rules\Length;

class [EstruturaDados]
{
    #[SchemaProperty(
        description: 'Descrição do campo obrigatório',
        required: true
    )]
    #[Length(exactly: 10)] // Ajuste conforme necessário
    public string $campo_obrigatorio;

    #[SchemaProperty(
        description: 'Descrição do campo opcional',
        required: false
    )]
    public string $campo_opcional;

    public function isComplete(): bool
    {
        return isset($this->campo_obrigatorio);
    }
}
```

### 3. Prompts
```php
<?php
// app/Neuron/[NomeDoSistema]/Prompts.php

namespace App\Neuron\[NomeDoSistema];

class Prompts
{
    const COLETA_INFORMACOES = <<<EOT
Com base na solicitação do usuário, extraia as seguintes informações:

Solicitação: {query}

Informações necessárias:
- Campo obrigatório: [descrição]
- Campo opcional: [descrição]

Se alguma informação estiver faltando, peça ao usuário de forma amigável e específica.
EOT;

    const GERACAO_RESULTADO = <<<EOT
Com base nos dados coletados, gere um resultado final:

---
{dados_agente1}
---
{dados_agente2}
---

Formate o resultado de forma clara e útil para o usuário.
EOT;
}
```

### 4. Componente Livewire
```php
<?php
// app/Livewire/[NomeDoComponente].php

namespace App\Livewire;

use App\Neuron\[NomeDoSistema]\[NomeDoSistema]Agent;
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
    public string $input = '';
    public array $messages = [];
    public bool $thinking = false;
    public bool $interrupted = false;

    protected ChatHistoryInterface $history;
    protected PersistenceInterface $persistence;

    public function __construct()
    {
        $this->history = new FileChatHistory(storage_path('ai'), '[nome_historico]');
        $this->persistence = new FilePersistence(storage_path('ai'), '[nome_persistencia]');
    }

    public function render()
    {
        return view('livewire.[nome-do-componente]');
    }

    public function enviarMensagem()
    {
        $this->messages[] = [
            'who' => 'user',
            'content' => $this->input,
        ];

        $this->thinking = true;
        $this->dispatch('scroll-bottom');
        $this->dispatch('processarResposta', $this->input);
        $this->input = '';
    }

    #[On('processarResposta')]
    public function processarResposta($input): void
    {
        $workflow = new [NomeDoSistema]Agent(
            $this->history,
            new WorkflowState(['query' => \array_first($this->messages)['content']]),
            $this->persistence,
            '[nome_workflow]'
        );

        try {
            if ($this->interrupted) {
                $handler = $workflow->wakeup($input);
            } else {
                $handler = $workflow->start();
            }

            $message = '';
            foreach ($handler->streamEvents() as $event) {
                if ($event instanceof \App\Neuron\[NomeDoSistema]\Events\ProgressEvent) {
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

### 5. View Blade
```blade
{{-- resources/views/livewire/[nome-do-componente].blade.php --}}
<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6 border-b">
            <h2 class="text-2xl font-bold text-gray-800">[Título do Sistema]</h2>
        </div>
        
        <div class="h-96 overflow-y-auto p-6 space-y-4" id="messages">
            @foreach($messages as $message)
                <div class="flex {{ $message['who'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message['who'] === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                        {!! $message['content'] !!}
                    </div>
                </div>
            @endforeach
            
            @if($thinking)
                <div class="flex justify-start">
                    <div class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-600"></div>
                            <span>Processando...</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="p-6 border-t">
            <form wire:submit.prevent="enviarMensagem" class="flex space-x-4">
                <input 
                    type="text" 
                    wire:model="input" 
                    placeholder="Digite sua mensagem..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    {{ $thinking ? 'disabled' : '' }}
                >
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50"
                    {{ $thinking ? 'disabled' : '' }}
                >
                    Enviar
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('scroll-bottom', () => {
            const messages = document.getElementById('messages');
            messages.scrollTop = messages.scrollHeight;
        });
    });
</script>
```

## 🔧 Configuração de Ambiente

### Variáveis de Ambiente (.env)
```bash
# APIs Externas
SERPAPI_KEY=sua_chave_aqui
OUTRA_API_KEY=sua_outra_chave_aqui

# Configurações do Sistema
APP_ENV=local
APP_DEBUG=true
```

### Rota (routes/web.php)
```php
Route::get('/[nome-da-rota]', function () {
    return view('[nome-da-view]');
});
```

## 📝 Exemplo de Uso

### 1. Sistema de Análise de Sentimentos
```php
// Agentes: Coletor, Analisador, Relatório
// Fluxo: Texto → Análise → Relatório de Sentimentos
```

### 2. Sistema de Recomendação de Filmes
```php
// Agentes: Perfil, Gêneros, Recomendador
// Fluxo: Preferências → Análise → Recomendações
```

### 3. Sistema de Monitoramento de Preços
```php
// Agentes: Coletor, Comparador, Notificador
// Fluxo: Produto → Preços → Comparação → Alerta
```

## 🚀 Comandos Úteis

### Criar Estrutura de Pastas
```bash
mkdir -p app/Neuron/[NomeDoSistema]/{Agents,Nodes,Events,Tools}
```

### Instalar Dependências
```bash
composer require inspector-apm/neuron-ai
```

### Testar Sistema
```bash
php artisan tinker
# Teste seu workflow aqui
```

## 📚 Próximos Passos

1. **Copie este template** para seu projeto
2. **Substitua os placeholders** pelos nomes reais
3. **Implemente os agentes** específicos do seu domínio
4. **Teste com dados reais**
5. **Itere e melhore** baseado no feedback

---

**💡 Dica**: Use este template como base e adapte conforme suas necessidades específicas. A arquitetura multi-agêntica é flexível e pode ser adaptada para diversos domínios!
