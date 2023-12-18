<?php

declare(strict_types=1);

namespace app\middlewares;

use app\core\Request;
use app\models\UserModel;
use app\services\JwtService;
use app\services\UserService;
use shared\enums\StatusCode;
use shared\enums\TypeJwt;
use shared\exceptions\ResponseException;

class AuthorizeRequest implements IMiddleware
{
    public function __construct(
        private Request $request,
        private readonly JwtService $jwtService,
        private readonly UserModel $userModel
    ) {
    }

    /**
     * @return true
     * @throws ResponseException
     */
    public function execute(): bool
    {
        if (!array_key_exists("Authorization", getallheaders())) {
            throw new ResponseException(StatusCode::FORBIDDEN, "No token", StatusCode::FORBIDDEN->name);
        }

        $token = getallheaders()["Authorization"];
        $token = str_replace("Bearer ", "", $token);

        $matched_user = $this->userModel->findOne('access_token', $token);

        if (!$matched_user) {
            throw new ResponseException(StatusCode::FORBIDDEN, "Invalid token", StatusCode::FORBIDDEN->name);
        }

        $payload = $this->jwtService->verifyToken(TypeJwt::ACCESS_TOKEN, $token);
        $user_id = $payload->userId;

        if ($matched_user['id'] != $user_id) {
            throw new ResponseException(StatusCode::FORBIDDEN, "Invalid token", StatusCode::FORBIDDEN->name);
        }

        $this->request->setUserId($user_id);

        return true;
    }
}
