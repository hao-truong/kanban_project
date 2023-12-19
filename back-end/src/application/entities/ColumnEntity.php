<?php

declare(strict_types=1);

namespace app\entities;

use DateTime;
use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class ColumnEntity
{
    private int $id;
    private int $boardId;
    private string $title;
    private int $creatorId;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private int $position;
    private static int $MIN_LENGTH_TITLE = 3;
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

    public function getBoardId(): int
    {
        return $this->boardId;
    }

    public function setBoardId(int $boardId): void
    {
        $this->boardId = $boardId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        if (strlen($title) < self::$MIN_LENGTH_TITLE) {
            throw new ResponseException(
                StatusCode::BAD_REQUEST,
                StatusCode::BAD_REQUEST->name,
                sprintf(ErrorMessage::STRING_SO_SHORT->value, 'title', self::$MIN_LENGTH_TITLE)
            );
        }

        if (strlen($title) > self::$MAX_LENGTH_TITLE) {
            throw new ResponseException(
                StatusCode::BAD_REQUEST,
                StatusCode::BAD_REQUEST->name,
                sprintf(ErrorMessage::STRING_SO_LONG->value, 'title', self::$MAX_LENGTH_TITLE)
            );
        }

        $this->title = $title;
    }

    public function getCreatorId(): int
    {
        return $this->creatorId;
    }

    public function setCreatorId(int $creatorId): void
    {
        $this->creatorId = $creatorId;
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

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function toArray(): array
    {
        return [
            'title'      => $this->title,
            'board_id'   => $this->boardId,
            'creator_id' => $this->creatorId,
            'position'   => $this->position,
        ];
    }

    public static function fromArray(array $data): ColumnEntity
    {
        $column_entity = new ColumnEntity();
        $column_entity->setId($data['id']);
        $column_entity->setPosition($data['position']);
        $column_entity->setCreatorId($data['creator_id']);
        $column_entity->setTitle($data['title']);
        $column_entity->setBoardId($data['board_id']);
        $column_entity->setCreatedAt(new DateTime($data['created_at']));
        $column_entity->setUpdatedAt(new DateTime($data['updated_at']));

        return $column_entity;
    }

    public function toFullArray(): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'board_id'   => $this->boardId,
            'creator_id' => $this->creatorId,
            'position'   => $this->position,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
