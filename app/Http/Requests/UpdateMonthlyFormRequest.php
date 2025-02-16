<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMonthlyFormRequest extends FormRequest
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
            'donor_id' => 'required|exists:donors,id|unique:monthly_forms,donor_id,' . $this->route('monthly_form')->id,
            'department_id' => 'required|exists:departments,id',
            'employee_id' => 'required|exists:employees,id',
            'collecting_donation_way' => 'required|string|in:location,online,representative',
            'status' => 'required|in:ongoing,cancelled',
            'notes' => 'nullable|string',
            'donation_type' => 'required|string|in:financial,inKind,both',
            'form_date' => 'required|date',
            'follow_up_department_id' => 'nullable|exists:departments,id',
            'cancellation_reason' => 'nullable|string',
            'cancellation_date' => 'nullable',
            'items' => 'required|array',
            // 'items.*.id' => 'nullable|exists:monthly_forms_items,id',
            'items.*.financial_donation_type' => 'sometimes|in:financial',
            'items.*.financial_donation_categories_id' => 'nullable|exists:donation_categories,id',
            'items.*.financial_monthly_donation_id' => 'nullable|exists:monthly_forms_items,id',
            'items.*.financial_amount' => 'nullable|numeric|min:0',
            'items.*.inKind_donation_type' => 'sometimes|in:inKind',
            'items.*.inkind_monthly_donation_id' => 'nullable|exists:monthly_forms_items,id',
            'items.*.in_kind_item_name' => 'nullable|string',
            'items.*.in_kind_quantity' => 'nullable|integer|min:1',
        ];
    }
}
