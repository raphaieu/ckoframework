<?php
/**
 * Script para criar um novo sistema multi-agêntico
 * 
 * Uso: php scripts/create_multi_agent_system.php [NomeDoSistema] [Agente1] [Agente2] [Agente3]
 * 
 * Exemplo: php scripts/create_multi_agent_system.php "AnaliseSentimentos" "Coletor" "Analisador" "Relatorio"
 */

if ($argc < 4) {
    echo "❌ Uso: php scripts/create_multi_agent_system.php [NomeDoSistema] [Agente1] [Agente2] [Agente3]\n";
    echo "📝 Exemplo: php scripts/create_multi_agent_system.php \"AnaliseSentimentos\" \"Coletor\" \"Analisador\" \"Relatorio\"\n";
    exit(1);
}

$nomeDoSistema = $argv[1];
$agente1 = $argv[2];
$agente2 = $argv[3];
$agente3 = $argv[4] ?? 'GeradorResultado';

$nomeDoSistemaLower = strtolower($nomeDoSistema);
$nomeDoSistemaKebab = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $nomeDoSistema));

echo "🚀 Criando sistema multi-agêntico: {$nomeDoSistema}\n";
echo "📁 Agentes: {$agente1}, {$agente2}, {$agente3}\n\n";

// Criar estrutura de pastas
$basePath = "app/Neuron/{$nomeDoSistema}";
$dirs = [
    $basePath,
    "{$basePath}/Agents",
    "{$basePath}/Nodes", 
    "{$basePath}/Events",
    "{$basePath}/Tools"
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Pasta criada: {$dir}\n";
    }
}

// 1. Criar Workflow Principal
$workflowContent = generateWorkflowContent($nomeDoSistema, $agente1, $agente2, $agente3);
file_put_contents("{$basePath}/{$nomeDoSistema}Agent.php", $workflowContent);
echo "✅ Workflow criado: {$basePath}/{$nomeDoSistema}Agent.php\n";

// 2. Criar Estrutura de Dados
$dataStructureContent = generateDataStructureContent($nomeDoSistema);
file_put_contents("{$basePath}/Agents/ExtractedInfo.php", $dataStructureContent);
echo "✅ Estrutura de dados criada: {$basePath}/Agents/ExtractedInfo.php\n";

// 3. Criar Prompts
$promptsContent = generatePromptsContent($nomeDoSistema, $agente1, $agente2, $agente3);
file_put_contents("{$basePath}/Prompts.php", $promptsContent);
echo "✅ Prompts criados: {$basePath}/Prompts.php\n";

// 4. Criar Receptionist
$receptionistContent = generateReceptionistContent($nomeDoSistema);
file_put_contents("{$basePath}/Nodes/Receptionist.php", $receptionistContent);
echo "✅ Receptionist criado: {$basePath}/Nodes/Receptionist.php\n";

// 5. Criar Delegator
$delegatorContent = generateDelegatorContent($nomeDoSistema, $agente1, $agente2, $agente3);
file_put_contents("{$basePath}/Nodes/Delegator.php", $delegatorContent);
echo "✅ Delegator criado: {$basePath}/Nodes/Delegator.php\n";

// 6. Criar Agentes Especializados
$agentes = [$agente1, $agente2, $agente3];
foreach ($agentes as $agente) {
    $agenteContent = generateAgenteContent($nomeDoSistema, $agente);
    file_put_contents("{$basePath}/Nodes/{$agente}.php", $agenteContent);
    echo "✅ Agente criado: {$basePath}/Nodes/{$agente}.php\n";
}

// 7. Criar Eventos
$eventos = ['Retrieve', 'Retrieve' . $agente1, 'Retrieve' . $agente2, 'Retrieve' . $agente3, 'CreateResult', 'ProgressEvent'];
foreach ($eventos as $evento) {
    $eventoContent = generateEventoContent($nomeDoSistema, $evento);
    file_put_contents("{$basePath}/Events/{$evento}.php", $eventoContent);
    echo "✅ Evento criado: {$basePath}/Events/{$evento}.php\n";
}

