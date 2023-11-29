<?php

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\services\AuthService;
use app\services\UserService;
use shared\enums\StatusCode;

class AuthController
{
    public function __construct(
        private Request     $request,
        private Response    $response,
        private AuthService $authService
    ) {
    }

    public function register(): mixed
    {
        $this->authService->handleRegister($this->request->getBody());
        return $this->response->content(StatusCode::CREATED, 'Register successfully!', null, 'Register successfully!');
    }

    public function login(): mixed
    {
        $tokens = $this->authService->handleLogin($this->request->getBody());
        return $this->response->content(StatusCode::OK, 'Login successfully!', null, $tokens);
    }

    public function logout(): mixed
    {
        $this->authService->handleLogout();
        return $this->response->content(StatusCode::OK, 'Logout successfully!', null, 'Logout successfully!');
    }
}
