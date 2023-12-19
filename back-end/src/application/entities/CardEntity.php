<?php

declare(strict_types=1);

namespace app\entities;

use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class CardEntity
{
    private int $id;
    private string $title;
    private int $columnId;
    private string $description;
    private int|null $assignedUser;
    private int $position;
    private int $createdAt;
    private int $updatedAt;
    private static int $MAX_LENGTH_STRING = 120;
    private static int $MIN_LENGTH_STRING = 3;

    public function __construct()
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        if (strlen($title) < self::$MIN_LENGTH_STRING) {
            throw new ResponseException(
                StatusCode::BAD_REQUEST,
                StatusCode::BAD_REQUEST->name,
                sprintf(ErrorMessage::STRING_SO_SHORT->value, 'title', self::$MIN_LENGTH_STRING)
            );
        }

        if (strlen($title) > self::$MAX_LENGTH_STRING) {
            throw new ResponseException(
                StatusCode::BAD_REQUEST,
                StatusCode::BAD_REQUEST->name,
                sprintf(ErrorMessage::STRING_SO_LONG->value, 'title', self::$MAX_LENGTH_STRING)
            );
        }

        $this->title = $title;
    }

    public function getColumnId(): int
    {
        return $this->columnId;
    }

    public function setColumnId(int $column_id): void
    {
        $this->columnId = $column_id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        if (strlen($description) < self::$MIN_LENGTH_STRING) {
            throw new ResponseException(
                StatusCode::BAD_REQUEST,
                StatusCode::BAD_REQUEST->name,
                sprintf(ErrorMessage::STRING_SO_SHORT->value, 'Description', self::$MIN_LENGTH_STRING)
            );
        }

        if (strlen($description) > self::$MAX_LENGTH_STRING) {
            throw new ResponseException(
                StatusCode::BAD_REQUEST,
                StatusCode::BAD_REQUEST->name,
                sprintf(ErrorMessage::STRING_SO_LONG->value, 'Description', self::$MAX_LENGTH_STRING)
            );
        }

        $this->description = $description;
    }

    public function getAssignedUser(): ?int
    {
        return $this->assignedUser;
    }

    public function setAssignedUser(?int $assigned_user): void
    {
        $this->assignedUser = $assigned_user;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $created_at): void
    {
        $this->createdAt = $created_at;
    }

    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(int $updated_at): void
    {
        $this->updatedAt = $updated_at;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function toCreateArray(): array
    {
        return [
            'column_id' => $this->columnId,
            'title'     => $this->title,
            'position'  => $this->position,
        ];
    }
}
