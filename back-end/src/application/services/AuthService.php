<?php
declare(strict_types=1);

namespace app\services;

use app\entities\UserEntity;
use app\models\UserModel;
use shared\enums\StatusCode;
use shared\enums\TypeJwt;
use shared\exceptions\ResponseException;
use shared\utils\Checker;

class AuthService
{
    public function __construct(
        private readonly UserModel  $userModel,
        private readonly JwtService $jwtService
    ) {
    }

    /**
     * @param UserEntity $user_entity
     * @return array
     * @throws ResponseException
     */
    public function handleRegister(UserEntity $user_entity): array
    {
        $existed_username = $this->userModel->findOne('username', $user_entity->getUsername());

        if ($existed_username) {
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, "Username existed!");
        }

        $user_entity->setPassword(password_hash($user_entity->getPassword(), PASSWORD_BCRYPT));
        return $this->userModel->save($user_entity->toArray());
    }

    /**
     * @throws ResponseException
     */
    public function handleLogin(UserEntity $user_entity): array
    {
        $matched_user = $this->userModel->findOne('username', $user_entity->getUsername());

        if (!$matched_user) {
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, "Username or password is wrong!");
        }

        $is_correct_password = password_verify($user_entity->getPassword(), $matched_user['password']);

        if (!$is_correct_password) {
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, "Username or password is wrong!");
        }

        $matched_user['access_token'] = $this->jwtService->generateToken(TypeJwt::ACCESS_TOKEN, $matched_user['id']);
        $matched_user['refresh_token'] = $this->jwtService->generateToken(TypeJwt::REFRESH_TOKEN, $matched_user['id']);

        $this->userModel->update($matched_user);

        return [
            "accessToken"  => $matched_user['access_token'],
            "refreshToken" => $matched_user['refresh_token'],
        ];
    }

    /**
     * @throws ResponseException
     */
    public function handleLogout(): void
    {
        $user_id = $_SESSION['user_id'];
        $_SESSION = array();
        session_destroy();

        $matched_user = $this->userModel->findOne('id', $user_id);

        if (!$matched_user) {
            throw new ResponseException(StatusCode::NOT_FOUND, StatusCode::NOT_FOUND->name, "User not found!");
        }

        $matched_user['access_token'] = null;
        $matched_user['refresh_token'] = null;
        $this->userModel->update($matched_user);
    }
}
