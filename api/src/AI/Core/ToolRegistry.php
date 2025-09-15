<?php

namespace CkoFramework\AI\Core;

use CkoFramework\AI\Adapters\NeuronToolAdapter;
use CkoFramework\AI\Adapters\MCPToolAdapter;
use CkoFramework\AI\MCP\Client;

/**
 * Tool Registry
 * 
 * Manages tools and their adapters for different AI systems
 */
class ToolRegistry
{
    private array $tools = [];
    private array $neuronAdapters = [];
    private array $mcpAdapters = [];
    private ?Client $mcpClient = null;

    public function __construct(?Client $mcpClient = null)
    {
        $this->mcpClient = $mcpClient;
    }

    /**
     * Register a tool
     */
    public function registerTool(ToolInterface $tool): self
    {
        $this->tools[$tool->getName()] = $tool;
        
        // Create Neuron AI adapter
        $this->neuronAdapters[$tool->getName()] = new NeuronToolAdapter($tool);
        
        // Create MCP adapter if MCP client is available
        if ($this->mcpClient) {
            $this->mcpAdapters[$tool->getName()] = new MCPToolAdapter($tool, $this->mcpClient);
        }

        return $this;
    }

    /**
     * Get tool by name
     */
    public function getTool(string $name): ?ToolInterface
    {
        return $this->tools[$name] ?? null;
    }

    /**
     * Get all tools
     */
    public function getTools(): array
    {
        return $this->tools;
    }

    /**
     * Get Neuron AI adapter for tool
     */
    public function getNeuronAdapter(string $name): ?NeuronToolAdapter
    {
        return $this->neuronAdapters[$name] ?? null;
    }

    /**
     * Get all Neuron AI adapters
     */
    public function getNeuronAdapters(): array
    {
        return $this->neuronAdapters;
    }

    /**
     * Get MCP adapter for tool
     */
    public function getMCPAdapter(string $name): ?MCPToolAdapter
    {
        return $this->mcpAdapters[$name] ?? null;
    }

    /**
     * Get all MCP adapters
     */
    public function getMCPAdapters(): array
    {
        return $this->mcpAdapters;
    }

    /**
     * Execute tool directly
     */
    public function executeTool(string $name, array $parameters): array
    {
        $tool = $this->getTool($name);
        if (!$tool) {
            return [
                'success' => false,
                'error' => "Tool '{$name}' not found"
            ];
        }

        return $tool->execute($parameters);
    }

    /**
     * Get tool schemas for MCP
     */
    public function getMCPSchemas(): array
    {
        $schemas = [];
        foreach ($this->mcpAdapters as $adapter) {
            $schemas[] = $adapter->getSchema();
        }
        return $schemas;
    }

    /**
     * Register all tools with MCP server
     */
    public function registerWithMCP(): array
    {
        if (!$this->mcpClient) {
            return [
                'success' => false,
                'error' => 'MCP client not available'
            ];
        }

        $results = [];
        foreach ($this->mcpAdapters as $name => $adapter) {
            $results[$name] = $adapter->register();
        }

        return $results;
    }
}
