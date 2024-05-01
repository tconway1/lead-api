<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use JustSteveKing\StatusCode\Http;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class CollectionResponse implements Responsable
{
    use Traits\SendResponse;

    private JsonApiResourceCollection $data;

    private Http $status;

    public function __construct(JsonApiResourceCollection $data, Http $status = Http::OK)
    {
        $this->data = $data;
        $this->status = $status;
    }
}
