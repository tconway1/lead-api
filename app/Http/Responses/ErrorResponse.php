<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ErrorResponse implements Responsable
{
    use Traits\SendResponse;

    private array $data;

    private \stdClass $status;

    public function __construct(\Throwable $e)
    {
        $this->data = [
            'errors' => [
                'code' => $e->status ?? $e->getStatusCode(),
                'title' => Str::headline(class_basename($e)),
                'detail' => $e instanceof ValidationException ? $e->errors() : $e->getMessage(),
            ],
        ];
        $this->status = (object) ['value' => $e->status ?? $e->getStatusCode()];
    }
}
