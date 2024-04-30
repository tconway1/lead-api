<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class LeadResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'electric_bill' => $this->electric_bill,
            'address' => [
                'street' => $this->address->street,
                'city' => $this->address->city,
                'state' => $this->address->state,
                'zip_code' => $this->address->zip_code,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
