<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDonorRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'street' => 'nullable|string|max:255',
            'governorate_id' => 'required|exists:governorates,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'nullable|exists:areas,id',
            'active' => 'required|boolean',
            'donor_type' => 'required|in:normal,monthly',
            'monthly_donation_day' => 'nullable|integer|min:1|max:31|exclude_if:donor_type,normal',
            'phones' => 'required|array|min:1',
            'phones.*.number' => [
                'required',
                'string',
                // 'regex:/^(\d{11}:mobile|(\d{1,15}:(home|work|other)))$/',
                'distinct',
                function ($attribute, $value, $fail) {
                    // Get the index of the phone number being validated (e.g., 0, 1, etc.)
                    preg_match('/phones\.(\d+)\.number/', $attribute, $matches);
                    $index = $matches[1] ?? null; // Extract the index
            
                    if ($index === null) {
                        return; // If index is not found, do nothing (this should never happen)
                    }
            
                    // Get the phone ID from the phones array in the request (phones[$index][id])
                    $phoneId = request()->input("phones.{$index}.id");
            
                    // Build the query to check if the phone number exists, excluding the current phone ID
                    $query = \DB::table('donor_phones')
                        ->where('phone_number', $value)
                        ->whereNull('deleted_at'); // Check for soft deletes if needed
            
                    if ($phoneId) {
                        // If phone ID is provided, exclude it from the check
                        $query->where('id', '<>', $phoneId);
                    }
            
                    // Check if a record exists with the same phone number
                    if ($query->exists()) {
                        $fail("The phone number {$value} has already been taken.");
                    }
                }   ],
            'phones.*.type' => 'required|string|in:mobile,home,work,other',
        ];
    }
    
    

    public function messages(): array
    {
        return [
            'phones.required' => 'At least one phone number is required.',
            'phones.*.number.required' => 'Phone number is required.',
            'phones.*.number.regex' => 'Phone number must be 11 digits.',
            'phones.*.number.distinct' => 'Phone numbers must be unique.',
            'phones.*.type.required' => 'Phone type is required.',
            'phones.*.type.in' => 'Phone type must be one of: mobile, home, work, other.'
        ];
    }
}
