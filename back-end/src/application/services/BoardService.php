<?php
declare(strict_types=1);

namespace app\services;

use app\models\BoardModel;
use app\models\UserBoardModel;
use app\models\UserModel;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;
use shared\utils\Checker;

class BoardService
{
    public function __construct(
        private readonly BoardModel     $boardModel,
        private readonly UserBoardModel $userBoardModel,
        private readonly UserModel      $userModel
    ) {

    }

    /**
     * @throws ResponseException
     */
    public function handleCreateBoard(array $board_req_data): array
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

    /**
     * @throws ResponseException
     */
    public function handleGetMyBoards(): array
    {
        $user_id = $_SESSION['user_id'];

        $boards = $this->userBoardModel->join(
            [
                'table'     => 'boards',
                'as'        => 'b',
                'condition' => [
                    'board_id',
                    'id'
                ],
                'select'    => [
                    'id',
                    'title',
                    'created_at',
                    'updated_at',
                    'creator_id',
                ],
            ],
            [
                'where' => [
                    'user_id',
                    $user_id,
                ]
            ]
        );

        return array_map(
            function ($board) {
                $board["number_of_members"] = $this->userBoardModel->count('board_id', $board['id']);
                return $board;
            }, $boards
        );
    }

    /**
     * @throws ResponseException
     */
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

    /**
     * @throws ResponseException
     */
    public function handleDeleteBoard(int $board_id): void
    {
        $user_id = $_SESSION['user_id'];
        $board_to_delete = $this->boardModel->findOne('id', strval($board_id));

        if ($board_to_delete['creator_id'] !== $user_id) {
            throw new ResponseException(StatusCode::FORBIDDEN, StatusCode::FORBIDDEN->name, "You don't have permission to view the information of board with id [{$board_id}]");
        }

        $this->userBoardModel->deleteById(
            [
                $user_id,
                $board_id
            ]
        );
        $this->userBoardModel->delete('board_id', $board_id);
    }

    /**
     * @throws ResponseException
     */
    public function handleGetBoard(int $board_id): array
    {
        $user_id = $_SESSION['user_id'];
        $board = $this->boardModel->findOne('id', strval($board_id));
        $is_member = $this->userBoardModel->findOne(
            [], [
                  'user_id'  => $user_id,
                  'board_id' => $board['id'],
              ]
        );

        if (!$is_member) {
            throw new ResponseException(StatusCode::FORBIDDEN, StatusCode::FORBIDDEN->name, "you are not the member of board with id [{$board_id}]");
        }

        return $board;
    }

    /**
     * @throws ResponseException
     */
    public function handleAddMemberToBoard(int $board_id, array $req_data): void
    {
        Checker::checkMissingFields(
            [
                'member',
            ], $req_data
        );

        $user_id = $_SESSION['user_id'];
        $board = $this->boardModel->findOne('id', $board_id);
        if ($board['creator_id'] !== $user_id) {
            throw new ResponseException(StatusCode::FORBIDDEN, StatusCode::FORBIDDEN->name, "you don't have permission to add member to board with id [{$board_id}]");
        }

        $member_to_add = $this->userModel->findOne('username', $req_data['member']);

        if (!$member_to_add) {
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, "User with username [{$req_data['member']}] not found!");
        }

        $is_joined = $this->userBoardModel->findOne(
            [], [
                  'user_id'  => $member_to_add['id'],
                  'board_id' => $board_id,
              ]
        );

        if ($is_joined) {
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, "User with username [{$req_data['member']}] is a member of this board!");
        }

        $this->userBoardModel->save(
            [
                'userId'  => $member_to_add['id'],
                'boardId' => $board_id,
            ]
        );
    }

    /**
     * @throws ResponseException
     */
    public function handleLeaveBoard(int $board_id): void
    {
        $user_id = $_SESSION['user_id'];
        $board = $this->boardModel->findOne('id', strval($board_id));
        $is_member = $this->userBoardModel->findOne(
            [], [
                  'user_id'  => $user_id,
                  'board_id' => $board['id'],
              ]
        );

        if (!$is_member) {
            throw new ResponseException(StatusCode::FORBIDDEN, StatusCode::FORBIDDEN->name, "you are not the member of board with id [{$board_id}]");
        }

        $this->userBoardModel->deleteById(
            [
                $user_id,
                $board_id
            ]
        );
    }

    /**
     * @throws ResponseException
     */
    public function handleGetMembersOfBoard(int $board_id): array
    {
        $user_id = $_SESSION['user_id'];
        $is_member = $this->userBoardModel->findOne(
            [], [
                  'user_id'  => $user_id,
                  'board_id' => $board_id,
              ]
        );

        if (!$is_member) {
            throw new ResponseException(StatusCode::FORBIDDEN, StatusCode::FORBIDDEN->name, "you are not the member of board with id [{$board_id}]");
        }

        return $this->userBoardModel->join(
            [
                'table'     => 'users',
                'as'        => 'u',
                'condition' => [
                    'user_id',
                    'id'
                ],
                'select'    => [
                    'id',
                    'username',
                    'alias',
                    'email',
                ],
            ],
            [
                'where' => [
                    'board_id',
                    $board_id
                ]
            ]
        );
    }
}
