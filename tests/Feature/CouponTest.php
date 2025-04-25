<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Coupon;
use Laravel\Passport\Passport;
use Tests\TestCase;
class CouponTest extends TestCase
{
    /**
     * @test
     */
    public function coupon_get_collection_unauthenticated() : void
    {
        $response = $this->get(
                    '/api/coupon',
                    ['Accept' => 'application/json']
        );
        
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function coupon_get_collection_authenticated() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/coupon',
                    ['Accept' => 'application/json']
        );
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                    '*' => [ 
                        'id',
                        'name',
                        'code',
                        'discount',
                        'description',
                ],
            ]
        ]);

        $user->forceDelete();
    }

    /**
     * @test
     */
    public function coupon_get_resource_unauthenticated() : void
    {
        $coupon = Coupon::factory()->create();

        $response = $this->get(
                    '/api/coupon/'.$coupon->id,
                    ['Accept' => 'application/json']
        );

        $coupon->forceDelete();
        
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function coupon_get_resource_authenticated() : void
    {
        $coupon = Coupon::factory()->create();
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/coupon/'.$coupon->id,
                    ['Accept' => 'application/json']
        );

        $coupon->forceDelete();
        $user->forceDelete();
        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'name',
                    'code',
                    'discount',
                    'description',
            ]
        ]);

        $response->assertStatus(200);
    }

   /**
     * @test
     */
    public function coupon_post_resource_unauthenticated() : void
    {
        $response = $this->postJson(
                    '/api/coupon',
                    [
                        'name' => 'the coupon',
                        'code' => 'rrrrrrrrrss',
                        'discount' => 40000,
                        'description' => 'ini adalah descriptionkah',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function coupon_post_resource_wrong_input() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/coupon',
                    [
                        'name' => 'the coupon',
                        'code' => 'kkkkkkkkkkk',
                        'discount' => 100,
                        'description' => 'ini adalah descriptionkah',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function coupon_post_resource_not_unique_code() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/coupon',
                    [
                        'name' => 'couponfake',
                        'code' => 'alalalal',
                        'discount' => 40000,
                        'description' => 'ini adalah descriptionkah',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The code has already been taken.',
        ]);

        $response->assertStatus(422);
        }

    /**
     * @test
     */
    public function coupon_post_resource_name_too_short() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/coupon',
                    [
                        'name' => 'the',
                        'code' => 'jjjjjjjjjjjj',
                        'discount' => 40000,
                        'description' => 'ini adalah descriptionkah',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The name field must be at least 4 characters.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function coupon_post_resource_unauthorized() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        
        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/coupon',
                    [
                        'name' => 'the coupon is fake',
                        'code' => 'askdoasasdoi',
                        'discount' => 40000,
                        'description' => 'ini adalah descriptionkah',
                    ],
                    ['Accept' => 'application/json']
        );

        

        Coupon::where('code', 'askdoasasdiosisoi')->forceDelete();
        $user->forceDelete();

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function coupon_post_resource_authorized() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/coupon',
                    [
                        'name' => 'theddd',
                        'code' => 'yuyuyuyyo',
                        'discount' => 40000,
                        'description' => 'ini adalah descriptionkah',
                    ],
                    ['Accept' => 'application/json']
        );
    
        $this->assertDatabaseHas('coupons', [
            'id' => $response->json('data.id'),
            'name' => 'theddd',
            'code' => 'yuyuyuyyo',
            'discount' => 40000,
            'description' => 'ini adalah descriptionkah',
        ]);
        
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'name',
                    'code',
                    'discount',
                    'description',
            ]
        ]);
        
        Coupon::where('code', 'yuyuyuyyo')->forceDelete();
        $user->forceDelete();
    }

    /**
     * @test
     */
    public function coupon_update_resource_unauthentificated() : void
    {
        $coupon = Coupon::factory()->create();
      
        $response = $this->putJson(
                    '/api/coupon/'.$coupon->id,
                    [
                        'name' => 'the coupon',
                        'code' => 'rrrrrrrrrw',
                        'discount' => 40000,
                        'description' => 'ini adalah descriptionkah',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $coupon->forceDelete();

        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function coupon_update_resource_unauthorized() : void
    {
        $coupon = Coupon::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/coupon/'.$coupon->id,
                    [
                        'name' => 'the coupon',
                        'code' => 'rrrrrrrrrss',
                        'discount' => 40000,
                        'description' => 'ini adalah descriptionkah',
                    ],
                    ['Accept' => 'application/json']
        );
        $coupon->forceDelete();

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function coupon_update_resource_wrong_input() : void
    {
        $coupon = Coupon::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/coupon/'.$coupon->id,
                        [
                            'name' => 'the coupon',
                            'code' => 'mmmmmmmmmm',
                            'discount' => 900,
                                'description' => 'ini adalah descriptionkah',
                        ],
                    ['Accept' => 'application/json']
        );

        $coupon->forceDelete();
        $user->forceDelete();

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function coupon_update_resource_not_unique_code() : void
    {
        $coupon = Coupon::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/coupon/'.$coupon->id,
                        [
                            'name' => 'the coupon',
                            'code' => 'alalalal',
                            'discount' => 40000,
                            'description' => 'ini adalah descriptionkah',
                        ],
                    ['Accept' => 'application/json']
        );

        $coupon->forceDelete();
        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The code has already been taken.',
        ]);
        
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function coupon_update_resource_name_too_short() : void{
        $coupon = Coupon::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/coupon/'.$coupon->id,
                        [
                            'name' => 'th',
                            'code' => 'qweqweqwe',
                            'discount' => 40000,
                            'description' => 'ini adalah descriptionkah',
                        ],
                    ['Accept' => 'application/json']
        );

        $coupon->forceDelete();
        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The name field must be at least 4 characters.',
        ]);
        
        $response->assertStatus(422);
    }


    /**
     * @test
     */
    public function coupon_update_resource_authorized() : void
    {
        $coupon = Coupon::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/coupon/'.$coupon->id,
                    [
                        'name' => 'the coupon',
                        'code' => 'rrrrrrrrrssv',
                        'discount' => 40000,
                        'description' => 'ini adalah descriptionkah',
                    ],
                    ['Accept' => 'application/json']    
        );

        $this->assertDatabaseHas('coupons', [
            'id' => $response->json('data.id'),
            'name' => 'the coupon',
            'code' => 'rrrrrrrrrssv',
            'discount' => 40000,
            'description' => 'ini adalah descriptionkah',
        ]);

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'name',
                    'code',
                    'discount',
                    'description',
            ]
        ]);

        $coupon->forceDelete();
        $user->forceDelete();

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function coupon_delete_resource_unauthentificated() : void
    {
        $coupon = Coupon::factory()->create();
      
        $response = $this->delete(
                    '/api/coupon/'.$coupon->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $coupon->forceDelete();
        
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function coupon_delete_resource_unauthorized() : void
    {
        $coupon = Coupon::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/coupon/'.$coupon->id,
                    [],
                    ['Accept' => 'application/json']
        );
        
        $coupon->forceDelete();
        $user->forceDelete();

        $response->assertStatus(403);
    }

    
    /**
     * @test
     */
     function coupon_delete_resource_authorized() : void
    {
        $coupon = Coupon::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/coupon/'.$coupon->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $response->assertNoContent();
        $this->assertSoftDeleted($coupon);
        $response->assertStatus(204);

        $coupon->forceDelete();
        $user->forceDelete();
        
    }

    
}
