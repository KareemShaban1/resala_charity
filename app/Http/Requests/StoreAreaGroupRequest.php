<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAreaGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Return true if the user is authorized to make this request
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255', // Area group name is required
            'areas' => 'nullable|array', // Areas are optional (can be empty)
            'areas.*' => 'exists:areas,id', // Each area ID must exist in the areas table
        ];
    }

    /**
     * Custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The area group name is required.',
            'name.string' => 'The area group name must be a string.',
            'name.max' => 'The area group name must not exceed 255 characters.',
            'areas.required' => 'At least one area must be selected.',
            'areas.array' => 'The areas must be provided as an array.',
            'areas.*.exists' => 'One or more selected areas are invalid.',
        ];
    }
}