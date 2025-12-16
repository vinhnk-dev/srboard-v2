<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\BoardType;
use App\Models\BoardCategory;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $password = Hash::make('Intube!234');
        $user = User::create([
            'username' => 'admin',
            'name' => 'admin',
            'email' => 'khanhvinhb2@gmail.com',
            'password' => Hash::make('Intube!234'),
            'active' => 1,

        ]);

        $role = Role::create([
            'name' => 'Admin',
        ]);

        $role = Role::create([
            'name' => 'User'
        ]);


         $permission = Permission::create([
            'name' => 'permission.*',
        ]);
        $permissions = Permission::all();
        $role->syncPermissions($permission);
        $role = Role::where('name', 'Admin')->first();
        $user->roles()->attach($role);


        $notice = BoardType::create([
            'type_name' => 'Notice'
        ]);
        $faq = BoardType::create([
            'type_name' => 'FAQ'
        ]);
        $url = BoardType::create([
            'type_name' => 'URL'
        ]);


        BoardCategory::create([
            'id' => 0,
            'category' => 'itnononnone'
        ]);

    }
}
