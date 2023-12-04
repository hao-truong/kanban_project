<?php
declare(strict_types=1);

namespace app\entities;

use DateTime;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class ColumnEntity {
    private int $id;
    private int $boardId;
    private string $title;
    private int $creatorId;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private static $MIN_LENGTH_TITLE = 2;
    private static $MAX_LENGTH_TITLE = 20;

    /**
     * @param int $board_id
     * @param string $title
     * @param int $creator_id
     * @throws ResponseException
     */
    public function __construct(int $board_id, string $title, int $creator_id) {
        $errors = $this->validate(
            [
                'title'     => $title,
                'creator_id' => $creator_id,
                'board_id' => $board_id,
            ]
        );

        if (count($errors)) {
            throw new ResponseException(StatusCode::BAD_REQUEST, $errors, StatusCode::BAD_REQUEST->name);
        }

        $this->title = $title;
        $this->creatorId = $creator_id;
    }

    private function validate(array $data_to_validate): array
    {
        $errors = [];

        if (strlen($data_to_validate['title']) <= self::$MIN_LENGTH_TITLE) {
            $errors['title'] = "Title should be at least 3 characters long";
        }

        if(strlen($data_to_validate['title']) > self::$MAX_LENGTH_TITLE) {
            $errors['title'] = "Title should be less than 20 characters long";
        }

        return $errors;
    }
}
