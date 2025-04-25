<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Contracts\Response;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
class RegisterController extends Controller
{
    public function register(UserRequest $request){
        $data = $request->validated();

        // if(!($data['timezone'] == 'WIB' || $data['timezone'] == 'WIT' || $data['timezone'] == 'WITA')){
        //     $data['timezone'] = 'WIB';
        // }

        $data['image_profile'] = $request->file('profile')?->hashName();

        $newUser = User::create($data);

        User::find($newUser->id)->assignRole(Role::where('name' , 'User')->first());

        $request->file('profile')?->store('public');
        
        return Response::json(new UserResource($newUser));
    }
}
