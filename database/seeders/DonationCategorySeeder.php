<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DonationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        \DB::table("donation_categories")->delete();
        \DB::table("donation_categories")->insert(
            [
                [
                    "name" => "زكاة",
                    "description" => "زكاة",
                    "active" => true,
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "صدقة",
                    "description" => "صدقة",
                    "active" => true,
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
                [
                    "name" => "بيت المال",
                    "description" => "بيت المال",
                    "active" => true,
                    "created_at" => now(),
                    "updated_at" => now(),
                ],
            ]
        );
    }
}
