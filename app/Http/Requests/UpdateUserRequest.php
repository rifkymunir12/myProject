<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name'  => 'required|string|alpha_dash|min:6|max:128',
            'email' => 'required|unique:users|email|min:12|max:128',  //cemana biar unique:users nya ga berlaku ke data sendiri
            'password' => 'required|string|alpha_dash|min:8|max:32',
            'tempat_lahir'=>'required|string|min:3|max:32',
            'tanggal_lahir'=>'required|date_format:Y-m-d',
            'gender'=>'required|string|in:Pria,Wanita',
            'lokasi'=> 'required|string|max:64',
            'nomor_telepon'=>'sometimes|nullable|string|min:8|max:32',
            'timezone'      => 'required|string|in:WIB,WIT,WITA',
            'profile'        => 'sometimes|nullable|image',
        ];
    }
}
