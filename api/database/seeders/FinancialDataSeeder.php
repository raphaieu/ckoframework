<?php

use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;

/**
 * Seeder para dados financeiros fictícios
 */
class FinancialDataSeeder
{
    public function run()
    {
        echo "🌱 Iniciando seed de dados financeiros...\n";

        // Limpar dados existentes
        $this->cleanData();

        // Criar categorias
        $this->createCategories();

        // Criar transações de fluxo de caixa
        $this->createCashflowTransactions();

        // Criar trades
        $this->createTrades();

        // Criar holdings
        $this->createHoldings();

        echo "✅ Seed concluído com sucesso!\n";
    }

    private function cleanData()
    {
        echo "🧹 Limpando dados existentes...\n";
        
        DB::table('holdings')->delete();
        DB::table('trades')->delete();
        DB::table('cashflow_transactions')->delete();
        DB::table('categories')->delete();
    }

    private function createCategories()
    {
        echo "📂 Criando categorias...\n";

        $categories = [
            // Categorias de Receita
            ['name' => 'Salário', 'color' => '#10B981', 'type' => 'income'],
            ['name' => 'Freelance', 'color' => '#059669', 'type' => 'income'],
            ['name' => 'Investimentos', 'color' => '#047857', 'type' => 'income'],
            ['name' => 'Vendas', 'color' => '#065F46', 'type' => 'income'],
            
            // Categorias de Despesa
            ['name' => 'Alimentação', 'color' => '#EF4444', 'type' => 'expense'],
            ['name' => 'Transporte', 'color' => '#DC2626', 'type' => 'expense'],
            ['name' => 'Moradia', 'color' => '#B91C1C', 'type' => 'expense'],
            ['name' => 'Saúde', 'color' => '#991B1B', 'type' => 'expense'],
            ['name' => 'Educação', 'color' => '#7F1D1D', 'type' => 'expense'],
            ['name' => 'Lazer', 'color' => '#F59E0B', 'type' => 'expense'],
            ['name' => 'Tecnologia', 'color' => '#D97706', 'type' => 'expense'],
            ['name' => 'Investimentos', 'color' => '#B45309', 'type' => 'expense'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert(array_merge($category, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }
    }

    private function createCashflowTransactions()
    {
        echo "💰 Criando transações de fluxo de caixa...\n";

        $categories = DB::table('categories')->get();
        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');

        $transactions = [];

        // Últimos 3 meses de dados
        for ($i = 0; $i < 90; $i++) {
            $date = Carbon::now()->subDays($i);
            
            // 1-2 transações de receita por dia
            if (rand(1, 3) <= 2) {
                $category = $incomeCategories->random();
                $transactions[] = [
                    'description' => $this->getIncomeDescription($category->name),
                    'amount' => $this->getIncomeAmount($category->name),
                    'type' => 'income',
                    'category_id' => $category->id,
                    'occurred_at' => $date->format('Y-m-d H:i:s'),
                    'notes' => rand(1, 10) <= 3 ? $this->getRandomNote() : null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
                ];
            }

            // 2-4 transações de despesa por dia
            $expenseCount = rand(2, 4);
            for ($j = 0; $j < $expenseCount; $j++) {
                $category = $expenseCategories->random();
                $transactions[] = [
                    'description' => $this->getExpenseDescription($category->name),
                    'amount' => $this->getExpenseAmount($category->name),
                    'type' => 'expense',
                    'category_id' => $category->id,
                    'occurred_at' => $date->format('Y-m-d H:i:s'),
                    'notes' => rand(1, 10) <= 2 ? $this->getRandomNote() : null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
                ];
            }
        }

        // Inserir em lotes
        $chunks = array_chunk($transactions, 100);
        foreach ($chunks as $chunk) {
            DB::table('cashflow_transactions')->insert($chunk);
        }
    }

    private function createTrades()
    {
        echo "📈 Criando trades...\n";

        $assets = ['PETR4', 'VALE3', 'ITUB4', 'BBDC4', 'ABEV3', 'WEGE3', 'MGLU3', 'LREN3'];
        $types = ['day_trade', 'forex', 'crypto'];
        
        $trades = [];

        // Últimos 30 dias de trades
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i);
            $tradesPerDay = rand(1, 5);
            
            for ($j = 0; $j < $tradesPerDay; $j++) {
                $asset = $assets[array_rand($assets)];
                $type = $types[array_rand($types)];
                $side = rand(0, 1) ? 'buy' : 'sell';
                $quantity = rand(100, 1000);
                $entryPrice = rand(1000, 50000) / 100;
                $exitPrice = $side === 'sell' ? rand(1000, 50000) / 100 : null;
                $pnl = $side === 'sell' ? ($exitPrice - $entryPrice) * $quantity : 0;
                $fees = $quantity * 0.01; // 1% de taxa

                $trades[] = [
                    'asset' => $asset,
                    'type' => $type,
                    'side' => $side,
                    'quantity' => $quantity,
                    'entry_price' => $entryPrice,
                    'exit_price' => $exitPrice,
                    'pnl' => $pnl,
                    'fees' => $fees,
                    'executed_at' => $date->format('Y-m-d H:i:s'),
                    'notes' => rand(1, 10) <= 3 ? $this->getRandomNote() : null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
                ];
            }
        }

        DB::table('trades')->insert($trades);
    }

    private function createHoldings()
    {
        echo "💼 Criando holdings...\n";

        $assets = [
            ['name' => 'PETR4', 'type' => 'swing'],
            ['name' => 'VALE3', 'type' => 'swing'],
            ['name' => 'ITUB4', 'type' => 'swing'],
            ['name' => 'BBDC4', 'type' => 'swing'],
            ['name' => 'BTC', 'type' => 'crypto'],
            ['name' => 'ETH', 'type' => 'crypto'],
            ['name' => 'ADA', 'type' => 'crypto'],
            ['name' => 'SOL', 'type' => 'crypto'],
        ];

        $holdings = [];

        foreach ($assets as $asset) {
            $quantity = rand(10, 1000);
            $entryPrice = rand(1000, 50000) / 100;
            $currentPrice = $entryPrice * (rand(80, 150) / 100); // Variação de -20% a +50%
            $totalCost = $quantity * $entryPrice;
            $currentValue = $quantity * $currentPrice;
            $pnl = $currentValue - $totalCost;
            $pnlPercent = $totalCost > 0 ? ($pnl / $totalCost) * 100 : 0;

            $holdings[] = [
                'asset' => $asset['name'],
                'type' => $asset['type'],
                'quantity' => $quantity,
                'entry_price' => $entryPrice,
                'current_price' => $currentPrice,
                'total_cost' => $totalCost,
                'current_value' => $currentValue,
                'pnl' => $pnl,
                'pnl_percent' => $pnlPercent,
                'bought_at' => Carbon::now()->subDays(rand(1, 365))->format('Y-m-d H:i:s'),
                'notes' => rand(1, 10) <= 2 ? $this->getRandomNote() : null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('holdings')->insert($holdings);
    }

    private function getIncomeDescription($category)
    {
        $descriptions = [
            'Salário' => ['Salário mensal', 'Pagamento de salário', 'Remuneração'],
            'Freelance' => ['Projeto freelance', 'Trabalho autônomo', 'Serviço prestado'],
            'Investimentos' => ['Dividendos', 'Juros de investimento', 'Rendimento'],
            'Vendas' => ['Venda de produto', 'Venda de serviço', 'Receita de venda'],
        ];

        return $descriptions[$category][array_rand($descriptions[$category])];
    }

    private function getExpenseDescription($category)
    {
        $descriptions = [
            'Alimentação' => ['Supermercado', 'Restaurante', 'Delivery', 'Padaria'],
            'Transporte' => ['Uber', 'Gasolina', 'Estacionamento', 'Ônibus'],
            'Moradia' => ['Aluguel', 'Condomínio', 'Energia elétrica', 'Água'],
            'Saúde' => ['Farmácia', 'Consulta médica', 'Exames', 'Plano de saúde'],
            'Educação' => ['Curso online', 'Livros', 'Material escolar', 'Faculdade'],
            'Lazer' => ['Cinema', 'Streaming', 'Viagem', 'Entretenimento'],
            'Tecnologia' => ['Software', 'Hardware', 'Assinatura', 'Manutenção'],
            'Investimentos' => ['Aplicação financeira', 'Compra de ações', 'Fundo de investimento'],
        ];

        return $descriptions[$category][array_rand($descriptions[$category])];
    }

    private function getIncomeAmount($category)
    {
        $ranges = [
            'Salário' => [5000, 15000],
            'Freelance' => [500, 3000],
            'Investimentos' => [100, 1000],
            'Vendas' => [200, 2000],
        ];

        $range = $ranges[$category] ?? [100, 1000];
        return rand($range[0] * 100, $range[1] * 100) / 100;
    }

    private function getExpenseAmount($category)
    {
        $ranges = [
            'Alimentação' => [20, 200],
            'Transporte' => [10, 100],
            'Moradia' => [500, 2000],
            'Saúde' => [50, 500],
            'Educação' => [100, 1000],
            'Lazer' => [30, 300],
            'Tecnologia' => [50, 500],
            'Investimentos' => [100, 2000],
        ];

        $range = $ranges[$category] ?? [10, 100];
        return rand($range[0] * 100, $range[1] * 100) / 100;
    }

    private function getRandomNote()
    {
        $notes = [
            'Pagamento via PIX',
            'Transferência bancária',
            'Cartão de crédito',
            'Dinheiro',
            'Nota fiscal emitida',
            'Reembolso',
            'Desconto aplicado',
            'Taxa adicional',
        ];

        return $notes[array_rand($notes)];
    }
}

// Função helper para now()
if (!function_exists('now')) {
    function now() {
        return new DateTime();
    }
}
