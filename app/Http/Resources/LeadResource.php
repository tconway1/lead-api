<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class LeadResource extends JsonApiResource
{
    public $attributes = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'electric_bill',
        'created_at',
        'updated_at',
    ];
}
