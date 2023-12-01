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

    public function getMyBoards() {
        $boards = $this->boardService->handleGetMyBoards();
        return $this->response->content(StatusCode::OK, "Get your boards successfully!", null, $boards);
    }
}
