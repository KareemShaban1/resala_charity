<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMonthlyDonationRequest extends FormRequest
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
            //
              'donor_id' => 'required|exists:donors,id',
            'department_id' => 'required|exists:departments,id',
            'employee_id' => 'required|exists:employees,id',
            'collecting_donation_way' => 'required|string|in:location,online,representative',
            'status' => 'required|in:ongoing,cancelled',
            'cancellation_reason' => 'nullable|string',
            'cancellation_date' => 'nullable',
            'donates' => 'required|array',
            'donates.*.financial_donation_type' => 'required|in:Financial',
            'donates.*.inKind_donation_type' => 'required|in:inKind',
            'donates.*.financial_donation_categories_id' => 'nullable|exists:donation_categories,id',
            'donates.*.financial_amount' => 'nullable|numeric|min:0',
            'donates.*.in_kind_item_name' => 'nullable|string',
            'donates.*.in_kind_quantity' => 'nullable|integer|min:1',
            
        ];
    }
}
