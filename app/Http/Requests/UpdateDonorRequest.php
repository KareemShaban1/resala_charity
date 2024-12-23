<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'area_id' => 'required|exists:areas,id',
            'active' => 'required|boolean',
            'donor_type'=>'required|in:normal,monthly',
            'monthly_donation_day'=>'nullable',
            'phones' => 'required|array|min:1',
            'phones.*.number' => 'required|string|regex:/^[0-9]{11}$/|distinct',
            'phones.*.type' => 'required|string|in:mobile,home,work,other'
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
