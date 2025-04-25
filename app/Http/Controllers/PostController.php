<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Contracts\Response;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Http\Resources\NewPostResource;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request){
        $posts = Post::paginate($request->show);

        return Response::json(new PostCollection($posts));
    }

    public function show(Post $post){
    
        return Response::json(new PostResource($post));
    }

    public function store(PostRequest $request){
        $data = $request->validated();

        $data['user_id'] = $request->user()->id;
        
        $newPost = Post::create($data);

        return Response::json(new NewPostResource($newPost));
    }
    
    public function update(PostRequest $request, Post $post){
        if (auth()->user()->hasRole('User') && auth()->user()->id !== $post->user_id){
            return Response::abortForbidden();
        }
        $data = $request->validated();
        $updatedPost = $post;

        $updatedPost->update($data );
        return Response::json(new PostResource($updatedPost));
        
   }

    public function destroy(Post $post){
        if (auth()->user()->hasRole('User') && auth()->user()->id !== $post->user_id){
            return Response::abortForbidden();
        }
        $post->delete();
        return Response::noContent();
   }
}