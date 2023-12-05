<?php
declare(strict_types=1);

namespace app\services;

use app\entities\ColumnEntity;
use app\models\ColumnModel;
use shared\exceptions\ResponseException;

class  ColumnService
{
    public function __construct(
        private readonly ColumnModel  $columnModel,
    ) {
    }

    /**
     * @param ColumnEntity $column_entity
     * @return array
     * @throws ResponseException
     */
    public function handleCreateColumn(ColumnEntity $column_entity): array
    {
        return $this->columnModel->save(
            $column_entity->toArray(),
        );
    }

    /**
     * @param int $board_id
     * @return array
     * @throws ResponseException
     */
    public function handleGetColumnsOfBoard(int $board_id): array
    {
        return $this->columnModel->find('board_id', $board_id);
    }

    /**
     * @param int $board_id
     * @return void
     * @throws ResponseException
     */
    public function handleDeleteColumnByBoardId(int $board_id): void {
        $this->columnModel->delete('board_id', $board_id);
    }
}
