<?php

namespace CkoFramework\AI\Tools;

use CkoFramework\AI\Core\ToolInterface;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Financial Tool for AI Analysis
 * 
 * This tool provides financial analysis capabilities to the AI agent
 * Works with both Neuron AI and MCP through unified interface
 */
class FinancialTool implements ToolInterface
{
    public function getName(): string
    {
        return 'financial_analysis';
    }

    public function getDescription(): string
    {
        return 'Provides comprehensive financial analysis including cashflow, trades, and portfolio data with insights and recommendations.';
    }

    public function getParameters(): array
    {
        return [
            'analysis_type' => [
                'type' => 'string',
                'description' => 'Type of financial analysis to perform',
                'required' => true,
                'enum' => ['cashflow', 'trades', 'portfolio', 'comprehensive'],
                'default' => 'comprehensive'
            ],
            'period' => [
                'type' => 'string',
                'description' => 'Time period for analysis',
                'required' => false,
                'enum' => ['week', 'month', 'quarter', 'year', 'all'],
                'default' => 'month'
            ],
            'filters' => [
                'type' => 'object',
                'description' => 'Optional filters for the analysis',
                'required' => false,
                'default' => []
            ]
        ];
    }

    public function execute(array $parameters): array
    {
        $analysisType = $parameters['analysis_type'] ?? 'comprehensive';
        $period = $parameters['period'] ?? 'month';
        $filters = $parameters['filters'] ?? [];

        return $this->analyze($analysisType, $period, $filters);
    }

    public function getMetadata(): array
    {
        return [
            'version' => '1.0.0',
            'author' => 'CKO Framework',
            'category' => 'financial',
            'tags' => ['finance', 'analysis', 'cashflow', 'trades', 'portfolio']
        ];
    }

