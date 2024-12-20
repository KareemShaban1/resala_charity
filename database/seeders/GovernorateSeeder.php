<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = [
            ['name' => 'القاهرة'],
            ['name' => 'الجيزة'],
            ['name' => 'القليوبية'],
            ['name' => 'الإسكندرية'],
            ['name' => 'البحيرة'],
            ['name' => 'مطروح'],
            ['name' => 'دمياط'],
            ['name' => 'الدقهلية'],
            ['name' => 'كفر الشيخ'],
            ['name' => 'الغربية'],
            ['name' => 'المنوفية'],
            ['name' => 'الشرقية'],
            ['name' => 'بورسعيد'],
            ['name' => 'الإسماعيلية'],
            ['name' => 'السويس'],
            ['name' => 'شمال سيناء'],
            ['name' => 'جنوب سيناء'],
            ['name' => 'بني سويف'],
            ['name' => 'الفيوم'],
            ['name' => 'المنيا'],
            ['name' => 'أسيوط'],
            ['name' => 'سوهاج'],
            ['name' => 'قنا'],
            ['name' => 'الأقصر'],
            ['name' => 'أسوان'],
            ['name' => 'البحر الأحمر'],
            ['name' => 'الوادي الجديد'],
        ];

        foreach ($governorates as $governorate) {
            Governorate::create($governorate);
        }
    }
}
