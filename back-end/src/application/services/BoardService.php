<?php
declare(strict_types=1);

namespace app\services;

use app\controllers\UserBoardService;
use app\entities\BoardEntity;
use app\entities\ColumnEntity;
use app\models\BoardModel;
use app\models\UserBoardModel;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class BoardService
{
    public function __construct(
        private readonly BoardModel     $boardModel,
        private readonly UserService    $userService,
        private readonly UserBoardModel $userBoardModel,
        private readonly ColumnService $columnService,
    ) {

    }

    /**
     * @throws ResponseException
     */
    public function handleCreateBoard(BoardEntity $board_entity): array
    {
        $new_board = $this->boardModel->save(
            $board_entity->toArray(),
        );

        $this->userBoardModel->save(
            [
                'user_id'  => $board_entity->getCreatorId(),
                'board_id' => $new_board['id']
            ]
        );

        return $new_board;
    }

    /**
     * @throws ResponseException
     */
    public function handleGetMyBoards(int $user_id): array
    {
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

    public function handleUpdateBoard(int $user_id, BoardEntity $board_entity): array
    {
        $board_to_update = $this->checkExistedBoard($board_entity->getId());

        $this->checkOwnerOfBoard($user_id, $board_to_update);

        $board_to_update['title'] = $board_entity->getTitle();
        $board_to_update['updated_at'] = (new \DateTime())->format('Y-m-d H:i:s');
        return $this->boardModel->update($board_to_update);
    }

    public function handleDeleteBoard(int $user_id, int $board_id): void
    {
        $board_to_delete = $this->boardModel->findOne('id', $board_id);
        $this->checkOwnerOfBoard($user_id, $board_to_delete);

        $this->userBoardModel->deleteById(
            [
                $user_id,
                $board_id
            ]
        );
        $this->userBoardModel->delete('board_id', $board_id);
        $this->columnService->handleDeleteColumnByBoardId($board_id);
        $this->boardModel->deleteById($board_id);
    }

    public function handleGetBoard(int $user_id, int $board_id): array
    {
        $board = $this->boardModel->findOne('id', $board_id);
        if (!$board) {
            throw new ResponseException(StatusCode::NOT_FOUND, StatusCode::NOT_FOUND->name, "Board id [$board_id] not found");
        }

        $this->checkMemberOfBoard($user_id, $board_id);
        return $board;
    }

    /**
     * @throws ResponseException
     */
    public function handleAddMemberToBoard(int $user_id, int $board_id, string $member_username): void
    {
        $board_need_add_member = $this->checkExistedBoard($board_id);
        $this->checkOwnerOfBoard($user_id, $board_need_add_member);
        $member_to_add = $this->userService->getUserByUsername($member_username);

        $is_joined = $this->userBoardModel->findOne(
            null, [
                    'user_id'  => $member_to_add['id'],
                    'board_id' => $board_id,
                ]
        );

        if ($is_joined) {
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, "User with username [$member_username] is a member of this board!");
        }

        $this->userBoardModel->save(
            [
                'user_id'  => $member_to_add['id'],
                'board_id' => $board_id,
            ]
        );
    }

    /**
     * @throws ResponseException
     */
    public function handleLeaveBoard(int $user_id, int $board_id): void
    {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);

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
    public function handleGetMembersOfBoard(int $user_id, int $board_id): array
    {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);

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

    public function checkMemberOfBoard(int $user_id, int $board_id): void
    {
        $is_member = $this->userBoardModel->findOne(
            null, [
                    'user_id'  => $user_id,
                    'board_id' => $board_id,
                ]
        );

        if (!$is_member) {
            throw new ResponseException(StatusCode::FORBIDDEN, StatusCode::FORBIDDEN->name, "you are not the member of board with id [{$board_id}]");
        }
    }

    public function checkExistedBoard(int $board_id): array
    {
        $board = $this->boardModel->findOne('id', $board_id);
        if (!$board) {
            throw new ResponseException(StatusCode::NOT_FOUND, StatusCode::NOT_FOUND->name, "Board id [$board_id] not found");
        }

        return $board;
    }

    public function checkOwnerOfBoard(int $user_id, array $board): void
    {
        if ($user_id !== $board['creator_id']) {
            throw new ResponseException(StatusCode::FORBIDDEN, StatusCode::FORBIDDEN->name, "You are not the owner of this board!");
        }
    }

    public function handleCreateColumn(int $user_id, ColumnEntity $column_entity): array {
        $this->checkExistedBoard($column_entity->getBoardId());
        $this->checkMemberOfBoard($user_id, $column_entity->getBoardId());

        $column_entity->setCreatorId($user_id);
        return $this->columnService->handleCreateColumn($column_entity);
    }

    public function handleGetColumnsOfBoard(int $user_id, int $board_id): array {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);

        return $this->columnService->handleGetColumnsOfBoard($board_id);
    }
}
