<?php

namespace Tests\Feature;

use App\Http\Controllers\ApiController;
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
        ])->assertDatabaseHas(Address::class, [
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

        $this->assertDatabaseCount(Lead::class, 3)
            ->assertDatabaseCount(Address::class, 3);

        $response = $this->getJson(action([ApiController::class, 'index']));
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

    public function test_can_create_lead_via_api()
    {
        $response = $this->postJson(action([ApiController::class, 'create']), [
            'firstname' => 'Test',
            'lastname' => 'User',
            'email' => 'test@test.com',
            'electric_bill' => 150,
            'street' => '123 Main Street',
            'city' => 'Somewhere',
            'state' => 'MD',
            'zip_code' => '12345',
        ]);

        $this->assertDatabaseHas(Lead::class, [
            'firstname' => 'Test',
            'lastname' => 'User',
            'email' => 'test@test.com',
            'electric_bill' => 150,
        ])->assertDatabaseHas(Address::class, [
            'street' => '123 Main Street',
            'city' => 'Somewhere',
            'state' => 'MD',
            'zip_code' => '12345',
        ]);

        $response->assertJsonCount(1)
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
            ])
            ->assertJsonFragment([
                'firstname' => 'Test',
                'lastname' => 'User',
                'email' => 'test@test.com',
                'electric_bill' => 150,
                'street' => '123 Main Street',
                'city' => 'Somewhere',
                'state' => 'MD',
                'zip_code' => '12345',
            ]);
    }

    public function test_create_user_validation_fails()
    {
        $response = $this->postJson(action([ApiController::class, 'create']), [
           // empty input
        ]);

        $this->assertDatabaseEmpty(Lead::class)
            ->assertDatabaseEmpty(Address::class);

        $response->assertJsonStructure([
            'errors' => [
                'code',
                'title',
                'detail',
            ],
        ]);
    }
}
