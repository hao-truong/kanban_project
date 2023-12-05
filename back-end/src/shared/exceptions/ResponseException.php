<?php
declare(strict_types=1);

namespace shared\exceptions;

use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use Throwable;

class ResponseException extends \Exception implements Throwable
{
    private StatusCode $statusCode;
    private mixed $errors;

    public function __construct(StatusCode $status_code, mixed $errors, ErrorMessage | string $message)
    {
        $this->statusCode = $status_code;

        if ($message instanceof ErrorMessage) {
            $this->message = $message->value;
        } else {
            $this->message = $message;
        }

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

