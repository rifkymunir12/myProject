<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = [
            'id'             => $this->user_id,
            'name'           => $this->user?->name,
            'image_profile'  => url('/').'/storage/'.$this->user?->image_profile,
            'email'          => $this->user?->email,
            'role'           => $this->user?->getRoleNames()->first(),
        ];

        $postUser = [
            'user'           => $this->post?->user?->name,
            'email'          => $this->post?->user?->email,   
            'image_profile'  => url('/').'/storage/'.$this->post?->user?->image_profile,
            'role'           => $this->post?->user?->getRoleNames()->first(),
        ];

        $post = [
            'id'    => $this->post_id,
            'title'    => $this->post?->title,
            'user'  => $postUser,
        ];  
       
        $created_at = $this->created_at->settings(['formatFunction' => 'translatedFormat'])->locale('id')
                        ->setTimezone(auth()->user()->timezone)->format('j F Y H:i:s').' '.(auth()->user()->timezone);
        $updated_at = $this->updated_at->settings(['formatFunction' => 'translatedFormat'])->locale('id')
                        ->setTimezone(auth()->user()->timezone)->format('j F Y H:i:s').' '.(auth()->user()->timezone);


        return [
            'id'            =>   $this->id,
            'content'       =>   $this->content,
    		'user'          =>   $user,
            'post'          =>   $post,
            
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ];
    }
}
