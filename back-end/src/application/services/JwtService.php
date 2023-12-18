<?php

declare(strict_types=1);

namespace app\services;

use DateTime;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use shared\enums\TypeJwt;
use shared\exceptions\ResponseException;

class JwtService
{
    public function __construct()
    {
    }

    /**
     * @param TypeJwt $type
     * @param int $userId
     * @return string
     */
    public function generateToken(TypeJwt $type, int $userId): string
    {
        $iat = new DateTime();

        $exp = clone $iat;
        $expire_days = $_ENV["JWT_{$type->name}_EXPIRE_TIME"];
        $exp->modify("+{$expire_days} day");

        $payload = array(
            "userId" => $userId,
            "iat"    => $iat->getTimestamp(),
            "exp"    => $exp->getTimestamp(),
        );

        return JWT::encode($payload, $_ENV["JWT_{$type->name}_KEY"], 'HS256');
    }

    /**
     * @param TypeJwt $type
     * @param string $token
     * @return \stdClass
     * @throws ResponseException
     */
    public function verifyToken(TypeJwt $type, string $token): \stdClass
    {
        try {
            return JWT::decode($token, new Key($_ENV["JWT_{$type->name}_KEY"], 'HS256'));
        } catch (Exception $exception) {
            throw new ResponseException(
                StatusCode::FORBIDDEN,
                StatusCode::FORBIDDEN->name,
                ErrorMessage::UNAUTHORIZED_TOKEN
            );
        } catch (ExpiredException $expiredException) {
            throw new ResponseException(
                StatusCode::UNAUTHORIZED,
                StatusCode::UNAUTHORIZED->name,
                ErrorMessage::EXPIRED_TOKEN
            );
        }
    }
}
