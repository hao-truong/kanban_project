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
    RequestMethod::GET, '/boards/search', [AuthorizeRequest::class], [
                          BoardController::class,
                          'searchBoard'
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
