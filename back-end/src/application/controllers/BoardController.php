<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\services\BoardService;
use shared\enums\StatusCode;

class BoardController
{
    public function __construct(
        private Request      $request,
        private Response     $response,
        private BoardService $boardService
    ) {

    }

    public function createBoard()
    {
        $new_board = $this->boardService->handleCreateBoard($this->request->getBody());
        return $this->response->content(StatusCode::CREATED, "Create new board successfully!", null, $new_board);
    }

    public function getMyBoards()
    {
        $boards = $this->boardService->handleGetMyBoards();
        return $this->response->content(StatusCode::OK, "Get your boards successfully!", null, $boards);
    }

    public function updateBoard()
    {
        $board_id = $this->request->getParam('boardId');
        $board = $this->boardService->handleUpdateBoard(intval($board_id), $this->request->getBody());
        return $this->response->content(StatusCode::OK, "Get your boards successfully!", null, $board);
    }

    public function deleteBoard()
    {
        $board_id = $this->request->getParam('boardId');
        $this->boardService->handleDeleteBoard(intval($board_id));
        return $this->response->content(StatusCode::OK, "Delete board id [{$board_id}] successfully!", null, null);
    }

    public function getBoard()
    {
        $board_id = $this->request->getParam('boardId');
        $board = $this->boardService->handleGetBoard(intval($board_id));
        return $this->response->content(StatusCode::OK, "Get board id [{$board_id}] successfully!", null, $board);
    }

    public function addMemberToBoard()
    {
        $board_id = $this->request->getParam('boardId');
        $req_data = $this->request->getBody();
        $this->boardService->handleAddMemberToBoard(intval($board_id), $req_data);
        return $this->response->content(StatusCode::OK, "Add member with username [{$req_data['member']}] to this board successfully!", null, "Add member with username [{$req_data['member']}] to this board successfully!");
    }

    public function leaveBoard()
    {
        $board_id = $this->request->getParam('boardId');
        $this->boardService->handleLeaveBoard(intval($board_id));
        return $this->response->content(StatusCode::OK, "Leave board with id [{$board_id}] successfully!", null, "Leave board with id [{$board_id}] successfully!");
    }

    public function getMembersOfBoard()
    {
        $board_id = $this->request->getParam('boardId');
        $members = $this->boardService->handleGetMembersOfBoard(intval($board_id));

        return $this->response->content(StatusCode::OK, "Get members of board with id [{$board_id}] successfully!", null, $members);
    }
}
