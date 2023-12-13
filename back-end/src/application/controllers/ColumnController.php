<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\entities\ColumnEntity;
use app\services\BoardColumnSerivce;
use shared\enums\StatusCode;
use shared\enums\SuccessMessage;
use shared\exceptions\ResponseException;
use shared\utils\Checker;

class ColumnController
{
    public function __construct(
        private readonly Request      $request,
        private readonly Response     $response,
        private readonly BoardColumnSerivce $boardColumnSerivce
    ) {
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function getColumnsOfBoard(): void
    {
        $board_id = $this->request->getIntParam('boardId');
        $user_id = $this->request->getUserId();

        $columns = $this->boardColumnSerivce->handleGetColumnsOfBoard($user_id, $board_id);
        $this->response->content(StatusCode::OK, null, null, $columns);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function createColumn(): void
    {
        $req_data = $this->request->getBody();
        $req_data = Checker::checkMissingFields(
            $req_data,
            [
                'title',
            ], [
                'title' => 'string',
            ]
        );

        $user_id = $this->request->getUserId();
        $column_entity = new ColumnEntity();
        $column_entity->setBoardId($this->request->getIntParam('boardId'));
        $column_entity->setTitle($req_data['title']);

        $new_column = $this->boardColumnSerivce->handleCreateColumn($user_id, $column_entity);
        $this->response->content(StatusCode::OK, null, null, $new_column);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function updateColumn(): void
    {
        $req_data = $this->request->getBody();
        $req_data = Checker::checkMissingFields(
            $req_data, [
            'title',
        ],  [
                'title' => 'string',
            ]
        );

        $user_id = $this->request->getUserId();
        $column_entity = new ColumnEntity();
        $column_entity->setTitle($req_data['title']);
        $column_entity->setBoardId($this->request->getIntParam('boardId'));
        $column_entity->setId($this->request->getIntParam('columnId'));

        $update_column = $this->boardColumnSerivce->handleUpdateColumn($user_id, $column_entity);
        $this->response->content(StatusCode::OK, null, null, $update_column);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function deleteColumn(): void
    {
        $user_id = $this->request->getUserId();
        $board_id = $this->request->getIntParam('boardId');
        $column_id = $this->request->getIntParam('columnId');

        $this->boardColumnSerivce->handleDeleteColumn($user_id, $board_id, $column_id);
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
        $req_data = Checker::checkMissingFields(
            $req_data, [
            'originalColumnId',
            'targetColumnId',
        ],  [
                'originalColumnId' => 'integer',
                'targetColumnId'   => 'integer',
            ]
        );

        $user_id = $this->request->getUserId();
        $board_id = $this->request->getIntParam('boardId');

        $this->boardColumnSerivce->handleSwapPositionOfCoupleColumn($user_id, $board_id, $req_data['originalColumnId'], $req_data['targetColumnId']);
        $this->response->content(StatusCode::OK, null, null, SuccessMessage::SWAP_POSITION_SUCCESSFULLY);
    }
}
