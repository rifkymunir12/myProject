<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'Mod Permission']);
        Permission::create(['name' => 'User Permission']); 

        $admin = Role::create(['name' => 'Admin']);

        $mod = Role::create(['name' => 'Mod']);
        $mod->givePermissionTo('Mod Permission');   

        $user = Role::create(['name' => 'User']);
        $user->givePermissionTo('User Permission');

        $mainAdmin = User::factory()->create([
            'name'          => 'Main Admin',
            'email'         => 'mainadmin10@gmail.com',
            'password'      => Hash::make("12345678"),
            'nomor_telepon' => '0802921000',
            'timezone'      => 'WIB',
        ]); 
        //ini dari factori yang sudah kita buat, terus bisa diedit dengan atribut kita sendiri
        
        $mainAdmin->assignRole($admin);

        // $justUser = User::factory()->create([
        //     'name'          => 'biasa aj',
        //     'email'         => 'biasakah@gmail.com',
        //     'password'      => Hash::make("12345678"),
        //     'nomor_telepon' => '00000000',
        // ]);

        // $justUser->assignRole(Role::where("name","User")->first());

        $users = User::get();

        for($i=0; $i<User::count(); $i++){
            if(DB::table('model_has_roles')->where('model_id',$users[$i]->id)->first() == null){
                $user->assignRole(Role::where('name' , 'User')->first());
            }
        }
    }

}