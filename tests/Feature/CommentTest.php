<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Comment;
use Laravel\Passport\Passport;
use Tests\TestCase;
class CommentTest extends TestCase
{
    /**
     * @test
     */
    public function comment_get_collection_unauthenticated() : void
    {
        $response = $this->get(
                    '/api/comment',
                    ['Accept' => 'application/json']
        );
        
        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function comment_get_collection_authenticated() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/comment',
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'content',
                    'user' => [ 
                        'id',
                        'name',
                        'image_profile',
                        'email',
                        'role',
                    ],
                    'post' =>[
                        "id",
                        "title",
                        "user" => [
                            "user",
                            "email",
                            "image_profile",
                            "role",
                        ],
                   
                ],
                    'created_at',
                    'updated_at',
            ],
        ]
        ]);
    }

    /**
     * @test
     */
    public function comment_get_resource_unauthenticated() : void
    {
        $comment = Comment::factory()->create();

        $response = $this->get(
                    '/api/comment/'.$comment->id,
                    ['Accept' => 'application/json']
        );
        
        $comment->forceDelete();

        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function comment_get_resource_authenticated() : void
    {
        $comment = Comment::factory()->create();
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->get(
                    '/api/comment/'.$comment->id,
                    ['Accept' => 'application/json']
        );
        
        $comment->forceDelete();
        $user->forceDelete();
    
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'content',
                    'user' => [ 
                        'id',
                        'name',
                        'image_profile',
                        'email',
                        'role',
                    ],
                    'post' =>[
                        "id",
                        "title",
                        "user" => [
                            "user",
                            "email",
                            "image_profile",
                            "role",
                        ],
                   
                ],
                    'created_at',
                    'updated_at',
            ],
        ]);
    }

    /**
     * @test
     */
    public function comment_post_resource_unauthenticated() : void
    {
        $response = $this->postJson(
                    '/api/comment',
                    [
                        //'post_id' => '9c96968e-db99-4380-9252-1721323d9794', yg asli
                        //'post_id' => '9cb8d5dd-b5ba-45f3-9f20-3bd9b0f92443', test
                        'post_id' => '9d0bbe52-302f-47b4-be3c-5636ceb971c6',
                        'content' => 'this is the comment content.',
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function comment_post_resource_wrong_input() : void
    {
        //tambah json fragment untukini
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/comment',
                    [
                        //'post_id' => '9c96968e-db99-4380-9252-1721323d9794', yg asli
                        //'post_id' => '9cb8d5dd-b5ba-45f3-9f20-3bd9b0f92443', test
                        'post_id' => '9d0bbe52-302f-47b4-be3c-5636ceb971c6',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function comment_post_resource_not_enough_content_characters() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/comment',
                    [
                        //'post_id' => '9c96968e-db99-4380-9252-1721323d9794', yg asli
                        //'post_id' => '9cb8d5dd-b5ba-45f3-9f20-3bd9b0f92443', test
                        'post_id' => '9d0bbe52-302f-47b4-be3c-5636ceb971c6',
                        'content' => 'tha',
                    ],
                    ['Accept' => 'application/json']
        );

        $response->assertJsonFragment([
            'message' => 'The content field must be at least 6 characters.',
        ]);

        $user->forceDelete();

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function comment_post_resource_no_post_selected() : void
    {
        //tambah json fragment untukini
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/comment', 
                    [
                        //'post_id' => '9c96968e-db99-4380-9252-1721323d9794', yg asli
                        'content' => 'thassssssss',
                    ],
                    ['Accept' => 'application/json']
        );

        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The post id field is required.',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function comment_post_resource_authenticated() : void
    {
        $user = User::factory()->create();

        Passport::actingAs(($user));

        $response = $this->postJson(
                    '/api/comment',
                    [
                        //'post_id' => '9c96968e-db99-4380-9252-1721323d9794', yg asli
                        //'post_id' => '9cb8d5dd-b5ba-45f3-9f20-3bd9b0f92443', test
                        'post_id' => '9d0bbe52-302f-47b4-be3c-5636ceb971c6',
                        'content' => 'this is the comment content.',
                    ],
                    ['Accept' => 'application/json']
        );

        

        $this->assertDatabaseHas('comments', [
            'id'        => $response->json('data.id'),
            'user_id'   => $user->id,
            //'post_id' => '9c96968e-db99-4380-9252-1721323d9794', yg asli
            //'post_id' => '9cb8d5dd-b5ba-45f3-9f20-3bd9b0f92443', test
            'post_id' => '9d0bbe52-302f-47b4-be3c-5636ceb971c6',
            'content'   => 'this is the comment content.'
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'content',
                    'user' => [ 
                        'id',
                        'name',
                        'image_profile',
                        'email',
                        'role',
                    ],
                    'post' =>[
                        "id",
                        "title",
                        "user" => [
                            "user",
                            "email",
                            "image_profile",
                            "role",
                        ],
                   
                ],
                    'created_at',
                    'updated_at',
            ],
        ]);

        $user->forceDelete();
    }

    

    /**
     * @test
     */
    public function comment_update_resource_unauthentificated() : void
    {
        $comment = Comment::factory()->create();
      
        $response = $this->putJson(
                    '/api/comment/'.$comment->id,
                    [
                        'content' => 'this is the comment content.',
                    ],
                    ['Accept' => 'application/json']
        );

        $comment->forceDelete();

        $response->assertStatus(401);
    }

     /**
     * @test
     */
    public function comment_update_resource_unauthorized() : void
    {
        $comment = Comment::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/comment/'.$comment->id,
                    [
                       'content' => 'this is the comment content.',
                    ],
                    ['Accept' => 'application/json']
        );

        $comment->forceDelete();
        $user->forceDelete();
        
        

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function comment_update_resource_not_enough_content_characters() : void
    {
        $comment = Comment::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/comment/'.$comment->id,
                    [
                        'content' => 'the',
                    ],
                    ['Accept' => 'application/json']
        );

        $comment->forceDelete();
        $user->forceDelete();

        $response->assertJsonFragment([
            'message' => 'The content field must be at least 6 characters.',
        ]);
        
        $response->assertStatus(422);
    }

    

    /**
     * @test
     */
    public function comment_update_resource_authorized() : void
    {
        $comment = Comment::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->putJson(
                    '/api/comment/'.$comment->id,
                    [
                        'content' => 'this is the comment content.',
                    ],
                    ['Accept' => 'application/json']
        );

        $this->assertDatabaseHas('comments', [
            'id'        => $comment->id,
            'content'   => 'this is the comment content.'
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                    'id',
                    'content',
                    'user' => [ 
                        'id',
                        'name',
                        'image_profile',
                        'email',
                        'role',
                    ],
                    'post' =>[
                        "id",
                        "title",
                        "user" => [
                            "user",
                            "email",
                            "image_profile",
                            "role",
                        ],
                   
                ],
                    'created_at',
                    'updated_at',
            ],
        ]);

        $comment->forceDelete();
        $user->forceDelete();
    }

    /**
     * @test
     */
    public function comment_delete_resource_unauthentificated() : void
    {
        $comment = Comment::factory()->create();
      
        $response = $this->delete(
                    '/api/comment/'.$comment->id,
                    [],
                    ['Accept' => 'application/json']
        );
        
        $comment->forceDelete();

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function comment_delete_resource_unauthorized() : void
    {
        $comment = Comment::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'User')->first());
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/comment/'.$comment->id,
                    [],
                    ['Accept' => 'application/json']
        );
        
        $response->assertStatus(403);

        $comment->forceDelete();
        $user->forceDelete();
    }

    
    /**
     * @test
     */
     function comment_delete_resource_authorized() : void
    {
        $comment = Comment::factory()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::where('name' ,'Admin')->first());
        Passport::actingAs($user);

        $response = $this->delete(
                    '/api/comment/'.$comment->id,
                    [],
                    ['Accept' => 'application/json']
        );

        $response->assertStatus(204);
        $response->assertNoContent();
        $this->assertSoftDeleted($comment);

        $comment->forceDelete();
        $user->forceDelete();
    }

    
}
