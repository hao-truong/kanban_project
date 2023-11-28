<?php
declare(strict_types=1);

use app\controllers\AuthController;
use app\core\Application;
use app\middlewares\AuthorizeRequest;
use shared\enums\RequestMethod;

require_once __DIR__ . '/../vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

session_start();

$container = require __DIR__ . '/../src/shared/configs/php-di/config.php';
$app = $container->get(Application::class);

$app->router->addRoute(
    RequestMethod::POST, "/auth/register", null, [
                           AuthController::class,
                           'register'
                       ]
);
$app->router->addRoute(
    RequestMethod::POST, "/auth/login", null, [
                           AuthController::class,
                           'login'
                       ]
);
$app->router->addRoute(
    RequestMethod::POST, '/auth/logout', [AuthorizeRequest::class], [
                          AuthController::class,
                          'logout'
                      ]
);

$app->run();
