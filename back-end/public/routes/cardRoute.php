<?php
declare(strict_types=1);

use app\controllers\BoardController;
use app\core\Application;
use app\middlewares\AuthorizeRequest;
use shared\enums\RequestMethod;

/** @var Application $app */

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
$app->router->addRoute(
    RequestMethod::GET, '/boards/{boardId}/columns/{columnId}/cards/{cardId}', [AuthorizeRequest::class], [
                          BoardController::class,
                          'getDetailCard'
                      ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}/columns/{columnId}/cards/{cardId}/assign-to-me', [AuthorizeRequest::class], [
                            BoardController::class,
                            'assignMeToCard'
                        ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}/columns/{columnId}/cards/{cardId}/assign-to-member', [AuthorizeRequest::class], [
                            BoardController::class,
                            'assignMemberToCard'
                        ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}/columns/{columnId}/cards/{cardId}/change-column', [AuthorizeRequest::class], [
                            BoardController::class,
                            'changeColumnForCard'
                        ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}/columns/{columnId}/cards/{cardId}/update-description', [AuthorizeRequest::class], [
                            BoardController::class,
                            'updateDescriptionOfCard'
                        ]
);
