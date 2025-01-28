<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NonMatchingNumbersExport implements FromCollection, WithHeadings
{
    protected $nonMatchingNumbers;

    public function __construct($nonMatchingNumbers)
    {
        $this->nonMatchingNumbers = $nonMatchingNumbers;
    }

    public function collection()
    {
        // Return the non-matching numbers as a collection
        return collect($this->nonMatchingNumbers)->map(function ($number) {
            return ['phone' => $number];
        });
    }

    public function headings(): array
    {
        // Add a header to the Excel file
        return ['Phone Number'];
    }
}