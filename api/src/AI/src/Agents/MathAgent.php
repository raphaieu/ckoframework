<?php

namespace CkoFramework\AI\Agents;

use CkoFramework\AI\MCP\Client as MCPClient;

/**
 * Math Agent using Neuron AI
 * 
 * This agent demonstrates integration between Neuron AI and MCP tools
 */
class MathAgent
{
    private MCPClient $mcpClient;
    private array $options;

    public function __construct(MCPClient $mcpClient, array $options = [])
    {
        $this->mcpClient = $mcpClient;
        $this->options = $options;
    }

    /**
     * Calculate using MCP Math Tool
     */
    public function calculate(string $operation, array $numbers): int|float
    {
        // Validate operation
        $validOperations = ['soma', 'multiplica', 'subtrai', 'divide', 'potencia', 'raizQuadrada'];
        
        if (!in_array($operation, $validOperations)) {
            throw new \InvalidArgumentException("Invalid operation: {$operation}. Valid operations: " . implode(', ', $validOperations));
        }

        // Validate numbers
        if (empty($numbers)) {
            throw new \InvalidArgumentException("Numbers array cannot be empty");
        }

        // Special handling for operations that need specific parameter counts
        if ($operation === 'potencia') {
            if (count($numbers) !== 2) {
                throw new \InvalidArgumentException("Power operation requires exactly 2 numbers: base and exponent");
            }
            return $this->mcpClient->callTool('math', 'potencia', $numbers);
        }

        if ($operation === 'raizQuadrada') {
            if (count($numbers) !== 1) {
                throw new \InvalidArgumentException("Square root operation requires exactly 1 number");
            }
            return $this->mcpClient->callTool('math', 'raizQuadrada', $numbers);
        }

        // For other operations, pass all numbers
        return $this->mcpClient->callTool('math', $operation, $numbers);
    }

    /**
     * Add numbers
     */
    public function add(array $numbers): int|float
    {
        return $this->calculate('soma', $numbers);
    }

    /**
     * Multiply numbers
     */
    public function multiply(array $numbers): int|float
    {
        return $this->calculate('multiplica', $numbers);
    }

    /**
     * Subtract numbers
     */
    public function subtract(array $numbers): int|float
    {
        return $this->calculate('subtrai', $numbers);
    }

    /**
     * Divide numbers
     */
    public function divide(array $numbers): int|float
    {
        return $this->calculate('divide', $numbers);
    }

    /**
     * Calculate power
     */
    public function power(float $base, float $exponent): float
    {
        return $this->calculate('potencia', [$base, $exponent]);
    }

    /**
     * Calculate square root
     */
    public function squareRoot(float $number): float
    {
        return $this->calculate('raizQuadrada', [$number]);
    }

    /**
     * Get available operations
     */
    public function getAvailableOperations(): array
    {
        return [
            'soma' => 'Add numbers together',
            'multiplica' => 'Multiply numbers',
            'subtrai' => 'Subtract numbers',
            'divide' => 'Divide numbers',
            'potencia' => 'Calculate power (base^exponent)',
            'raizQuadrada' => 'Calculate square root'
        ];
    }

    /**
     * Check if MCP server is available
     */
    public function isAvailable(): bool
    {
        return $this->mcpClient->isAvailable();
    }
}
