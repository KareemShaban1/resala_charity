<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // $user = User::create([
        //     'name' => 'Admin',
        //     'email' => 'admin@admin.com',
        //     'password' => Hash::make('password'),     
       
        // ]);
        $superAdminUser = User::where('email', 'admin@admin.com')->first();
        if (!$superAdminUser) {
            $superAdminUser = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),     
           
            ]);
        }
        Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminUser->assignRole('Super Admin');

    }
}
