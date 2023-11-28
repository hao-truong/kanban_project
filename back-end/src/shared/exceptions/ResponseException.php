<?php
declare(strict_types=1);

namespace shared\exceptions;

use shared\enums\StatusCode;
use Throwable;

class ResponseException extends \Exception implements Throwable
{
    private StatusCode $statusCode;
    private array | string $errors;

    public function __construct(StatusCode $status_code, array | string | null $errors = null, string $message)
    {
        $this->statusCode = $status_code;
        $this->message = $message;

        if ($errors) {
            $this->errors = $errors;
        }
    }

    public function getStatusCode(): StatusCode
    {
        return $this->statusCode;
    }

    public function getErrors(): array | string
    {
        return $this->errors;
    }
}

