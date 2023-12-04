<?php
declare(strict_types=1);

use app\controllers\AuthController;
use app\controllers\BoardController;
use app\controllers\ColumnController;
use app\controllers\UserController;
use app\core\Application;
use app\middlewares\AuthorizeRequest;
use shared\enums\RequestMethod;

require_once __DIR__ . '/../vendor/autoload.php';

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PATCH, PUT, DELETE");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

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
$app->router->addRoute(
    RequestMethod::GET, '/users/me', [AuthorizeRequest::class], [
                          UserController::class,
                          'getProfile'
                      ]
);
$app->router->addRoute(
    RequestMethod::POST, '/boards', [AuthorizeRequest::class], [
                           BoardController::class,
                           'createBoard'
                       ]
);
$app->router->addRoute(
    RequestMethod::GET, '/boards/me', [AuthorizeRequest::class], [
                          BoardController::class,
                          'getMyBoards'
                      ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}', [AuthorizeRequest::class], [
                            BoardController::class,
                            'updateBoard'
                        ]
);
$app->router->addRoute(
    RequestMethod::DELETE, '/boards/{boardId}', [AuthorizeRequest::class], [
                             BoardController::class,
                             'deleteBoard'
                         ]
);
$app->router->addRoute(
    RequestMethod::GET, '/boards/{boardId}', [AuthorizeRequest::class], [
                          BoardController::class,
                          'getBoard'
                      ]
);
$app->router->addRoute(
    RequestMethod::POST, '/boards/{boardId}/members', [AuthorizeRequest::class], [
                           BoardController::class,
                           'addMemberToBoard'
                       ]
);
$app->router->addRoute(
    RequestMethod::DELETE, '/boards/{boardId}/members/leave', [AuthorizeRequest::class], [
                             BoardController::class,
                             'leaveBoard'
                         ]
);
$app->router->addRoute(
    RequestMethod::GET, '/boards/{boardId}/members', [AuthorizeRequest::class], [
                          BoardController::class,
                          'getMembersOfBoard'
                      ]
);
$app->router->addRoute(
    RequestMethod::POST, '/columns', [AuthorizeRequest::class], [
                           ColumnController::class,
                           'createColumn'
                       ]
);

$app->run();
