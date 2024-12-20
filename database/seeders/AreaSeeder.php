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
    }
}
