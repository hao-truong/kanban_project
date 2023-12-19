<?php

declare(strict_types=1);

namespace app\services;

use app\entities\ColumnEntity;
use shared\exceptions\ResponseException;

class BoardColumnSerivce
{
    public function __construct(
        private readonly BoardService $boardService,
        private readonly ColumnService $columnService,
    ) {
    }

    /**
     * @param int $user_id
     * @param ColumnEntity $column_entity
     * @return array
     * @throws ResponseException
     */
    public function handleCreateColumn(int $user_id, ColumnEntity $column_entity): array
    {
        $this->boardService->checkExistedBoard($column_entity->getBoardId());
        $this->boardService->checkMemberOfBoard($user_id, $column_entity->getBoardId());
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
        $this->boardService->checkExistedBoard($board_id);
        $this->boardService->checkMemberOfBoard($user_id, $board_id);
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
        $this->boardService->checkExistedBoard($column_entity->getBoardId());
        $this->boardService->checkMemberOfBoard($user_id, $column_entity->getBoardId());
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
        $this->boardService->checkExistedBoard($board_id);
        $this->boardService->checkMemberOfBoard($user_id, $board_id);
        $this->columnService->handleDeleteColumn($column_id, $board_id);
    }

    /**
     * @param int $user_id
     * @param int $column_id_first
     * @param int $column_id_second
     * @return void
     * @throws ResponseException
     */
    public function handleSwapPositionOfCoupleColumn(
        int $user_id,
        int $board_id,
        int $column_id_first,
        int $column_id_second
    ): void {
        $this->boardService->checkMemberOfBoard($user_id, $board_id);

        $column_first = $this->columnService->checkColumnInBoard($column_id_first, $board_id);
        $column_second = $this->columnService->checkColumnInBoard($column_id_second, $board_id);

        $this->boardService->checkMemberOfBoard($user_id, $column_first['board_id']);
        $this->columnService->handleSwapPositionOfCoupleColumn(
            ColumnEntity::fromArray($column_first),
            ColumnEntity::fromArray($column_second)
        );
    }
}
