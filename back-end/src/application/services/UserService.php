<?php
declare(strict_types=1);

namespace app\services;

use app\models\UserModel;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class UserService
{
    public function __construct(private readonly UserModel $userModel)
    {
    }

    public function handleGetProfile(int $user_id): array
    {
        $matched_user = $this->userModel->findOne('id', strval($user_id));

        if (!$matched_user) {
            throw new ResponseException(StatusCode::NOT_FOUND, StatusCode::NOT_FOUND->name, "User not found");
        }

        return [
            'id'       => $matched_user['id'],
            'username' => $matched_user['username'],
            'email'    => $matched_user['email'],
            'alias'    => $matched_user['alias'],
        ];
    }
}
