<?php

declare(strict_types=1);

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\entities\CardEntity;
use app\services\ColumnCardService;
use shared\enums\StatusCode;
use shared\enums\SuccessMessage;
use shared\exceptions\ResponseException;
use shared\utils\Checker;

class CardController
{
    public function __construct(
        private readonly Request $request,
        private readonly Response $response,
        private readonly ColumnCardService $columnCardService
    ) {
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function createCardForColumn(): void
    {
        $req_data = $this->request->getBody();
        $req_data = Checker::checkMissingFields(
            $req_data, [
            'title',
        ], [
                'title' => 'string',
            ]
        );

        $user_id = $this->request->getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_entity = new CardEntity();
        $card_entity->setTitle($req_data['title']);
        $card_entity->setColumnId($column_id);

        $new_card = $this->columnCardService->handleCreateCardForColumn($user_id, $board_id, $card_entity);
        $this->response->content(StatusCode::OK, null, null, $new_card);
    }

    /**
     * @return void
     * @throws ResponseException
     * @throws \Exception
     */
    public function getCardsOfColumn(): void
    {
        $user_id = $this->request->getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');

        $cards = $this->columnCardService->handleGetCardsOfColumn($user_id, $board_id, $column_id);
        $this->response->content(StatusCode::OK, null, null, $cards);
    }

    /**
     * @return void
     * @throws ResponseException
     * @throws \Exception
     */
    public function updateTitleCard(): void
    {
        $req_data = $this->request->getBody();
        $req_data = Checker::checkMissingFields(
            $req_data, [
            'title',
        ], [
                'title' => 'string',
            ]
        );

        $user_id = $this->request->getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_id = $this->request->getIntParam('cardId');
        $card_entity = new CardEntity();
        $card_entity->setId($card_id);
        $card_entity->setTitle($req_data['title']);
        $card_entity->setColumnId($column_id);

        $card = $this->columnCardService->handleUpdateTitleCard($user_id, $board_id, $card_entity);
        $this->response->content(StatusCode::OK, null, null, $card);
    }

    /**
     * @return void
     * @throws ResponseException
     * @throws \Exception
     */
    public function deleteCardOfColumn(): void
    {
        $user_id = $this->request->getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_id = $this->request->getIntParam('cardId');

        $this->columnCardService->handleDeleteCardOfColumn($user_id, $board_id, $column_id, $card_id);
        $this->response->content(StatusCode::OK, null, null, SuccessMessage::DELETE_SUCCESSFULLY);
    }

    /**
     * @return void
     * @throws ResponseException
     * @throws \Exception
     */
    public function assignMeToCard(): void
    {
        $user_id = $this->request->getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_id = $this->request->getIntParam('cardId');
        $this->columnCardService->handleAssignMeToCard($user_id, $board_id, $column_id, $card_id);
        $this->response->content(StatusCode::OK, null, null, SuccessMessage::ASSIGN_USER_TO_CARD_SUCCESSFULLY);
    }

    /**
     * @return void
     * @throws ResponseException
     * @throws \Exception
     */
    public function assignMemberToCard(): void
    {
        $req_data = $this->request->getBody();
        $req_data = Checker::checkMissingFields(
            $req_data, [
            'assignToMemberId',
        ], [
                'assignToMemberId' => 'integer',
            ]
        );

        $user_id = $this->request->getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_id = $this->request->getIntParam('cardId');
        $this->columnCardService->handleAssignMemberToCard(
            $user_id,
            $board_id,
            $column_id,
            $card_id,
            $req_data['assignToMemberId']
        );

        $this->response->content(StatusCode::OK, null, null, SuccessMessage::ASSIGN_USER_TO_CARD_SUCCESSFULLY);
    }

    /**
     * @return void
     * @throws ResponseException
     * @throws \Exception
     */
    public function getDetailCard(): void
    {
        $user_id = $this->request->getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_id = $this->request->getIntParam('cardId');
        $detail_card = $this->columnCardService->handleGetDetailCard($user_id, $board_id, $column_id, $card_id);

        $this->response->content(StatusCode::OK, null, null, $detail_card);
    }

    /**
     * @return void
     * @throws ResponseException
     * @throws \Exception
     */
    public function changeColumnForCard(): void
    {
        $req_data = $this->request->getBody();
        $req_data = Checker::checkMissingFields(
            $req_data, [
            'destinationColumnId',
        ], [
                'destinationColumnId' => 'integer',
            ]
        );

        $user_id = $this->request->getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_id = $this->request->getIntParam('cardId');

        $this->columnCardService->handleChangeColumnForCard(
            $user_id,
            $board_id,
            $column_id,
            $card_id,
            $req_data['destinationColumnId']
        );
        $this->response->content(StatusCode::OK, null, null, SuccessMessage::CHANGE_COLUMN_SUCCESSFULLY);
    }

    /**
     * @return void
     * @throws ResponseException
     * @throws \Exception
     */
    public function updateDescriptionOfCard(): void
    {
        $req_data = $this->request->getBody();
        $req_data = Checker::checkMissingFields(
            $req_data, [
            'description',
        ], [
                'description' => 'string',
            ]
        );

        $user_id = $this->request->getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_id = $this->request->getIntParam('cardId');

        $card = $this->columnCardService->handleUpdateDescriptionOfCard(
            $user_id,
            $board_id,
            $column_id,
            $card_id,
            $req_data['description']
        );
        $this->response->content(StatusCode::OK, null, null, $card);
    }

    /**
     * @return void
     * @throws ResponseException
     * @throws \Exception
     */
    public function moveCard(): void
    {
        $req_data = $this->request->getBody();
        $req_data = Checker::checkMissingFields(
            $req_data, [
            'originalCardId',
            'originalColumnId',
            'targetCardId',
            'targetColumnId',
        ], [
                'originalCardId' => 'integer',
                'originalColumnId' => 'integer',
                'targetCardId' => 'integer',
                'targetColumnId' => 'integer',
            ]
        );

        $user_id = $this->request->getUserId();
        $board_id = $this->request->getIntParam('boardId');

        $this->columnCardService->handleMoveCard($user_id, $board_id, $req_data);
        $this->response->content(StatusCode::OK, null, null, SuccessMessage::SWAP_TWO_CARDS_SUCCESSFULLY);
    }
}
