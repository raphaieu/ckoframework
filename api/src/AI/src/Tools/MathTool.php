<?php

namespace CkoFramework\AI\Tools;

/**
 * Math Tool for MCP Server
 * 
 * Simple mathematical operations for testing MCP integration
 */
class MathTool
{
    /**
     * Add numbers
     */
    public function soma(array $numbers): int|float
    {
        if (empty($numbers)) {
            return 0;
        }

        return array_sum($numbers);
    }

    /**
     * Multiply numbers
     */
    public function multiplica(array $numbers): int|float
    {
        if (empty($numbers)) {
            return 0;
        }

        return array_product($numbers);
    }

    /**
     * Subtract numbers (first - rest)
     */
    public function subtrai(array $numbers): int|float
    {
        if (empty($numbers)) {
            return 0;
        }

        $result = array_shift($numbers);
        foreach ($numbers as $number) {
            $result -= $number;
        }

        return $result;
    }

    /**
     * Divide numbers (first / rest)
     */
    public function divide(array $numbers): int|float
    {
        if (empty($numbers)) {
            return 0;
        }

        $result = array_shift($numbers);
        foreach ($numbers as $number) {
            if ($number == 0) {
                throw new \InvalidArgumentException("Division by zero is not allowed");
            }
            $result /= $number;
        }

        return $result;
    }

    /**
     * Calculate power
     */
    public function potencia(float $base, float $exponent): float
    {
        return pow($base, $exponent);
    }

    /**
     * Calculate square root
     */
    public function raizQuadrada(float $number): float
    {
        if ($number < 0) {
            throw new \InvalidArgumentException("Cannot calculate square root of negative number");
        }

        return sqrt($number);
    }

    /**
     * Get tool information
     */
    public function getInfo(): array
    {
        return [
            'name' => 'math',
            'description' => 'Mathematical operations tool',
            'methods' => [
                'soma' => 'Add numbers together',
                'multiplica' => 'Multiply numbers',
                'subtrai' => 'Subtract numbers',
                'divide' => 'Divide numbers',
                'potencia' => 'Calculate power',
                'raizQuadrada' => 'Calculate square root'
            ]
        ];
    }
}
