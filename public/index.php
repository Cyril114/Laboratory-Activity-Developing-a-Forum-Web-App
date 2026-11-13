<?php
if (PHP_SAPI === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

require __DIR__ . '/../vendor/autoload.php';

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
]);
session_start();

$router = new \Bramus\Router\Router();


$router->get('/', function () {
    header('Location: /discussions/1');
});


$router->get('/discussions/(\d+)', [new App\Controllers\DiscussionController(), 'show']);
$router->post('/discussions/(\d+)/comments', [new App\Controllers\CommentController(), 'store']);

$auth = new App\Controllers\AuthController();
$router->get('/register', [$auth, 'showRegister']);
$router->post('/register', [$auth, 'register']);
$router->get('/login', [$auth, 'showLogin']);
$router->post('/login', [$auth, 'login']);
$router->post('/logout', [$auth, 'logout']);

$router->set404(function () {
    App\View::error(404, 'Page not found.');
});

$router->run();