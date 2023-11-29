<?php
declare(strict_types=1);

namespace app\services;

use app\models\UserModel;
use shared\enums\StatusCode;
use shared\enums\TypeJwt;
use shared\exceptions\ResponseException;
use shared\utils\Checker;

class AuthService
{
    public function __construct(
        private UserModel  $userModel,
        private JwtService $jwtService
    ) {

    }

    /**
     * @throws ResponseException
     */
    public function handleRegister(array $register_req_data): array
    {
        Checker::checkMissingFields(
            [
                'username',
                'password',
                'email',
                'alias'
            ], $register_req_data
        );

        $existed_username = $this->userModel->findOne('username', $register_req_data['username']);

        if ($existed_username) {
            throw new ResponseException(StatusCode::BAD_REQUEST, "Username existed!", StatusCode::BAD_REQUEST->name);
        }

        $register_req_data['password'] = password_hash($register_req_data['password'], PASSWORD_BCRYPT);
        return $this->userModel->save($register_req_data);
    }

    public function handleLogin(array $login_req_data): array
    {
        Checker::checkMissingFields(
            [
                'username',
                'password'
            ], $login_req_data
        );

        $matched_user = $this->userModel->findOne('username', $login_req_data['username']);

        if (!$matched_user) {
            throw new ResponseException(StatusCode::BAD_REQUEST, "Username or password is wrong!", StatusCode::BAD_REQUEST->name);
        }

        $is_correct_password = password_verify($login_req_data['password'], $matched_user['password']);

        if (!$is_correct_password) {
            throw new ResponseException(StatusCode::BAD_REQUEST, "Username or password is wrong!", StatusCode::BAD_REQUEST->name);
        }

        $matched_user['access_token'] = $this->jwtService->generateToken(TypeJwt::ACCESS_TOKEN, $matched_user['id']);
        $matched_user['refresh_token'] = $this->jwtService->generateToken(TypeJwt::REFRESH_TOKEN, $matched_user['id']);

        $this->userModel->update($matched_user);

        return [
            "accessToken"  => $this->jwtService->generateToken(TypeJwt::ACCESS_TOKEN, $matched_user['id']),
            "refreshToken" => $this->jwtService->generateToken(TypeJwt::REFRESH_TOKEN, $matched_user['id']),
        ];
    }

    public function handleLogout(): void
    {
        $user_id = $_SESSION['user_id'];

        $matched_user = $this->userModel->findOne('id', strval($user_id));

        if (!$matched_user) {
            throw new ResponseException(StatusCode::NOT_FOUND, "User not found!", StatusCode::NOT_FOUND->name);
        }

        $matched_user['access_token'] = null;
        $matched_user['refresh_token'] = null;
        $this->userModel->update($matched_user);
    }
}
