<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Contracts\Response;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UpdateUserController extends Controller
{
    public function user_update(UpdateUserRequest $request){
            $updatedUser = auth()->user();

            $data = $request->validated();
            
            $data['image_profile'] = $request->file('profile')?->hashName() != NULL ?  $request->file('profile')->hashName() : null;
    
            $updatedUser->update($data);

            // $updatedUser->update([
            //     'name'          => $data['name'] ?? $updatedUser->name,
            //     'email'         => $data['email'] ?? $updatedUser->email,
            //     'image_profile' => $data['image_profile'],
            //     'password'  => Hash::make($data['password']) ?? $updatedUser->password,
            //     'tempat_lahir' => $data['tempat_lahir'],
            //     'tanggal_lahir' => $data['tanggal_lahir'],  
            //     'gender'     => $data['gender'] ?? $updatedUser->gender,
            //     'lokasi'     => $data['lokasi'],
            //     'nomor_telepon'    => $data['nomor_telepon'],
            //     'timezone'     => $data['timezone'] ?? $updatedUser->timezone,
            // ]);

            $request->file('profile')?->store('public');

            return Response::json(new UserResource($updatedUser));
    
    }
}