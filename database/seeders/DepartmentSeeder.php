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
                    "name" => "الحسابات",
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "التبرعات",
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
            ]
        );
    }
}
