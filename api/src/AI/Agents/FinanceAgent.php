<?php

namespace CkoFramework\AI\Agents;

use NeuronAI\Agent;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\Providers\Anthropic\Anthropic;
use NeuronAI\Providers\Gemini\Gemini;
use CkoFramework\AI\Tools\DatabaseTool;
use CkoFramework\AI\Tools\AnalysisTool;
use CkoFramework\AI\Tools\FinancialTool;
use CkoFramework\AI\Core\ToolRegistry;

/**
 * Finance Agent using Neuron AI
 * 
 * This agent handles financial analysis and insights using LLM + tools
 */
class FinanceAgent extends Agent
{
    private DatabaseTool $databaseTool;
    private AnalysisTool $analysisTool;
    private ToolRegistry $toolRegistry;

    public function __construct()
    {
        $this->databaseTool = new DatabaseTool();
        $this->analysisTool = new AnalysisTool();
        
        // Initialize tool registry
        $this->toolRegistry = new ToolRegistry();
        
        // Register financial tool
        $this->toolRegistry->registerTool(new FinancialTool());
        
        // Add tools to Neuron AI for tool calling
        $this->addTool($this->toolRegistry->getNeuronAdapter('financial_analysis'));
    }

    protected function provider(): AIProviderInterface
    {
        $provider = $_ENV['AI_PROVIDER'] ?? 'openai';
        $apiKey = $_ENV['AI_API_KEY'] ?? '';
        $model = $_ENV['AI_MODEL'] ?? 'gpt-4';

        return match ($provider) {
            'openai' => new OpenAI($apiKey, $model),
            'anthropic' => new Anthropic($apiKey, $model),
            'gemini' => new Gemini($apiKey, $model),
            default => new OpenAI($apiKey, $model)
        };
    }

    /**
     * Analyze financial data based on user query
     */
    public function analyzeFinance(string $query): array
    {
        try {
            // Get financial data
            $cashflowData = $this->databaseTool->getCashflowData();
            $tradesData = $this->databaseTool->getTradesData();
            $holdingsData = $this->databaseTool->getHoldingsData();
            
            // Prepare context for LLM
            $context = $this->prepareFinancialContext($cashflowData, $tradesData, $holdingsData);
            
            // Create system prompt
            $systemPrompt = $this->getSystemPrompt();
            
            // Create user message with context
            $userMessage = $this->createUserMessage($query, $context);
            
            // Call LLM
            $response = $this->chat([
                new UserMessage($systemPrompt . "\n\n" . $userMessage)
            ]);
            
            return [
                'query' => $query,
                'response' => $response->getContent(),
                'data' => [
                    'cashflow' => $cashflowData['summary'],
                    'trades' => $tradesData['metrics'],
                    'holdings' => $holdingsData['portfolio']
                ],
                'timestamp' => date('Y-m-d H:i:s'),
                'llm_provider' => $_ENV['AI_PROVIDER'] ?? 'unknown',
                'llm_model' => $_ENV['AI_MODEL'] ?? 'unknown'
            ];
            
        } catch (\Exception $e) {
            // Fallback to simple analysis if LLM fails
            return $this->getFallbackAnalysis($query);
        }
    }

    /**
     * Get cashflow insights
     */
    public function getCashflowInsights(string $period = 'month'): array
    {
        $query = "Analyze my cashflow for the last {$period}. Show me income vs expenses, trends, and any concerns.";
        
        return $this->analyzeFinance($query);
    }

    /**
     * Get trading performance analysis
     */
    public function getTradingAnalysis(string $period = 'month'): array
    {
        $query = "Analyze my trading performance for the last {$period}. Show me P&L, win rate, best/worst trades, and recommendations.";
        
        return $this->analyzeFinance($query);
    }

    /**
     * Get portfolio analysis
     */
    public function getPortfolioAnalysis(): array
    {
        $query = "Analyze my investment portfolio. Show me asset allocation, performance, risk metrics, and rebalancing suggestions.";
        
        return $this->analyzeFinance($query);
    }

