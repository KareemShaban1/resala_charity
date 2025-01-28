<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCollectingLineRequest extends FormRequest
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
            'representative_id' => 'required|exists:employees,id',
            'driver_id' => 'nullable|exists:employees,id',
            'employee_id' => 'required|exists:employees,id',
            'area_group_id' => 'required|exists:area_groups,id',
            'collecting_date' => 'required',
        ];
    }
}