// 8. Criar Componente Livewire
$livewireContent = generateLivewireContent($nomeDoSistema, $nomeDoSistemaKebab);
file_put_contents("app/Livewire/{$nomeDoSistema}Component.php", $livewireContent);
echo "✅ Componente Livewire criado: app/Livewire/{$nomeDoSistema}Component.php\n";

// 9. Criar View Blade
$viewContent = generateViewContent($nomeDoSistema, $nomeDoSistemaKebab);
$viewDir = "resources/views/livewire";
if (!is_dir($viewDir)) {
    mkdir($viewDir, 0755, true);
}
file_put_contents("{$viewDir}/{$nomeDoSistemaKebab}-component.blade.php", $viewContent);
echo "✅ View criada: {$viewDir}/{$nomeDoSistemaKebab}-component.blade.php\n";

// 10. Criar Rota
$routeContent = generateRouteContent($nomeDoSistema, $nomeDoSistemaKebab);
file_put_contents("routes/{$nomeDoSistemaLower}.php", $routeContent);
echo "✅ Rota criada: routes/{$nomeDoSistemaLower}.php\n";

echo "\n🎉 Sistema multi-agêntico '{$nomeDoSistema}' criado com sucesso!\n";
echo "📝 Próximos passos:\n";
echo "1. Configure as variáveis de ambiente necessárias\n";
echo "2. Implemente as ferramentas externas em {$basePath}/Tools/\n";
echo "3. Ajuste os prompts conforme seu domínio\n";
echo "4. Teste o sistema com dados reais\n";
echo "5. Adicione a rota em routes/web.php: require_once '{$nomeDoSistemaLower}.php';\n";

function generateWorkflowContent($nomeDoSistema, $agente1, $agente2, $agente3) {
    return "<?php

declare(strict_types=1);

namespace App\Neuron\\{$nomeDoSistema};

use App\Neuron\\{$nomeDoSistema}\\Nodes\\Receptionist;
use App\Neuron\\{$nomeDoSistema}\\Nodes\\Delegator;
use App\Neuron\\{$nomeDoSistema}\\Nodes\\{$agente1};
use App\Neuron\\{$nomeDoSistema}\\Nodes\\{$agente2};
use App\Neuron\\{$nomeDoSistema}\\Nodes\\{$agente3};
use NeuronAI\Chat\History\ChatHistoryInterface;
use NeuronAI\Workflow\Persistence\PersistenceInterface;
use NeuronAI\Workflow\Workflow;
use NeuronAI\Workflow\WorkflowState;

class {$nomeDoSistema}Agent extends Workflow
{
    public function __construct(
        protected ChatHistoryInterface \$history,
        ?WorkflowState \$state = null,
        ?PersistenceInterface \$persistence = null,
        ?string \$workflowId = null
    ) {
        parent::__construct(\$state, \$persistence, \$workflowId);
    }

    protected function nodes(): array
    {
        return [
            new Receptionist(\$this->history),
            new Delegator(),
            new {$agente1}(),
            new {$agente2}(),
            new {$agente3}(\$this->history)
        ];
    }
}";
}

function generateDataStructureContent($nomeDoSistema) {
    return "<?php

namespace App\Neuron\\{$nomeDoSistema}\\Agents;

use NeuronAI\StructuredOutput\SchemaProperty;
use NeuronAI\StructuredOutput\Validation\Rules\Length;

class ExtractedInfo
{
    #[SchemaProperty(
        description: 'Use este campo para descrever as informações que estão faltando. Use markdown para formatar o texto.',
        required: true
    )]
    public string \$description;

    #[SchemaProperty(
        description: 'As informações extraídas. Se não conseguir extrair completamente, deixe este campo vazio.',
        required: false
    )]
    public TourInfo \$tour;

    public function isComplete(): bool
    {
        return isset(\$this->tour) && \$this->tour->isComplete();
    }
}

