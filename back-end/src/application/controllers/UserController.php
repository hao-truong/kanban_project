<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\Request;
use app\core\Response;
use app\services\UserService;
use shared\enums\StatusCode;

class UserController
{
    public function __construct(private Request $request, private Response $response, private UserService $userService)
    {

    }

    public function getProfile(): mixed {
        $user_id = $_SESSION['user_id'];

        $profile = $this->userService->handleGetProfile($user_id);

        return $this->response->content(StatusCode::OK, "Get profile successfully!", null, $profile);
    }
}
