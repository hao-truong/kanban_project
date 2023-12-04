<?php

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\services\AuthService;
use app\services\UserService;
use JetBrains\PhpStorm\NoReturn;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class AuthController
{
    public function __construct(
        private Request     $request,
        private Response    $response,
        private AuthService $authService
    ) {
    }

    /**
     * @return mixed
     * @throws ResponseException
     */
    #[NoReturn] public function register(): mixed
    {
        $this->authService->handleRegister($this->request->getBody());
        return $this->response->content(StatusCode::CREATED, 'Register successfully!', null, 'Register successfully!');
    }

    /**
     * @return mixed
     * @throws ResponseException
     */
    #[NoReturn] public function login(): mixed
    {
        $tokens = $this->authService->handleLogin($this->request->getBody());
        return $this->response->content(StatusCode::OK, 'Login successfully!', null, $tokens);
    }

    /**
     * @return mixed
     * @throws ResponseException
     */
    #[NoReturn] public function logout(): mixed
    {
        $this->authService->handleLogout();
        return $this->response->content(StatusCode::OK, 'Logout successfully!', null, 'Logout successfully!');
    }
}
