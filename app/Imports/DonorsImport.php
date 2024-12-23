<?php
namespace App\Imports;

use App\Models\Donor;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Area;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class DonorsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
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

                // Skip if no phone is provided
                if (empty($data['phones'])) {
                    throw new \Exception('No phone records provided.');
                }

                // Validate phones
                $phones = explode(',', $data['phones']);
                $validPhones = [];
                foreach ($phones as $phone) {
                    [$number, $type] = explode(':', $phone) + [null, null];

                    // Skip if phone number is "0" regardless of type
                    // if (trim($number) === '0') {
                    //     throw new \Exception("Invalid phone number: {$number}");
                    // }
                    // Skip if phone number is not 11 digits for mobiles
                    // if ($type === 'mobile' && strlen(trim($number)) !== 11) {
                    //     throw new \Exception("Invalid mobile phone number: {$number}");
                    // }

                    $validPhones[] = ['number' => trim($number), 'type' => trim($type ?? 'unknown')];
                }

                // Find relationships for governorate, city, and area
                $governorate = Governorate::where('name', $data['governorate_name'])->first();
                $city = City::where('name', $data['city_name'])
                    ->where('governorate_id', $governorate->id ?? null)
                    ->first();
                $area = Area::where('name', $data['area_name'])
                    ->where('city_id', $city->id ?? null)
                    ->first();

                // Handle donor record (update or create)
                try {
                    $donor = Donor::updateOrCreate(
                        ['name' => $data['name']],
                        [
                            'address' => $data['address'] ?? null,
                            'street' => $data['street'] ?? null,
                            'governorate_id' => $governorate->id ?? null,
                            'city_id' => $city->id ?? null,
                            'area_id' => $area->id ?? null,
                            'active' => $data['active'] ?? true,
                            'donor_type' => $data['donor_type'],
                            'monthly_donation_day' => $data['monthly_donation_day'],
                        ]
                    );
                } catch (\Exception $e) {
                    \Log::error("Error creating donor: {$e->getMessage()}", ['data' => $data]);
                    throw $e; // Optional: Let it bubble up
                }
                

                // Process valid phones and associate them with the donor
                foreach ($validPhones as $index => $validPhone) {
                    $donor->phones()->updateOrCreate(
                        ['phone_number' => $validPhone['number']],
                        [
                            'phone_type' => $validPhone['type'],
                            'is_primary' => $index === 0,
                        ]
                    );
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
            'donor_type' => 'required|string|in:normal,monthly',
            'monthly_donation_day' => 'nullable',
            'phones' => [
                'nullable',
                'string',
                // 'regex:/^(\d{11}:(mobile|home|work)(,\d{10,15}:(mobile|home|work))*)?$/',
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
            'phones.regex' => 'Each phone must follow the format: 11 digits (for mobile) or 10-15 digits with type.',
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
