<?php

namespace App\Imports;

use App\Models\Donor;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Area;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\ValidationException;

class DonorsImport implements ToCollection, WithHeadingRow, WithValidation
{
    // use SkipsFailures;

    private $skippedRows = []; // Collect skipped rows

    /**
     * Process the collection.
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $data = $row->toArray();

            try {
                // Find relationships for governorate, city, and area
                $governorate = Governorate::where('name', $data['governorate_name'])->first();
                $city = City::where('name', $data['city_name'])
                    ->where('governorate_id', $governorate->id ?? null)
                    ->first();
                $area = Area::where('name', $data['area_name'])
                    ->where('city_id', $city->id ?? null)
                    ->first();

                // Handle donor record (update or create)
                $donor = Donor::updateOrCreate(
                    ['name' => $data['name']],
                    [
                        'address' => $data['address'] ?? null,
                        'street' => $data['street'] ?? null,
                        'governorate_id' => $governorate->id ?? null,
                        'city_id' => $city->id ?? null,
                        'area_id' => $area->id ?? null,
                        'active' => $data['active'] ?? true,
                    ]
                );

                // Process phones and associate them with the donor
                if (!empty($data['phones'])) {
                    $phones = explode(',', $data['phones']);
                    foreach ($phones as $index => $phone) {
                        [$number, $type] = explode(':', $phone) + [null, null];
                        if (!empty($number)) {
                            $donor->phones()->updateOrCreate(
                                ['phone_number' => trim($number)],
                                [
                                    'phone_type' => trim($type ?? 'unknown'),
                                    'is_primary' => $index === 0,
                                ]
                            );
                        }
                    }
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
            'address' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'governorate_name' => 'required|string|exists:governorates,name',
            'city_name' => 'required|string|exists:cities,name',
            'area_name' => 'required|string|exists:areas,name',
            'phones' => [
                'nullable',
                'string',
                'regex:/^(\d{10,15}:(mobile|home|work)(,\d{10,15}:(mobile|home|work))*)?$/',
            ],
            'active' => 'nullable|boolean',
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
            'area_name.exists' => 'The area :input does not exist.',
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
