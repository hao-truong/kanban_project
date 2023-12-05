<?php
declare(strict_types=1);

namespace shared\handlers;

class SessionHandler
{
    public static function getUserId(): int
    {
        return $_SESSION['user_id'];
    }
}
