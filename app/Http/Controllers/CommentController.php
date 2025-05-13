<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Contracts\Response;
use App\Http\Requests\CommentCreateRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request){   
        $comments = Comment::paginate($request->show);
        return Response::json(new CommentCollection($comments));
    }

    public function show(Comment $comment){
        return Response::json(new CommentResource($comment));
    }

    public function store(CommentCreateRequest $request){
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        
        // $newComment = Comment::create([
        //     'user_id'   => $request->user()->id,
        //     'post_id'   => $data['post_id'],
            //     'content'   => $data['content'],
        // ]);

        $newComment = Comment::create($data);

        return Response::json(new CommentResource($newComment));
    }

    public function update(CommentUpdateRequest $request, Comment $comment)
    {
        if (auth()->user()->hasRole('User') && auth()->user()->id !== $comment->user_id){
            return Response::abortForbidden();
        }

        $data = $request->validated();
        $updatedComment = $comment;

        // $updatedComment->update([
        //     'content'   => $data['content'] ?? $comment->content,
        // ]);

       $updatedComment->update($data);
    
        return Response::json(new CommentResource($updatedComment));   
    }

    public function destroy(Comment $comment){
        if (auth()->user()->hasRole('User') && auth()->user()->id !== $comment->user_id){
            return Response::abortForbidden();
        }
            
        $comment->delete();
        return Response::noContent();
   }
   
}