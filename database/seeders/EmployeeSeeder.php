<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        \DB::table("employees")->delete();
        \DB::table("employees")->insert(
            [
                [
                    "name" => " محمد محمود",
                    "department_id" => 1,
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "أحمد مصطفى",
                    "department_id" => 2,
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
            ]
        );
    }
}
