<?php
declare(strict_types=1);

namespace app\services;

use app\entities\ColumnEntity;
use app\models\ColumnModel;

class  ColumnService
{
    public function __construct(
        private readonly ColumnModel  $columnModel,
    ) {
    }

    public function handleCreateColumn(ColumnEntity $column_entity): array
    {
        return $this->columnModel->save(
            $column_entity->toArray(),
        );
    }

    public function handleGetColumnsOfBoard(int $board_id): array
    {
        return $this->columnModel->find('board_id', $board_id);
    }

    public function handleDeleteColumnByBoardId(int $board_id): void {
        $this->columnModel->delete('board_id', $board_id);
    }
}
