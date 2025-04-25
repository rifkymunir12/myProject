<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
class AddAdminandModTest extends TestCase
{
    /**
     * @test
     */
    public function add_resource_unauthenticated() : void
    {
        $response = $this->postJson(
                    '/api/add_admin_mod',
                    [
                        //'user_id' => '9c9d01f9-305a-485d-b39a-9f469e34caf7', yang asli
                        //'user_id' => '9cb8cc7c-1794-4712-ad6f-a0be5eb5eab0', test
                        'user_id' => '9d0b3a66-d8d6-46b5-8202-720e3316fb36',
                        'role' => 'Mod',
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function add_resource_no_role_chosen() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/add_admin_mod',
                    [
                        //'user_id' => '9c9d01f9-305a-485d-b39a-9f469e34caf7', yang asli
                        //'user_id' => '9cb8cc7c-1794-4712-ad6f-a0be5eb5eab0', test
                        'user_id' => '9d0b3a66-d8d6-46b5-8202-720e3316fb36',
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertJsonFragment([
            'message' => 'The role field is required.',
        ]);

        $user->forceDelete();

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function add_resource_user_dont_exist() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/add_admin_mod',
                    [
                        //'user_id' => '9c9d01f9-305a-485d-b39a-9f469e34caf7', yang asli
                        //'user_id' => '9cb8cc7c-1794-4712-ad6f-a0be5eb5eab0', test
                        'user_id' => '9d0b3a66-d8d6-46b5-8202-720e3316fab6',
                        'role' => 'Mod',
                        ],
                    ['Accept' => 'application/json']
        );

        $response->assertJsonFragment([
            'message' => 'The selected user id is invalid.',
        ]);

        $user->forceDelete();

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function add_resource_no_existing_role() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/add_admin_mod',
                    
                    [
                        //'user_id' => '9c9d01f9-305a-485d-b39a-9f469e34caf7', yang asli
                        //'user_id' => '9cb8cc7c-1794-4712-ad6f-a0be5eb5eab0', test
                        'user_id' => '9d0b3a66-d8d6-46b5-8202-720e3316fb36',
                        'role' => 'Mau',
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertJsonFragment([
            'message' => 'The selected role is invalid.',
        ]);

        $user->forceDelete();

        $response->assertStatus(422);
    }


    /**
     * @test
     */
    public function add_resource_unauthorized() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        
        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/add_admin_mod',
                    [
                        //'user_id' => '9c9d01f9-305a-485d-b39a-9f469e34caf7', yang asli
                        //'user_id' => '9cb8cc7c-1794-4712-ad6f-a0be5eb5eab0', test
                        'user_id' => '9d0b3a66-d8d6-46b5-8202-720e3316fb36',
                        'role' => 'Mod',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertStatus(403);
    }


    /**
     * @test
     */
    public function add_resource_authorized() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/add_admin_mod',
                    [
                        //'user_id' => '9c9d01f9-305a-485d-b39a-9f469e34caf7', yang asli
                        //'user_id' => '9cb8cc7c-1794-4712-ad6f-a0be5eb5eab0', test
                        'user_id' => '9d0b3a66-d8d6-46b5-8202-720e3316fb36',
                        'role' => 'Mod',
                    ],
                    ['Accept' => 'application/json']
        );
    
        $this->assertDatabaseHas('model_has_roles', [
            // 'role_id'            => '9c968f0d-133c-4f0c-9e1f-f545d76c76b0',
            // 'model_type'         => 'App\Models\User',
            // 'model_id'           => '9c9d01f9-305a-485d-b39a-9f469e34caf7',
            'role_id'            => '9d0b37c5-fd89-4a36-908c-83a7c0f48a8d',
            'model_type'         => 'App\Models\User',
            'model_id'           => '9d0b3a66-d8d6-46b5-8202-720e3316fb36',
        ]);

        $response->assertStatus(201);//benar 

        $user->forceDelete();   
    }
}
