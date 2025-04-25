<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemUpdateRequest extends FormRequest
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
            'name'        => 'required|string|min:2|max:128',
            'unit_id'     => 'required|uuid|exists:item_units,id',
            'price'       => 'required|numeric|min:1',
            'description' => 'sometimes|nullable|string|min:8',
            'image'       => 'sometimes|nullable|image',
        ];
    }
}
