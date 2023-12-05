<?php
declare(strict_types=1);

use app\controllers\AuthController;
use app\core\Application;
use app\middlewares\AuthorizeRequest;
use shared\enums\RequestMethod;

/** @var Application $app */

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
