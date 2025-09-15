<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/database/migrations/001_create_financial_tables.php';
require_once __DIR__ . '/database/seeders/FinancialDataSeeder.php';

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

echo "🚀 Configurando banco de dados...\n\n";

try {
    // Executar migration
    echo "📋 Executando migration...\n";
    $migration = new CreateFinancialTables();
    $migration->up();
    echo "✅ Migration executada com sucesso!\n\n";

    // Executar seeder
    echo "🌱 Executando seeder...\n";
    $seeder = new FinancialDataSeeder();
    $seeder->run();
    echo "✅ Seeder executado com sucesso!\n\n";

    // Mostrar estatísticas
    echo "📊 Estatísticas do banco:\n";
    echo "- Categorias: " . DB::table('categories')->count() . "\n";
    echo "- Transações: " . DB::table('cashflow_transactions')->count() . "\n";
    echo "- Trades: " . DB::table('trades')->count() . "\n";
    echo "- Holdings: " . DB::table('holdings')->count() . "\n\n";

    // Mostrar resumo financeiro
    $totalIncome = DB::table('cashflow_transactions')
        ->where('type', 'income')
        ->sum('amount');
    
    $totalExpenses = DB::table('cashflow_transactions')
        ->where('type', 'expense')
        ->sum('amount');
    
    $balance = $totalIncome - $totalExpenses;
    
    echo "💰 Resumo Financeiro:\n";
    echo "- Total de Receitas: R$ " . number_format($totalIncome, 2, ',', '.') . "\n";
    echo "- Total de Despesas: R$ " . number_format($totalExpenses, 2, ',', '.') . "\n";
    echo "- Saldo: R$ " . number_format($balance, 2, ',', '.') . "\n\n";

    echo "🎉 Banco de dados configurado com sucesso!\n";
    echo "🔗 Agora você pode testar a API: http://localhost:8000/api/ai/status\n";

} catch (Exception $e) {
    echo "❌ Erro ao configurar banco: " . $e->getMessage() . "\n";
    exit(1);
}
