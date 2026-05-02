<?php

date_default_timezone_set('America/Sao_Paulo');


require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// ENV
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// LOGS / ERROS
require_once 'api/helpers/logger.php';

set_exception_handler(function ($e) {
    Logger::error($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'internal server error']);
    exit;
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) {

    $nonFatal = [
        E_NOTICE,
        E_WARNING,
        E_DEPRECATED,
        E_USER_NOTICE,
        E_USER_WARNING
    ];

    Logger::error("[$errno] $errstr in $errfile:$errline");

    if (in_array($errno, $nonFatal)) {
        return true;
    }

    http_response_code(500);
    echo json_encode(['error' => 'internal server error']);
    exit;
});


// HTTPS (produção)
if (!empty($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
        $url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header("Location: $url");
        exit;
    }
}

/**
 * ==========================================
 * NORMALIZA URI (CORREÇÃO PRINCIPAL)
 * ==========================================
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// remove base /overgrace corretamente
$base = '/overgrace';

if (str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
}

$uri = '/' . ltrim($uri, '/'); // 🔥 garante padrão /rota


/**
 * ==========================================
 * 1. ARQUIVOS ESTÁTICOS (CORRIGIDO)
 * ==========================================
 */
$staticFile = __DIR__ . $uri;

if (file_exists($staticFile) && !is_dir($staticFile)) {

    $ext = pathinfo($staticFile, PATHINFO_EXTENSION);

    $mime = [
        'html' => 'text/html',
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'svg'  => 'image/svg+xml',
        'webp' => 'image/webp'
    ];

    if (isset($mime[$ext])) {
        header("Content-Type: " . $mime[$ext]);
    }

    readfile($staticFile);
    exit;
}


/**
 * ==========================================
 * 2. API ROUTER
 * ==========================================
 */
if (str_starts_with($uri, '/api')) {

    header('Content-Type: application/json');

    require_once 'api/core/Router.php';
    require_once 'api/middleware/RateLimit.php';

    if (str_ends_with($uri, '/login')) {
        RateLimit::handle($_SERVER['REMOTE_ADDR'] . '_login');
    }

    if (str_ends_with($uri, '/register')) {
        RateLimit::handle($_SERVER['REMOTE_ADDR'] . '_register');
    }

    RateLimit::handle();

    $router = new Router();

    $router->add('POST', '/login', 'Auth/AuthController@login');
    $router->add('POST', '/register', 'Auth/AuthController@register');

    $router->add('POST', '/cart', 'Cart/CartController@add');
    $router->add('GET', '/cart/item', 'Cart/CartController@get');
    $router->add('GET', '/cart/count', 'Cart/CartController@count');
    $router->add('POST', '/cart/alter/{id}', 'Cart/CartController@update');
    $router->add('DELETE', '/cart/{id}', 'Cart/CartController@delete');


    $router->add('POST', '/orders', 'Orders/OrderController@create');


    $router->add('POST', '/product', 'Products/ProductController@create');
    $router->add('GET', '/product', 'Products/ProductController@get');
    $router->add('GET', '/product/uuid/{uuid}', 'Products/ProductController@getByUuid');
    $router->add('GET', '/product/{id}', 'Products/ProductController@getById');
    $router->add('POST', '/product/{id}', 'Products/ProductController@update');
    $router->add('DELETE', '/product/{id}', 'Products/ProductController@delete');

    $router->add('POST', '/stock', 'Stock/StockController@create');
    $router->add('GET', '/stock', 'Stock/StockController@get');
    $router->add('GET', '/stock/{id}', 'Stock/StockController@getById');
    $router->add('POST', '/stock/{id}', 'Stock/StockController@update');
    $router->add('DELETE', '/stock/{id}', 'Stock/StockController@delete');

    $router->run();
    exit;
}


/**
 * ==========================================
 * 3. FRONTEND ROUTES
 * ==========================================
 */
header('Content-Type: text/html; charset=utf-8');

$routes = [
    //rotas padrão
    '/'          => 'frontend/pages/index.php',
    '/exit'      => 'frontend/pages/index.php',
    '/login'     => 'frontend/pages/login/login.php',

    //loja
    '/loja'      => 'frontend/pages/index.php',
    '/carrinho'  => 'frontend/pages/pages/carrinho.php',
    '/colecoes'  => 'frontend/pages/pages/colecoes.php',
    '/lista'     => 'frontend/pages/pages/loja.php',
    '/checkout'  => 'frontend/pages/pages/checkout.php',
    '/produto'   => 'frontend/pages/pages/produto.php',
    '/sobre'     => 'frontend/pages/pages/sobre.php',


    //painel adm
    '/dashboard'      => 'frontend/pages/paineladm/pages/dashboard.php',
    '/product'        => 'frontend/pages/paineladm/pages/produtos.php',
    '/orders'         => 'frontend/pages/paineladm/pages/pedidos.php',
    '/product_stock'  => 'frontend/pages/paineladm/pages/estoque.php',
    '/client'         => 'frontend/pages/paineladm/pages/clientes.php',
    '/configuration'  => 'frontend/pages/paineladm/pages/configuracoes.php',
    '/coupons'        => 'frontend/pages/paineladm/pages/cupons.php',
];

if (isset($routes[$uri])) {
    require $routes[$uri];
    exit;
}


/**
 * ==========================================
 * 4. 404
 * ==========================================
 */
http_response_code(404);
echo "<h1>Página não encontrada</h1>";