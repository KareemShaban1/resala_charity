<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Governorate;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Cairo governorate
        $cairo = Governorate::where('name', 'القاهرة')->first();
        
        // Cairo cities
        $cairoCities = [
            'حلوان',
            'المعادي',
            'مصر القديمة',
            'السيدة زينب',
            'مصر الجديدة',
            'النزهة',
            'عين شمس',
            'المطرية',
            'المرج',
            'الزيتون',
            'حدائق القبة',
            'الشرابية',
            'الساحل',
            'شبرا',
            'روض الفرج',
            'الأميرية',
            'الزاوية الحمراء',
            'بولاق',
            'الموسكي',
            'عابدين',
            'الأزبكية',
            'منشأة ناصر',
            'الوايلي',
            'باب الشعرية',
            'الخليفة',
            'مدينة نصر',
            'المقطم',
            'البساتين',
            'دار السلام',
            '15 مايو',
            'طره',
            'المعصرة',
            'التبين',
        ];

        foreach ($cairoCities as $cityName) {
            City::create([
                'name' => $cityName,
                'governorate_id' => $cairo->id
            ]);
        }

        // Get Giza governorate
        $giza = Governorate::where('name', 'الجيزة')->first();
        
        // Giza cities
        $gizaCities = [
            'الجيزة',
            'السادس من أكتوبر',
            'الشيخ زايد',
            'الحوامدية',
            'البدرشين',
            'الصف',
            'أطفيح',
            'العياط',
            'الواحات البحرية',
            'منشأة القناطر',
            'أوسيم',
            'كرداسة',
            'أبو النمرس',
        ];

        foreach ($gizaCities as $cityName) {
            City::create([
                'name' => $cityName,
                'governorate_id' => $giza->id
            ]);
        }

        // Get Alexandria governorate
        $alexandria = Governorate::where('name', 'الإسكندرية')->first();
        
        // Alexandria cities
        $alexandriaCities = [
            'المنتزه',
            'شرق',
            'وسط',
            'الجمرك',
            'غرب',
            'العجمي',
            'العامرية',
            'برج العرب',
        ];

        foreach ($alexandriaCities as $cityName) {
            City::create([
                'name' => $cityName,
                'governorate_id' => $alexandria->id
            ]);
        }
    }
}
