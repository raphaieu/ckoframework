<?php

namespace CkoFramework\AI\Core;

use CkoFramework\AI\Agents\MathAgent;
use CkoFramework\AI\MCP\Client as MCPClient;
use CkoFramework\AI\Tools\MathTool;

/**
 * AI-Kit - Main class for AI functionality integration
 * 
 * This class provides a unified interface for MCP and Neuron AI integration
 */
class AIKit
{
    private ?MCPClient $mcpClient = null;
    private array $agents = [];
    private array $tools = [];
    private Config $config;

    public function __construct(?Config $config = null)
    {
        $this->config = $config ?? new Config();
        $this->initializeMCP();
        $this->registerDefaultTools();
    }

    /**
     * Initialize MCP Client
     */
    private function initializeMCP(): void
    {
        $this->mcpClient = new MCPClient(
            $this->config->get('mcp.host', 'localhost'),
            $this->config->get('mcp.port', 3001),
            $this->config->get('mcp.path', '/mcp')
        );
    }

    /**
     * Register default tools
     */
    private function registerDefaultTools(): void
    {
        $this->registerTool('math', new MathTool());
    }

    /**
     * Create an AI agent
     */
    public function createAgent(string $type, array $options = []): object
    {
        return match ($type) {
            'math' => new MathAgent($this->mcpClient, $options),
            default => throw new \InvalidArgumentException("Unknown agent type: {$type}")
        };
    }

    /**
     * Register a custom tool
     */
    public function registerTool(string $name, object $tool): void
    {
        $this->tools[$name] = $tool;
    }

    /**
     * Get a registered tool
     */
    public function getTool(string $name): ?object
    {
        return $this->tools[$name] ?? null;
    }

    /**
     * Get MCP Client
     */
    public function getMCPClient(): MCPClient
    {
        return $this->mcpClient;
    }

    /**
     * Get configuration
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}
