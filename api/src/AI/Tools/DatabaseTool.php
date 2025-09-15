<?php

namespace CkoFramework\AI\Tools;

use Illuminate\Database\Capsule\Manager as DB;

/**
 * Database Tool for AI Agents
 * 
 * Provides access to financial data from the database
 */
class DatabaseTool
{
    public function __construct()
    {
        // Database connection is already set up in the API
    }

    /**
     * Get cashflow data for analysis
     */
    public function getCashflowData(array $filters = []): array
    {
        $query = DB::table('cashflow_transactions');
        
        // Apply filters
        if (isset($filters['start_date'])) {
            $query->where('occurred_at', '>=', $filters['start_date']);
        }
        
        if (isset($filters['end_date'])) {
            $query->where('occurred_at', '<=', $filters['end_date']);
        }
        
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        $transactions = $query->orderBy('occurred_at', 'desc')->get();
        
        // Calculate summary metrics
        $income = $transactions->where('type', 'income')->sum('amount');
        $expenses = $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expenses;
        
        return [
            'transactions' => $transactions->toArray(),
            'summary' => [
                'total_income' => $income,
                'total_expenses' => $expenses,
                'balance' => $balance,
                'transaction_count' => $transactions->count(),
                'income_count' => $transactions->where('type', 'income')->count(),
                'expense_count' => $transactions->where('type', 'expense')->count()
            ],
            'period' => [
                'start_date' => $filters['start_date'] ?? null,
                'end_date' => $filters['end_date'] ?? null
            ]
        ];
    }

    /**
     * Get trades data for analysis
     */
    public function getTradesData(array $filters = []): array
    {
        $query = DB::table('trades');
        
        // Apply filters
        if (isset($filters['start_date'])) {
            $query->where('executed_at', '>=', $filters['start_date']);
        }
        
        if (isset($filters['end_date'])) {
            $query->where('executed_at', '<=', $filters['end_date']);
        }
        
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        $trades = $query->orderBy('executed_at', 'desc')->get();
        
        // Calculate trading metrics
        $totalPnL = $trades->sum('pnl');
        $winningTrades = $trades->where('pnl', '>', 0);
        $losingTrades = $trades->where('pnl', '<', 0);
        $winRate = $trades->count() > 0 ? ($winningTrades->count() / $trades->count()) * 100 : 0;
        
        return [
            'trades' => $trades->toArray(),
            'metrics' => [
                'total_trades' => $trades->count(),
                'total_pnl' => $totalPnL,
                'winning_trades' => $winningTrades->count(),
                'losing_trades' => $losingTrades->count(),
                'win_rate' => round($winRate, 2),
                'avg_win' => $winningTrades->count() > 0 ? $winningTrades->avg('pnl') : 0,
                'avg_loss' => $losingTrades->count() > 0 ? $losingTrades->avg('pnl') : 0
            ],
            'period' => [
                'start_date' => $filters['start_date'] ?? null,
                'end_date' => $filters['end_date'] ?? null
            ]
        ];
    }

    /**
     * Get holdings data for analysis
     */
    public function getHoldingsData(array $filters = []): array
    {
        $query = DB::table('holdings');
        
        // Apply filters
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        $holdings = $query->get();
        
        // Calculate portfolio metrics
        $totalValue = $holdings->sum('current_value');
        $totalCost = $holdings->sum('total_cost');
        $totalPnL = $totalValue - $totalCost;
        $totalPnLPercent = $totalCost > 0 ? ($totalPnL / $totalCost) * 100 : 0;
        
        return [
            'holdings' => $holdings->toArray(),
            'portfolio' => [
                'total_holdings' => $holdings->count(),
                'total_value' => $totalValue,
                'total_cost' => $totalCost,
                'total_pnl' => $totalPnL,
                'total_pnl_percent' => round($totalPnLPercent, 2)
            ]
        ];
    }

    /**
     * Get recent transactions for context
     */
    public function getRecentTransactions(int $limit = 10): array
    {
        $cashflow = DB::table('cashflow_transactions')
            ->orderBy('occurred_at', 'desc')
            ->limit($limit)
            ->get();
            
        $trades = DB::table('trades')
            ->orderBy('executed_at', 'desc')
            ->limit($limit)
            ->get();
        
        return [
            'cashflow' => $cashflow->toArray(),
            'trades' => $trades->toArray()
        ];
    }

    /**
     * Get category breakdown for expenses
     */
    public function getExpenseCategories(array $filters = []): array
    {
        $query = DB::table('cashflow_transactions')
            ->where('type', 'expense');
            
        if (isset($filters['start_date'])) {
            $query->where('occurred_at', '>=', $filters['start_date']);
        }
        
        if (isset($filters['end_date'])) {
            $query->where('occurred_at', '<=', $filters['end_date']);
        }
        
        $categories = $query->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->orderBy('total', 'desc')
            ->get();
            
        return $categories->toArray();
    }
}
