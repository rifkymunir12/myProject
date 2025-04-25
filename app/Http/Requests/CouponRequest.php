<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
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
            'name'  => 'required|string|unique:coupons|min:4|max:128',
            'code'  => 'required|string|unique:coupons|alpha|min:8|max:16',
            'discount'   => 'required|numeric|min:1000',
            'description'=> 'sometimes|nullable|string',
        ];
    }
}
