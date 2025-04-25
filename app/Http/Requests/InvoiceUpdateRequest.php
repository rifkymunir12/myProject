<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array  
    {
        return [
            'coupon_code' => 'sometimes|nullable|string|exists:coupons,code',
            'items'       => 'required',
            'shipment_id' => 'required|uuid|exists:shipments,id',
            'destination' => 'required|string',
            'note'        => 'sometimes|nullable|string',
            'payment'     => 'required|numeric|min:0',
            'status'      => 'required|string|max:9|in:Unpaid,Waiting,Cancelled,Paid',
        ];
    }
}
