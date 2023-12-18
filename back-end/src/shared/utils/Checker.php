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
    public static function checkMissingFields(array $data, array $fields, array $field_types): array
    {
        $missing_fields = array_diff(
            $fields,
            array_keys($data)
        );

        if (!empty($missing_fields)) {
            throw new ResponseException(
                StatusCode::BAD_REQUEST,
                StatusCode::BAD_REQUEST->name,
                "Missing fields: " . implode(", ", $missing_fields)
            );
        }

        $data = Checker::removeRedundantFields($data, $fields);
        Checker::checkTypeFields($data, $field_types);
        return Checker::sanitizeData($data, $field_types);
    }

    /**
     * @param array $data
     * @param array $field_types
     * @return void
     * @throws ResponseException
     */
    public static function checkTypeFields(array $data, array $field_types): void
    {
        foreach ($data as $field => $value) {
            if (!array_key_exists($field, $field_types)) {
                throw new ResponseException(
                    StatusCode::BAD_REQUEST,
                    StatusCode::BAD_REQUEST->name,
                    "Type for field '$field' must be $field_types[$field]"
                );
            }

            $expected_type = $field_types[$field];

            if (!is_null($value) && gettype($value) !== $expected_type) {
                throw new ResponseException(
                    StatusCode::BAD_REQUEST,
                    StatusCode::BAD_REQUEST->name,
                    "Invalid type for field '$field'. Expected '$expected_type', got '" . gettype($value) . "'."
                );
            }
        }
    }

    public static function removeRedundantFields(array $data, array $fields): array
    {
        foreach ($data as $field => $value) {
            if (!in_array($field, $fields)) {
                unset($data[$field]);
            }
        }

        return $data;
    }

    public static function sanitizeField(mixed $input, string $type): mixed
    {
        return match ($type) {
            'integer' => intval(filter_var($input, FILTER_SANITIZE_NUMBER_INT)),
            'string' => $input,
        };
    }

    public static function sanitizeData(array $data, array $types): array
    {
        return array_combine(
            array_keys($data),
            array_map(
                function ($field, $value) use ($types) {
                    $type = $types[$field];
                    return Checker::sanitizeField($value, $type);
                },
                array_keys($data),
                $data
            )
        );
    }
}
