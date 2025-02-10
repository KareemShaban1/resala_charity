<?php
namespace App\Exports;

use App\Models\MonthlyForm;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MonthlyFormsExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect([$this->data]);
    }

    public function headings(): array
    {
        return [
            'Total Monthly Forms',
            'Total Monthly Forms Amount',
            'Monthly Forms Not Collected Count',
            'Monthly Forms Collected Count',
            'Monthly Forms Not Collected Amount',
            'Monthly Forms Collected Amount'
        ];
    }
}
