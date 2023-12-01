<?php
declare(strict_types=1);

namespace app\services;

use app\models\BoardModel;
use app\models\UserBoardModel;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;
use shared\utils\Checker;

class BoardService
{
    public function __construct(private BoardModel $boardModel, private UserBoardModel $userBoardModel)
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

        $new_board = $this->boardModel->save(
            [
                'title'     => $board_req_data['title'],
                'creatorId' => $user_id
            ]
        );

        $this->userBoardModel->save(
            [
                'userId'  => $user_id,
                'boardId' => $new_board['id']
            ]
        );

        return $new_board;
    }

    public function handleGetMyBoards()
    {
        $user_id = $_SESSION['user_id'];

        return $this->userBoardModel->join(
            [
                'table'    => 'boards',
                'as' => 'b',
                'condition' => ['board_id', 'id'],
                'select' => ['id', 'title', 'created_at', 'updated_at'],
            ],
            [
                'where' => ['user_id', $user_id]
            ]
        );
    }

    public function handleUpdateBoard(int $board_id, array $req_data): array
    {
        Checker::checkMissingFields(
            [
                'title',
            ], $req_data
        );

        $user_id = $_SESSION['user_id'];
        $board_to_update = $this->boardModel->findOne('id', strval($board_id));

        if ($board_to_update['creator_id'] !== $user_id) {
            throw new ResponseException(StatusCode::FORBIDDEN, StatusCode::FORBIDDEN->name, "you don't have permission to update board with id [{$board_id}]");
        }

        $board_to_update['title'] = $req_data['title'];
        $board_to_update['updated_at'] = (new \DateTime())->format('Y-m-d H:i:s');
        return $this->boardModel->update($board_to_update);
    }

    public function handleDeleteBoard(int $board_id): void
    {
        $user_id = $_SESSION['user_id'];
        $board_to_delete = $this->boardModel->findOne('id', strval($board_id));

        if ($board_to_delete['creator_id'] !== $user_id) {
            throw new ResponseException(StatusCode::FORBIDDEN, StatusCode::FORBIDDEN->name, "You don't have permission to update board with id [{$board_id}]");
        }

        $this->userBoardModel->deleteById(
            [
                $user_id,
                $board_id
            ]
        );
        $this->boardModel->deleteById($board_id);
    }

    public function handleGetBoard(int $board_id): array
    {
        $user_id = $_SESSION['user_id'];
        $board = $this->boardModel->findOne('id', strval($board_id));
        $is_member = $this->userBoardModel->findOne([], [
            'user_id' => $user_id,
            'board_id' => $board['id'],
        ]);

        if (!$is_member) {
            throw new ResponseException(StatusCode::FORBIDDEN, StatusCode::FORBIDDEN->name, "you don't have permission to update board with id [{$board_id}]");
        }

        return $board;
    }
}
