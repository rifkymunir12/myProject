<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class InvoiceItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'           =>   $this->name,
    		'price'          =>   $this->price,
            'quantity'       =>   $this->pivot->quantity,
            'item_image'     =>   url('/').'/storage/'.$this->item_image,
        ];
    }
}