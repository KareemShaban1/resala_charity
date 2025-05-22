<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'donor_id' => $this->donor_id,
            'employee_id' => $this->employee_id,
            'department_id' => $this->department_id,
            'follow_up_department_id' => $this->follow_up_department_id,
            'notes' => $this->notes,
            'status' => $this->status,
            'donation_type' => $this->donation_type,
            'collecting_donation_way' => $this->collecting_donation_way,
            'form_date' => $this->form_date,
            'items' => MonthlyFormItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
