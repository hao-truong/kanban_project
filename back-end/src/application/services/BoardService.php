<?php
declare(strict_types=1);

namespace app\services;

use app\models\BoardModel;
use shared\utils\Checker;

class BoardService
{
    public function __construct(private BoardModel $boardModel)
    {

    }

    public function handleCreateBoard(array $board_req_data)
    {
        Checker::checkMissingFields(
            [
                'title',
            ], $board_req_data
        );

        $user_id = $_SESSION['user_id'];

        return $this->boardModel->save(
            [
                'title'     => $board_req_data['title'],
                'creatorId' => $user_id
            ]
        );
    }

    public function handleGetMyBoards() {
        $user_id = $_SESSION['user_id'];

        return $this->boardModel->find('creator_id', strval($user_id));
    }
}
