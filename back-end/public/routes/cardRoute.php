<?php
declare(strict_types=1);

use app\controllers\CardController;
use app\core\Application;
use app\middlewares\AuthorizeRequest;
use shared\enums\RequestMethod;

/** @var Application $app */

$app->router->addRoute(
    RequestMethod::POST, '/boards/{boardId}/columns/{columnId}/cards', [AuthorizeRequest::class], [
                           CardController::class,
                           'createCardForColumn'
                       ]
);
$app->router->addRoute(
    RequestMethod::GET, '/boards/{boardId}/columns/{columnId}/cards', [AuthorizeRequest::class], [
                          CardController::class,
                          'getCardsOfColumn'
                      ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}/columns/{columnId}/cards/{cardId}', [AuthorizeRequest::class], [
                            CardController::class,
                            'updateTitleCard'
                        ]
);
$app->router->addRoute(
    RequestMethod::DELETE, '/boards/{boardId}/columns/{columnId}/cards/{cardId}', [AuthorizeRequest::class], [
                             CardController::class,
                             'deleteCardOfColumn'
                         ]
);
$app->router->addRoute(
    RequestMethod::GET, '/boards/{boardId}/columns/{columnId}/cards/{cardId}', [AuthorizeRequest::class], [
                          CardController::class,
                          'getDetailCard'
                      ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}/columns/{columnId}/cards/{cardId}/assign-to-me', [AuthorizeRequest::class], [
                            CardController::class,
                            'assignMeToCard'
                        ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}/columns/{columnId}/cards/{cardId}/assign-to-member', [AuthorizeRequest::class], [
                            CardController::class,
                            'assignMemberToCard'
                        ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}/columns/{columnId}/cards/{cardId}/change-column', [AuthorizeRequest::class], [
                            CardController::class,
                            'changeColumnForCard'
                        ]
);
$app->router->addRoute(
    RequestMethod::PATCH, '/boards/{boardId}/columns/{columnId}/cards/{cardId}/update-description', [AuthorizeRequest::class], [
                            CardController::class,
                            'updateDescriptionOfCard'
                        ]
);
