<?php

namespace App\Http\Resources;

use App\Http\Resources\InvoiceItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $customer = [
            'id'        => $this->customer_id,
            'customer'  => $this->customer?->name,
        ];
        
        $shipment = [
            'id'        => $this->shipment_id,
            'name'      => $this->shipment?->name,
    		'price'     => $this->shipment?->price,
            'description' => $this->shipment?->description,
        ];

        $coupon = [
            'name'        => $this->coupon?->name,
            'description' => $this->coupon?->description,
    		'discount'    => $this->coupon?->discount ?? 0,
        ];

        $created_at = $this->created_at->settings(['formatFunction' => 'translatedFormat'])->locale('id')
                        ->setTimezone(auth()->user()->timezone)->format('j F Y H:i:s').' '.(auth()->user()->timezone);

        return [
            'id'             => $this->id,
            'invoice_code'   => $this->invoice_code,
            'customer'       => $customer,
            'destination'    => $this->destination,
            'status'         => $this->status,
            'items'          => InvoiceItemResource::collection($this->items),
            'total_price'    => $this->total_price,
            'coupon'         => $coupon,
            'shipment'       => $shipment,
            'final_price'    => $this->final_price,
            'payment'        => $this->payment,
            'barcode'        => url('/').'/storage/qr-codes/'.$this->barcode,
            'note'           => $this->note,

            'created_at' => $created_at,
        ];
    }
}