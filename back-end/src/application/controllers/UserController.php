<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\services\UserService;
use JetBrains\PhpStorm\NoReturn;
use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class UserController
{
    public function __construct(
        private readonly Request     $request,
        private readonly Response    $response,
        private readonly UserService $userService
    ) {

    }

    /**
     * @return mixed
     * @throws ResponseException
     */
    #[NoReturn] public function getProfile(): mixed
    {
        $user_id = $_SESSION['user_id'];

        $profile = $this->userService->handleGetProfile($user_id);

        return $this->response->content(StatusCode::OK, "Get profile successfully!", null, $profile);
    }
}
