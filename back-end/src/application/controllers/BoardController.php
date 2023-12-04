<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\entities\BoardEntity;
use app\services\BoardService;
use JetBrains\PhpStorm\NoReturn;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;
use shared\utils\Checker;

class BoardController
{
    public function __construct(
        private readonly Request      $request,
        private readonly Response     $response,
        private readonly BoardService $boardService
    ) {

    }

    #[NoReturn] public function createBoard()
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
        $board_entity->setCreatorId($_SESSION['user_id']);

        $new_board = $this->boardService->handleCreateBoard($board_entity);
        return $this->response->content(StatusCode::CREATED, "Create new board successfully!", null, $new_board);
    }

    #[NoReturn] public function getMyBoards()
    {
        $user_id = $_SESSION['user_id'];
        $boards = $this->boardService->handleGetMyBoards($user_id);
        return $this->response->content(StatusCode::OK, "Get your boards successfully!", null, $boards);
    }

    #[NoReturn] public function updateBoard()
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
        $board_entity->setId(intval($this->request->getParam('boardId')));
        $user_id = $_SESSION['user_id'];

        $board = $this->boardService->handleUpdateBoard($user_id, $board_entity);
        return $this->response->content(StatusCode::OK, "Get your boards successfully!", null, $board);
    }

    #[NoReturn] public function deleteBoard()
    {
        $board_id = intval($this->request->getParam('boardId'));
        $user_id = $_SESSION['user_id'];

        $this->boardService->handleDeleteBoard($user_id, $board_id);
        return $this->response->content(StatusCode::OK, "Delete board id [{$board_id}] successfully!", null, "Delete board id [{$board_id}] successfully!");
    }

    /**
     * @return null
     * @throws ResponseException
     */
    #[NoReturn] public function getBoard()
    {
        $user_id = $_SESSION['user_id'];
        $board_id = intval($this->request->getParam('boardId'));
        $board = $this->boardService->handleGetBoard($user_id, $board_id);
        return $this->response->content(StatusCode::OK, "Get board id [{$board_id}] successfully!", null, $board);
    }

    /**
     * @return null
     * @throws ResponseException
     */
    #[NoReturn] public function addMemberToBoard()
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

        $board_id = intval($this->request->getParam('boardId'));
        $user_id = $_SESSION['user_id'];
        $this->boardService->handleAddMemberToBoard($user_id, $board_id, $req_data['member']);
        return $this->response->content(StatusCode::OK, "Add member with username [{$req_data['member']}] to this board successfully!", null, "Add member with username [{$req_data['member']}] to this board successfully!");
    }

    #[NoReturn] public function leaveBoard()
    {
        $board_id = intval($this->request->getParam('boardId'));
        $user_id = $_SESSION['user_id'];

        $this->boardService->handleLeaveBoard($user_id, $board_id);
        return $this->response->content(StatusCode::OK, "Leave board with id [{$board_id}] successfully!", null, "Leave board with id [{$board_id}] successfully!");
    }

    /**
     * @return null
     * @throws ResponseException
     */
    #[NoReturn] public function getMembersOfBoard()
    {
        $board_id = intval($this->request->getParam('boardId'));
        $user_id = $_SESSION['user_id'];

        $members = $this->boardService->handleGetMembersOfBoard($user_id, $board_id);
        return $this->response->content(StatusCode::OK, "Get members of board with id [{$board_id}] successfully!", null, $members);
    }
}
