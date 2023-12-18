<?php

declare(strict_types=1);

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\services\UserService;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class UserController
{
    public function __construct(
        private readonly Request $request,
        private readonly Response $response,
        private readonly UserService $userService
    ) {
    }

    /**
     * @return void
     * @throws ResponseException
     */
    public function getProfile(): void
    {
        $user_id = $this->request->getUserId();
        $profile = $this->userService->handleGetProfile($user_id);
        $this->response->content(StatusCode::OK, null, null, $profile);
    }
}
