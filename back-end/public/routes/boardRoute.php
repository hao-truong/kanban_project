<?php
declare(strict_types=1);

use app\controllers\BoardController;
use app\core\Application;
use app\middlewares\AuthorizeRequest;
use shared\enums\RequestMethod;

/** @var Application $app */

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
$app->router->addRoute(
    RequestMethod::POST, '/boards/{boardId}/columns/{columnId}/cards', [AuthorizeRequest::class], [
                           BoardController::class,
                           'createCardForColumn'
                       ]
);
$app->router->addRoute(
    RequestMethod::GET, '/boards/{boardId}/columns/{columnId}/cards', [AuthorizeRequest::class], [
                          BoardController::class,
                          'getCardsOfColumn'
                      ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}/columns/{columnId}/cards/{cardId}', [AuthorizeRequest::class], [
                          BoardController::class,
                          'updateTitleCard'
                      ]
);
$app->router->addRoute(
    RequestMethod::DELETE, '/boards/{boardId}/columns/{columnId}/cards/{cardId}', [AuthorizeRequest::class], [
                            BoardController::class,
                            'deleteCardOfColumn'
                        ]
);
