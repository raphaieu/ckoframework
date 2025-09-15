<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CkoFramework\AI\Agents\FinanceAgent;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * AI Controller for financial analysis
 */
class AIController extends BaseController
{
    private FinanceAgent $financeAgent;

    public function __construct()
    {
        $this->financeAgent = new FinanceAgent();
    }

    /**
     * Analyze financial data based on user query
     */
    public function analyze(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $query = $data['query'] ?? '';

        if (empty($query)) {
            return $this->jsonResponse($response, [
                'error' => 'Query is required'
            ], 400);
        }

        try {
            $result = $this->financeAgent->analyzeFinance($query);
            
            return $this->jsonResponse($response, [
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse($response, [
                'error' => 'Analysis failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cashflow insights
     */
    public function cashflowInsights(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $period = $queryParams['period'] ?? 'month';

        try {
            $result = $this->financeAgent->getCashflowInsights($period);
            
            return $this->jsonResponse($response, [
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse($response, [
                'error' => 'Failed to get cashflow insights: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trading analysis
     */
    public function tradingAnalysis(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $period = $queryParams['period'] ?? 'month';

        try {
            $result = $this->financeAgent->getTradingAnalysis($period);
            
            return $this->jsonResponse($response, [
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse($response, [
                'error' => 'Failed to get trading analysis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get portfolio analysis
     */
    public function portfolioAnalysis(Request $request, Response $response): Response
    {
        try {
            $result = $this->financeAgent->getPortfolioAnalysis();
            
            return $this->jsonResponse($response, [
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse($response, [
                'error' => 'Failed to get portfolio analysis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get AI agent status
     */
    public function status(Request $request, Response $response): Response
    {
        try {
            // Test if AI agent is working
            $testResult = $this->financeAgent->analyzeFinance("Test connection");
            
            return $this->jsonResponse($response, [
                'success' => true,
                'status' => 'active',
                'provider' => $_ENV['AI_PROVIDER'] ?? 'unknown',
                'model' => $_ENV['AI_MODEL'] ?? 'unknown',
                'test_result' => $testResult
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse($response, [
                'success' => false,
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
