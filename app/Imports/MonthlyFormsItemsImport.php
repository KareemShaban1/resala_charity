<?php

namespace App\Imports;

use App\Models\DonationCategory;
use App\Models\MonthlyFormItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MonthlyFormsItemsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Ensure the key exists to avoid "Undefined array key" error
        $donationCategory = null;
        if (!empty($row['financial_donation_category'])) {
            $donationCategory = DonationCategory::where('name', $row['financial_donation_category'])->first();
        }

        // Validation
        $validator = Validator::make([
            'monthly_form_id' => $row['form_id'] ?? null,
            'financial_donation_categories_id' => optional($donationCategory)->id,
            'financial_amount' => $row['financial_amount'] ?? null,
            'in_kind_item_name' => $row['in_kind_item_name'] ?? null,
            'in_kind_quantity' => $row['in_kind_quantity'] ?? null,
        ], [
            'monthly_form_id' => 'required|exists:monthly_forms,id',
            'financial_donation_categories_id' => 'nullable|exists:donation_categories,id',
            'financial_amount' => 'nullable|numeric|min:0',
            'in_kind_item_name' => 'nullable|string',
            'in_kind_quantity' => 'nullable|integer|min:1',
        ]);

        Log::info('Processing row', $row);

        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            return null; // Skip invalid rows
        }

        return DB::transaction(function () use ($row, $donationCategory) {
            // **Update or create financial donation**
            if ($donationCategory && !empty($row['financial_amount'])) {
                MonthlyFormItem::updateOrCreate(
                    [
                        'monthly_form_id' => $row['form_id'],
                        'donation_category_id' => optional($donationCategory)->id,
                        'donation_type' => 'financial',
                    ],
                    [
                        'amount' => $row['financial_amount'],
                    ]
                );
            }

            // **Update or create in-kind donation**
            if (!empty($row['in_kind_item_name']) && !empty($row['in_kind_quantity'])) {
                MonthlyFormItem::updateOrCreate(
                    [
                        'monthly_form_id' => $row['form_id'],
                        'item_name' => $row['in_kind_item_name'],
                        'donation_type' => 'inKind',
                    ],
                    [
                        'amount' => $row['in_kind_quantity'],
                    ]
                );
            }

            return null;
        });
    }
}
