<?php

declare(strict_types=1);

namespace app\core;

use shared\enums\ErrorMessage;
use shared\enums\StatusCode;
use shared\enums\SuccessMessage;

class Response
{
    /**
     * @param StatusCode $status_code
     * @param string|null $message
     * @param mixed $errors
     * @param mixed $data
     * @return void
     */
    public function content(StatusCode $status_code, ?string $message, mixed $errors, mixed $data): void
    {
        header('Content-Type: application/json');
        http_response_code($status_code->value);

        $response_content['statusCode'] = $status_code->value;
        $response_content['message'] = $message;

        if ($errors) {
            $response_content["errors"] = $errors;
        }

        if ($data instanceof SuccessMessage) {
            echo json_encode($data);
            return;
        }

        if (is_array($data) || $data != null) {
            echo json_encode($data);
            return;
        }

        echo json_encode($response_content);
    }
}
