<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Contracts\Response;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index(Request $request){
        $users = User::paginate($request->show);
        return Response::json(new UserCollection($users));
    }

    public function show(User $user){
        
        return Response::json(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, User $user){
        if (!auth()->user()->hasRole('Admin')){
            return Response::abortForbidden();
        }

        $data = $request->validated();
        $updatedUser = $user;
            
        $data['image_profile'] = $request->file('profile')?->hashName() != NULL ?  $request->file('profile')->hashName() : null;
        //cek emailnya  gausah
        $updatedUser->update($data);

        // $updatedUser->update([
        //     'name'      => $data['name'] ?? $updatedUser->name,
        //     'email'     => $data['email'] ?? $updatedUser->email,
        //     'image_profile' => $link,
        //     'password'  => $data['password'] ?? $updatedUser->password,
        //     'tempat_lahir' => $data['tempat_lahir'],
        //     'tanggal_lahir' => $data['tanggal_lahir'],  
        //     'gender'     => $data['gender'] ?? $updatedUser->gender,
        //     'lokasi'     => $data['lokasi'] ,
        //     'nomor_telepon'  => $data['nomor_telepon'],
        //     'timezone'     => $data['timezone'] ?? $updatedUser->timezone,
        // ]);
    
        $request->file('profile')?->store('public');
        return Response::json(new UserResource($updatedUser));
       
    }

    public function destroy(User $user){
        if (auth()->user()->hasRole('Admin')){
            $user->delete();
            return Response::noContent();
        }
        
        return Response::abortForbidden();
   }

   
}


