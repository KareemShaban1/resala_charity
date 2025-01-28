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
        // Get Maadi city
        $maadi = City::where('name', 'المعادي')->first();

        // Maadi areas
        $maadiAreas = [
            'المعادي الجديدة',
            'كورنيش المعادي',
            'حدائق المعادي',
            'المعادي القديمة',
            'دجلة المعادي',
            'ميدان الحرية',
            'المعادي السرايات',
            'زهراء المعادي',
        ];

        foreach ($maadiAreas as $areaName) {
            Area::create([
                'name' => $areaName,
                'city_id' => $maadi->id
            ]);
        }

        // Get Nasr City
        $nasrCity = City::where('name', 'مدينة نصر')->first();

        // Nasr City areas
        $nasrCityAreas = [
            'الحي الأول',
            'الحي الثاني',
            'الحي الثالث',
            'الحي الرابع',
            'الحي الخامس',
            'الحي السادس',
            'الحي السابع',
            'الحي الثامن',
            'الحي التاسع',
            'الحي العاشر',
            'المنطقة الأولى',
            'المنطقة الثانية',
            'المنطقة الثالثة',
            'المنطقة الرابعة',
            'المنطقة الخامسة',
            'المنطقة السادسة',
            'المنطقة السابعة',
            'المنطقة الثامنة',
            'المنطقة التاسعة',
            'المنطقة العاشرة',
            'زهراء مدينة نصر',
            'الحي الحادي عشر',
        ];

        foreach ($nasrCityAreas as $areaName) {
            Area::create([
                'name' => $areaName,
                'city_id' => $nasrCity->id
            ]);
        }

        // Get 6th of October city
        $october = City::where('name', 'السادس من أكتوبر')->first();

        // 6th of October areas
        $octoberAreas = [
            'الحي الأول',
            'الحي الثاني',
            'الحي الثالث',
            'الحي الرابع',
            'الحي الخامس',
            'الحي السادس',
            'الحي السابع',
            'الحي الثامن',
            'الحي التاسع',
            'الحي العاشر',
            'الحي الحادي عشر',
            'الحي الثاني عشر',
            'المحور المركزي',
            'الأحياء السكنية',
            'المنطقة الصناعية',
            'مدينة الإنتاج الإعلامي',
            'الحي المتميز',
            'الواحة',
        ];

        foreach ($octoberAreas as $areaName) {
            Area::create([
                'name' => $areaName,
                'city_id' => $october->id
            ]);
        }


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
