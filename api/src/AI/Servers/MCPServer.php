<?php

namespace CkoFramework\AI\Servers;

use CkoFramework\AI\Core\ToolRegistry;
use CkoFramework\AI\Tools\FinancialTool;

/**
 * MCP Server
 * 
 * Provides MCP server functionality for external tool access
 */
class MCPServer
{
    private ToolRegistry $toolRegistry;
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'host' => 'localhost',
            'port' => 8080,
            'path' => '/mcp'
        ], $config);

        // Initialize tool registry without MCP client for now
        $this->toolRegistry = new ToolRegistry();
        
        // Register tools
        $this->registerTools();
    }

    /**
     * Register all available tools
     */
    private function registerTools(): void
    {
        // Register financial tool
        $this->toolRegistry->registerTool(new FinancialTool());
    }

    /**
     * Start MCP server
     */
    public function start(): void
    {
        echo "🚀 Starting MCP Server...\n";
        echo "📍 Host: {$this->config['host']}\n";
        echo "🔌 Port: {$this->config['port']}\n";
        echo "🛠️  Tools: " . count($this->toolRegistry->getTools()) . "\n";
        echo "✅ Server ready for external connections!\n";
    }

    /**
     * Get tool registry
     */
    public function getToolRegistry(): ToolRegistry
    {
        return $this->toolRegistry;
    }

    /**
     * Get MCP client (placeholder)
     */
    public function getMCPClient(): ?object
    {
        return null;
    }

    /**
     * Execute tool via MCP
     */
    public function executeTool(string $toolName, array $parameters): array
    {
        return $this->toolRegistry->executeTool($toolName, $parameters);
    }

    /**
     * Get available tools
     */
    public function getAvailableTools(): array
    {
        $tools = [];
        foreach ($this->toolRegistry->getTools() as $name => $tool) {
            $tools[] = [
                'name' => $name,
                'description' => $tool->getDescription(),
                'parameters' => $tool->getParameters(),
                'metadata' => $tool->getMetadata()
            ];
        }
        return $tools;
    }
}
