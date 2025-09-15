<?php

require_once __DIR__ . '/vendor/autoload.php';

use CkoFramework\AI\Tools\MathTool;

/**
 * Simple MCP Server for testing
 * 
 * This server exposes the MathTool via HTTP endpoints
 */

$mathTool = new MathTool();

// Simple HTTP server using PHP built-in server
if (php_sapi_name() === 'cli-server') {
    $requestUri = $_SERVER['REQUEST_URI'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    
    // Set CORS headers
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json');
    
    // Handle preflight requests
    if ($requestMethod === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
    
    // Route handling
    switch ($requestUri) {
        case '/health':
            echo json_encode(['status' => 'ok', 'message' => 'MCP Server is running']);
            break;
            
        case '/tools/list':
            $info = $mathTool->getInfo();
            echo json_encode(['tools' => [$info]]);
            break;
            
        case '/tools/call':
            if ($requestMethod !== 'POST') {
                http_response_code(405);
                echo json_encode(['error' => ['message' => 'Method not allowed']]);
                break;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['tool']) || !isset($input['method'])) {
                http_response_code(400);
                echo json_encode(['error' => ['message' => 'Missing tool or method']]);
                break;
            }
            
            if ($input['tool'] !== 'math') {
                http_response_code(400);
                echo json_encode(['error' => ['message' => 'Unknown tool: ' . $input['tool']]]);
                break;
            }
            
            $method = $input['method'];
            $parameters = $input['parameters'] ?? [];
            
            if (!method_exists($mathTool, $method)) {
                http_response_code(400);
                echo json_encode(['error' => ['message' => 'Unknown method: ' . $method]]);
                break;
            }
            
            try {
                // For array parameters, we need to handle them differently
                if ($method === 'soma' || $method === 'multiplica' || $method === 'subtrai' || $method === 'divide') {
                    $result = call_user_func_array([$mathTool, $method], [$parameters]);
                } else {
                    $result = call_user_func_array([$mathTool, $method], $parameters);
                }
                echo json_encode(['result' => $result]);
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => ['message' => $e->getMessage()]]);
            }
            break;
            
        case '/tools/math':
            $info = $mathTool->getInfo();
            echo json_encode($info);
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => ['message' => 'Not found']]);
            break;
    }
} else {
    echo "This script should be run with PHP built-in server:\n";
    echo "php -S localhost:3001 " . __FILE__ . "\n";
}
