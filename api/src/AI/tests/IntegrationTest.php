<?php

namespace CkoFramework\AI\Tests;

use CkoFramework\AI\Core\AIKit;
use CkoFramework\AI\Core\Config;
use CkoFramework\AI\Agents\MathAgent;
use CkoFramework\AI\MCP\Client as MCPClient;

/**
 * Integration Test for AI-Kit
 * 
 * Tests the integration between MCP and Neuron AI
 */
class IntegrationTest
{
    private AIKit $aiKit;
    private MathAgent $mathAgent;

    public function __construct()
    {
        $this->aiKit = new AIKit();
        $this->mathAgent = $this->aiKit->createAgent('math');
    }

    /**
     * Run all tests
     */
    public function runAllTests(): array
    {
        $results = [];

        $results['mcp_availability'] = $this->testMCPAvailability();
        $results['math_operations'] = $this->testMathOperations();
        $results['error_handling'] = $this->testErrorHandling();
        $results['agent_creation'] = $this->testAgentCreation();

        return $results;
    }

    /**
     * Test MCP server availability
     */
    private function testMCPAvailability(): array
    {
        try {
            $mcpClient = $this->aiKit->getMCPClient();
            $isAvailable = $mcpClient->isAvailable();
            
            return [
                'status' => $isAvailable ? 'PASS' : 'FAIL',
                'message' => $isAvailable ? 'MCP server is available' : 'MCP server is not available',
                'details' => ['available' => $isAvailable]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'ERROR',
                'message' => 'Error checking MCP availability: ' . $e->getMessage(),
                'details' => ['error' => $e->getMessage()]
            ];
        }
    }

    /**
     * Test math operations
     */
    private function testMathOperations(): array
    {
        $tests = [
            'addition' => [
                'operation' => 'soma',
                'numbers' => [5, 3, 2],
                'expected' => 10
            ],
            'multiplication' => [
                'operation' => 'multiplica',
                'numbers' => [4, 5],
                'expected' => 20
            ],
            'subtraction' => [
                'operation' => 'subtrai',
                'numbers' => [10, 3, 2],
                'expected' => 5
            ],
            'division' => [
                'operation' => 'divide',
                'numbers' => [20, 4, 2],
                'expected' => 2.5
            ],
            'power' => [
                'operation' => 'potencia',
                'numbers' => [2, 3],
                'expected' => 8
            ],
            'square_root' => [
                'operation' => 'raizQuadrada',
                'numbers' => [16],
                'expected' => 4
            ]
        ];

        $results = [];

        foreach ($tests as $testName => $test) {
            try {
                $result = $this->mathAgent->calculate($test['operation'], $test['numbers']);
                $passed = abs($result - $test['expected']) < 0.0001; // Float comparison
                
                $results[$testName] = [
                    'status' => $passed ? 'PASS' : 'FAIL',
                    'expected' => $test['expected'],
                    'actual' => $result,
                    'operation' => $test['operation'],
                    'numbers' => $test['numbers']
                ];
            } catch (\Exception $e) {
                $results[$testName] = [
                    'status' => 'ERROR',
                    'message' => $e->getMessage(),
                    'operation' => $test['operation'],
                    'numbers' => $test['numbers']
                ];
            }
        }

        return $results;
    }

    /**
     * Test error handling
     */
    private function testErrorHandling(): array
    {
        $tests = [
            'invalid_operation' => [
                'operation' => 'invalid_op',
                'numbers' => [1, 2, 3],
                'should_fail' => true
            ],
            'empty_numbers' => [
                'operation' => 'soma',
                'numbers' => [],
                'should_fail' => true
            ],
            'division_by_zero' => [
                'operation' => 'divide',
                'numbers' => [10, 0],
                'should_fail' => true
            ],
            'negative_square_root' => [
                'operation' => 'raizQuadrada',
                'numbers' => [-4],
                'should_fail' => true
            ]
        ];

        $results = [];

        foreach ($tests as $testName => $test) {
            try {
                $result = $this->mathAgent->calculate($test['operation'], $test['numbers']);
                
                $results[$testName] = [
                    'status' => $test['should_fail'] ? 'FAIL' : 'PASS',
                    'message' => $test['should_fail'] ? 'Expected error but got result: ' . $result : 'Operation succeeded as expected',
                    'result' => $result
                ];
            } catch (\Exception $e) {
                $results[$testName] = [
                    'status' => $test['should_fail'] ? 'PASS' : 'FAIL',
                    'message' => $test['should_fail'] ? 'Error caught as expected: ' . $e->getMessage() : 'Unexpected error: ' . $e->getMessage(),
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Test agent creation
     */
    private function testAgentCreation(): array
    {
        try {
            $agent = $this->aiKit->createAgent('math');
            $isMathAgent = $agent instanceof MathAgent;
            
            return [
                'status' => $isMathAgent ? 'PASS' : 'FAIL',
                'message' => $isMathAgent ? 'Math agent created successfully' : 'Failed to create math agent',
                'agent_type' => get_class($agent)
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'ERROR',
                'message' => 'Error creating agent: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Print test results
     */
    public function printResults(array $results): void
    {
        echo "=== AI-Kit Integration Test Results ===\n\n";

        foreach ($results as $category => $tests) {
            echo "📋 {$category}:\n";
            
            if (is_array($tests) && isset($tests['status'])) {
                // Single test result
                $icon = match ($tests['status']) {
                    'PASS' => '✅',
                    'FAIL' => '❌',
                    'ERROR' => '⚠️',
                    default => '❓'
                };
                
                echo "  {$icon} {$tests['status']}: {$tests['message']}\n";
            } elseif (is_array($tests)) {
                // Multiple test results
                foreach ($tests as $testName => $result) {
                    if (is_array($result) && isset($result['status'])) {
                        $status = $result['status'];
                        $icon = match ($status) {
                            'PASS' => '✅',
                            'FAIL' => '❌',
                            'ERROR' => '⚠️',
                            default => '❓'
                        };
                        
                        echo "  {$icon} {$testName}: {$status}\n";
                        
                        if (isset($result['message'])) {
                            echo "     Message: {$result['message']}\n";
                        }
                        
                        if (isset($result['expected']) && isset($result['actual'])) {
                            echo "     Expected: {$result['expected']}, Got: {$result['actual']}\n";
                        }
                    }
                }
            }
            
            echo "\n";
        }
    }
}
