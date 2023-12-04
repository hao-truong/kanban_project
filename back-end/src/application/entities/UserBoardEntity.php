<?php
declare(strict_types=1);

namespace app\entities;

class UserBoardEntity
{
    private int $userId;
    private int $boardId;

    public function __construct()
    {

    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getBoardId(): int
    {
        return $this->boardId;
    }

    public function setBoardId(int $boardId): void
    {
        $this->boardId = $boardId;
    }

    public function toArray(): array
    {
        return [
            'user_id'  => $this->userId,
            'board_id' => $this->boardId,
        ];
    }
}
