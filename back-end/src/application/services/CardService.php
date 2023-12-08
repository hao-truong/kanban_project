<?php
declare(strict_types=1);

namespace app\services;

use app\entities\CardEntity;
use app\models\CardModel;
use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;
use shared\utils\Converter;

class CardService
{
    public function __construct(private readonly CardModel $cardModel)
    {
    }

    /**
     * @param int $card_id
     * @return array
     * @throws ResponseException
     */
    public function checkExistedCard(int $card_id): array
    {
        $matched_card = $this->cardModel->findOne('id', $card_id);

        if (!$matched_card) {
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, ErrorMessage::CARD_NOT_FOUND);
        }

        return $matched_card;
    }

    /**
     * @param int $column_id
     * @param int $card_id
     * @return array
     * @throws ResponseException
     */
    public function checkCardInColumn(int $column_id, int $card_id): array
    {
        $matched_card = $this->checkExistedCard($card_id);

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
     * @throws ResponseException
     */
    public function handleGetCardsByColumn(int $column_id): array
    {
        $cards = $this->cardModel->join(
            [
                'table'     => 'users',
                'as'        => 'u',
                'condition' => [
                    'assigned_user',
                    'id'
                ],
                'select'    => [
                    'id as assigned_user_id',
                    'username as assigned_user_username',
                    'alias as assigned_user_alias',
                    'email as assigned_user_email',
                ],
            ],
            [
                'where' => [
                    'column_id',
                    $column_id
                ]
            ]
        );

        return array_map(
            function ($card) {
                return Converter::toCardResponse($card);
            }, $cards
        );
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

    /**
     * @param int $card_id
     * @return void
     * @throws ResponseException
     */
    public function handleDeleteCard(int $card_id): void
    {
        $this->cardModel->deleteById($card_id);
    }

    /**
     * @param int $column_id
     * @return void
     * @throws ResponseException
     */
    public function handleDeleteCardsFollowingColumn(int $column_id): void
    {
        $this->cardModel->delete('column_id', $column_id);
    }

    /**
     * @param int $user_id
     * @param int $card_id
     * @return array
     * @throws ResponseException
     */
    public function checkAssignedUserOfCard(int $user_id, int $card_id): array
    {
        $matched_card = $this->cardModel->findOne('id', $card_id);

        if ($matched_card['assigned_user'] && $matched_card['assigned_user'] === $user_id) {
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, ErrorMessage::USER_WAS_ASSIGNED_USER_OF_THIS_CARD);
        }

        return $matched_card;
    }

    /**
     * @param int $user_id
     * @param int $card_id
     * @return void
     * @throws ResponseException
     */
    public function handleAssignMemberToBoard(int $user_id, int $card_id): void
    {
        $card_to_update = $this->checkAssignedUserOfCard($user_id, $card_id);
        $card_to_update['assigned_user'] = $user_id;
        $this->cardModel->update($card_to_update);
    }

    /**
     * @param int $card_id
     * @return array
     * @throws ResponseException
     */
    public function handleGetDetailCard(int $card_id): array
    {
        $this->checkExistedCard($card_id);
        $card = $this->cardModel->join(
            [
                'table'     => 'users',
                'as'        => 'u',
                'condition' => [
                    'assigned_user',
                    'id'
                ],
                'select'    => [
                    'id as assigned_user_id',
                    'username as assigned_user_username',
                    'alias as assigned_user_alias',
                    'email as assigned_user_email',
                ],
            ],
            [
                'where' => [
                    'id',
                    $card_id
                ]
            ]
        );

        return Converter::toCardResponse($card[0]);
    }
}
