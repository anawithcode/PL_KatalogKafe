<?php

declare(strict_types=1);

if (PHP_SAPI === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . $path;
    if ($path !== '/' && is_file($file)) {
        return false;
    }
}

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

require dirname(__DIR__) . '/app/helpers.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = dirname(__DIR__) . '/app/' . $relative . '.php';
    if (is_file($file)) {
        require $file;
    }
});

use App\Controllers\MenuController;
use App\Core\Router;

$router = new Router();
$router->get('/', [MenuController::class, 'index']);
$router->get('/menus', [MenuController::class, 'index']);
$router->get('/menus/create', [MenuController::class, 'create']);
$router->post('/menus/store', [MenuController::class, 'store']);
$router->get('/menus/show', [MenuController::class, 'show']);
$router->get('/menus/edit', [MenuController::class, 'edit']);
$router->post('/menus/update', [MenuController::class, 'update']);
$router->post('/menus/delete', [MenuController::class, 'delete']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
