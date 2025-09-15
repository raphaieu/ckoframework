<?php

use App\Controllers\UserController;
use App\Controllers\AIController;
use Slim\Routing\RouteCollectorProxy;

// Grupo de rotas da API
$app->group('/api', function (RouteCollectorProxy $group) {
    
    // Rotas de usuários
    $group->group('/users', function (RouteCollectorProxy $group) {
        $group->get('', [UserController::class, 'index']);
        $group->get('/{id}', [UserController::class, 'show']);
        $group->post('', [UserController::class, 'store']);
        $group->put('/{id}', [UserController::class, 'update']);
        $group->delete('/{id}', [UserController::class, 'destroy']);
        
        // Rotas OPTIONS para CORS
        $group->options('', function ($request, $response) {
            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
                ->withHeader('Access-Control-Max-Age', '86400')
                ->withStatus(200);
        });
        $group->options('/{id}', function ($request, $response) {
            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
                ->withHeader('Access-Control-Max-Age', '86400')
                ->withStatus(200);
        });
    });

    // Rotas de AI
    $group->group('/ai', function (RouteCollectorProxy $group) {
        $group->post('/analyze', [AIController::class, 'analyze']);
        $group->get('/cashflow-insights', [AIController::class, 'cashflowInsights']);
        $group->get('/trading-analysis', [AIController::class, 'tradingAnalysis']);
        $group->get('/portfolio-analysis', [AIController::class, 'portfolioAnalysis']);
        $group->get('/status', [AIController::class, 'status']);
        
        // Rotas OPTIONS para CORS
        $group->options('/analyze', function ($request, $response) {
            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
                ->withHeader('Access-Control-Max-Age', '86400')
                ->withStatus(200);
        });
    });

    // Rota de teste
    $group->get('/test', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'message' => 'API funcionando!',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0.0'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });

});

// Rota raiz
$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'message' => 'CKO Framework API',
        'version' => '1.0.0',
        'endpoints' => [
            'health' => '/health',
            'api' => '/api',
            'users' => '/api/users'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});
