<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class StoreDonorRequest extends FormRequest
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
            'address' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'governorate_id' => 'nullable|exists:governorates,id',
            'city_id' => 'nullable|exists:cities,id',
            'area_id' => 'nullable|exists:areas,id',
            'department_id' => 'nullable|exists:departments,id',
            'active' => 'required|boolean',
            'donor_type' => 'required|in:normal,monthly',
            'donor_category' => 'required|in:normal,special,random',
            'notes' => 'nullable|string',
            'monthly_donation_day' => 'nullable',
            'phones' => 'nullable|array|min:1',
            'phones.*.number' => ['nullable', 'distinct', 'unique:donor_phones,phone_number'],
            'phones.*.type' => 'nullable|string|in:mobile,home,work,other',
        ];
    }

    public function withValidator(Validator $validator)
{
    $validator->after(function ($validator) {
        $phones = $this->input('phones', []);

        foreach ($phones as $index => $phone) {
            if (!isset($phone['number'], $phone['type'])) {
                continue;
            }

            $type = $phone['type'];
            $number = $phone['number'];

            if ($type === 'home' && !preg_match('/^\d{7}$/', $number)) {
                $validator->errors()->add("phones.$index.number", 'رقم الهاتف المنزلي يجب أن يتكون من 7 أرقام.');
            }

            if ($type !== 'home' && !preg_match('/^\d{11}$/', $number)) {
                $validator->errors()->add("phones.$index.number", 'رقم الهاتف يجب أن يتكون من 11 رقمًا.');
            }
        }
    });
}

    public function messages(): array
    {
        return [
            'phones.required' => 'مطلوب إدخال رقم هاتف واحد على الأقل.',
            'phones.*.number.required' => 'رقم الهاتف مطلوب.',
            'phones.*.number.unique' => 'رقم الهاتف موجود بالفعل.',
            'phones.*.number.regex' => 'يجب أن يتكون رقم الهاتف من 11 رقمًا.',
            'phones.*.number.distinct' => 'يجب أن تكون أرقام الهواتف فريدة.',
            'phones.*.type.required' => 'نوع الهاتف مطلوب.',
            'phones.*.type.in' => 'يجب أن يكون نوع الهاتف واحدًا من الأنواع التالية: موبايل، منزل، عمل، آخر.'
        ];
        // return [
        //     'phones.required' => 'At least one phone number is required.',
        //     'phones.*.number.required' => 'Phone number is required.',
        //     'phones.*.number.regex' => 'Phone number must be 11 digits.',
        //     'phones.*.number.distinct' => 'Phone numbers must be unique.',
        //     'phones.*.type.required' => 'Phone type is required.',
        //     'phones.*.type.in' => 'Phone type must be one of: mobile, home, work, other.'
        // ];
    }
}
