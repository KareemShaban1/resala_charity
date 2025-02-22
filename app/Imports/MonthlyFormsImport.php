<?php

namespace App\Imports;

use App\Models\MonthlyForm;
use App\Models\Donor;
use App\Models\Department;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class MonthlyFormsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure,  WithBatchInserts, WithChunkReading
{
    use SkipsFailures; // This trait already handles failures

    public function batchSize(): int
    {
        return 200; // Insert 100 records at a time
    }

    public function chunkSize(): int
    {
        return 200; // Read 100 records at a time
    }
    public function model(array $row)
{
    $donor = Donor::where('name', $row['donor_name'])->first();
    $department = Department::where('name', $row['department_name'])->first();
    $employee = Employee::where('name', $row['employee_name'])->first();
    $followUpDepartment = Department::where('name', $row['follow_up_department_name'])->first();

    // Skip if the donor already has a record for the same form_date
    if ($donor && MonthlyForm::where('donor_id', $donor->id)->where('form_date', $row['form_date'])->exists()) {
        return null; // Skip this row
    }

    return new MonthlyForm([
        'donor_id' => optional($donor)->id,
        'collecting_donation_way' => $row['collecting_donation_way'],
        'status' => $row['status'],
        'notes' => $row['notes'] ?? null,
        'department_id' => optional($department)->id,
        'employee_id' => optional($employee)->id,
        'cancellation_reason' => $row['cancellation_reason'] ?? null,
        'cancellation_date' => $row['cancellation_date'] ?? null,
        'donation_type' => $row['donation_type'],
        'form_date' => $row['form_date'],
        'follow_up_department_id' => optional($followUpDepartment)->id,
        'created_by' => auth()->user()->id,
    ]);
}

    /**
     * Define validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'donor_name' => 'required|exists:donors,name',
            'department_name' => 'required|exists:departments,name',
            'employee_name' => 'required|exists:employees,name',
            'follow_up_department_name' => 'nullable|exists:departments,name',
            'collecting_donation_way' => 'required|string|in:location,online,representative',
            'status' => 'required|string|in:ongoing,cancelled',
            'donation_type' => 'required|string|in:financial,inKind,both',
            'form_date' => 'required|date',
            'notes' => 'nullable|string',
            'cancellation_reason' => 'nullable|string',
            'cancellation_date' => 'nullable|date',
        ];
    }
}
