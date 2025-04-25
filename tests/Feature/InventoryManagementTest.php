<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Role;
use App\Models\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
class InventoryManagementTest extends TestCase
{
    /**
     * @test
     */
    public function update_item_amount_unauthenticated() : void
    {
        $item = Item::factory()->create();  
        // $user = User::factory()->create();
        // $user->assignRole(Role::where('name' ,'Admin')->first());
        // Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/update_item_amount',
                    [
                        'item_id' =>  $item->id,
                        'item_in' => 1000,
                    ],
                    ['Accept' => 'application/json']
        );

        $item->forceDelete();

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function update_item_amount_wrong_input() : void
    {
        //buat utk item in gapake minus 
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/update_item_amount',
                    [
                        'item_in' => 1000,
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertStatus(422);
    }


    /**
     * @test
     */
    public function update_item_amount_uuid_item_not_exist() : void
    {
        //buat utk item in gapake minus 
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/update_item_amount',
                    [
                        'item_id' => '9d0bc426-41a5-42b7-ac14-a418c24e3da9',
                        'item_in' => 1000,
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The selected item id is invalid.',
        ]);

        
        $response->assertStatus(422);
    }

    /**
     * @test    
     */
    public function update_item_amount_item_in_minus() : void
    {
        //buat utk item in gapake minus 
        $user = User::factory()->create(); 
        $item = Item::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/update_item_amount',
                    [
                        'item_id' => $item->id,
                        'item_in' => -100,
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
        $item->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The item in field must be at least 0.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function update_item_amount_unauthorized() : void
    {
        $item = Item::factory()->create();  
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/update_item_amount',
                    [
                        'item_id' =>  $item->id,
                        'item_in' => 1000,
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
        $item->forceDelete();

        $response->assertStatus(403);
    }


    /**
     * @test
     */
    public function update_item_amount_authorized() : void
    {
        $item = Item::factory()->create();  
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Mod')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/update_item_amount',
                    [
                        'item_id' => $item->id,
                        'item_in' => 1000,
                    ],
                    ['Accept' => 'application/json']
        );

        $this->assertDatabaseHas('items', [
            'id'                => $item->id,
            'amount'            => $response->json('data.amount'),
            'stock_in'          => $response->json('data.stock_in'),
            'stock_out'         => 0,
        ]);

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'name',
                    'unit',
                    'price',
                    'item_image',
                    'amount',
                    'stock_in',
                    'stock_out',
                    'description',
            ]
        ]);

        $user->forceDelete();
        $item->forceDelete();        

        $response->assertStatus(200);

          
    }

    /**
     * @test
     */
    public function edit_item_stock_unauthenticated() : void
    {
        $item = Item::factory()->create();  
        // $user = User::factory()->create();
        // $user->assignRole(Role::where('name' ,'Admin')->first());
        // Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/edit_item_stock',
                    [
                        'item_id'   =>  $item->id,
                        'stock_in'  =>  10000,
                        'stock_out' =>  5000,
                    ],
                    ['Accept' => 'application/json']
        );

        $item->forceDelete();

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function edit_item_stock_wrong_input() : void
    { 
        $user = User::factory()->create();
        $item = Item::factory()->create(); 
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/edit_item_stock',
                    [
                        'item_id'   => $item->id,
                        'stock_in'  => 50000,
                    ],
                    ['Accept' => 'application/json']
        );
        
        $item->forceDelete();
        $user->forceDelete();

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function edit_item_stock_uuid_item_not_exist() : void
    { 
        $user = User::factory()->create(); 
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/edit_item_stock',
                    [
                        'item_id'   => '9cb8d5dd-b5ba-45f3-9f20-3bd9b0f92443',
                        'stock_in'  => 50000,
                        'stock_out' => 5000,
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The selected item id is invalid.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function edit_item_stock_stock_in_lower_than_stock_out() : void
    {
        $item = Item::factory()->create();  
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/edit_item_stock',
                    [
                        'item_id'   => $item->id,
                        'stock_in'  => 500,
                        'stock_out' => 1000,
                    ],
                    ['Accept' => 'application/json']
                    
                );

        $item->forceDelete();
        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The stock in field must be greater than or equal to 1000.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function edit_item_stock_minus_stock_out() : void
    {
        $item = Item::factory()->create();  
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/edit_item_stock',
                    [
                        'item_id'   => $item->id,
                        'stock_in'  => 100,
                        'stock_out' => -20,
                    ],
                    ['Accept' => 'application/json']
                    
                );
        $item->forceDelete();
        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The stock out field must be at least 0.',
        ]);

        $response->assertStatus(422);
    }


    /**
     * @test
     */
    public function edit_item_stock_unauthorized() : void
    {
        $item = Item::factory()->create();  
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/edit_item_stock',
                    [
                        'item_id'   => $item->id,
                        'stock_in'  => 50000,
                        'stock_out' => 5000,
                    ],
                    ['Accept' => 'application/json']
        );

        $item->forceDelete();
        $user->forceDelete();

        $response->assertStatus(403);
    }


    /**
     * @test
     */
    public function edit_item_stock_authorized() : void
    {
        $item = Item::factory()->create();  
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/edit_item_stock',
                    [
                        'item_id'   => $item->id,
                        'stock_in'  => 50000,
                        'stock_out' => 5000,
                    ],
                    ['Accept' => 'application/json']
        );

        $this->assertDatabaseHas('items', [
            'id'                => $item->id,
            'amount'            => $response->json('data.amount'),
            'stock_in'          => $response->json('data.stock_in'),
            'stock_out'         => $response->json('data.stock_out'),
        ]);

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'name',
                    'unit',
                    'price',
                    'item_image',
                    'amount',
                    'stock_in',
                    'stock_out',
                    'description',
            ]
        ]);

        $item->forceDelete();
        $user->forceDelete();

        $response->assertStatus(200);
    }
}
