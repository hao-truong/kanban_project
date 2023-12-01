<?php
declare(strict_types=1);

namespace app\core;

use shared\enums\StatusCode;

class Response
{
    public function setStatusCode(StatusCode $status_code): void
    {
        http_response_code($status_code->value);
    }

    public function content(StatusCode $status_code, string $message, mixed $errors, mixed $data): void
    {
        header('Content-Type: application/json');
        http_response_code($status_code->value);

        $response_content['statusCode'] = $status_code->value;
        $response_content['message'] = $message;

        if ($errors) {
            $response_content["errors"] = $errors;
        }

        if(is_array($data)) {
            echo json_encode($data);
            die();
        }

        if ($data != null) {
            echo json_encode($data);
            die();
        }

        echo json_encode($response_content);
        die();
    }
}
