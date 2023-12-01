<?php
declare(strict_types=1);

namespace app\entities;

class UserBoardEntity {
    private int $userId;
    private int $boardId;
    public function __construct(int $user_id, int $board_id) {
        $this->userId = $user_id;
        $this->boardId = $board_id;
    }
}
