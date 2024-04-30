<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_retrieve_lead_from_db()
    {
        $lead = Lead::factory()
            ->has(Address::factory())
            ->afterCreating(function (Lead $lead) {
                $lead->load('address');
            })
            ->create();

        $this->assertDatabaseHas(Lead::class, [
            'firstname' => $lead->firstname,
            'lastname' => $lead->lastname,
            'email' => $lead->email,
            'electric_bill' => $lead->electric_bill,
        ]);

        $this->assertDatabaseHas(Address::class, [
            'street' => $lead->address->street,
            'city' => $lead->address->city,
            'state' => $lead->address->state,
            'zip_code' => $lead->address->zip_code,
        ]);
    }
}
