<?php
declare(strict_types=1);

use app\controllers\UserController;
use app\middlewares\AuthorizeRequest;
use shared\enums\RequestMethod;

/** @var Application $app */

$app->router->addRoute(
    RequestMethod::GET, '/users/me', [AuthorizeRequest::class], [
                          UserController::class,
                          'getProfile'
                      ]
);
