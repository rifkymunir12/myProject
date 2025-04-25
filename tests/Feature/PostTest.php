<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Post;
use Laravel\Passport\Passport;
use Tests\TestCase;
class PostTest extends TestCase
{
    /**
     * @test
     */
    public function post_get_collection_unauthenticated() : void
    {
        $response = $this->get(
                    '/api/post',
                    ['Accept' => 'application/json']
        );
        
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function post_get_collection_authenticated() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/post',
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();
        
        $response->assertStatus(200);   

        $response->assertJsonStructure([
            'data' => [
                    '*' => [ 
                        'id',
                        'title',
                        'content',
                        'user'  => [ 
                            'id',
                            'name',
                            'email',
                            'role',
                        ],
                        'comment',
                        'created_at',
                        'updated_at',
                ],
            ]
        ]);
    }

    /**
     * @test
     */
    public function post_get_resource_unauthenticated() : void
    {
        $post = Post::factory()->create();

        $response = $this->get(
                    '/api/post/'.$post->id,
                    ['Accept' => 'application/json']
        );

        $post->forceDelete();
        
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function post_get_resource_authenticated() : void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/post/'.$post->id,
                    ['Accept' => 'application/json']
        );
        
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'title',
                    'content',
                    'user'  => [ 
                        'id',
                        'name',
                        'email',
                        'role',
                    ],
                    'comment',
                    'created_at',
                    'updated_at',
               
            ]
        ]);

        $post->forceDelete();
        $user->forceDelete();
    }

    /**
     * @test
     */
    public function post_post_resource_unauthenticated() : void
    {
        $response = $this->postJson(
                    '/api/post',
                    [
                        'title' => 'this is the title',
                        'content' => 'ini adalah konten yang terletak di post ini haloha',
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function post_post_resource_wrong_input() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/post',
                    [
                        'title' => 'dddddddddddd',
                    ],
                    ['Accept' => 'app   lication/json']
        );

        $user->forceDelete();

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function post_post_resource_short_title() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/post',
                    [
                        'title' => 'thi',
                        'content' => 'ini adalah konten yang terletak di post ini haloha',
                    ],
                    ['Accept' => 'app   lication/json']
        );

        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The title field must be at least 8 characters.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function post_post_resource_short_content() : void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/post',
                    [
                        'title' => 'thssssssssi',
                        'content' => 'the',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The content field must be at least 20 characters.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function post_post_resource_authentificated() : void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->postJson(
                    '/api/post',
                    [
                        'title' => 'this is the title',
                        'content' => 'ini adalah konten yang terletak di post ini haloha',
                    ],
                    ['Accept' => 'application/json']
        );
        

        $this->assertDatabaseHas('posts', [
            'id'        => $response->json('data.id'),
            'user_id'   => $user->id,
            'title'     => 'this is the title',
            'content'   => 'ini adalah konten yang terletak di post ini haloha',
        ]);


        $response->assertStatus(200);


        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'title',
                    'content',
                    'user'  => [ 
                        'id',
                        'name',
                        'image_profile',
                    ],
                    'created_at',
            ]
        ]);

        $user->forceDelete();

    }

    /**
     * @test
     */
    public function post_update_resource_unauthentificated() : void
    {
        $post = Post::factory()->create();
      
        $response = $this->putJson(
                    '/api/post/'.$post->id,
                    [
                        'title' => 'this is the title',
                        'content' => 'ini adalah konten yang terletak di post ini haloha',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $post->forceDelete();

        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function post_update_resource_unauthorized() : void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/post/'.$post->id,
                    [
                        'title' => 'this is the title',
                        'content' => 'ini adalah konten yang terletak di post ini haloha',
                    ],
                    ['Accept' => 'application/json']
        );

        $post->forceDelete();
        $user->forceDelete();
        
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function post_update_resource_wrong_input() : void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/post/'.$post->id,
                    [
                        'content' => 'ini adalah konten yang terletak di post ini haloha',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $post->forceDelete();
        $user->forceDelete();

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function post_update_resource_short_title() : void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/post/'.$post->id,
                    [
                        'title' => 'thi',
                        'content' => 'ini adalah konten yang terletak di post ini haloha',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $post->forceDelete();
        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The title field must be at least 8 characters.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function post_update_resource_short_content() : void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/post/'.$post->id,
                    [
                        'title' => 'iiiiiiiiiiiii',
                        'content' => 'the',
                    ],
                    ['Accept' => 'application/json']
        );
        
        $post->forceDelete();
        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The content field must be at least 20 characters.',
        ]);

        $response->assertStatus(422);
    }


    /**
     * @test
     */
    public function post_update_resource_authorized() : void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/post/'.$post->id,
                    [
                        'title' => 'this is the title',
                        'content' => 'ini adalah konten yang terletak di post ini haloha',
                    ],
                    ['Accept' => 'application/json']
        );

        $this->assertDatabaseHas('posts', [
            'id'        => $response->json('data.id'),
            'title'     => 'this is the title',
            'content'   => 'ini adalah konten yang terletak di post ini haloha',
        ]);

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'title',
                    'content',
                    'user'  => [ 
                        'id',
                        'name',
                        'email',
                        'role',
                    ],
                    'comment',
                    'created_at',
                    'updated_at',
               
            ]
        ]);

        $response->assertStatus(200);

        $post->forceDelete();
        $user->forceDelete();
    }

    /**
     * @test
     */
    public function post_delete_resource_unauthentificated() : void
    {
        $post = Post::factory()->create();
      
        $response = $this->delete(
                    '/api/post/'.$post->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $post->forceDelete();
        
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function post_delete_resource_unauthorized() : void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/post/'.$post->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $post->forceDelete();
        $user->forceDelete();
        
        $response->assertStatus(403);
    }

    
    /**
     * @test
     */
     function post_delete_resource_authorized() : void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/post/'.$post->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $response->assertNoContent();
        $this->assertSoftDeleted($post);

        $post->forceDelete();
        $user->forceDelete();

        $response->assertStatus(204);
    }

    
}
    