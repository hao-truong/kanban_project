<?php

declare(strict_types=1);

namespace shared\enums;

enum RequestMethod
{
    case GET;
    case POST;
    case DELETE;
    case PUT;
    case PATCH;
    case OPTIONAL;

    public static function from(string $method): self
    {
        return match ($method) {
            self::GET->name => self::GET,
            self::POST->name => self::POST,
            self::PUT->name => self::PUT,
            self::DELETE->name => self::DELETE,
            self::PATCH->name => self::PATCH,
            default => throw new Exception("Invalid method"),
        };
    }
}
