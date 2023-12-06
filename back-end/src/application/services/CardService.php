<?php
declare(strict_types=1);

namespace app\services;

use app\entities\CardEntity;
use app\models\CardModel;
use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class CardService
{
    public function __construct(private readonly CardModel $cardModel)
    {
    }

    public function checkCardInColumn($column_id, $card_id): array
    {
        $matched_card = $this->cardModel->findOne('id', $card_id);

        if (!$matched_card) {
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, ErrorMessage::CARD_NOT_FOUND);
        }

        if ($matched_card['column_id'] !== $column_id) {
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, ErrorMessage::CARD_NOT_IN_COLUMN);
        }

        return $matched_card;
    }

    /**
     * @param CardEntity $card_entity
     * @return array
     * @throws \shared\exceptions\ResponseException
     */
    public function handleCreateCard(CardEntity $card_entity): array
    {
        $position = $this->cardModel->count('column_id', $card_entity->getColumnId());
        $card_entity->setPosition($position + 1);
        return $this->cardModel->save($card_entity->toCreateArray());
    }

    /**
     * @param int $column_id
     * @return array
     * @throws \shared\exceptions\ResponseException
     */
    public function handleGetCardsByColumn(int $column_id): array
    {
        return $this->cardModel->find('column_id', $column_id);
    }

    /**
     * @param CardEntity $card_entity
     * @return array
     * @throws ResponseException
     */
    public function handleUpdateTitleCard(CardEntity $card_entity): array
    {
        $matched_card = $this->checkCardInColumn($card_entity->getColumnId(), $card_entity->getId());

        $matched_card['title'] = $card_entity->getTitle();
        return $this->cardModel->update($matched_card);
    }
}
