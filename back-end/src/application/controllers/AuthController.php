<?php

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\entities\UserEntity;
use app\services\AuthService;
use JetBrains\PhpStorm\NoReturn;
use shared\enums\StatusCode;
use shared\utils\Checker;

class AuthController
{
    public function __construct(
        private Request     $request,
        private Response    $response,
        private AuthService $authService
    ) {
    }

    #[NoReturn] public function register(): mixed
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
        return $this->response->content(StatusCode::CREATED, null, null, 'Register successfully!');
    }

    #[NoReturn] public function login(): mixed
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
        return $this->response->content(StatusCode::OK, null, null, $tokens);
    }

    #[NoReturn] public function logout(): mixed
    {
        $this->authService->handleLogout();
        return $this->response->content(StatusCode::OK, null, null, 'Logout successfully!');
    }
}
