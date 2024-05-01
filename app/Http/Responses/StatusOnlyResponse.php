<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use JustSteveKing\StatusCode\Http;

class StatusOnlyResponse implements Responsable
{
    use Traits\SendResponse;

    private array $data;

    private Http $status;

    public function __construct(Http $status = Http::OK)
    {
        $this->data = [];
        $this->status = $status;
    }
}