class TourInfo
{
    #[SchemaProperty(
        description: 'Campo obrigatório 1 - ajuste conforme necessário',
        required: true
    )]
    #[Length(exactly: 10)]
    public string \$campo1;

    #[SchemaProperty(
        description: 'Campo obrigatório 2 - ajuste conforme necessário',
        required: true
    )]
    public string \$campo2;

    #[SchemaProperty(
        description: 'Campo opcional - ajuste conforme necessário',
        required: false
    )]
    public string \$campo3;

    public function isComplete(): bool
    {
        return isset(\$this->campo1) && isset(\$this->campo2);
    }
}";
}

function generatePromptsContent($nomeDoSistema, $agente1, $agente2, $agente3) {
    return "<?php

namespace App\Neuron\\{$nomeDoSistema};

class Prompts
{
    const COLETA_INFORMACOES = <<<EOT
Com base na solicitação do usuário, extraia as seguintes informações:

Solicitação do usuário: {query}

Informações necessárias:
- Campo 1: [descrição do campo 1]
- Campo 2: [descrição do campo 2]
- Campo 3: [descrição do campo 3 - opcional]

Se alguma informação estiver faltando, peça ao usuário de forma amigável e específica.
EOT;

    const GERACAO_RESULTADO = <<<EOT
Com base nos dados coletados, gere um resultado final:

---
{dados_{$agente1}}
---
{dados_{$agente2}}
---
{dados_{$agente3}}
---

Formate o resultado de forma clara e útil para o usuário.
EOT;
}";
}

function generateReceptionistContent($nomeDoSistema) {
    return "<?php

declare(strict_types=1);

namespace App\Neuron\\{$nomeDoSistema}\\Nodes;

use App\Neuron\\{$nomeDoSistema}\\Agents\\ExtractedInfo;
use App\Neuron\\{$nomeDoSistema}\\Prompts;
use NeuronAI\Chat\History\ChatHistoryInterface;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Exceptions\AgentException;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StartEvent;
use NeuronAI\Workflow\WorkflowInterrupt;
use NeuronAI\Workflow\WorkflowState;

class Receptionist extends Node
{
    public function __construct(protected ChatHistoryInterface \$history)
    {
    }

    public function __invoke(StartEvent \$event, WorkflowState \$state): \\App\Neuron\\{$nomeDoSistema}\\Events\\Retrieve
    {
        \$query = \$this->consumeInterruptFeedback();

        if (\$query === null) {
            \$query = \\str_replace('{query}', \$state->get('query'), Prompts::COLETA_INFORMACOES);
        }

        /** @var ExtractedInfo \$info */
        \$info = \\NeuronAI\Agents\ResearchAgent::make()
            ->withChatHistory(\$this->history)
            ->structured(
                new UserMessage(\$query),
                ExtractedInfo::class
            );

        if (!\$info->isComplete()) {
            \$this->interrupt(['question' => \$info->description]);
        }

        return new \\App\Neuron\\{$nomeDoSistema}\\Events\\Retrieve(\$info->tour);
    }
}";
}

