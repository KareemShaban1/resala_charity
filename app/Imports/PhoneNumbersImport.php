<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;

class PhoneNumbersImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $phoneNumbers = [];

        foreach ($rows as $row) {
            // Skip the header row
            if ($row[0] === 'Phone Number') {
                continue;
            }

            // Skip empty rows
            if (empty($row[0])) {
                continue;
            }

            // Remove unwanted characters (e.g., ":mobile", ":home") and extra spaces
            $cleanedRow = str_replace([':mobile', ':home', ' '], '', $row[0]);

            // Split the row by commas to handle multiple phone numbers in a single cell
            $numbers = explode(',', $cleanedRow);

            foreach ($numbers as $number) {
                // Skip empty values
                if (empty($number)) {
                    continue;
                }

                // Log each number for debugging
                Log::info('Processing number:', ['number' => $number]);

                // Add the phone number to the array if it's not empty
                if (!empty($number)) {
                    $phoneNumbers[] = (string) $number; // Convert to string
                }
            }
        }

        // Log the final list of phone numbers for debugging
        Log::info('Final phone numbers:', $phoneNumbers);

        // Return the unique phone numbers as a flat collection
        return collect($phoneNumbers)->unique();
    }
}