    /**
     * Prepare financial context for LLM
     */
    private function prepareFinancialContext(array $cashflowData, array $tradesData, array $holdingsData): string
    {
        $context = "=== DADOS FINANCEIROS ATUAIS ===\n\n";
        
        // Cashflow context
        $context .= "FLUXO DE CAIXA:\n";
        $context .= "- Total de Receitas: R$ " . number_format($cashflowData['summary']['total_income'], 2, ',', '.') . "\n";
        $context .= "- Total de Despesas: R$ " . number_format($cashflowData['summary']['total_expenses'], 2, ',', '.') . "\n";
        $context .= "- Saldo Atual: R$ " . number_format($cashflowData['summary']['balance'], 2, ',', '.') . "\n";
        $context .= "- Total de Transações: " . $cashflowData['summary']['transaction_count'] . "\n";
        $context .= "- Receitas: " . $cashflowData['summary']['income_count'] . " | Despesas: " . $cashflowData['summary']['expense_count'] . "\n\n";
        
        // Trades context
        $context .= "TRADES:\n";
        $context .= "- Total de Trades: " . $tradesData['metrics']['total_trades'] . "\n";
        $context .= "- P&L Total: R$ " . number_format($tradesData['metrics']['total_pnl'], 2, ',', '.') . "\n";
        $context .= "- Taxa de Acerto: " . $tradesData['metrics']['win_rate'] . "%\n";
        $context .= "- Trades Vencedores: " . $tradesData['metrics']['winning_trades'] . "\n";
        $context .= "- Trades Perdedores: " . $tradesData['metrics']['losing_trades'] . "\n";
        $context .= "- Ganho Médio: R$ " . number_format($tradesData['metrics']['avg_win'], 2, ',', '.') . "\n";
        $context .= "- Perda Média: R$ " . number_format($tradesData['metrics']['avg_loss'], 2, ',', '.') . "\n\n";
        
        // Holdings context
        $context .= "PORTFÓLIO:\n";
        $context .= "- Total de Holdings: " . $holdingsData['portfolio']['total_holdings'] . "\n";
        $context .= "- Valor Total: R$ " . number_format($holdingsData['portfolio']['total_value'], 2, ',', '.') . "\n";
        $context .= "- Custo Total: R$ " . number_format($holdingsData['portfolio']['total_cost'], 2, ',', '.') . "\n";
        $context .= "- P&L Total: R$ " . number_format($holdingsData['portfolio']['total_pnl'], 2, ',', '.') . "\n";
        $context .= "- P&L %: " . $holdingsData['portfolio']['total_pnl_percent'] . "%\n\n";
        
        return $context;
    }

    /**
     * Create user message with context
     */
    private function createUserMessage(string $query, string $context): string
    {
        return "CONTEXTO FINANCEIRO:\n" . $context . "\nPERGUNTA DO USUÁRIO: " . $query . "\n\nPor favor, analise os dados financeiros fornecidos e responda à pergunta do usuário de forma detalhada e acionável. Use os números específicos fornecidos e dê recomendações práticas.";
    }

    /**
     * Get fallback analysis when LLM fails
     */
    private function getFallbackAnalysis(string $query): array
    {
        $cashflowData = $this->databaseTool->getCashflowData();
        $tradesData = $this->databaseTool->getTradesData();
        $holdingsData = $this->databaseTool->getHoldingsData();
        
        $response = "Análise financeira para: " . $query . "\n\n";
        $response .= "📊 Resumo dos Dados:\n";
        $response .= "- Transações de Fluxo de Caixa: " . $cashflowData['summary']['transaction_count'] . "\n";
        $response .= "- Saldo Atual: R$ " . number_format($cashflowData['summary']['balance'], 2, ',', '.') . "\n";
        $response .= "- Trades Realizados: " . $tradesData['metrics']['total_trades'] . "\n";
        $response .= "- Holdings no Portfólio: " . $holdingsData['portfolio']['total_holdings'] . "\n";
        $response .= "\n⚠️ Análise simplificada (LLM indisponível)";
        
        return [
            'query' => $query,
            'response' => $response,
            'data' => [
                'cashflow' => $cashflowData['summary'],
                'trades' => $tradesData['metrics'],
                'holdings' => $holdingsData['portfolio']
            ],
            'timestamp' => date('Y-m-d H:i:s'),
            'llm_provider' => 'fallback',
            'llm_model' => 'none'
        ];
    }

    /**
     * Get system prompt for financial analysis
     */
    private function getSystemPrompt(): string
    {
        return "Você é um assistente de análise financeira especializado. Sua função é analisar dados financeiros e fornecer insights acionáveis.

INSTRUÇÕES:
1. Analise os dados financeiros fornecidos no contexto
2. Use números específicos e percentuais quando relevante
3. Identifique tendências e padrões
4. Forneça recomendações práticas e acionáveis
5. Seja conservador e consciente de riscos
6. Formate números em Real brasileiro (R$)
7. Use emojis para tornar a análise mais visual
8. Estruture a resposta de forma clara e organizada

FORMATO DE RESPOSTA:
- Resumo executivo
- Análise detalhada por categoria
- Tendências identificadas
- Recomendações específicas
- Alertas de risco (se houver)

Seja preciso, profissional e útil na sua análise.";
    }

    /**
     * Get list of tools used in the last conversation
     */
    private function getUsedTools(): array
    {
        // This would be implemented based on Neuron AI's tool tracking
        return [];
    }
}
