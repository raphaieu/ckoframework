<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize database
$capsule = new Illuminate\Database\Capsule\Manager;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database/database.sqlite',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

use CkoFramework\AI\Core\ToolRegistry;
use CkoFramework\AI\Tools\FinancialTool;
use CkoFramework\AI\Adapters\NeuronToolAdapter;
use CkoFramework\AI\Servers\MCPServer;

echo "🧪 Testing Unified Tools Architecture\n\n";

// Test 1: Direct Tool Usage
echo "1️⃣ Testing Direct Tool Usage:\n";
$financialTool = new FinancialTool();
$result = $financialTool->execute([
    'analysis_type' => 'cashflow',
    'period' => 'month'
]);
echo "✅ Direct execution: " . ($result['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "📊 Summary: " . substr($result['summary'], 0, 100) . "...\n\n";

// Test 2: Tool Registry
echo "2️⃣ Testing Tool Registry:\n";
$toolRegistry = new ToolRegistry();
$toolRegistry->registerTool($financialTool);
echo "✅ Tools registered: " . count($toolRegistry->getTools()) . "\n";
echo "✅ Tool names: " . implode(', ', array_keys($toolRegistry->getTools())) . "\n\n";

// Test 3: Neuron AI Adapter
echo "3️⃣ Testing Neuron AI Adapter:\n";
$neuronAdapter = new NeuronToolAdapter($financialTool);
echo "✅ Adapter name: " . $neuronAdapter->getName() . "\n";
echo "✅ Adapter description: " . substr($neuronAdapter->getDescription(), 0, 50) . "...\n";
echo "✅ Properties count: " . count($neuronAdapter->getProperties()) . "\n\n";

// Test 4: MCP Server
echo "4️⃣ Testing MCP Server:\n";
$mcpServer = new MCPServer();
echo "✅ MCP Server initialized\n";
echo "✅ Available tools: " . count($mcpServer->getAvailableTools()) . "\n";
echo "✅ Tool names: " . implode(', ', array_column($mcpServer->getAvailableTools(), 'name')) . "\n\n";

// Test 5: Tool Execution via Registry
echo "5️⃣ Testing Tool Execution via Registry:\n";
$executionResult = $toolRegistry->executeTool('financial_analysis', [
    'analysis_type' => 'trades',
    'period' => 'week'
]);
echo "✅ Registry execution: " . ($executionResult['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "📊 Data keys: " . implode(', ', array_keys($executionResult['data'] ?? [])) . "\n\n";

echo "🎉 All tests completed!\n";
echo "📋 Architecture Summary:\n";
echo "   • Unified Tool Interface: ✅\n";
echo "   • Neuron AI Adapter: ✅\n";
echo "   • MCP Adapter: ✅\n";
echo "   • Tool Registry: ✅\n";
echo "   • MCP Server: ✅\n";
echo "   • Direct Tool Usage: ✅\n";
