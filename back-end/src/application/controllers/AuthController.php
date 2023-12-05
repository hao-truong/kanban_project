<?php

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\entities\UserEntity;
use app\services\AuthService;
use shared\enums\ResponseMessage;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;
use shared\utils\Checker;

class AuthController
{
    public function __construct(
        private readonly Request     $request,
        private readonly Response    $response,
        private readonly AuthService $authService,
    ) {
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function register(): void
    {
        $req_data = $this->request->getBody();
        Checker::checkMissingFields(
            $req_data,
            [
                'username',
                'password',
                'email',
                'alias'
            ], [
                'username' => 'string',
                'password' => 'string',
                'email'    => 'string',
                'alias'    => 'string',
            ]
        );

        $user_entity = new UserEntity();
        $user_entity->setUsername($req_data['username']);
        $user_entity->setPassword($req_data['password']);
        $user_entity->setEmail($req_data['email']);
        $user_entity->setAlias($req_data['alias']);

        $this->authService->handleRegister($user_entity);
        $this->response->content(StatusCode::CREATED, null, null, ResponseMessage::REGISTER_SUCCESSFULLY->value);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function login(): void
    {
        $req_data = $this->request->getBody();
        Checker::checkMissingFields(
            $req_data,
            [
                'username',
                'password'
            ], [
                'username' => 'string',
                'password' => 'string',
            ]
        );

        $user_entity = new UserEntity();
        $user_entity->setUsername($req_data['username']);
        $user_entity->setPassword($req_data['password']);

        $tokens = $this->authService->handleLogin($user_entity);
        $this->response->content(StatusCode::OK, null, null, $tokens);
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function logout(): void
    {
        $this->authService->handleLogout();
        $this->response->content(StatusCode::OK, null, null, ResponseMessage::LOGOUT_SUCCESSFULLY->value);
    }
}
