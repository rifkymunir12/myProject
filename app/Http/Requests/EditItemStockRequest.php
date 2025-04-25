<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditItemStockRequest extends FormRequest
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
            'item_id'   => 'required|uuid|exists:items,id',
            'stock_in'  => 'sometimes|numeric|min:0|gte:stock_out',
            'stock_out' => 'sometimes|numeric|min:0',
        ];  
    }
}
