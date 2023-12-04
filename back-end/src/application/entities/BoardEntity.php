<?php
declare(strict_types=1);

namespace app\entities;

use DateTime;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class BoardEntity
{
    private int $id;
    private string $title;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private int $creatorId;
    private static int $MIN_LENGTH_TITLE = 2;
    private static int $MAX_LENGTH_TITLE = 20;

    public function __construct(string $title, int $creator_id)
    {
        $errors = $this->validate(
            [
                'title'     => $title,
                'creatorId' => $creator_id
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
