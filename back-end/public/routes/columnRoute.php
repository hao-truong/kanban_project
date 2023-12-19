<?php

declare(strict_types=1);

use app\controllers\ColumnController;
use app\core\Application;
use app\middlewares\AuthorizeRequest;
use shared\enums\RequestMethod;

/** @var Application $app */

$app->router->addRoute(
    RequestMethod::GET, '/boards/{boardId}/columns', [AuthorizeRequest::class], [
                          ColumnController::class,
                          'getColumnsOfBoard'
                      ]
);
$app->router->addRoute(
    RequestMethod::POST, '/boards/{boardId}/columns', [AuthorizeRequest::class], [
                           ColumnController::class,
                           'createColumn'
                       ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}/columns/position', [AuthorizeRequest::class], [
                            ColumnController::class,
                            'swapPositionOfCoupleColumn'
                        ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}/columns/{columnId}', [AuthorizeRequest::class], [
                            ColumnController::class,
                            'updateColumn'
                        ]
);
$app->router->addRoute(
    RequestMethod::DELETE, '/boards/{boardId}/columns/{columnId}', [AuthorizeRequest::class], [
                             ColumnController::class,
                             'deleteColumn'
                         ]
);
