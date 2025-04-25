<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Item;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
class ItemTest extends TestCase
{
    /**
     * @test
     */
    public function item_get_collection_unauthenticated() : void
    {
        $response = $this->get(
                    '/api/item',
                    ['Accept' => 'application/json']
        );
      
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function item_get_collection_authenticated() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/item',
                    ['Accept' => 'application/json']
        );

        $response->assertJsonStructure([
            'data' => [
                '*' =>[
                    'id',
                    'name',
                    'unit'=>[
                        'id',
                        'name',
                        'multiple',
                        'note',
                    ],
                    'price',
                    'item_image',
                    'amount',
                    'stock_in',
                    'stock_out',
                    'description',
                ]
            ]
        ]);

        $user->forceDelete();

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function item_get_resource_unauthenticated() : void
    {
        $item = Item::factory()->create();

        $response = $this->get(
                    '/api/item/'.$item->id,
                    ['Accept' => 'application/json']
        );
        
        $item->forceDelete();

        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function item_get_resource_authenticated() : void
    {
        $item = Item::factory()->create();
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/item/'.$item->id,
                    ['Accept' => 'application/json']
        );

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'name',
                    'unit'=>[
                        'id',
                        'name',
                        'multiple',
                        'note',
                    ],
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

    /**
     * @test
     */
    public function item_post_resource_unauthenticated() : void
    {
        $response = $this->postJson(
                    '/api/item',
                    [
                        'name' => 'the item',
                        'unit' => 'unit',
                        'price' => 1009,
                        'description' => 'sebuah item ya',
                        'amount' => 1000000,   
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function item_post_resource_wrong_input() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        $img = UploadedFile::fake()->image('namanfileya.jpg');

        Passport::actingAs(($user));

           $response = $this->postJson(
                    '/api/item',
                    [
                        'name' => 'e',
                        'unit_id' => '9dbd0f2c-aa32-49b1-8a9b-aa9349446c7c',
                        'price' => 4009,
                        'image' => $img,
                        'description' => 'sebuah item ya',
                        'amount' => 1000000, 
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function item_post_resource_negative_price() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        $img = UploadedFile::fake()->image('namanfileya.jpg');


        Passport::actingAs(($user));

           $response = $this->postJson(
                    '/api/item',
                    [
                        'name' => 'vvvvvvvvvvvvv',
                        'unit_id' => '9dbd0f2c-aa32-49b1-8a9b-aa9349446c7c',
                        'price' => -5,
                        'image' => $img,
                        'description' => 'sebuah item ya',
                        
                        'amount' => 1000000, 
                    ],
                    ['Accept' => 'application/json']
        );
        $user->forceDelete();

        $response->assertStatus(422);

        $response->assertJsonFragment([
            'message' => 'The selected status is invalid.',
        ]);
    }


    /**
     * @test
     */
    public function item_post_resource_unauthorized() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());

        $img = UploadedFile::fake()->image('namanfileya.jpg');
        
        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/item',
                    [
                        'name' => 'vvvvvvvv',
                        'unit_id' => '9dbd0f2c-aa32-49b1-8a9b-aa9349446c7c',
                        'price' => 4009,
                        'description' => 'sebuah item ya',
                        'image' => $img,
                        'amount' => 1000000, 
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function item_post_resource_authorized() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $img = UploadedFile::fake()->image('namanfileya.jpg');

        $response = $this->postJson(
                    '/api/item',
                    [
                        'name' => 'the item',
                        'unit_id' => '9dbd0f2c-aa32-49b1-8a9b-aa9349446c7c',
                        'price' => 4009,
                        'description' => 'sebuah item ya',
                        'image' => $img,
                        'amount' => 1000000, 
                    ],
                    ['Accept' => 'application/json']
        );
        
        $user->forceDelete();
        
        $this->assertDatabaseHas('items', [
            'id' => $response->json('data.id'),
            'name' => 'the item',
            'item_image' => $img->hashName(),
            'unit_id' => '9dbd0f2c-aa32-49b1-8a9b-aa9349446c7c',
            'price' => 4009,
            'description' => 'sebuah item ya',
            'amount'    => 1000000, 
            'stock_in'  => 1000000, 
            'stock_out' => 0,
        ]);

        $response->assertJsonStructure([
           'data' => [
                    'id',
                    'name',
                    'unit'=>[
                        'id',
                        'name',
                        'multiple',
                        'note',
                    ],
                    'price',
                    'item_image',
                    'amount',
                    'stock_in',
                    'stock_out',
                    'description',
            ]
        ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function item_update_resource_unauthentificated() : void
    {
        $item = Item::factory()->create();
      
        $response = $this->putJson(
                    '/api/item/'.$item->id,
                    [
                        'name' => 'the item',
                        'unit_id' => '9dbd0f2c-aa32-49b1-8a9b-aa9349446c7c',
                        'price' => 4009,
                        'description' => 'sebuah item ya',
                        'amount' => 1000000, 
                    ],
                    ['Accept' => 'application/json']
        );
        
        $item->forceDelete();

        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function item_update_resource_unauthorized() : void
    {
        $item = item::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $img = UploadedFile::fake()->image('namanfileya.jpg');

        $response = $this->putJson(
                    '/api/item/'.$item->id,
                    [
                        'name' => 'the item',
                        'unit_id' => '9dbd0f2c-aa32-49b1-8a9b-aa9349446c7c',
                        'price' => 4009,
                        'description' => 'sebuah item ya',
                        'image' => $img,
                        'amount' => 1000000, 
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
    public function item_update_resource_wrong_input() : void
    {
        $item = Item::factory()->create();  
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $img = UploadedFile::fake()->image('namanfileya.jpg');

        $response = $this->putJson(
                    '/api/item/'.$item->id,
                    [
                        'name' => '3',
                        'unit_id' => '9dbd0f2c-aa32-49b1-8a9b-aa9349446c7c',
                        'price' => 4009,
                        'description' => 'sebuah item ya',
                        'image' => $img,
                        'amount' => 1000000, 
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
    public function item_update_resource_negative_price() : void
    {
        $item = Item::factory()->create();  
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $img = UploadedFile::fake()->image('namanfileya.jpg');

        $response = $this->putJson(
                    '/api/item/'.$item->id,
                    [
                        'name' => 'ddddddddd',
                        'unit_id' => '9dbd0f2c-aa32-49b1-8a9b-aa9349446c7c',
                        'price' => -10,
                        'description' => 'sebuah item ya',
                        'image' => $img,
                        'amount' => 1000000, 
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
    public function item_update_resource_authorized() : void
    {
        $item = Item::factory()->create();
        $user = User::factory()->create();
        $img = UploadedFile::fake()->image('namanfileya.jpg');

        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/item/'.$item->id,
                    [
                        'name' => 'the item',
                        'unit_id' => '9dbd0f2c-aa32-49b1-8a9b-aa9349446c7c',
                        'price' => 4009,
                        'description' => 'sebuah item ya',
                        'image' => $img,
                        'amount' => 1000000, 
                    ],
                    ['Accept' => 'application/json']
        );


        $this->assertDatabaseHas('items', [
            'id' => $response->json('data.id'),
            'name' => 'the item',
            'unit_id' => '9dbd0f2c-aa32-49b1-8a9b-aa9349446c7c',
            'price' => 4009,
            'item_image' => $img->hashName(),
            'description' => 'sebuah item ya',
        ]);

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'name',
                    'unit'=>[
                        'id',
                        'name',
                        'multiple',
                        'note',
                    ],
                    'price',
                    'item_image',
                    'amount',
                    'stock_in',
                    'stock_out',
                    'description',
            ]
        ]);
        
        $response->assertStatus(200);

        $item->forceDelete();
        $user->forceDelete();
    }

    /**
     * @test
     */
    public function item_delete_resource_unauthentificated() : void
    {
        $item = Item::factory()->create();
      
        $response = $this->delete(
                    '/api/item/'.$item->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $item->forceDelete();
        
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function item_delete_resource_unauthorized() : void
    {
        $item = Item::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/item/'.$item->id,
                    [],
                    ['Accept' => 'application/json']
        );
        
        $item->forceDelete();
        $user->forceDelete();

        $response->assertStatus(403);
    }

}
