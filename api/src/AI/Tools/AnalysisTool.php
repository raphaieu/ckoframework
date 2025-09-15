<?php

namespace CkoFramework\AI\Tools;

/**
 * Analysis Tool for AI Agents
 * 
 * Provides financial analysis and calculation capabilities
 */
class AnalysisTool
{
    /**
     * Analyze trends in financial data
     */
    public function analyzeTrends(array $data, string $type = 'cashflow'): array
    {
        switch ($type) {
            case 'cashflow':
                return $this->analyzeCashflowTrends($data);
            case 'trades':
                return $this->analyzeTradingTrends($data);
            case 'holdings':
                return $this->analyzePortfolioTrends($data);
            default:
                return ['error' => 'Unknown analysis type'];
        }
    }

    /**
     * Calculate financial metrics
     */
    public function calculateMetrics(array $data, string $type = 'cashflow'): array
    {
        switch ($type) {
            case 'cashflow':
                return $this->calculateCashflowMetrics($data);
            case 'trades':
                return $this->calculateTradingMetrics($data);
            case 'holdings':
                return $this->calculatePortfolioMetrics($data);
            default:
                return ['error' => 'Unknown metrics type'];
        }
    }

    /**
     * Generate actionable insights
     */
    public function generateInsights(array $data, string $type = 'cashflow'): array
    {
        $insights = [];
        
        switch ($type) {
            case 'cashflow':
                $insights = $this->generateCashflowInsights($data);
                break;
            case 'trades':
                $insights = $this->generateTradingInsights($data);
                break;
            case 'holdings':
                $insights = $this->generatePortfolioInsights($data);
                break;
        }
        
        return [
            'type' => $type,
            'insights' => $insights,
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Analyze cashflow trends
     */
    private function analyzeCashflowTrends(array $data): array
    {
        $transactions = $data['transactions'] ?? [];
        $summary = $data['summary'] ?? [];
        
        $trends = [];
        
        // Monthly trend analysis
        $monthlyData = [];
        foreach ($transactions as $transaction) {
            $month = date('Y-m', strtotime($transaction['occurred_at']));
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = ['income' => 0, 'expense' => 0];
            }
            
            if ($transaction['type'] === 'income') {
                $monthlyData[$month]['income'] += $transaction['amount'];
            } else {
                $monthlyData[$month]['expense'] += $transaction['amount'];
            }
        }
        
        // Calculate month-over-month growth
        $months = array_keys($monthlyData);
        sort($months);
        
        if (count($months) >= 2) {
            $lastMonth = $monthlyData[$months[count($months) - 1]];
            $previousMonth = $monthlyData[$months[count($months) - 2]];
            
            $incomeGrowth = $previousMonth['income'] > 0 
                ? (($lastMonth['income'] - $previousMonth['income']) / $previousMonth['income']) * 100 
                : 0;
                
            $expenseGrowth = $previousMonth['expense'] > 0 
                ? (($lastMonth['expense'] - $previousMonth['expense']) / $previousMonth['expense']) * 100 
                : 0;
            
            $trends['monthly_growth'] = [
                'income_growth' => round($incomeGrowth, 2),
                'expense_growth' => round($expenseGrowth, 2)
            ];
        }
        
        // Spending pattern analysis
        $expenseTransactions = array_filter($transactions, fn($t) => $t['type'] === 'expense');
        $avgExpense = count($expenseTransactions) > 0 
            ? array_sum(array_column($expenseTransactions, 'amount')) / count($expenseTransactions) 
            : 0;
            
        $trends['spending_patterns'] = [
            'average_expense' => round($avgExpense, 2),
            'total_expenses' => $summary['total_expenses'] ?? 0,
            'expense_count' => $summary['expense_count'] ?? 0
        ];
        
        return $trends;
    }

    /**
     * Analyze trading trends
     */
    private function analyzeTradingTrends(array $data): array
    {
        $trades = $data['trades'] ?? [];
        $metrics = $data['metrics'] ?? [];
        
        $trends = [];
        
        // Win rate trend
        $trends['performance'] = [
            'win_rate' => $metrics['win_rate'] ?? 0,
            'total_trades' => $metrics['total_trades'] ?? 0,
            'total_pnl' => $metrics['total_pnl'] ?? 0
        ];
        
        // Daily P&L analysis
        $dailyPnL = [];
        foreach ($trades as $trade) {
            $date = date('Y-m-d', strtotime($trade['executed_at']));
            if (!isset($dailyPnL[$date])) {
                $dailyPnL[$date] = 0;
            }
            $dailyPnL[$date] += $trade['pnl'];
        }
        
        $trends['daily_pnl'] = $dailyPnL;
        
        return $trends;
    }

    /**
     * Analyze portfolio trends
     */
    private function analyzePortfolioTrends(array $data): array
    {
        $holdings = $data['holdings'] ?? [];
        $portfolio = $data['portfolio'] ?? [];
        
        $trends = [];
        
        // Asset allocation
        $totalValue = $portfolio['total_value'] ?? 0;
        $allocation = [];
        
        foreach ($holdings as $holding) {
            $percentage = $totalValue > 0 ? ($holding['current_value'] / $totalValue) * 100 : 0;
            $allocation[$holding['asset']] = round($percentage, 2);
        }
        
        $trends['asset_allocation'] = $allocation;
        $trends['portfolio_metrics'] = $portfolio;
        
        return $trends;
    }

    /**
     * Calculate cashflow metrics
     */
    private function calculateCashflowMetrics(array $data): array
    {
        $summary = $data['summary'] ?? [];
        
        return [
            'balance' => $summary['balance'] ?? 0,
            'income' => $summary['total_income'] ?? 0,
            'expenses' => $summary['total_expenses'] ?? 0,
            'savings_rate' => $summary['total_income'] > 0 
                ? (($summary['balance'] / $summary['total_income']) * 100) 
                : 0
        ];
    }

    /**
     * Calculate trading metrics
     */
    private function calculateTradingMetrics(array $data): array
    {
        $metrics = $data['metrics'] ?? [];
        
        return [
            'win_rate' => $metrics['win_rate'] ?? 0,
            'total_pnl' => $metrics['total_pnl'] ?? 0,
            'avg_win' => $metrics['avg_win'] ?? 0,
            'avg_loss' => $metrics['avg_loss'] ?? 0,
            'profit_factor' => $metrics['avg_loss'] != 0 
                ? abs($metrics['avg_win'] / $metrics['avg_loss']) 
                : 0
        ];
    }

    /**
     * Calculate portfolio metrics
     */
    private function calculatePortfolioMetrics(array $data): array
    {
        $portfolio = $data['portfolio'] ?? [];
        
        return [
            'total_value' => $portfolio['total_value'] ?? 0,
            'total_cost' => $portfolio['total_cost'] ?? 0,
            'total_pnl' => $portfolio['total_pnl'] ?? 0,
            'total_pnl_percent' => $portfolio['total_pnl_percent'] ?? 0
        ];
    }

    /**
     * Generate cashflow insights
     */
    private function generateCashflowInsights(array $data): array
    {
        $insights = [];
        $summary = $data['summary'] ?? [];
        
        $balance = $summary['balance'] ?? 0;
        $income = $summary['total_income'] ?? 0;
        $expenses = $summary['total_expenses'] ?? 0;
        
        if ($balance < 0) {
            $insights[] = "⚠️ Seu saldo está negativo (R$ " . number_format($balance, 2, ',', '.') . "). Considere reduzir gastos ou aumentar a renda.";
        } elseif ($balance > $income * 0.1) {
            $insights[] = "✅ Excelente! Você está poupando " . number_format(($balance / $income) * 100, 1) . "% da sua renda.";
        }
        
        if ($expenses > $income * 0.8) {
            $insights[] = "⚠️ Seus gastos representam mais de 80% da sua renda. Considere revisar seu orçamento.";
        }
        
        return $insights;
    }

    /**
     * Generate trading insights
     */
    private function generateTradingInsights(array $data): array
    {
        $insights = [];
        $metrics = $data['metrics'] ?? [];
        
        $winRate = $metrics['win_rate'] ?? 0;
        $totalPnL = $metrics['total_pnl'] ?? 0;
        
        if ($winRate < 40) {
            $insights[] = "⚠️ Sua taxa de acerto está baixa ({$winRate}%). Considere revisar sua estratégia de trading.";
        } elseif ($winRate > 60) {
            $insights[] = "✅ Excelente taxa de acerto ({$winRate}%)! Continue com sua estratégia.";
        }
        
        if ($totalPnL < 0) {
            $insights[] = "⚠️ Seu P&L está negativo (R$ " . number_format($totalPnL, 2, ',', '.') . "). Considere pausar as operações para revisar sua estratégia.";
        }
        
        return $insights;
    }

    /**
     * Generate portfolio insights
     */
    private function generatePortfolioInsights(array $data): array
    {
        $insights = [];
        $portfolio = $data['portfolio'] ?? [];
        
        $totalPnL = $portfolio['total_pnl'] ?? 0;
        $totalPnLPercent = $portfolio['total_pnl_percent'] ?? 0;
        
        if ($totalPnLPercent > 10) {
            $insights[] = "✅ Seu portfólio está performando bem (+{$totalPnLPercent}%).";
        } elseif ($totalPnLPercent < -10) {
            $insights[] = "⚠️ Seu portfólio está com perdas significativas ({$totalPnLPercent}%). Considere rebalancear.";
        }
        
        return $insights;
    }
}
