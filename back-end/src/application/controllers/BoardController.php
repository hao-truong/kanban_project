<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\entities\BoardEntity;
use app\entities\CardEntity;
use app\entities\ColumnEntity;
use app\services\BoardService;
use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use shared\enums\SuccessMessage;
use shared\exceptions\ResponseException;
use shared\handlers\SessionHandler;
use shared\utils\Checker;

class BoardController
{
    public function __construct(
        private readonly Request      $request,
        private readonly Response     $response,
        private readonly BoardService $boardService
    ) {

    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function createBoard(): void
    {
        $req_data = $this->request->getBody();
        Checker::checkMissingFields(
            $req_data,
            [
                'title',
            ], [
                'title' => 'string'
            ]
        );

        $board_entity = new BoardEntity();
        $board_entity->setTitle($req_data['title']);
        $board_entity->setCreatorId(SessionHandler::getUserId());

        $new_board = $this->boardService->handleCreateBoard($board_entity);
        $this->response->content(StatusCode::CREATED, null, null, $new_board);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function getMyBoards(): void
    {
        $user_id = SessionHandler::getUserId();
        $boards = $this->boardService->handleGetMyBoards($user_id);
        $this->response->content(StatusCode::OK, null, null, $boards);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function updateBoard(): void
    {
        $req_data = $this->request->getBody();
        Checker::checkMissingFields(
            $req_data,
            [
                'title',
            ], [
                'title' => 'string'
            ]
        );

        $board_entity = new BoardEntity();
        $board_entity->setTitle($req_data['title']);
        $board_entity->setId($this->request->getIntParam('boardId'));
        $user_id = SessionHandler::getUserId();

        $board = $this->boardService->handleUpdateBoard($user_id, $board_entity);
        $this->response->content(StatusCode::OK, null, null, $board);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function deleteBoard(): void
    {
        $board_id = $this->request->getIntParam('boardId');
        $user_id = SessionHandler::getUserId();

        $this->boardService->handleDeleteBoard($user_id, $board_id);
        $this->response->content(StatusCode::OK, null, null, "Delete board id [{$board_id}] successfully!");
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function getBoard(): void
    {
        $user_id = SessionHandler::getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $board = $this->boardService->handleGetBoard($user_id, $board_id);
        $this->response->content(StatusCode::OK, null, null, $board);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function addMemberToBoard(): void
    {
        $req_data = $this->request->getBody();
        Checker::checkMissingFields(
            $req_data,
            [
                'member',
            ], [
                'member' => 'string'
            ]
        );

        $board_id = $this->request->getIntParam('boardId');
        $user_id = SessionHandler::getUserId();
        $this->boardService->handleAddMemberToBoard($user_id, $board_id, $req_data['member']);
        $this->response->content(StatusCode::OK, null, null, "Add member with username [{$req_data['member']}] to this board successfully!");
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function leaveBoard(): void
    {
        $board_id = $this->request->getIntParam('boardId');
        $user_id = SessionHandler::getUserId();

        $this->boardService->handleLeaveBoard($user_id, $board_id);
        $this->response->content(StatusCode::OK, null, null, "Leave board with id [{$board_id}] successfully!");
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function getMembersOfBoard(): void
    {
        $board_id = $this->request->getIntParam('boardId');
        $user_id = SessionHandler::getUserId();

        $members = $this->boardService->handleGetMembersOfBoard($user_id, $board_id);
        $this->response->content(StatusCode::OK, null, null, $members);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function getColumnsOfBoard(): void
    {
        $board_id = $this->request->getIntParam('boardId');
        $user_id = SessionHandler::getUserId();

        $columns = $this->boardService->handleGetColumnsOfBoard($user_id, $board_id);
        $this->response->content(StatusCode::OK, null, null, $columns);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function createColumn(): void
    {
        $req_data = $this->request->getBody();
        Checker::checkMissingFields(
            $req_data,
            [
                'title',
            ], [
                'title' => 'string',
            ]
        );

        $user_id = SessionHandler::getUserId();
        $column_entity = new ColumnEntity();
        $column_entity->setBoardId($this->request->getIntParam('boardId'));
        $column_entity->setTitle($req_data['title']);

        $new_column = $this->boardService->handleCreateColumn($user_id, $column_entity);
        $this->response->content(StatusCode::OK, null, null, $new_column);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function updateColumn(): void
    {
        $req_data = $this->request->getBody();
        Checker::checkMissingFields(
            $req_data, [
            'title',
        ],  [
                'title' => 'string',
            ]
        );

        $user_id = SessionHandler::getUserId();
        $column_entity = new ColumnEntity();
        $column_entity->setTitle($req_data['title']);
        $column_entity->setBoardId($this->request->getIntParam('boardId'));
        $column_entity->setId($this->request->getIntParam('columnId'));

        $update_column = $this->boardService->handleUpdateColumn($user_id, $column_entity);
        $this->response->content(StatusCode::OK, null, null, $update_column);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function deleteColumn(): void
    {
        $user_id = SessionHandler::getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');

        $this->boardService->handleDeleteColumn($user_id, $board_id, $column_id);
        $this->response->content(StatusCode::OK, null, null, SuccessMessage::DELETE_SUCCESSFULLY);
    }

    /**
     * @return void
     * @throws ResponseException
     * @throws \Exception
     */
    public function swapPositionOfCoupleColumn(): void
    {
        $req_data = $this->request->getBody();
        Checker::checkMissingFields(
            $req_data, [
            'originalColumnId',
            'targetColumnId',
        ],  [
                'originalColumnId' => 'integer',
                'targetColumnId'   => 'integer',
            ]
        );

        $user_id = SessionHandler::getUserId();
        $board_id = $this->request->getIntParam('boardId');

        $this->boardService->handleSwapPositionOfCoupleColumn($user_id, $board_id, $req_data['originalColumnId'], $req_data['targetColumnId']);
        $this->response->content(StatusCode::OK, null, null, SuccessMessage::SWAP_POSITION_SUCCESSFULLY);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function createCardForColumn(): void
    {
        $req_data = $this->request->getBody();
        Checker::checkMissingFields(
            $req_data, [
            'title',
        ],  [
                'title' => 'string',
            ]
        );

        $user_id = SessionHandler::getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_entity = new CardEntity();
        $card_entity->setTitle($req_data['title']);
        $card_entity->setColumnId($column_id);

        $new_card = $this->boardService->handleCreateCardForColumn($user_id, $board_id, $card_entity);
        $this->response->content(StatusCode::OK, null, null, $new_card);
    }

    /**
     * @return void
     * @throws ResponseException
     * @throws \Exception
     */
    public function getCardsOfColumn(): void
    {
        $user_id = SessionHandler::getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');

        $cards = $this->boardService->handleGetCardsOfColumn($user_id, $board_id, $column_id);
        $this->response->content(StatusCode::OK, null, null, $cards);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function updateTitleCard(): void
    {
        $req_data = $this->request->getBody();
        Checker::checkMissingFields(
            $req_data, [
            'title',
        ],  [
                'title' => 'string',
            ]
        );

        $user_id = SessionHandler::getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_id = $this->request->getIntParam('cardId');
        $card_entity = new CardEntity();
        $card_entity->setId($card_id);
        $card_entity->setTitle($req_data['title']);
        $card_entity->setColumnId($column_id);

        $card = $this->boardService->handleUpdateTitleCard($user_id, $board_id, $card_entity);
        $this->response->content(StatusCode::OK, null, null, $card);
    }

    /**
     * @return void
     * @throws ResponseException
     * @throws \Exception
     */
    public function deleteCardOfColumn(): void
    {
        $user_id = SessionHandler::getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_id = $this->request->getIntParam('cardId');

        $this->boardService->handleDeleteCardOfColumn($user_id, $board_id, $column_id, $card_id);
        $this->response->content(StatusCode::OK, null, null, SuccessMessage::DELETE_SUCCESSFULLY);
    }

    /**
     * @return void
     * @throws ResponseException
     * @throws \Exception
     */
    public function assignMeToCard(): void
    {
        $user_id = SessionHandler::getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_id = $this->request->getIntParam('cardId');
        $this->boardService->handleAssignMeToCard($user_id, $board_id, $column_id, $card_id);
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
        Checker::checkMissingFields(
            $req_data, [
            'assignToMemberId',
        ],  [
                'assignToMemberId' => 'integer',
            ]
        );

        $user_id = SessionHandler::getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_id = $this->request->getIntParam('cardId');
        $this->boardService->handleAssignMemberToCard($user_id, $board_id, $column_id, $card_id, $req_data['assignToMemberId']);

        $this->response->content(StatusCode::OK, null, null, SuccessMessage::ASSIGN_USER_TO_CARD_SUCCESSFULLY);
    }

    /**
     * @return void
     * @throws ResponseException
     * @throws \Exception
     */
    public function getDetailCard(): void
    {
        $user_id = SessionHandler::getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_id = $this->request->getIntParam('cardId');
        $detail_card = $this->boardService->handleGetDetailCard($user_id, $board_id, $column_id, $card_id);

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
        Checker::checkMissingFields(
            $req_data, [
            'destinationColumnId',
        ],  [
                'destinationColumnId' => 'integer',
            ]
        );

        $user_id = SessionHandler::getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_id = $this->request->getIntParam('cardId');

        $card = $this->boardService->handleChangeColumnForCard($user_id, $board_id, $column_id, $card_id, $req_data['destinationColumnId']);
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
        Checker::checkMissingFields(
            $req_data, [
            'description',
        ],  [
                'description' => 'string',
            ]
        );

        $user_id = SessionHandler::getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');
        $card_id = $this->request->getIntParam('cardId');

        $card = $this->boardService->handleUpdateDescriptionOfCard($user_id, $board_id, $column_id, $card_id, $req_data['description']);
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
        Checker::checkMissingFields(
            $req_data, [
            'originalCardId',
            'originalColumnId',
            'targetCardId',
            'targetColumnId',
        ],  [
                'originalCardId'   => 'integer',
                'originalColumnId' => 'integer',
                'targetCardId'     => 'integer',
                'targetColumnId'   => 'integer',
            ]
        );

        $user_id = SessionHandler::getUserId();
        $board_id = $this->request->getIntParam('boardId');

        $this->boardService->handleMoveCard($user_id, $board_id, $req_data);
        $this->response->content(StatusCode::OK, null, null, SuccessMessage::SWAP_TWO_CARDS_SUCCESSFULLY);
    }
}
