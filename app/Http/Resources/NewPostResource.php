<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class NewPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = [
            'id'    => $this->user_id,
            'name'    => $this->user->name,
            'image_profile' => url('/').'/storage/'.$this->user?->image_profile,
        ];

        $created_at = $this->created_at->settings(['formatFunction' => 'translatedFormat'])->locale('id')
                        ->setTimezone(auth()->user()->timezone)->format('j F Y H:i:s').' '.(auth()->user()->timezone);

        return [
            'id'               =>   $this->id,
            'title'            =>   $this->title,
    		'content'          =>   $this->content,
            'user'             =>   $user,

            'created_at' => $created_at,
        ];
    }
}
