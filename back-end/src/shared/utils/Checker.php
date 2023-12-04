<?php
declare(strict_types=1);

namespace shared\utils;

use shared\enums\StatusCode;
use shared\exceptions\ResponseException;

class Checker
{
    /**
     * @param array $fields string[]
     * @param array $data key value []
     * @return void
     * @throws ResponseException
     */
    public static function checkMissingFields(array $data, array $fields, array $field_types): void
    {
        $missing_fields = array_diff(
            $fields, array_keys($data)
        );

        if (!empty($missing_fields)) {
            throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, "Missing fields: " . implode(", ", $missing_fields));
        }

        Checker::checkTypeFields($data, $field_types);
    }

    /**
     * @param array $data
     * @param array $field_types
     * @return void
     * @throws ResponseException
     */
    public static function checkTypeFields(array $data, array $field_types): void {
        foreach ($data as $field => $value) {
            if (!array_key_exists($field, $field_types)) {
                throw new ResponseException( StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, "Type for field '$field' must be $field_types[$field]");
            }

            $expectedType = $field_types[$field];
            if (!is_null($value) && gettype($value) !== $expectedType) {
                throw new ResponseException(StatusCode::BAD_REQUEST, StatusCode::BAD_REQUEST->name, "Invalid type for field '$field'. Expected '$expectedType', got '" . gettype($value) . "'.");
            }
        }
    }
}
