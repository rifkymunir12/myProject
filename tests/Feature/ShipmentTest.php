<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Shipment;
use Laravel\Passport\Passport;
use Tests\TestCase;
class ShipmentTest extends TestCase
{
    /**
     * @test
     */
    public function shipment_get_collection_unauthenticated() : void
    {
        $response = $this->get(
                    '/api/shipment',
                    ['Accept' => 'application/json']
        );
        
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function shipment_get_collection_authenticated() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/shipment',
                    ['Accept' => 'application/json']
        );

        $response->assertJsonStructure([
            'data' => [
                '*' => [ 
                        'id',
                        'name',
                        'price',
                        'description',
                ],
            ]
        ]);

        $response->assertStatus(200);

        $user->forceDelete();
    }

    /**
     * @test
     */
    public function shipment_get_resource_unauthenticated() : void
    {
        $shipment = Shipment::factory()->create();

        $response = $this->get(
                    '/api/shipment/'.$shipment->id,
                    ['Accept' => 'application/json']
        );

        $shipment->forceDelete();
        
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function shipment_get_resource_authenticated() : void
    {
        $shipment = Shipment::factory()->create();
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/shipment/'.$shipment->id,
                    ['Accept' => 'application/json']
        );
        
        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'name',
                    'price',
                    'description',
            ]
        ]);

        $response->assertStatus(200);

        $shipment->forceDelete();
        $user->forceDelete();
    }

    /**
     * @test
     */
    public function shipment_post_resource_unauthenticated() : void
    {
        $response = $this->postJson(
                    '/api/shipment',
                    [
                        'name' => 'aaaaaavvv',
                        'price' => 12000,
                        'description' => 'shipment kah',
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function shipment_post_resource_wrong_input() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/shipment',
                    [
                        'name' => 1111,
                        'price' => 100000,
                        'description' => 'shipment kah',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function shipment_post_resource_low_price() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/shipment',
                    [
                        'name' => 'cccccc',
                        'price' => 100,
                        'description' => 'shipment kah',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The price field must be at least 1000.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function shipment_post_resource_unauthorized() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        
        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/shipment',
                    [
                        'name' => 'aaaaaavvv',
                        'price' => 12000,
                        'description' => 'shipment kah',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertStatus(403);
    }


    /**
     * @test
     */
    public function shipment_post_resource_authorized() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/shipment',
                    [
                        'name' => 'aaaaaavvv',
                        'price' => 12000,
                        'description' => 'shipment kah',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $this->assertDatabaseHas('shipments', [
            'id'          => $response->json('data.id'),
            'name'        => 'aaaaaavvv',
            'price'       => 12000,
            'description' => 'shipment kah'
        ]);

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'name',
                    'price',
                    'description',
            ]
        ]);

        $user->forceDelete();

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function shipment_update_resource_unauthentificated() : void
    {
        $shipment = Shipment::factory()->create();
      
        $response = $this->putJson(
                    '/api/shipment/'.$shipment->id,
                    [
                        'name' => 'aaaaaavvv',
                        'price' => 12000,
                        'description' => 'shipment kah',
                    ],
                    ['Accept' => 'application/json']
        );

        $shipment->forceDelete();
        
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function shipment_update_resource_unauthorized() : void
    {
        $shipment = Shipment::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/shipment/'.$shipment->id,
                    [
                        'name' => 'aaaaaavvv',
                        'price' => 12000,
                        'description' => 'shipment kah',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $shipment->forceDelete();
        $user->forceDelete();

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function shipment_update_resource_wrong_input() : void
    {
        $shipment = Shipment::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/shipment/'.$shipment->id,
                    [
                        'name' => 'aaaaaavvv',
                        'price' => 120,
                        'description' => 'shipment kah',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $shipment->forceDelete();
        $user->forceDelete();

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function shipment_update_resource_low_price() : void
    {
        $shipment = Shipment::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/shipment/'.$shipment->id,
                    [
                        'name' => 'cccccc',
                        'price' => 100,
                        'description' => 'shipment kah',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $shipment->forceDelete();
        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The price field must be at least 1000.',
        ]);

        $response->assertStatus(422);
    }


    /**
     * @test
     */
    public function shipment_update_resource_authorized() : void
    {
        $shipment = Shipment::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/shipment/'.$shipment->id,
                    [
                        'name' => 'aaaaaavvv',
                        'price' => 12000,
                        'description' => 'shipment kah',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $this->assertDatabaseHas('shipments', [
            'id'          => $response->json('data.id'),
            'name'        => 'aaaaaavvv',
            'price'       => 12000,
            'description' => 'shipment kah'
        ]);

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'name',
                    'price',
                    'description',
            ]
        ]);

        $response->assertStatus(200);

        $shipment->forceDelete();
        $user->forceDelete();

    }

    /**
     * @test
     */
    public function shipment_delete_resource_unauthentificated() : void
    {
        $shipment = Shipment::factory()->create();
      
        $response = $this->delete(
                    '/api/comment/'.$shipment->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $shipment->forceDelete();
        
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function shipment_delete_resource_unauthorized() : void
    {
        $shipment = Shipment::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/shipment/'.$shipment->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $shipment->forceDelete();
        $user->forceDelete();
        
        $response->assertStatus(403);
    }

    
    /**
     * @test
     */
     function shipment_delete_resource_authorized() : void
    {
        $shipment = Shipment::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/shipment/'.$shipment->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $response->assertStatus(204);
        $response->assertNoContent();
        $this->assertSoftDeleted($shipment);

        $shipment->forceDelete();
        $user->forceDelete();
    }

    
}
