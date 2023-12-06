<?php
declare(strict_types=1);

namespace app\services;

use app\entities\ColumnEntity;
use app\models\ColumnModel;
use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class  ColumnService
{
    public function __construct(
        private readonly ColumnModel  $columnModel,
        private readonly CardService $cardService,
    ) {
    }

    /**
     * @param ColumnEntity $column_entity
     * @return array
     * @throws ResponseException
     */
    public function handleCreateColumn(ColumnEntity $column_entity): array
    {
        $position = $this->columnModel->count('board_id', $column_entity->getBoardId());
        $column_entity->setPosition($position + 1);

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

    /**
     * @param int $column_id
     * @return array
     * @throws ResponseException
     */
    public function checkExistedColumn(int $column_id): array {
        $column = $this->columnModel->findOne('id', $column_id);

        if(!$column) {
            throw new ResponseException(StatusCode::NOT_FOUND, StatusCode::NOT_FOUND->name, ErrorMessage::COLUMN_NOT_FOUND);
        }

        return $column;
    }

    /**
     * @param int $column_id
     * @param int $board_id
     * @return array
     * @throws ResponseException
     */
    public function checkColumnInBoard(int $column_id, int $board_id): array {
        $matched_column = $this->checkExistedColumn($column_id);

        if($matched_column['board_id'] !== $board_id) {
            throw new ResponseException(StatusCode::NOT_FOUND, StatusCode::NOT_FOUND->name, ErrorMessage::COLUMN_NOT_IN_BOARD);
        }

        return $matched_column;
    }

    /**
     * @param ColumnEntity $entity
     * @return array
     * @throws ResponseException
     */
    public function handleUpdateColumn(ColumnEntity $entity): array {
        $column_to_update = $this->checkColumnInBoard($entity->getId(), $entity->getBoardId());
        $column_to_update['title'] = $entity->getTitle();
        $column_to_update['updated_at'] = (new \DateTime())->format('Y-m-d H:i:s');

        return $this->columnModel->update($column_to_update);
    }

    /**
     * @param int $column_id
     * @return void
     * @throws ResponseException
     */
    public function handleDeleteColumn(int $column_id, int $board_id): void {
        $this->checkColumnInBoard($column_id, $board_id);
        $this->columnModel->deleteById($column_id);
    }

    /**
     * @param ColumnEntity $column_first
     * @param ColumnEntity $column_second
     * @return void
     * @throws ResponseException
     */
    public function handleSwapPositionOfCoupleColumn(ColumnEntity $column_first, ColumnEntity $column_second): void {
        $temp_position = $column_first->getPosition();
        $column_first->setPosition($column_second->getPosition());
        $column_second->setPosition($temp_position);
        $column_first->setUpdatedAt(new \DateTime());
        $column_second->setUpdatedAt(new \DateTime());

        $this->columnModel->update($column_first->toFullArray());
        $this->columnModel->update($column_second->toFullArray());
    }
}
