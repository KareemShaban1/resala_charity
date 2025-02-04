<?php

namespace Database\Seeders;

use App\Models\CallType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CallTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        CallType::firstOrCreate(['name' => 'مكالمة عشوائية']);
    }
}
