<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::create([
            'username' => 'admin',
            'name' => 'admin',
            'password' => Hash::make('Intube!234'),
            'active' => 1,

        ]);

        $role = Role::create([
            'name' => 'Admin',
        ]);


         $permission = Permission::create([
            'name' => 'permission.*',
        ]);
        $permissions = Permission::all();
        $role->syncPermissions($permission);
        $role = Role::where('name', 'Admin')->first();
        $user->roles()->attach($role);



    }
}
