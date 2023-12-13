<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\entities\BoardEntity;
use app\services\BoardService;
use shared\enums\StatusCode;
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
        $req_data = Checker::checkMissingFields(
            $req_data,
            [
                'title',
            ], [
                'title' => 'string'
            ]
        );

        $board_entity = new BoardEntity();
        $board_entity->setTitle($req_data['title']);
        $board_entity->setCreatorId($this->request->getUserId());

        $new_board = $this->boardService->handleCreateBoard($board_entity);
        $this->response->content(StatusCode::CREATED, null, null, $new_board);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function getMyBoards(): void
    {
        $user_id = $this->request->getUserId();
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
        $req_data = Checker::checkMissingFields(
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
        $user_id = $this->request->getUserId();

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
        $user_id = $this->request->getUserId();

        $this->boardService->handleDeleteBoard($user_id, $board_id);
        $this->response->content(StatusCode::OK, null, null, "Delete board id [{$board_id}] successfully!");
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function getBoard(): void
    {
        $user_id = $this->request->getUserId();
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
        $req_data = Checker::checkMissingFields(
            $req_data,
            [
                'member',
            ], [
                'member' => 'string'
            ]
        );

        $board_id = $this->request->getIntParam('boardId');
        $user_id = $this->request->getUserId();
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
        $user_id = $this->request->getUserId();

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
        $user_id = $this->request->getUserId();

        $members = $this->boardService->handleGetMembersOfBoard($user_id, $board_id);
        $this->response->content(StatusCode::OK, null, null, $members);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function searchBoard(): void
    {
        $req_queries = $this->request->getQueries();
        $req_queries = Checker::checkMissingFields(
            $req_queries, [
            'title',
        ],  [
                'title' => 'string',
            ]
        );

        $user_id = $this->request->getUserId();
        $title_search = urldecode($req_queries['title']);
        $boards = $this->boardService->handleSearchByTitle($user_id, $title_search);
        $this->response->content(StatusCode::OK, null, null, $boards);
    }
}
