<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        \DB::table("departments")->delete();
        \DB::table("departments")->insert(  
            [
                [
                    "name" => "Accounts",
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "Donations",
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "Management",
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "Employees",
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "Users",
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "Representatives",
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "Drivers",
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
            ]
        );
    }
}
