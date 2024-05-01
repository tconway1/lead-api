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

        $response->assertStatus(Http::OK->value)
            ->assertJsonCount(1)
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

    public function test_create_lead_validation_fails_via_api()
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

    public function test_can_update_lead_via_api()
    {
        $lead = Lead::factory()
            ->has(Address::factory())
            ->afterCreating(function (Lead $lead) {
                $lead->load('address');
            })
            ->create();

        $response = $this->patchJson(action([ApiController::class, 'update'], ['id' => $lead->id]), [
           'phone' => 1231231234,
        ]);

        $this->assertDatabaseHas(Lead::class, [
            'id' => $lead->id,
            'phone' => 1231231234,
        ]);

        $response->assertStatus(Http::OK->value)
            ->assertJsonCount(1)
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
                'id' => (string) $lead->id,
                'phone' => 1231231234
            ]);
    }

    public function test_update_lead_not_found_via_api()
    {
        $response = $this->patchJson(action([ApiController::class, 'update'], ['id' => rand(10, 100)]), [
            'phone' => 1231231234,
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

    public function test_update_lead_validation_fails_via_api()
    {
        $lead = Lead::factory()->create();

        $response = $this->patchJson(action([ApiController::class, 'update'], ['id' => $lead->id]), [
            'phone' => 123,
        ]);

        $this->assertDatabaseHas(Lead::class, [
            'phone' => null,
        ]);

        $response->assertJsonStructure([
            'errors' => [
                'code',
                'title',
                'detail',
            ],
        ]);
    }

    public function test_can_delete_lead_via_api()
    {
        $lead = Lead::factory()
            ->has(Address::factory())
            ->create();

        $response = $this->deleteJson(action([ApiController::class, 'delete'], ['id' => $lead->id]));

        $lead->refresh();
        $address = $lead->address()->withTrashed()->first();

        $this->assertDatabaseHas(Lead::class, [
            'id' => $lead->id,
            'firstname' => null,
            'email' => null
        ])->assertSoftDeleted($lead)
            ->assertDatabaseHas(Address::class, [
                'id' => $address->id,
                'street' => null,
                'city' => null,
            ])
            ->assertSoftDeleted($lead->address);

        $response->assertStatus(Http::OK->value);
    }

    public function test_delete_lead_not_found_via_api()
    {
        $response = $this->deleteJson(action([ApiController::class, 'delete'], ['id' => rand(10, 100)]));

        $response->assertJsonStructure([
            'errors' => [
                'code',
                'title',
                'detail',
            ],
        ]);
    }

    public function test_can_retrieve_single_lead_via_api()
    {
        $lead = Lead::factory()
            ->has(Address::factory())
            ->afterCreating(function (Lead $lead) {
                $lead->load('address');
            })
            ->create();

        $this->assertDatabaseCount(Lead::class, 1)
            ->assertDatabaseCount(Address::class, 1);

        $response = $this->getJson(action([ApiController::class, 'show'], ['id' => $lead->id]));
        $response->assertStatus(Http::OK->value)
            ->assertJsonCount(1)
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

    public function test_show_lead_not_found_via_api()
    {
        $response = $this->getJson(action([ApiController::class, 'show'], ['id' => rand(10, 100)]));

        $response->assertJsonStructure([
            'errors' => [
                'code',
                'title',
                'detail',
            ],
        ]);
    }
}
