<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JustSteveKing\StatusCode\Http;
use Tests\TestCase;

class ApiTest extends TestCase
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

    public function test_can_retrieve_multiple_leads_via_api()
    {
        $leads = Lead::factory()
            ->count(3)
            ->has(Address::factory())
            ->afterCreating(function (Lead $lead) {
                $lead->load('address');
            })
            ->create();

        $this->assertDatabaseCount(Lead::class, 3);
        $this->assertDatabaseCount(Address::class, 3);

        $response = $this->getJson('/api/leads');
        $response->assertStatus(Http::OK->value)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'attributes' => [
                        'address' => [
                            'street',
                            'city',
                            'state',
                            'zip_code',
                        ],
                    ],
                ],
            ]);
    }
}
