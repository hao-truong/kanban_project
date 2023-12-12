<?php
declare(strict_types=1);

namespace app\services;

use app\controllers\UserBoardService;
use app\entities\BoardEntity;
use app\entities\CardEntity;
use app\entities\ColumnEntity;
use app\models\BoardModel;
use app\models\UserBoardModel;
use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class BoardService
{
    public function __construct(
        private readonly BoardModel     $boardModel,
        private readonly UserService    $userService,
        private readonly UserBoardModel $userBoardModel,
        private readonly ColumnService  $columnService,
        private readonly CardService    $cardService,
    ) {
    }

    /**
     * @param BoardEntity $board_entity
     * @return array
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
     * @param int $user_id
     * @return array
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

        return $this->boardsWithNumberOfMembers($boards);
    }

    /**
     * @param int $user_id
     * @param BoardEntity $board_entity
     * @return array
     * @throws ResponseException
     */
    public function handleUpdateBoard(int $user_id, BoardEntity $board_entity): array
    {
        $board_to_update = $this->checkExistedBoard($board_entity->getId());

        $this->checkOwnerOfBoard($user_id, $board_to_update);

        $board_to_update['title'] = $board_entity->getTitle();
        $board_to_update['updated_at'] = (new \DateTime())->format('Y-m-d H:i:s');
        return $this->boardModel->update($board_to_update);
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @return void
     * @throws ResponseException
     */
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

    /**
     * @param int $user_id
     * @param int $board_id
     * @return array
     * @throws ResponseException
     */
    public function handleGetBoard(int $user_id, int $board_id): array
    {
        $board = $this->boardModel->findOne('id', $board_id);
        if (!$board) {
            throw new ResponseException(StatusCode::NOT_FOUND, StatusCode::NOT_FOUND->name, ErrorMessage::BOARD_NOT_FOUND);
        }

        $this->checkMemberOfBoard($user_id, $board_id);
        return $board;
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @param string $member_username
     * @return void
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
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, ErrorMessage::JOINED_MEMBER_BOARD);
        }

        $this->userBoardModel->save(
            [
                'user_id'  => $member_to_add['id'],
                'board_id' => $board_id,
            ]
        );
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @return void
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
     * @param int $user_id
     * @param int $board_id
     * @return array
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

    /**
     * @param int $user_id
     * @param int $board_id
     * @return void
     * @throws ResponseException
     */
    public function checkMemberOfBoard(int $user_id, int $board_id): void
    {
        $is_member = $this->userBoardModel->findOne(
            null, [
                    'user_id'  => $user_id,
                    'board_id' => $board_id,
                ]
        );

        if (!$is_member) {
            throw new ResponseException(StatusCode::FORBIDDEN, StatusCode::FORBIDDEN->name, ErrorMessage::NOT_BOARD_MEMBER);
        }
    }

    /**
     * @param int $board_id
     * @return array
     * @throws ResponseException
     */
    public function checkExistedBoard(int $board_id): array
    {
        $board = $this->boardModel->findOne('id', $board_id);
        if (!$board) {
            throw new ResponseException(StatusCode::NOT_FOUND, StatusCode::NOT_FOUND->name, ErrorMessage::BOARD_NOT_FOUND);
        }

        return $board;
    }

    /**
     * @param int $user_id
     * @param array $board
     * @return void
     * @throws ResponseException
     */
    public function checkOwnerOfBoard(int $user_id, array $board): void
    {
        if ($user_id !== $board['creator_id']) {
            throw new ResponseException(StatusCode::FORBIDDEN, StatusCode::FORBIDDEN->name, ErrorMessage::NOT_BOARD_OWNER);
        }
    }

    /**
     * @param int $user_id
     * @param ColumnEntity $column_entity
     * @return array
     * @throws ResponseException
     */
    public function handleCreateColumn(int $user_id, ColumnEntity $column_entity): array
    {
        $this->checkExistedBoard($column_entity->getBoardId());
        $this->checkMemberOfBoard($user_id, $column_entity->getBoardId());
        $column_entity->setCreatorId($user_id);
        return $this->columnService->handleCreateColumn($column_entity);
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @return array
     * @throws ResponseException
     */
    public function handleGetColumnsOfBoard(int $user_id, int $board_id): array
    {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);

        return $this->columnService->handleGetColumnsOfBoard($board_id);
    }

    /**
     * @param int $user_id
     * @param ColumnEntity $column_entity
     * @return array
     * @throws ResponseException
     */
    public function handleUpdateColumn(int $user_id, ColumnEntity $column_entity): array
    {
        $this->checkExistedBoard($column_entity->getBoardId());
        $this->checkMemberOfBoard($user_id, $column_entity->getBoardId());

        return $this->columnService->handleUpdateColumn($column_entity);
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @param int $column_id
     * @return void
     * @throws ResponseException
     */
    public function handleDeleteColumn(int $user_id, int $board_id, int $column_id): void
    {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);
        $this->columnService->handleDeleteColumn($column_id, $board_id);
    }

    /**
     * @param int $user_id
     * @param int $column_id_first
     * @param int $column_id_second
     * @return void
     * @throws ResponseException
     */
    public function handleSwapPositionOfCoupleColumn(int $user_id, int $board_id, int $column_id_first, int $column_id_second): void
    {
        $this->checkMemberOfBoard($user_id, $board_id);

        $column_first = $this->columnService->checkColumnInBoard($column_id_first, $board_id);
        $column_second = $this->columnService->checkColumnInBoard($column_id_second, $board_id);

        $this->checkMemberOfBoard($user_id, $column_first['board_id']);
        $this->columnService->handleSwapPositionOfCoupleColumn(ColumnEntity::fromArray($column_first), ColumnEntity::fromArray($column_second));
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @param CardEntity $card_entity
     * @return array
     * @throws ResponseException
     */
    public function handleCreateCardForColumn(int $user_id, int $board_id, CardEntity $card_entity): array
    {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);
        $this->columnService->checkColumnInBoard($card_entity->getColumnId(), $board_id);

        return $this->cardService->handleCreateCard($card_entity);
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @param int $column_id
     * @return array
     * @throws ResponseException
     */
    public function handleGetCardsOfColumn(int $user_id, int $board_id, int $column_id): array
    {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);
        $this->columnService->checkColumnInBoard($column_id, $board_id);

        return $this->cardService->handleGetCardsByColumn($column_id);
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @param CardEntity $card_entity
     * @return array
     * @throws ResponseException
     */
    public function handleUpdateTitleCard(int $user_id, int $board_id, CardEntity $card_entity): array
    {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);
        $this->columnService->checkColumnInBoard($card_entity->getColumnId(), $board_id);

        return $this->cardService->handleUpdateTitleCard($card_entity);
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @param int $column_id
     * @param int $card_id
     * @return void
     * @throws ResponseException
     */
    public function handleDeleteCardOfColumn(int $user_id, int $board_id, int $column_id, int $card_id): void
    {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);
        $this->columnService->checkColumnInBoard($column_id, $board_id);
        $this->cardService->checkCardInColumn($column_id, $card_id);
        $this->cardService->handleDeleteCard($card_id);
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @param int $column_id
     * @param int $card_id
     * @return void
     * @throws ResponseException
     */
    public function handleAssignMeToCard(int $user_id, int $board_id, int $column_id, int $card_id): void
    {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);
        $this->columnService->checkColumnInBoard($column_id, $board_id);
        $this->cardService->checkCardInColumn($column_id, $card_id);
        $this->cardService->handleAssignMemberToBoard($user_id, $card_id);
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @param int $column_id
     * @param int $card_id
     * @param int $user_need_assign
     * @return void
     * @throws ResponseException
     */
    public function handleAssignMemberToCard(int $user_id, int $board_id, int $column_id, int $card_id, int $user_need_assign): void
    {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);
        $this->checkMemberOfBoard($user_need_assign, $board_id);
        $this->columnService->checkColumnInBoard($column_id, $board_id);
        $this->cardService->checkCardInColumn($column_id, $card_id);
        $this->cardService->handleAssignMemberToBoard($user_need_assign, $card_id);
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @param int $column_id
     * @param int $card_id
     * @return array
     * @throws ResponseException
     */
    public function handleGetDetailCard(int $user_id, int $board_id, int $column_id, int $card_id): array
    {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);
        $this->columnService->checkColumnInBoard($column_id, $board_id);
        $this->cardService->checkCardInColumn($column_id, $card_id);
        return $this->cardService->handleGetDetailCard($card_id);
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @param int $column_id
     * @param int $card_id
     * @return array
     * @throws ResponseException
     */
    public function handleChangeColumnForCard(int $user_id, int $board_id, int $column_id, int $card_id, int $destination_column_id): array
    {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);
        $this->columnService->checkColumnInBoard($column_id, $board_id);
        $this->columnService->checkColumnInBoard($destination_column_id, $board_id);
        $this->cardService->checkCardInColumn($column_id, $card_id);

        return $this->cardService->handleChangeColumn($card_id, $destination_column_id);
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @param int $column_id
     * @param int $card_id
     * @param string $new_description
     * @return array
     * @throws ResponseException
     */
    public function handleUpdateDescriptionOfCard(int $user_id, int $board_id, int $column_id, int $card_id, string $new_description): array
    {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);
        $this->columnService->checkColumnInBoard($column_id, $board_id);
        $this->cardService->checkCardInColumn($column_id, $card_id);

        return $this->cardService->handleUpdateDescription($card_id, $new_description);
    }

    /**
     * @param int $user_id
     * @param int $board_id
     * @param array $req_data
     * @return void
     * @throws ResponseException
     */
    public function handleMoveCard(int $user_id, int $board_id, array $req_data): void
    {
        $this->checkExistedBoard($board_id);
        $this->checkMemberOfBoard($user_id, $board_id);
        $this->columnService->checkColumnInBoard($req_data['originalColumnId'], $board_id);
        $this->columnService->checkColumnInBoard($req_data['targetColumnId'], $board_id);
        $this->cardService->handleMoveCardInBoard(
            $req_data['originalCardId'],
            $req_data['originalColumnId'],
            $req_data['targetCardId'],
            $req_data['targetColumnId']
        );
    }

    /**
     * @param int $user_id
     * @param string $title
     * @return array
     * @throws ResponseException
     */
    public function handleSearchByTitle(int $user_id, string $title): array
    {
        $boards = $this->boardModel->search('title', $title, $user_id);
        return $this->boardsWithNumberOfMembers($boards);
    }

    /**
     * @param array $boards
     * @return array
     * @throws ResponseException
     */
    public function boardsWithNumberOfMembers(array $boards): array
    {
        return array_map(
            function ($board) {
                $board["number_of_members"] = $this->userBoardModel->count('board_id', $board['id']);
                return $board;
            }, $boards
        );
    }
}
