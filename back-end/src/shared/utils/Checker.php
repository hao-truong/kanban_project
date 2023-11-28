<?php
declare(strict_types=1);

namespace shared\utils;

use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class Checker
{
    public static function checkMissingFields(array $fields, array $data)
    {
        $missing_fields = array_diff(
            $fields, array_keys($data)
        );

        if (!empty($missing_fields)) {
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, "Missing fields: " . implode(", ", $missing_fields));
        }
    }
}
