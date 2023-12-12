<?php
declare(strict_types=1);

namespace app\services;

use app\models\UserModel;
use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class UserService
{
    public function __construct(private readonly UserModel $userModel)
    {
    }

    /**
     * @param int $user_id
     * @return array
     * @throws ResponseException
     */
    public function handleGetProfile(int $user_id): array
    {
        $matched_user = $this->userModel->findOne('id', strval($user_id));

        if (!$matched_user) {
            throw new ResponseException(StatusCode::NOT_FOUND, StatusCode::NOT_FOUND->name, ErrorMessage::USER_NOT_FOUND);
        }

        return [
            'id'       => $matched_user['id'],
            'username' => $matched_user['username'],
            'email'    => $matched_user['email'],
            'alias'    => $matched_user['alias'],
        ];
    }

    /**
     * @param string $username
     * @return array
     * @throws ResponseException
     */
    public function getUserByUsername(string $username): array
    {
        $matched_user = $this->userModel->findOne("username", $username);

        if (!$matched_user) {
            throw new ResponseException(StatusCode::NOT_FOUND, StatusCode::NOT_FOUND->name, ErrorMessage::USER_NOT_FOUND);
        }

        return $matched_user;
    }
}
