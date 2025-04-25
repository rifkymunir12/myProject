<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Role;
use App\Models\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
class UserTest extends TestCase
{
    /**
     * @test
     */
    public function user_get_collection_unauthenticated() : void
    {
        $response = $this->get(
                    '/api/user',
                    ['Accept' => 'application/json']
        );
        
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function user_get_collection_authenticated() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/user',
                    ['Accept' => 'application/json']
        );
        
        $response->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'image_profile',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'gender',
                    'lokasi',
                    'nomor_telepon',
                    'timezone',
                    'role'
                ]
            ],
        ]);

        $response->assertStatus(200);

        $user->forceDelete();
    }

     /**
     * @test
     */
    public function user_get_resource_unauthenticated() : void
    {
        $user = User::factory()->create();

        $response = $this->get(
                    '/api/user/'.$user->id,
                    ['Accept' => 'application/json']
        );
        
        $user->forceDelete();

        $response->assertStatus(401);
    }


     /**
     * @test
     */
    public function user_get_resource_authenticated() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/user/'.$user->id,
                    ['Accept' => 'application/json']
        );

        $response->assertJsonStructure([
            'message',
            'data' => [
                    'id',
                    'name',
                    'email',
                    'image_profile',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'gender',
                    'lokasi',
                    'nomor_telepon',
                    'timezone',
                    'role'
            ],
        ]);
        
        $response->assertStatus(200);

        $user->forceDelete();
    }

    /**
     * @test
     */
    public function user_register() : void
    {
        $email = fake()->unique()->safeEmail();
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        $response = $this->postJson(
                    '/api/register',
                    [
                        'name' => 'asasasdasd',
                        'email' => $email,
                        'profile' => $img,
                        'password'  => '12345678',
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );

        //$response->dd();

        $this->assertDatabaseHas('users', [
            'id'          => $response->json('data.id'),
            'name' => 'asasasdasd',
            'email' => $email,
            'image_profile' => $img->hashName(),
            'tempat_lahir'=>'tempat',
            'tanggal_lahir'=> '1999-09-09',
            'gender'=>'Wanita',
            'lokasi'=> 'lokasi',
            'nomor_telepon'=>'010110101000',
            'timezone'=>'WITA',
        ]);

        $response->assertJsonStructure([
            'message',
            'data' => [
           
                    'id',
                    'name',
                    'email',
                    'image_profile',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'gender',
                    'lokasi',
                    'nomor_telepon',
                    'timezone',
                    'role'
                
            ],
        ]);
        

        $response->assertStatus(200);
    }

     /**
     * @test
     */
    public function user_register_with_no_profile_image() : void
    {
        $email = fake()->unique()->safeEmail();
        $response = $this->postJson(
                    '/api/register',
                    [
                        'name' => 'asasasdasd',
                        'email' => $email,
                        'password'  => '12345678',
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );

        //$response->dd();

        $this->assertDatabaseHas('users', [
            'id'          => $response->json('data.id'),
            'name' => 'asasasdasd',
            'email' => $email,
            'image_profile' => null,
            'tempat_lahir'=>'tempat',
            'tanggal_lahir'=> '1999-09-09',
            'gender'=>'Wanita',
            'lokasi'=> 'lokasi',
            'nomor_telepon'=>'010110101000',
            'timezone'=>'WITA',
        ]);

        $response->assertJsonStructure([
            'message',
            'data' => [
           
                    'id',
                    'name',
                    'email',
                    'image_profile',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'gender',
                    'lokasi',
                    'nomor_telepon',
                    'timezone',
                    'role'
                
            ],
        ]);
        

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function user_register_wrong_input() : void
    {
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        $response = $this->postJson(
                    '/api/register',
                    [
                        'name' => 'asasasdasd',
                        'email' => fake()->unique()->safeEmail(),
                        'profile' => $img,
                        'password'  => '12345678',
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'timezone'=>'WITA',
                        'nomor_telepon'=>'01011',
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function user_register_not_unique_email() : void
    {
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        $response = $this->postJson(
                    '/api/register',
                    [
                        'name' => 'asasasdasd',
                        'email' => 'aajajajaj@gmail.com',
                        'profile' => $img,
                        'password'  => '12345678',
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertJsonFragment([
            'message' => 'The email has already been taken.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function user_register_symbol_in_name() : void
    {
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        $response = $this->postJson(
                    '/api/register',
                    [
                        'name' => 'ccccccccccccc++*+*+{{~}',
                        'email' => fake()->unique()->safeEmail(),
                        'profile' => $img,
                        'password'  => '12345678',
                        'tempat_lahir'=>'tempat',
                        'gender'=>'Wanita',
                        'tanggal_lahir'=> '1999-09-09',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertJsonFragment([
            'message' => 'The name field must only contain letters, numbers, dashes, and underscores.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function user_register_symbol_in_password() : void
    {
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        $response = $this->postJson(
                    '/api/register',
                    [
                        'name' => 'ccccccccccccc',
                        'email' => fake()->unique()->safeEmail(),
                        'profile' => $img,
                        'password'  => '1+*2-345678',
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertJsonFragment([
            'message' => 'The password field must only contain letters, numbers, dashes, and underscores.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function user_register_redirected_after_login() : void
    {
        $user = User::factory()->create();
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/register',
                    [
                        'name' => 'asasasdasd',
                        'email' => fake()->unique()->safeEmail(),
                        'password'  => '12345678',
                        'profile' => $img,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WIB',
                    ],
                    ['Accept' => 'application/json']
        );
       //$response->dd();

        $response->assertStatus(302);
    }

    /**
     * @test
     */
    public function user_update_resource_unauthentificated() : void
    {
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        $user = User::factory()->create();
      
        $response = $this->putJson(
                    '/api/user/'.$user->id,
                    [
                        'name' => 'asasasdasd',
                        'email' => fake()->unique()->safeEmail(),
                        'password'  => '12345678',
                        'profile' => $img,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $user->forceDelete();

        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function user_update_resource_unauthorized() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/user/'.$user->id,
                    [
                        'name' => 'asasasdasd',
                        'email' => fake()->unique()->safeEmail(),
                        'password'  => '12345678',
                        'image_profile' => $img,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $user->forceDelete();

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function user_update_resource_wrong_input() : void
    {
        $user = User::factory()->create();
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/user/'.$user->id,
                    [
                        'name' => 'asasasdasd',
                        'email' => fake()->unique()->safeEmail(),
                        'password'  => '12345678',
                        'profile' => $img,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-0912312',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $user->forceDelete();

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function user_update_resource_not_unique_email() : void
    {
        $user = User::factory()->create();
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/user/'.$user->id,
                    [
                        'name' => 'asasasdasd',
                        'email' => 'aajajajaj@gmail.com',
                        'password'  => '12345678',
                        'profile' => $img,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The email has already been taken.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function user_update_resource_symbol_in_name() : void
    {
        $user = User::factory()->create();
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/user/'.$user->id,
                    [
                        'name' => 'asasasdasd****++++',
                        'email' => fake()->unique()->safeEmail(),
                        'password'  => '12345678',
                        'profile' => $img,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The name field must only contain letters, numbers, dashes, and underscores.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function user_update_resource_symbol_in_password() : void
    {
        $user = User::factory()->create();
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/user/'.$user->id,
                    [
                        'name' => 'asasasdasd',
                        'email' => fake()->unique()->safeEmail(),
                        'password'  => '1234567@@++8',
                        'profile' => $img,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-0912312',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $user->forceDelete();

        $response->assertStatus(422);
    }

    


    /**
     * @test
     */
    public function user_update_resource_authorized() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        $email = fake()->unique()->safeEmail();

        $response = $this->putJson(
                    '/api/user/'.$user->id,
                    [
                        'name' => 'asasasdasd',
                        'email' => $email,
                        'password'  => '12345678',
                        'profile' => $img,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );

        $this->assertDatabaseHas('users', [
            'id'          => $response->json('data.id'),
            'name' => 'asasasdasd',
            'email' => $email,
            'image_profile' => $img->hashName(),
            'tempat_lahir'=>'tempat',
            'tanggal_lahir'=> '1999-09-09',
            'gender'=>'Wanita',
            'lokasi'=> 'lokasi',
            'nomor_telepon'=>'010110101000',
            'timezone'=>'WITA',
        ]);

        $response->assertJsonStructure([
            'message',
            'data' => [
                    'id',
                    'name',
                    'email',
                    'image_profile',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'gender',
                    'lokasi',
                    'nomor_telepon',
                    'timezone',
                    'role'
            ],
        ]);
        
        $response->assertStatus(200);

        $user->forceDelete();        
    }

    /**
     * @test
     */
    public function user_update_resource_authorized_remove_image_profile() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);
        $email = fake()->unique()->safeEmail();

        $response = $this->putJson(
                    '/api/user/'.$user->id,
                    [
                        'name' => 'asasasdasd',
                        'email' => $email,
                        'password'  => '12345678',
                        'profile' => null,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );

        $this->assertDatabaseHas('users', [
            'id'   => $response->json('data.id'),
            'name' => 'asasasdasd',
            'email' => $email,
            'image_profile' => null,
            'tempat_lahir'=>'tempat',
            'tanggal_lahir'=> '1999-09-09',
            'gender'=>'Wanita',
            'lokasi'=> 'lokasi',
            'nomor_telepon'=>'010110101000',
            'timezone'=>'WITA',
        ]);

        $response->assertJsonStructure([
            'message',
            'data' => [
                    'id',
                    'name',
                    'email',
                    'image_profile',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'gender',
                    'lokasi',
                    'nomor_telepon',
                    'timezone',
                    'role'
            ],
        ]);
        
        $response->assertJsonFragment([
            'image_profile' => 'http://localhost/storage/',
        ]);

        $response->assertStatus(200);

        $user->forceDelete();        
    }

    /**
     * @test
     */
    public function user_delete_resource_unauthentificated() : void
    {
        $user = User::factory()->create();
      
        $response = $this->delete(
                    '/api/user/'.$user->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
        
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function user_delete_resource_unauthorized() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/user/'.$user->id,
                    [],
                    ['Accept' => 'application/json']
        );
        
        $user->forceDelete();

        $response->assertStatus(403);
    }

    
    /**
     * @test
     */
     function user_delete_resource_authorized() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/user/'.$user->id,
                    [],
                    ['Accept' => 'application/json']
        );
        
        $response->assertStatus(204);
        $response->assertNoContent();
        $this->assertSoftDeleted($user);

        $user->forceDelete();
    }

    //test case update user untuk individu sendiri juga dibuat


     /**
     * @test
     */
    public function user_update_own_resource_unauthentificated() : void
    {
        $user = User::factory()->create();
        $img = UploadedFile::fake()->image('namanfileya.jpg');
      
        $response = $this->postJson(
                    '/api/user_update',
                    [
                        'name' => 'asasasdasd',
                        'email' => fake()->unique()->safeEmail(),
                        'password'  => '12345678',
                        'profile' => $img,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $user->forceDelete();

        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function user_update_own_resource_wrong_input() : void
    {
        $user = User::factory()->create();
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/user_update',
                    [
                        'name' => 'aa',
                        'email' => fake()->unique()->safeEmail(),
                        'password'  => '12345678',
                        'profile' => $img,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $response->assertStatus(422);

        $user->forceDelete();
    }

    /**
     * @test
     */
    public function user_update_own_resource_not_unique_email() : void
    {
        $user = User::factory()->create();
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/user_update',
                    [
                        'name' => 'aaaaaaaaaaaa',
                        'email' => 'aajajajaj@gmail.com',
                        'password'  => '12345678',
                        'profile' => $img,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertJsonFragment([
            'message' => 'The email has already been taken.',
        ]);
        
        $response->assertStatus(422);

        $user->forceDelete();
    }

    /**
     * @test
     */
    public function user_update_own_resource_symbol_in_name() : void
    {
        $user = User::factory()->create();
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/user_update',
                    [
                        'name' => 'aaaaaaaa+***a',
                        'email' => fake()->unique()->safeEmail(),
                        'password'  => '12345678',
                        'profile' => $img,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $response->assertStatus(422);

        $response->assertJsonFragment([
            'message' => 'The name field must only contain letters, numbers, dashes, and underscores.',
        ]);

        $user->forceDelete();
    }

    /**
     * @test
     */
    public function user_update_own_resource_symbol_in_password() : void
    {
        $user = User::factory()->create();
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/user_update',
                    [
                        'name' => 'aaaaaaaaaaa',
                        'email' => fake()->unique()->safeEmail(),
                        'password'  => '1234****+`{}5678',
                        'profile' => $img,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $response->assertStatus(422);

        $response->assertJsonFragment([
            'message' => 'The password field must only contain letters, numbers, dashes, and underscores.',
        ]);

        $user->forceDelete();
    }
    

     /**
     * @test
     */
    public function user_update_own_resource_authentificated() : void
    {
        $user = User::factory()->create();
        $img = UploadedFile::fake()->image('namanfileya.jpg');
        Passport::actingAs($user);
        $email = fake()->unique()->safeEmail();

        $response = $this->postJson(
                    '/api/user_update',
                    [
                        'name' => 'asasasdasd',
                        'email' => $email,
                        'password'  => '12345678',
                        'profile' => $img,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $this->assertDatabaseHas('users', [
            'id'          => $response->json('data.id'),
            'name' => 'asasasdasd',
            'email' => $email,
            'image_profile' => $img->hashName(),
            'tempat_lahir'=>'tempat',
            'tanggal_lahir'=> '1999-09-09',
            'gender'=>'Wanita',
            'lokasi'=> 'lokasi',
            'nomor_telepon'=>'010110101000',
            'timezone'=>'WITA',
        ]);

        $response->assertJsonStructure([
            'message',
            'data' => [
                    'id',
                    'name',
                    'email',
                    'image_profile',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'gender',
                    'lokasi',
                    'nomor_telepon',
                    'timezone',
                    'role'
            ],
        ]);
        
        $response->assertStatus(200);

        $user->forceDelete();
    }

    /**
     * @test
     */
    public function user_update_own_resource_authentificated_remove_image_profile() : void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        $email = fake()->unique()->safeEmail();

        $response = $this->postJson(
                    '/api/user_update',
                    [
                        'name' => 'asasasdasd',
                        'email' => $email,
                        'password'  => '12345678',
                        'profile' =>  null,
                        'tempat_lahir'=>'tempat',
                        'tanggal_lahir'=> '1999-09-09',
                        'gender'=>'Wanita',
                        'lokasi'=> 'lokasi',
                        'nomor_telepon'=>'010110101000',
                        'timezone'=>'WITA',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $this->assertDatabaseHas('users', [
            'id'          => $response->json('data.id'),
            'name' => 'asasasdasd',
            'email' => $email,
            'image_profile' => null,
            'tempat_lahir'=>'tempat',
            'tanggal_lahir'=> '1999-09-09',
            'gender'=>'Wanita',
            'lokasi'=> 'lokasi',
            'nomor_telepon'=>'010110101000',
            'timezone'=>'WITA',
        ]);

        $response->assertJsonStructure([
            'message',
            'data' => [
                    'id',
                    'name',
                    'email',
                    'image_profile',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'gender',
                    'lokasi',
                    'nomor_telepon',
                    'timezone',
                    'role'
            ],
        ]);

        $response->assertJsonFragment([
            'image_profile' => 'http://localhost/storage/',
        ]);
        
        $response->assertStatus(200);

        $user->forceDelete();
    }
    //buat wrong input untuk segala fungsi test, meskipun 422 error nya sudah detail
    

    //tambah lagi jika inputnya tidak sesuai ekspektasi (422) (ini aja dulu sama 401,403 , kumpul jumat)

    /*
    /
     * @test
     */
    // public function if_user_is_authenticated(): void
    // {
    //     $user = User::factory()->create();

    //     Passport::actingAs($user);

    //     $response = $this->get(
    //         '/api/user',
    //         ['Accept' => 'application/javascript']
    //     );
    //     $response->assertStatus(200);

    //     $user->delete();
    // }

    // /** 
    //  * @test
    // */
    // public function if_all_users_have_role(): void
    // {
    //     $users = User::get();
    //     $result = true;

    //     foreach($users as $user){
    //         if ($user->getRoleNames()->first() == NULL){
    //             $result = false;
    //         }
    //     }

    //     $this->assertTrue($result);
    // }

    // /** 
    //  * @test
    // */
    // public function if_user_has_proper_structure(): void
    // {
    //     $user = User::factory()->create();
    //     $user->assignRole(Role::where('name' ,'User')->first());
    //     Passport::actingAs($user);
    //     $response =  $this->get('/api/user/'.$user->id);

    //     $response->assertJsonStructure([
    //         'message',
    //         'data' => [
    //             'id',
    //             'name',
    //             'email',
    //             'image_profile',
    //             'tempat_lahir',
    //             'tanggal_lahir',
    //             'gender',
    //             'lokasi',
    //             'nomor_telepon',
    //             'timezone',
    //             'role'
    //         ]
    //     ]);
    // }

    // /** 
    //  * @test
    // */
    // public function is_the_content_type_javascript(): void
    // {
    //     $user = User::factory()->create();

    //     Passport::actingAs($user);

    //     $response = $this->get(
    //         '/api/user',
    //         ['Content-Type' =>  'application/json'],
    //     );

    //     $user->delete();
    //     $response->assertHeader('Content-Type', 'application/javascript');
    //     //pas parameter di get dan assertHeader dibalik, hasilnya malah true
    // }
    

    // /** 
    //  * @test
    // */
    // public function check_users_collection(): void
    // {
    //     $user = User::factory()->create();

    //     Passport::actingAs($user);

    //     $response = $this->get(
    //         '/api/user',
    //     );

    //     $user->delete();

    //     $response->assertJsonStructure([
    //         'message',
    //         'data' => [
    //             '*' => [
    //                 'id',
    //                 'name',
    //                 'email',
    //                 'image_profile',
    //                 'tempat_lahir',
    //                 'tanggal_lahir',
    //                 'gender',
    //                 'lokasi',
    //                 'nomor_telepon',
    //                 'timezone',
    //                 'role'
    //             ]
    //         ],
    //     ]);
    // }

    // /** 
    //  * @test
    // */
    // public function check_if_user_is_deleted(): void
    // {
    //     $user = User::factory()->create();
    //     $user->assignRole(Role::where('name' ,'Admin')->first());

    //     Passport::actingAs($user);

    //     $this->delete(
    //         '/api/user/'.$user->id,
    //     );

    //     $this->assertTrue(User::find($user->id) == null);
    // }
    
    //nama kelas dan filenya harus ada test di terakhirnya
}
