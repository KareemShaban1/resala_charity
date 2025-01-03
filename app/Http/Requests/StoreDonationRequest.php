<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'donor_id' => 'required|exists:donors,id',
            'date' => 'required|date',
            'status' => 'required|in:collected,not_collected',
            'employee_id' => 'nullable|exists:employees,id',
            'collecting_date' => 'nullable|date',
            'receipt_number' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    dd($attribute , $value);
                    if (!empty($value)) {
                        $financialExists = \DB::table('donations')
                            ->join('donation_items', 'donations.id', '=', 'donation_items.donation_id')
                            ->join('donation_collectings', 'donations.id', '=', 'donation_collectings.donation_id')
                            ->where('donation_collectings.receipt_number', $value)
                            ->where('donation_items.donation_type', 'Financial')
                            ->exists();

                        $inKindExists = \DB::table('donations')
                            ->join('donation_items', 'donations.id', '=', 'donation_items.donation_id')
                            ->join('donation_collectings', 'donations.id', '=', 'donation_collectings.donation_id')
                            ->where('donation_collectings.receipt_number', $value)
                            ->where('donation_items.donation_type', 'inKind')
                            ->exists();

                        // Fail validation if multiple financial donations share the same receipt number
                        if (
                            $financialExists && \DB::table('donation_items')
                            ->join('donation_collectings', 'donation_items.donation_id', '=', 'donation_collectings.donation_id')
                            ->where('donation_collectings.receipt_number', $value)
                            ->where('donation_items.donation_type', 'Financial')
                            ->count() >= 1
                        ) {
                            $fail(__('validation.unique_receipt_number_financial'));
                        }

                        // Fail validation if multiple in-kind donations share the same receipt number
                        if (
                            $inKindExists && \DB::table('donation_items')
                            ->join('donation_collectings', 'donation_items.donation_id', '=', 'donation_collectings.donation_id')
                            ->where('donation_collectings.receipt_number', $value)
                            ->where('donation_items.donation_type', 'inKind')
                            ->count() >= 1
                        ) {
                            $fail(__('validation.unique_receipt_number_inKind'));
                        }
                    }
                },
            ],

            'donates' => 'required|array',
            'donates.*.financial_donation_type' => 'nullable|in:Financial',
            'donates.*.financial_donation_categories_id' => 'nullable|exists:donation_categories,id',
            'donates.*.financial_amount' => 'nullable|numeric|min:0',
            'donates.*.inKind_donation_type' => 'nullable|in:inKind',
            'donates.*.in_kind_item_name' => 'nullable|string',
            'donates.*.in_kind_quantity' => 'nullable|integer|min:1',
        ];
    }
}
