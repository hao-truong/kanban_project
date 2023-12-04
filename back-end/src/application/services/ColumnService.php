<?php
declare(strict_types=1);

namespace app\services;

use app\entities\ColumnEntity;
use app\models\BoardModel;
use app\models\ColumnModel;
use app\models\UserBoardModel;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;
use shared\utils\Checker;

class  ColumnService
{
    public function __construct(
        private readonly ColumnModel  $columnModel,
        private readonly BoardService $boardService,
    ) {
    }

    public function handleCreateColumn(int $user_id, ColumnEntity $column_entity): array
    {
        $board = $this->boardService->checkExistedBoard($column_entity->getBoardId());
        $this->boardService->checkMemberOfBoard($user_id, $board['id']);
        $column_entity->setCreatorId($user_id);

        return $this->columnModel->save(
            $column_entity->toArray(),
        );
    }
}
