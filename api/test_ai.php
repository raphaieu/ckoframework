<?php

require_once __DIR__ . '/vendor/autoload.php';

use CkoFramework\AI\Agents\FinanceAgent;
use Illuminate\Database\Capsule\Manager as DB;

// Carregar configurações
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configurar banco de dados
$dbPath = __DIR__ . '/database/database.sqlite';
$dbConfig = [
    'driver' => 'sqlite',
    'database' => $dbPath,
    'prefix' => '',
];

// Configurar Eloquent
$capsule = new DB;
$capsule->addConnection($dbConfig);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🤖 Testando FinanceAgent com LLM...\n\n";

// Verificar se API key está configurada
$apiKey = $_ENV['AI_API_KEY'] ?? '';
if (empty($apiKey) || $apiKey === 'your_openai_api_key_here') {
    echo "❌ API Key não configurada!\n";
    echo "Por favor, configure AI_API_KEY no arquivo .env\n";
    echo "Exemplo: AI_API_KEY=sk-...\n\n";
    exit(1);
}

echo "✅ API Key configurada: " . substr($apiKey, 0, 10) . "...\n";
echo "🔧 Provider: " . ($_ENV['AI_PROVIDER'] ?? 'openai') . "\n";
echo "🧠 Model: " . ($_ENV['AI_MODEL'] ?? 'gpt-4') . "\n\n";

try {
    $agent = new FinanceAgent();
    
    // Teste 1: Análise geral
    echo "📊 Teste 1: Análise geral do fluxo de caixa\n";
    echo "=" . str_repeat("=", 50) . "\n";
    
    $result1 = $agent->analyzeFinance("Analise meu fluxo de caixa e me dê insights sobre minha situação financeira");
    echo "Query: " . $result1['query'] . "\n";
    echo "LLM Provider: " . $result1['llm_provider'] . "\n";
    echo "LLM Model: " . $result1['llm_model'] . "\n";
    echo "Response:\n" . $result1['response'] . "\n\n";
    
    // Teste 2: Análise de trades
    echo "📈 Teste 2: Análise de performance de trades\n";
    echo "=" . str_repeat("=", 50) . "\n";
    
    $result2 = $agent->analyzeFinance("Analise minha performance de trading e me dê recomendações para melhorar");
    echo "Query: " . $result2['query'] . "\n";
    echo "Response:\n" . $result2['response'] . "\n\n";
    
    // Teste 3: Análise de portfólio
    echo "💼 Teste 3: Análise de portfólio\n";
    echo "=" . str_repeat("=", 50) . "\n";
    
    $result3 = $agent->analyzeFinance("Analise meu portfólio de investimentos e sugira rebalanceamento");
    echo "Query: " . $result3['query'] . "\n";
    echo "Response:\n" . $result3['response'] . "\n\n";
    
    echo "🎉 Todos os testes executados com sucesso!\n";
    echo "🔗 Agora você pode testar via API: http://localhost:8000/api/ai/analyze\n";
    
} catch (Exception $e) {
    echo "❌ Erro durante o teste: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
