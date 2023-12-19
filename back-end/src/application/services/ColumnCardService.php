<?php

declare(strict_types=1);

namespace app\services;

use app\entities\CardEntity;
use shared\exceptions\ResponseException;

class ColumnCardService
{
    public function __construct(
        private readonly BoardService $boardService,
        private readonly ColumnService $columnService,
        private readonly CardService $cardService,
    ) {
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
        $this->boardService->checkExistedBoard($board_id);
        $this->boardService->checkMemberOfBoard($user_id, $board_id);
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
        $this->boardService->checkExistedBoard($board_id);
        $this->boardService->checkMemberOfBoard($user_id, $board_id);
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
        $this->boardService->checkExistedBoard($board_id);
        $this->boardService->checkMemberOfBoard($user_id, $board_id);
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
        $this->boardService->checkExistedBoard($board_id);
        $this->boardService->checkMemberOfBoard($user_id, $board_id);
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
        $this->boardService->checkExistedBoard($board_id);
        $this->boardService->checkMemberOfBoard($user_id, $board_id);
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
    public function handleAssignMemberToCard(
        int $user_id,
        int $board_id,
        int $column_id,
        int $card_id,
        int $user_need_assign
    ): void {
        $this->boardService->checkExistedBoard($board_id);
        $this->boardService->checkMemberOfBoard($user_id, $board_id);
        $this->boardService->checkMemberOfBoard($user_need_assign, $board_id);
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
        $this->boardService->checkExistedBoard($board_id);
        $this->boardService->checkMemberOfBoard($user_id, $board_id);
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
    public function handleChangeColumnForCard(
        int $user_id,
        int $board_id,
        int $column_id,
        int $card_id,
        int $destination_column_id
    ): array {
        $this->boardService->checkExistedBoard($board_id);
        $this->boardService->checkMemberOfBoard($user_id, $board_id);
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
    public function handleUpdateDescriptionOfCard(
        int $user_id,
        int $board_id,
        int $column_id,
        int $card_id,
        string $new_description
    ): array {
        $this->boardService->checkExistedBoard($board_id);
        $this->boardService->checkMemberOfBoard($user_id, $board_id);
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
        $this->boardService->checkExistedBoard($board_id);
        $this->boardService->checkMemberOfBoard($user_id, $board_id);
        $this->columnService->checkColumnInBoard($req_data['originalColumnId'], $board_id);
        $this->columnService->checkColumnInBoard($req_data['targetColumnId'], $board_id);
        $this->cardService->handleMoveCardInBoard(
            $req_data['originalCardId'],
            $req_data['originalColumnId'],
            $req_data['targetCardId'],
            $req_data['targetColumnId']
        );
    }
}
