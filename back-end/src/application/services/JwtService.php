<?php
declare(strict_types=1);

namespace app\services;

use DateTime;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use shared\enums\StatusCode;
use shared\enums\TypeJwt;
use shared\exceptions\ResponseException;

class JwtService
{
    public function __construct()
    {
    }

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

    public function verifyToken(TypeJwt $type, string $token)
    {
        try {
            return JWT::decode($token, new Key($_ENV["JWT_{$type->name}_KEY"], 'HS256'));
        } catch (Exception $exception) {
            throw new ResponseException(StatusCode::UNAUTHORIZED, "Unauthorized token");
        }
    }
}
