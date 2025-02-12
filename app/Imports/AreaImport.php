<?php

namespace App\Imports;

use App\Models\Area;
use App\Models\AreaGroup;
use App\Models\City;
use App\Models\Governorate;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AreaImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
{

    private $skippedRows = []; // Collect skipped rows

    /**
     * Process the collection.
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $data = $row->toArray();

            try {

                // Skip if the name is missing
                if (empty($data['name'])) {
                    throw new \Exception('Name is required.');
                }

                // Find relationships for governorate, city, and area
                $governorate = Governorate::where('name', $data['governorate_name'])->first();
                $city = City::where('name', $data['city_name'])
                    ->where('governorate_id', $governorate->id ?? null)
                    ->first();
                
               $areaGroup = AreaGroup::where('name', $data['area_group_name'])->first();




                try {
                    $area = Area::updateOrCreate(
                        ['name' => $data['name']],
                        [
                            'governorate_id' => $governorate->id ?? null,
                            'city_id' => $city->id ?? null,
                        ]
                    );

                    if (isset($areaGroup)) {
                        // $areaGroup = AreaGroup::findOrFail($areaGroup->id);
                        $areaGroup->areas()->attach([$area->id]);
                    }else {
                        
                    }
                } catch (\Exception $e) {
                    \Log::error("Error creating donor: {$e->getMessage()}", ['data' => $data]);
                    throw $e; // Optional: Let it bubble up
                }


            } catch (\Exception $e) {
                // Skip rows with issues
                $this->skippedRows[] = [
                    'row' => $index + 2, // Offset for heading row
                    'data' => $data,
                    'error' => $e->getMessage(),
                ];
            }
        }
    }


    /**
     * Define validation rules for rows.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'governorate_name' => 'nullable|string|exists:governorates,name',
            'city_name' => 'nullable|string|exists:cities,name',
            'area_group_name' => 'nullable|string|exists:area_groups,name',
        ];
    }

    /**
     * Customize error messages (optional).
     */
    public function customValidationMessages(): array
    {
        return [
            'governorate_name.exists' => 'The governorate :input does not exist.',
            'city_name.exists' => 'The city :input does not exist.',
        ];
    }

    /**
     * Retrieve skipped rows for logging or reporting.
     */
    public function getSkippedRows()
    {
        return $this->skippedRows;
    }
}
