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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonthlyFormsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading
{

    use SkipsFailures;

    public function __construct()
    {
        ini_set('max_execution_time', 300); // Set to 5 minutes
    }


    protected $rows = [];
    protected $hasErrors = false;

    public function batchSize(): int
    {
        return 200;
    }

    public function chunkSize(): int
    {
        return 200;
    }

    public function model(array $row)
    {
        $donor = Donor::where('name', $row['donor_name'])->first();
        $department = Department::where('name', $row['department_name'])->first();
        $employee = Employee::where('name', $row['employee_name'])->first();
        $followUpDepartment = Department::where('name', $row['follow_up_department_name'])->first();

        if (!$donor || !$department || !$employee) {
            $this->hasErrors = true;
            return null;
        }

        if (MonthlyForm::where('donor_id', $donor->id)->exists()) {
            Log::info("Skipping donor_id {$donor->id} - Already Exists.");
            return null;
        }



        $this->rows[] = [
            'donor_id' => $donor->id,
            'collecting_donation_way' => $row['collecting_donation_way'],
            'status' => $row['status'],
            'notes' => $row['notes'] ?? null,
            'department_id' => $department->id,
            'employee_id' => $employee->id,
            'cancellation_reason' => $row['cancellation_reason'] ?? null,
            'cancellation_date' => $row['cancellation_date'] ?? null,
            'donation_type' => $row['donation_type'],
            'form_date' => $row['form_date'],
            'follow_up_department_id' => optional($followUpDepartment)->id,
            'created_by' => auth()->user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return null;
    }

    public function onFailure(Failure ...$failures)
    {
        $this->hasErrors = true;
        foreach ($failures as $failure) {
            Log::error('Import Validation Failed', [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ]);
        }
    }

    public function afterImport()
{
    if ($this->hasErrors) {
        Log::warning('Import aborted due to validation errors');
        return;
    }

    if (!empty($this->rows)) {
        DB::beginTransaction(); // Start transaction explicitly

        try {
            // REMOVE this line: DB::statement("ALTER TABLE monthly_forms AUTO_INCREMENT = 221");

            DB::table('monthly_forms')->upsert($this->rows, ['donor_id'], [
                'collecting_donation_way',
                'status',
                'notes',
                'department_id',
                'employee_id',
                'cancellation_reason',
                'cancellation_date',
                'donation_type',
                'form_date',
                'follow_up_department_id',
                'updated_at'
            ]);

            DB::commit(); // Commit transaction
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            Log::error('Import failed', ['error' => $e->getMessage()]);
            throw $e; // Re-throw to handle it in your controller
        }
    }
}

    


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
