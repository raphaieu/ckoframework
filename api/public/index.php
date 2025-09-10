<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';

// Carregar variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Configurar container DI
$container = new Container();

// Configurar Eloquent
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => $_ENV['DB_CONNECTION'] ?? 'sqlite',
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'database' => $_ENV['DB_DATABASE'] ?? __DIR__ . '/../database/database.sqlite',
    'username' => $_ENV['DB_USERNAME'] ?? '',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Configurar container
$container->set('db', $capsule);

// Criar aplicação Slim
$app = AppFactory::createFromContainer($container);

// Adicionar middleware de CORS (deve ser o primeiro)
$app->add(function ($request, $handler) {
    // Handle preflight OPTIONS requests
    if ($request->getMethod() === 'OPTIONS') {
        $response = new \Slim\Psr7\Response();
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Access-Control-Max-Age', '86400')
            ->withStatus(200);
    }
    
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Adicionar middleware de parsing JSON
$app->addBodyParsingMiddleware();

// Adicionar middleware de roteamento
$app->addRoutingMiddleware();

// Adicionar middleware de erro
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Carregar rotas
require __DIR__ . '/../src/Routes/api.php';

// Rota de health check
$app->get('/health', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'status' => 'ok',
        'timestamp' => date('Y-m-d H:i:s'),
        'environment' => $_ENV['APP_ENV'] ?? 'development'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Executar aplicação
$app->run();