    /**
     * Analyze financial data based on type and period
     */
    public function analyze(string $analysisType = 'comprehensive', string $period = 'month', array $filters = []): array
    {
        try {
            $result = match ($analysisType) {
                'cashflow' => $this->analyzeCashflow($period, $filters),
                'trades' => $this->analyzeTrades($period, $filters),
                'portfolio' => $this->analyzePortfolio($period, $filters),
                'comprehensive' => $this->analyzeComprehensive($period, $filters),
                default => throw new \InvalidArgumentException("Unknown analysis type: {$analysisType}")
            };

            return [
                'success' => true,
                'summary' => $result['summary'],
                'data' => $result['data'],
                'metadata' => [
                    'analysis_type' => $analysisType,
                    'period' => $period,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'tools_used' => $result['tools_used'] ?? []
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => "Erro na análise financeira: " . $e->getMessage(),
                'data' => [],
                'metadata' => ['error' => true, 'message' => $e->getMessage()]
            ];
        }
    }

    private function analyzeCashflow(string $period, array $filters): array
    {
        $dateRange = $this->getDateRange($period);
        $filters = array_merge($filters, $dateRange);

        $query = DB::table('cashflow_transactions');
        
        if (isset($filters['start_date'])) {
            $query->where('occurred_at', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $query->where('occurred_at', '<=', $filters['end_date']);
        }
        if (isset($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        $transactions = $query->get();
        
        $income = $transactions->where('type', 'income')->sum('amount');
        $expenses = $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expenses;

        // Calculate trends
        $monthlyData = $this->calculateMonthlyTrends($transactions);
        $categoryBreakdown = $this->getCategoryBreakdown($transactions);

        return [
            'summary' => "Análise de Fluxo de Caixa - Período: {$period}\n" .
                        "📊 Receitas: R$ " . number_format($income, 2, ',', '.') . "\n" .
                        "📉 Despesas: R$ " . number_format($expenses, 2, ',', '.') . "\n" .
                        "💰 Saldo: R$ " . number_format($balance, 2, ',', '.') . "\n" .
                        "📈 Transações: " . $transactions->count() . " (" . $transactions->where('type', 'income')->count() . " receitas, " . $transactions->where('type', 'expense')->count() . " despesas)",
            'data' => [
                'total_income' => $income,
                'total_expenses' => $expenses,
                'balance' => $balance,
                'transaction_count' => $transactions->count(),
                'monthly_trends' => $monthlyData,
                'category_breakdown' => $categoryBreakdown
            ],
            'tools_used' => ['database_query', 'trend_analysis', 'category_analysis']
        ];
    }

    private function analyzeTrades(string $period, array $filters): array
    {
        $dateRange = $this->getDateRange($period);
        $filters = array_merge($filters, $dateRange);

        $query = DB::table('trades');
        
        if (isset($filters['start_date'])) {
            $query->where('executed_at', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $query->where('executed_at', '<=', $filters['end_date']);
        }
        if (isset($filters['asset'])) {
            $query->where('asset', $filters['asset']);
        }

        $trades = $query->get();
        
        $totalPnL = $trades->sum('pnl');
        $winningTrades = $trades->where('pnl', '>', 0);
        $losingTrades = $trades->where('pnl', '<', 0);
        $winRate = $trades->count() > 0 ? ($winningTrades->count() / $trades->count()) * 100 : 0;
        
        $avgWin = $winningTrades->count() > 0 ? $winningTrades->avg('pnl') : 0;
        $avgLoss = $losingTrades->count() > 0 ? $losingTrades->avg('pnl') : 0;
        $profitFactor = $avgLoss != 0 ? abs($avgWin / $avgLoss) : 0;

        return [
            'summary' => "Análise de Trades - Período: {$period}\n" .
                        "📈 Total de Trades: " . $trades->count() . "\n" .
                        "💰 P&L Total: R$ " . number_format($totalPnL, 2, ',', '.') . "\n" .
                        "🎯 Taxa de Acerto: " . number_format($winRate, 2) . "%\n" .
                        "✅ Trades Vencedores: " . $winningTrades->count() . "\n" .
                        "❌ Trades Perdedores: " . $losingTrades->count() . "\n" .
                        "📊 Ganho Médio: R$ " . number_format($avgWin, 2, ',', '.') . "\n" .
                        "📉 Perda Média: R$ " . number_format($avgLoss, 2, ',', '.') . "\n" .
                        "⚖️ Profit Factor: " . number_format($profitFactor, 2),
            'data' => [
                'total_trades' => $trades->count(),
                'total_pnl' => $totalPnL,
                'winning_trades' => $winningTrades->count(),
                'losing_trades' => $losingTrades->count(),
                'win_rate' => $winRate,
                'avg_win' => $avgWin,
                'avg_loss' => $avgLoss,
                'profit_factor' => $profitFactor
            ],
            'tools_used' => ['database_query', 'performance_metrics', 'risk_analysis']
        ];
    }

    private function analyzePortfolio(string $period, array $filters): array
    {
        $query = DB::table('holdings');
        
        if (isset($filters['asset'])) {
            $query->where('asset', $filters['asset']);
        }

        $holdings = $query->get();
        
        $totalValue = $holdings->sum('current_value');
        $totalCost = $holdings->sum('total_cost');
        $totalPnL = $totalValue - $totalCost;
        $totalPnLPercent = $totalCost > 0 ? ($totalPnL / $totalCost) * 100 : 0;

        // Asset allocation
        $allocation = [];
        foreach ($holdings as $holding) {
            $percentage = $totalValue > 0 ? ($holding->current_value / $totalValue) * 100 : 0;
            $allocation[$holding->asset] = [
                'value' => $holding->current_value,
                'percentage' => $percentage,
                'pnl' => $holding->pnl,
                'pnl_percent' => $holding->pnl_percent
            ];
        }

        return [
            'summary' => "Análise de Portfólio\n" .
                        "💼 Total de Holdings: " . $holdings->count() . "\n" .
                        "💰 Valor Total: R$ " . number_format($totalValue, 2, ',', '.') . "\n" .
                        "💵 Custo Total: R$ " . number_format($totalCost, 2, ',', '.') . "\n" .
                        "📊 P&L Total: R$ " . number_format($totalPnL, 2, ',', '.') . " (" . number_format($totalPnLPercent, 2) . "%)\n" .
                        "📈 Alocação por Ativo: " . count($allocation) . " ativos diferentes",
            'data' => [
                'total_holdings' => $holdings->count(),
                'total_value' => $totalValue,
                'total_cost' => $totalCost,
                'total_pnl' => $totalPnL,
                'total_pnl_percent' => $totalPnLPercent,
                'asset_allocation' => $allocation
            ],
            'tools_used' => ['database_query', 'portfolio_analysis', 'allocation_calculation']
        ];
    }

    private function analyzeComprehensive(string $period, array $filters): array
    {
        $cashflow = $this->analyzeCashflow($period, $filters);
        $trades = $this->analyzeTrades($period, $filters);
        $portfolio = $this->analyzePortfolio($period, $filters);

        $summary = "📊 ANÁLISE FINANCEIRA COMPLETA - Período: {$period}\n\n";
        $summary .= "=== FLUXO DE CAIXA ===\n";
        $summary .= "💰 Saldo: R$ " . number_format($cashflow['data']['balance'], 2, ',', '.') . "\n";
        $summary .= "📈 Receitas: R$ " . number_format($cashflow['data']['total_income'], 2, ',', '.') . "\n";
        $summary .= "📉 Despesas: R$ " . number_format($cashflow['data']['total_expenses'], 2, ',', '.') . "\n\n";
        
        $summary .= "=== TRADES ===\n";
        $summary .= "📊 Total: " . $trades['data']['total_trades'] . " trades\n";
        $summary .= "💰 P&L: R$ " . number_format($trades['data']['total_pnl'], 2, ',', '.') . "\n";
        $summary .= "🎯 Taxa Acerto: " . number_format($trades['data']['win_rate'], 2) . "%\n\n";
        
        $summary .= "=== PORTFÓLIO ===\n";
        $summary .= "💼 Holdings: " . $portfolio['data']['total_holdings'] . "\n";
        $summary .= "💰 Valor: R$ " . number_format($portfolio['data']['total_value'], 2, ',', '.') . "\n";
        $summary .= "📊 P&L: " . number_format($portfolio['data']['total_pnl_percent'], 2) . "%\n";

        return [
            'summary' => $summary,
            'data' => [
                'cashflow' => $cashflow['data'],
                'trades' => $trades['data'],
                'portfolio' => $portfolio['data']
            ],
            'tools_used' => array_merge(
                $cashflow['tools_used'],
                $trades['tools_used'],
                $portfolio['tools_used']
            )
        ];
    }

    private function getDateRange(string $period): array
    {
        $now = new \DateTime();
        
        return match ($period) {
            'week' => [
                'start_date' => $now->modify('-1 week')->format('Y-m-d'),
                'end_date' => (new \DateTime())->format('Y-m-d')
            ],
            'month' => [
                'start_date' => $now->modify('-1 month')->format('Y-m-d'),
                'end_date' => (new \DateTime())->format('Y-m-d')
            ],
            'quarter' => [
                'start_date' => $now->modify('-3 months')->format('Y-m-d'),
                'end_date' => (new \DateTime())->format('Y-m-d')
            ],
            'year' => [
                'start_date' => $now->modify('-1 year')->format('Y-m-d'),
                'end_date' => (new \DateTime())->format('Y-m-d')
            ],
            default => []
        };
    }

    private function calculateMonthlyTrends($transactions): array
    {
        $monthlyData = [];
        foreach ($transactions as $transaction) {
            $month = date('Y-m', strtotime($transaction->occurred_at));
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = ['income' => 0, 'expense' => 0];
            }
            
            if ($transaction->type === 'income') {
                $monthlyData[$month]['income'] += $transaction->amount;
            } else {
                $monthlyData[$month]['expense'] += $transaction->amount;
            }
        }
        
        return $monthlyData;
    }

    private function getCategoryBreakdown($transactions): array
    {
        $categories = DB::table('categories')->get()->keyBy('id');
        $breakdown = [];
        
        foreach ($transactions as $transaction) {
            $category = $categories->get($transaction->category_id);
            if ($category) {
                $key = $category->name;
                if (!isset($breakdown[$key])) {
                    $breakdown[$key] = ['income' => 0, 'expense' => 0, 'count' => 0];
                }
                
                if ($transaction->type === 'income') {
                    $breakdown[$key]['income'] += $transaction->amount;
                } else {
                    $breakdown[$key]['expense'] += $transaction->amount;
                }
                $breakdown[$key]['count']++;
            }
        }
        
        return $breakdown;
    }
}
