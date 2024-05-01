<?php

namespace App\Http\Responses\Traits;

use Illuminate\Http\JsonResponse;

trait SendResponse
{
    public function toResponse($request): JsonResponse
    {
        return new JsonResponse($this->data, $this->status->value);
    }
}
