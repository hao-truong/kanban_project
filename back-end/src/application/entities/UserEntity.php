<?php

declare(strict_types=1);

namespace app\entities;

use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class UserEntity
{
    private int|null $id = null;
    private string $username;
    private string $password;
    private string $email;
    private string $alias;
    private string $accessToken;
    private string $refreshToken;
    private array $fields = [
        'username',
        'password',
        'email',
        'alias'
    ];
    private static int $MIN_LENGTH_PASSWORD = 8;
    private static int $MIN_LENGTH_STRING = 3;
    private static int $MAX_LENGTH_STRING = 256;
    private static int $MAX_LENGTH_ALIAS_NAME = 30;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        if (strlen($username) < self::$MIN_LENGTH_STRING) {
            throw new ResponseException(
                StatusCode::BAD_REQUEST,
                StatusCode::BAD_REQUEST->name,
                sprintf(ErrorMessage::STRING_SO_SHORT->value, 'Username', self::$MIN_LENGTH_STRING)
            );
        }

        if (strlen($username) > self::$MAX_LENGTH_STRING) {
            throw new ResponseException(
                StatusCode::BAD_REQUEST,
                StatusCode::BAD_REQUEST->name,
                sprintf(ErrorMessage::STRING_SO_LONG->value, 'Username', self::$MAX_LENGTH_STRING)
            );
        }

        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        if (strlen($password) < self::$MIN_LENGTH_PASSWORD) {
            throw new ResponseException(
                StatusCode::BAD_REQUEST,
                StatusCode::BAD_REQUEST->name,
                sprintf(ErrorMessage::STRING_SO_SHORT->value, 'Passowrd', self::$MIN_LENGTH_PASSWORD)
            );
        }

        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ResponseException(
                StatusCode::BAD_REQUEST,
                StatusCode::BAD_REQUEST->name,
                ErrorMessage::INVALID_EMAIL_ADDRESS
            );
        }

        $this->email = $email;
    }


    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): void
    {
        if (strlen($alias) < self::$MIN_LENGTH_STRING) {
            throw new ResponseException(
                StatusCode::BAD_REQUEST,
                StatusCode::BAD_REQUEST->name,
                sprintf(ErrorMessage::STRING_SO_SHORT->value, 'Alias name', self::$MIN_LENGTH_STRING)
            );
        }

        if (strlen($alias) > self::$MAX_LENGTH_ALIAS_NAME) {
            throw new ResponseException(
                StatusCode::BAD_REQUEST,
                StatusCode::BAD_REQUEST->name,
                sprintf(ErrorMessage::STRING_SO_LONG->value, 'Alias name', self::$MAX_LENGTH_ALIAS_NAME)
            );
        }

        $this->alias = $alias;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function toArray(): array
    {
        return [
            "username" => $this->username,
            "password" => $this->password,
            "email"    => $this->email,
            "alias"    => $this->alias,
        ];
    }
}
