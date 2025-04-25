<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Contracts\Response;
use App\Http\Requests\RoleRequest;
use Illuminate\Support\Facades\DB;


class AddAdminAndModController extends Controller
{
    public function add_admin_mod(RoleRequest $request){
        if (!auth()->user()->hasRole('Admin')){
            return Response::abortForbidden();
        }
        $data = $request->validated();
        
        if(DB::table('model_has_roles')->where('model_id', $data['user_id'] )->first() !== null){
             DB::table('model_has_roles')->where('model_id', $data['user_id'] )->delete();   
        }
            
        User::find($data['user_id'])->assignRole(Role::where('name' , $data['role'])->first());
        
        return Response::okCreated('User telah diberikan role!');
        
    }   

}

