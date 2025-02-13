<?php

namespace App\Imports;

use App\Models\Donor;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Area;
use App\Models\Department;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class DonorsImport implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    SkipsEmptyRows,
    ShouldQueue,
    WithChunkReading
{

    private $skippedRows = [];
    private $governorates;
    private $cities;
    private $areas;
    private $departments;

    public function __construct()
    {
        // Cache data to reduce database queries
        $this->governorates = Governorate::pluck('id', 'name')->toArray();
        $this->cities = City::pluck('id', 'name')->toArray();
        $this->areas = Area::pluck('id', 'name')->toArray();
        $this->departments = Department::pluck('id', 'name')->toArray();
    }

    public function chunkSize(): int
    {
        return 500; // Process 500 rows at a time
    }

    public function collection(Collection $rows)
    {
        // \DB::disableQueryLog(); // Disable query logging
        // \DB::beginTransaction(); // Start transaction

        try {
            $rows->chunk(500)->each(function ($chunk) {
                foreach ($chunk as $index => $row) {
                    $data = $row->toArray();

                    try {
                        // Validate required fields
                        if (empty($data['name'])) {
                            throw new \Exception('Name is required ' . $data['name'] . '');
                        }

                        // Process phones only if the field is not empty
                        $validPhones = [];
                        if (!empty($data['phones'])) {
                            $phones = explode(',', $data['phones']);
                            foreach ($phones as $phone) {
                                [$number, $type] = explode(':', $phone) + [null, null];
                                $number = trim($number);
                                $type = trim($type ?? 'unknown');

                                // Skip if the phone number is empty
                                if (empty($number)) {
                                    continue;
                                }

                                $validPhones[] = ['number' => $number, 'type' => $type];
                            }
                        }

                        // Find relationships using cached data
                        $governorateId = $this->governorates[$data['governorate_name']] ?? null;
                        $cityId = $this->cities[$data['city_name']] ?? null;
                        $areaId = $this->areas[$data['area_name']] ?? null;
                        $departmentId = $this->departments[$data['department_name']] ?? null;

                        // Create or update donor
                        $donor = Donor::create(
                            [
                                'name' => $data['name'],
                                'address' => $data['address'] ?? null,
                                'street' => $data['street'] ?? null,
                                'governorate_id' => $governorateId,
                                'city_id' => $cityId,
                                'area_id' => $areaId,
                                'active' => $data['active'] ?? true,
                                'donor_type' => $data['donor_type'],
                                'monthly_donation_day' => $data['monthly_donation_day'],
                                'donor_category' => $data['donor_category'],
                                'department_id' => $departmentId,
                                'notes' => $data['notes'],
                            ]
                        );

                        // Process phones only if there are valid phone numbers
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
                        \Log::info('errors', [$e]);
                        $this->skippedRows[] = [
                            'row' => $index + 2,
                            'data' => $data,
                            'error' => $e->getMessage(),
                        ];
                        \Log::info('skipped rows', [$this->skippedRows]);
                    }
                }
            });

            // \DB::commit(); // Commit transaction
        } catch (\Exception $e) {
            \DB::rollBack(); // Rollback on error
            throw $e;
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
            'governorate_name' => 'nullable|string|exists:governorates,name|regex:/^[\p{Arabic}\s]+$/u',
            'city_name' => 'nullable|string|exists:cities,name|regex:/^[\p{Arabic}\s]+$/u',
            'area_name' => 'nullable|string|exists:areas,name|regex:/^[\p{Arabic}\s]+$/u',
            'department_name' => 'nullable|string|exists:departments,name|regex:/^[\p{Arabic}\s]+$/u',
            'donor_type' => 'nullable|string|in:normal,monthly',
            'donor_category' => 'nullable|string|in:normal,special',
            'notes' => '',
            'monthly_donation_day' => 'nullable',
            'phones' => [
                'nullable',
                'string',
                'regex:/^((\d{11}:mobile)|(\d{1,15}:(home|work)))(,((\d{11}:mobile)|(\d{1,15}:(home|work))))*$/',
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
