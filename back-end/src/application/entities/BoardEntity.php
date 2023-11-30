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

        if (strlen($data_to_validate['title']) <= 2) {
            $errors['title'] = "Title should be at least 3 characters long";
        }



        return $errors;
    }
}
