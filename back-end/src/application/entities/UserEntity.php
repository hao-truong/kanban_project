<?php
declare(strict_types=1);

namespace app\entities;

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

    public function __construct(string $username, string $password, string $email, string $alias)
    {
        $errors = $this->validate(
            [
                "username" => $username,
                "password" => $password,
                "email"    => $email,
                "alias"    => $alias,
            ]
        );

        if (count($errors)) {
            throw new ResponseException(StatusCode::BAD_REQUEST, $errors, StatusCode::BAD_REQUEST->name);
        }

        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->alias = $alias;
    }

    /**
     * @param array $data_to_validate ["username" => string, "password" => string, "email" => string, "alias" => string"]
     * @return array
     */
    private function validate(array $data_to_validate): array
    {
        $errors = [];

        $validation_fields = [
            "email"    => FILTER_VALIDATE_EMAIL,
            "username" => [
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => '/\w+/'],
            ],
            "password" => [
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => '/\w+/'],
            ],
        ];

        $message_errors = [
            "email"    => "Email is invalid!",
            "username" => "Username cannot be null",
            "password" => "Password cannot be null",
        ];

        $field_errors = filter_var_array($data_to_validate, $validation_fields);

        foreach ($field_errors as $field => $is_error) {
            if ($is_error === false || $is_error === null) {
                $errors[$field] = $message_errors[$field];
            }
        }

        if (strlen($data_to_validate['password']) < self::$MIN_LENGTH_PASSWORD) {
            $errors['password'] = "Password should be at least " . self::$MIN_LENGTH_PASSWORD . " characters long!";
        }

        return $errors;
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
