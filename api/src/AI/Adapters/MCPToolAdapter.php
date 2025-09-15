<?php

namespace CkoFramework\AI\Adapters;

use CkoFramework\AI\Core\ToolInterface;
use CkoFramework\AI\MCP\Client;

/**
 * MCP Tool Adapter
 * 
 * Adapts unified tools to work with MCP (Model Context Protocol)
 */
class MCPToolAdapter
{
    private ToolInterface $tool;
    private Client $mcpClient;

    public function __construct(ToolInterface $tool, Client $mcpClient)
    {
        $this->tool = $tool;
        $this->mcpClient = $mcpClient;
    }

    /**
     * Get tool schema for MCP
     */
    public function getSchema(): array
    {
        return [
            'name' => $this->tool->getName(),
            'description' => $this->tool->getDescription(),
            'inputSchema' => [
                'type' => 'object',
                'properties' => $this->convertParametersToJsonSchema($this->tool->getParameters()),
                'required' => $this->getRequiredParameters()
            ],
            'metadata' => $this->tool->getMetadata()
        ];
    }

    /**
     * Execute tool via MCP
     */
    public function execute(array $parameters): array
    {
        try {
            // Execute tool locally first
            $result = $this->tool->execute($parameters);

            // Send result to MCP server if needed
            $mcpResult = $this->mcpClient->sendToolResult([
                'tool_name' => $this->tool->getName(),
                'parameters' => $parameters,
                'result' => $result
            ]);

            return array_merge($result, [
                'mcp_response' => $mcpResult,
                'execution_method' => 'mcp_enhanced'
            ]);

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => "MCP Tool execution failed: " . $e->getMessage(),
                'data' => [],
                'metadata' => [
                    'error' => true,
                    'message' => $e->getMessage(),
                    'tool_name' => $this->tool->getName()
                ]
            ];
        }
    }

    /**
     * Register tool with MCP server
     */
    public function register(): array
    {
        try {
            return $this->mcpClient->registerTool($this->getSchema());
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => "Failed to register tool with MCP: " . $e->getMessage()
            ];
        }
    }

    /**
     * Convert parameters to JSON Schema format
     */
    private function convertParametersToJsonSchema(array $parameters): array
    {
        $schema = [];

        foreach ($parameters as $name => $param) {
            $schema[$name] = [
                'type' => $param['type'],
                'description' => $param['description']
            ];

            if (isset($param['enum'])) {
                $schema[$name]['enum'] = $param['enum'];
            }

            if (isset($param['default'])) {
                $schema[$name]['default'] = $param['default'];
            }
        }

        return $schema;
    }

    /**
     * Get required parameters
     */
    private function getRequiredParameters(): array
    {
        $required = [];

        foreach ($this->tool->getParameters() as $name => $param) {
            if ($param['required'] ?? false) {
                $required[] = $name;
            }
        }

        return $required;
    }
}
