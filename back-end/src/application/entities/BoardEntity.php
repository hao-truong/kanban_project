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

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        if (strlen($title) < self::$MIN_LENGTH_TITLE) {
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, "Title should be at least " . self::$MIN_LENGTH_TITLE . " characters long");
        }

        if (strlen($title) > self::$MAX_LENGTH_TITLE) {
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, "Title should be less than " . self::$MAX_LENGTH_TITLE . " characters long");
        }

        $this->title = $title;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCreatorId(): int
    {
        return $this->creatorId;
    }

    public function setCreatorId(int $creatorId): void
    {
        $this->creatorId = $creatorId;
    }

    public function toArray(): array
    {
        return [
            'creator_id' => $this->creatorId,
            'title'      => $this->title,
        ];
    }
}
