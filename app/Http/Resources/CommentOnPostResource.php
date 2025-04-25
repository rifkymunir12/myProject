<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentOnPostResource extends JsonResource
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

        return [
            'id'            =>   $this->id,
            'content'       =>   $this->content,
    		'user'          =>   $user,
        ];
    }
}
