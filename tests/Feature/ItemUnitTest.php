<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\ItemUnit;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
class ItemUnitTest extends TestCase
{
    /**
     * @test
     */
    public function item_unit_get_collection_unauthenticated() : void
    {
        $response = $this->get(
                    '/api/item_unit',
                    ['Accept' => 'application/json']
        );
      
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function item_unit_get_collection_authenticated() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/item_unit',
                    ['Accept' => 'application/json']
        );

        $response->assertJsonStructure([
            'data' => [
                '*' =>[
                        'id',
                        'name',
                        'multiple',
                        'note',
                    ],
            ]
        ]);

        $user->forceDelete();

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function item_unit_get_resource_unauthenticated() : void
    {
        $item_unit = ItemUnit::factory()->create();

        $response = $this->get(
                    '/api/item_unit/'.$item_unit->id,
                    ['Accept' => 'application/json']
        );
        
        $item_unit->forceDelete();

        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function item_unit_get_resource_authenticated() : void
    {
        $item_unit = ItemUnit::factory()->create();
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/item_unit/'.$item_unit->id,
                    ['Accept' => 'application/json']
        );

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'name',
                    'multiple',
                    'note',
            ]
        ]);

        $item_unit->forceDelete();
        $user->forceDelete();
        
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function item_unit_post_resource_unauthenticated() : void
    {
        $response = $this->postJson(
                    '/api/item_unit',
                    [
                        'id',
                        'name',
                        'multiple',
                        'note',  
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function item_unit_post_resource_wrong_input() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));

           $response = $this->postJson(
                    '/api/item_unit',
                    [
                        'name'      => 'itemfake',
                        'multiple'  => '0',
                        'note'      => 'thisisnote',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function item_unit_post_resource_unauthorized() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());

        $img = UploadedFile::fake()->image('namanfileya.jpg');
        
        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/item_unit',
                    [
                        'name'      => 'itemfake',
                        'multiple'  => 5,
                        'note'      => 'thisisnote',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function item_unit_post_resource_authorized() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/item_unit',
                    [
                        'name'      => 'itemfake',
                        'multiple'  =>  5,
                        'note'      => 'thisisnote', 
                    ],
                    ['Accept' => 'application/json']
        );
        
        $user->forceDelete();
        
        $this->assertDatabaseHas('item_units', [
            'id' => $response->json('data.id'),
            'name' => 'itemfake',
            'multiple' => 5,
            'note' => 'thisisnote',
        ]);

        
        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'name',
                    'multiple',
                    'note',
            ]
        ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function item_unit_update_resource_unauthentificated() : void
    {
        $item_unit = ItemUnit::factory()->create();
      
        $response = $this->putJson(
                    '/api/item_unit/'.$item_unit->id,
                    [
                        'name'      => 'itemfake',
                        'multiple'  => '5',
                        'note'      => 'thisisnote', 
                    ],
                    ['Accept' => 'application/json']
        );
        
        $item_unit->forceDelete();

        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function item_unit_update_resource_unauthorized() : void
    {
        $item_unit = ItemUnit::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $img = UploadedFile::fake()->image('namanfileya.jpg');

        $response = $this->putJson(
                    '/api/item_unit/'.$item_unit->id,
                    [
                        'name'      => 'itemfake',
                        'multiple'  => '5',
                        'note'      => 'thisisnote',
                    ],
                    ['Accept' => 'application/json']
        );

        $item_unit->forceDelete();
        $user->forceDelete();
        
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function item_unit_update_resource_wrong_input() : void
    {
        $item_unit = ItemUnit::factory()->create();  
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/item_unit/'.$item_unit->id,
                    [
                        'name'      => 'itemfake',
                        'multiple'  =>  -1,
                        'note'      => 'thisisnote',
                    ],
                    ['Accept' => 'application/json']
        );
    
        $item_unit->forceDelete();
        $user->forceDelete();
        
        $response->assertStatus(422);
    }


    /**
     * @test
     */
    public function item_unit_update_resource_authorized() : void
    {
        $item_unit = ItemUnit::factory()->create();
        $user = User::factory()->create();
        $img = UploadedFile::fake()->image('namanfileya.jpg');

        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/item_unit/'.$item_unit->id,
                    [
                        'name'      => 'itemfake',
                        'multiple'  => '5',
                        'note'      => 'thisisnote',
                    ],
                    ['Accept' => 'application/json']
        );


        $this->assertDatabaseHas('item_units', [
            'id' => $response->json('data.id'),
            'name' => 'itemfake',
            'multiple' => 5,
            'note' => 'thisisnote',
        ]);

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'name',
                    'multiple',
                    'note',
            ]
        ]);
        
        $response->assertStatus(200);

        $item_unit->forceDelete();
        $user->forceDelete();
    }

    /**
     * @test
     */
    public function item_unit_unit_delete_resource_unauthentificated() : void
    {
        $item_unit = ItemUnit::factory()->create();
      
        $response = $this->delete(
                    '/api/item_unit/'.$item_unit->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $item_unit->forceDelete();
        
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function item_unit_delete_resource_unauthorized() : void
    {
        $item_unit = ItemUnit::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/item_unit/'.$item_unit->id,
                    [],
                    ['Accept' => 'application/json']
        );
        
        $item_unit->forceDelete();
        $user->forceDelete();

        $response->assertStatus(403);
    }

    
    /**
     * @test
     */
     function item_unit_delete_resource_authorized() : void
    {
        $item_unit = ItemUnit::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/item_unit/'.$item_unit->id,
                    [],
                    ['Accept' => 'application/json']
        );
       
        $response->assertNoContent();
        $this->assertSoftDeleted($item_unit);
        $response->assertStatus(204);

        $item_unit->forceDelete();
        $user->forceDelete();
    }    
}
