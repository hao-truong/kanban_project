<?php

declare(strict_types=1);

use app\controllers\BoardController;
use app\core\Application;
use app\middlewares\AuthorizeRequest;
use shared\enums\RequestMethod;

/** @var Application $app */

$app->router->addRoute(
    RequestMethod::GET, '/boards/{boardId}/columns', [AuthorizeRequest::class], [
                          BoardController::class,
                          'getColumnsOfBoard'
                      ]
);
$app->router->addRoute(
    RequestMethod::POST, '/boards/{boardId}/columns', [AuthorizeRequest::class], [
                           BoardController::class,
                           'createColumn'
                       ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}/columns/position', [AuthorizeRequest::class], [
                            BoardController::class,
                            'swapPositionOfCoupleColumn'
                        ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}/columns/{columnId}', [AuthorizeRequest::class], [
                            BoardController::class,
                            'updateColumn'
                        ]
);
$app->router->addRoute(
    RequestMethod::DELETE, '/boards/{boardId}/columns/{columnId}', [AuthorizeRequest::class], [
                             BoardController::class,
                             'deleteColumn'
                         ]
);
