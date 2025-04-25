<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $unit = [
            'id'           => $this->unit_id,
            'name'         => $this->unit?->name,
    		'multiple'     => $this->unit?->multiple,
            'note'         => $this->unit?->note,
        ];

        return [
            'id'             =>   $this->id,
            'name'           =>   $this->name,
            'unit'           =>   $unit,
    		'price'          =>   $this->price,
            'item_image'     =>   url('/').'/storage/'.$this->item_image,
            'amount'         =>   $this->amount,
            'stock_in'       =>   $this->stock_in,
            'stock_out'      =>   $this->stock_out,
            'description'    =>   $this->description,
        ];
    }
}