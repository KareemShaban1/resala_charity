<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\City;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        // Get 6th of October city
        $benha = City::where('name', 'بنها')->first();

        // 6th of October areas
        $benhaAreas = [
            'اتريب',
            'الاهرام',
            'البرنس',
            'الحرس الوطني',
            'الرملة',
            'الشدية',
            'الشموت',
            'الفلل',
            'منشية النور',
            'سندنهور',
            'وسط البلد',
            'عزبة المربع',
            'كفر الجزار',
            'المنشية',
            'مرصفا',
            'عزبة الزراعة',
            'عزبه المتينى',
            'بتمدة',
            'شبلنجة',
            'شبين',
            'فرسيس',
            'كفر الشموت',
            'كفر العرب',
            'كفر سعد',
            'كفر سندهور',
            'كفر طلحه',
            'كفر فرسيس',
            'كلية العلوم',
            'مجول',
            'مساكن الموالح',
            'منية السباع',
            'ميت السباع',
            'ميت العطار',
            'ميت عاصم',
            'نقباس'
        ];

        foreach ($benhaAreas as $areaName) {
            Area::create([
                'name' => $areaName,
                'city_id' => $benha->id
            ]);
        }


         // Get 6th of October city
         $kafrShukr = City::where('name', 'كفر شكر')->first();

         // 6th of October areas
         $kafrShukrAreas = [
             'كفر شكر',
         ];
 
         foreach ($kafrShukrAreas as $areaName) {
             Area::create([
                 'name' => $areaName,
                 'city_id' => $kafrShukr->id
             ]);
         }
    }
}
