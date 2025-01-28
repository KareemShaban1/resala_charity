<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMonthlyFormRequest extends FormRequest
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
            'donor_id' => 'required|exists:donors,id|unique:monthly_forms,donor_id',
            'department_id' => 'required|exists:departments,id',
            'employee_id' => 'required|exists:employees,id',
            'collecting_donation_way' => 'required|string|in:location,online,representative',
            'status' => 'required|in:ongoing,cancelled',
            'donation_type' => 'required|string|in:financial,inKind,both',
            'notes' => 'nullable|string',
            'cancellation_reason' => 'nullable|string',
            'cancellation_date' => 'nullable',
            'items' => 'required|array',
            'items.*.financial_donation_type' => 'nullable|in:financial',
            'items.*.inKind_donation_type' => 'nullable|in:inKind',
            'items.*.financial_donation_categories_id' => 'nullable|exists:donation_categories,id',
            'items.*.financial_amount' => 'nullable|numeric|min:0',
            'items.*.in_kind_item_name' => 'nullable|string',
            'items.*.in_kind_quantity' => 'nullable|integer|min:1',
            
        ];
    }
}
