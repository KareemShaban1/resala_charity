<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDonationRequest extends FormRequest
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
            'date' => 'required|date',
            'status' => 'required|in:collected,not_collected',
            'donates' => 'required|array',
            'donates.*.financial_donation_type' => 'required|in:Financial',
            'donates.*.financial_donation_id' => 'nullable|exists:donation_items,id',
            'donates.*.financial_donation_categories_id' => 'nullable|exists:donation_categories,id',
            'donates.*.financial_amount' => 'nullable|numeric|min:0',
            'donates.*.inKind_donation_type' => 'required|in:inKind',
            'donates.*.in_kind_item_name' => 'nullable|string',
            'donates.*.inkind_donation_id' => 'nullable|exists:donation_items,id',
            'donates.*.in_kind_quantity' => 'nullable|integer|min:1',
        ];
    }
}
