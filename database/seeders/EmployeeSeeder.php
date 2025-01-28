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
                    "name" => "مندوب 1",
                    "department_id" => 6,
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "مندوب 2",
                    "department_id" => 6,
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "سائق 1",
                    "department_id" => 7,
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "سائق 2",
                    "department_id" => 7,
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "موظف 1",
                    "department_id" => 4,
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "موظف 2",
                    "department_id" => 4,
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
            ]
        );
    }
}