function generateDelegatorContent($nomeDoSistema, $agente1, $agente2, $agente3) {
    return "<?php

namespace App\Neuron\\{$nomeDoSistema}\\Nodes;

use App\Neuron\\{$nomeDoSistema}\\Events\\Retrieve;
use App\Neuron\\{$nomeDoSistema}\\Events\\Retrieve{$agente1};
use App\Neuron\\{$nomeDoSistema}\\Events\\Retrieve{$agente2};
use App\Neuron\\{$nomeDoSistema}\\Events\\Retrieve{$agente3};
use App\Neuron\\{$nomeDoSistema}\\Events\\CreateResult;
use App\Neuron\\{$nomeDoSistema}\\Events\\ProgressEvent;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;

class Delegator extends Node
{
    public function __invoke(
        Retrieve \$event, 
        WorkflowState \$state
    ): \\Generator|Retrieve{$agente1}|Retrieve{$agente2}|Retrieve{$agente3}|CreateResult {

        if (!\$state->has('dados_{$agente1}')) {
            yield new ProgressEvent(\"\\n- Executando {$agente1}...\");
            return new Retrieve{$agente1}(\$event->data);
        }

        if (!\$state->has('dados_{$agente2}')) {
            yield new ProgressEvent(\"\\n- Executando {$agente2}...\");
            return new Retrieve{$agente2}(\$event->data);
        }

        if (!\$state->has('dados_{$agente3}')) {
            yield new ProgressEvent(\"\\n- Executando {$agente3}...\");
            return new Retrieve{$agente3}(\$event->data);
        }

        return new CreateResult(\$event->data);
    }
}";
}

function generateAgenteContent($nomeDoSistema, $agente) {
    return "<?php

namespace App\Neuron\\{$nomeDoSistema}\\Nodes;

use App\Neuron\\{$nomeDoSistema}\\Events\\Retrieve;
use App\Neuron\\{$nomeDoSistema}\\Events\\Retrieve{$agente};
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;

class {$agente} extends Node
{
    public function __invoke(Retrieve{$agente} \$event, WorkflowState \$state): Retrieve
    {
        // TODO: Implementar lógica específica do {$agente}
        // Exemplo:
        // \$response = ResearchAgent::make()
        //     ->addTool(new SuaFerramenta())
        //     ->chat(new UserMessage(\"Sua mensagem aqui\"));

        \$state->set('dados_{$agente}', 'Resultado do {$agente} - implementar lógica específica');

        return new Retrieve(\$event->data);
    }
}";
}

function generateEventoContent($nomeDoSistema, $evento) {
    if ($evento === 'ProgressEvent') {
        return "<?php

namespace App\Neuron\\{$nomeDoSistema}\\Events;

use NeuronAI\Workflow\Event;

class ProgressEvent implements Event
{
    public function __construct(public string \$message)
    {
    }
}";
    }

    if ($evento === 'Retrieve') {
        return "<?php

namespace App\Neuron\\{$nomeDoSistema}\\Events;

use NeuronAI\Workflow\Event;

class Retrieve implements Event
{
    public function __construct(public \\App\Neuron\\{$nomeDoSistema}\\Agents\\TourInfo \$data)
    {
    }
}";
    }

    if ($evento === 'CreateResult') {
        return "<?php

namespace App\Neuron\\{$nomeDoSistema}\\Events;

use NeuronAI\Workflow\Event;

class CreateResult implements Event
{
    public function __construct(public \\App\Neuron\\{$nomeDoSistema}\\Agents\\TourInfo \$data)
    {
    }
}";
    }

    // Eventos Retrieve[Agente]
    return "<?php

namespace App\Neuron\\{$nomeDoSistema}\\Events;

use NeuronAI\Workflow\Event;

class {$evento} implements Event
{
    public function __construct(public \\App\Neuron\\{$nomeDoSistema}\\Agents\\TourInfo \$data)
    {
    }
}";
}

function generateLivewireContent($nomeDoSistema, $nomeDoSistemaKebab) {
    return "<?php

namespace App\Livewire;

use App\Neuron\\{$nomeDoSistema}\\{$nomeDoSistema}Agent;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use NeuronAI\Chat\History\ChatHistoryInterface;
use NeuronAI\Chat\History\FileChatHistory;
use NeuronAI\Workflow\Persistence\FilePersistence;
use NeuronAI\Workflow\Persistence\PersistenceInterface;
use NeuronAI\Workflow\WorkflowInterrupt;
use NeuronAI\Workflow\WorkflowState;

class {$nomeDoSistema}Component extends Component
{
    public string \$input = '';
    public array \$messages = [];
    public bool \$thinking = false;
    public bool \$interrupted = false;

    protected ChatHistoryInterface \$history;
    protected PersistenceInterface \$persistence;

    public function __construct()
    {
        \$this->history = new FileChatHistory(storage_path('ai'), '{$nomeDoSistemaKebab}_history');
        \$this->persistence = new FilePersistence(storage_path('ai'), '{$nomeDoSistemaKebab}_persistence');
    }

    public function render()
    {
        return view('livewire.{$nomeDoSistemaKebab}-component');
    }

    public function enviarMensagem()
    {
        \$this->messages[] = [
            'who' => 'user',
            'content' => \$this->input,
        ];

        \$this->thinking = true;
        \$this->dispatch('scroll-bottom');
        \$this->dispatch('processarResposta', \$this->input);
        \$this->input = '';
    }

    #[On('processarResposta')]
    public function processarResposta(\$input): void
    {
        \$workflow = new {$nomeDoSistema}Agent(
            \$this->history,
            new WorkflowState(['query' => \\array_first(\$this->messages)['content']]),
            \$this->persistence,
            '{$nomeDoSistemaKebab}_workflow'
        );

        try {
            if (\$this->interrupted) {
                \$handler = \$workflow->wakeup(\$input);
            } else {
                \$handler = \$workflow->start();
            }

            \$message = '';
            foreach (\$handler->streamEvents() as \$event) {
                if (\$event instanceof \\App\Neuron\\{$nomeDoSistema}\\Events\\ProgressEvent) {
                    \$message .= \$event->message;
                    \$this->stream('response', Str::markdown(\$message, ['html_input' => 'strip']), true);
                }
            }

            \$this->messages[] = [
                'who' => 'ai',
                'content' => \$handler->getResult()->get('resultado_final'),
            ];
            \$this->thinking = false;
            \$this->interrupted = false;
            \$this->dispatch('scroll-bottom');

        } catch (WorkflowInterrupt \$interrupt) {
            \$this->interrupted = true;
            \$this->messages[] = [
                'who' => 'ai',
                'content' => \$interrupt->getData()['question'],
            ];
            \$this->thinking = false;
        }
    }
}";
}

function generateViewContent($nomeDoSistema, $nomeDoSistemaKebab) {
    return "<div class=\"max-w-4xl mx-auto p-6\">
    <div class=\"bg-white rounded-lg shadow-lg\">
        <div class=\"p-6 border-b\">
            <h2 class=\"text-2xl font-bold text-gray-800\">{$nomeDoSistema}</h2>
            <p class=\"text-gray-600\">Sistema multi-agêntico para processamento inteligente</p>
        </div>
        
        <div class=\"h-96 overflow-y-auto p-6 space-y-4\" id=\"messages\">
            @foreach(\$messages as \$message)
                <div class=\"flex {{\$message['who'] === 'user' ? 'justify-end' : 'justify-start'}}\">
                    <div class=\"max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{\$message['who'] === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800'}}\">
                        {!! \$message['content'] !!}
                    </div>
                </div>
            @endforeach
            
            @if(\$thinking)
                <div class=\"flex justify-start\">
                    <div class=\"bg-gray-200 text-gray-800 px-4 py-2 rounded-lg\">
                        <div class=\"flex items-center space-x-2\">
                            <div class=\"animate-spin rounded-full h-4 w-4 border-b-2 border-gray-600\"></div>
                            <span>Processando...</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class=\"p-6 border-t\">
            <form wire:submit.prevent=\"enviarMensagem\" class=\"flex space-x-4\">
                <input 
                    type=\"text\" 
                    wire:model=\"input\" 
                    placeholder=\"Digite sua mensagem...\"
                    class=\"flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500\"
                    {{\$thinking ? 'disabled' : ''}}
                >
                <button 
                    type=\"submit\" 
                    class=\"px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50\"
                    {{\$thinking ? 'disabled' : ''}}
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
</script>";
}

function generateRouteContent($nomeDoSistema, $nomeDoSistemaKebab) {
    return "<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\\{$nomeDoSistema}Component;

Route::get('/{$nomeDoSistemaKebab}', function () {
    return view('{$nomeDoSistemaKebab}');
})->name('{$nomeDoSistemaKebab}');

Route::get('/{$nomeDoSistemaKebab}/component', {$nomeDoSistema}Component::class)->name('{$nomeDoSistemaKebab}.component');";
}
