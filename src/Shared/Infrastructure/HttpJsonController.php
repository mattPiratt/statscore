<?php

namespace App\Shared\Infrastructure;

use Exception;

abstract class HttpJsonController
{
    protected function getJsonData(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON');
        }

        return $data ?? [];
    }

    protected function sendResponse(int $code, array $data): void
    {
        http_response_code($code);
        echo json_encode($data);
    }

    public function sendNotFound(): void
    {
        $this->sendResponse(404, ['error' => 'Not found']);
    }
}
