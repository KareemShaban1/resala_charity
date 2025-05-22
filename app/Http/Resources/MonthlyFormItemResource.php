<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyFormItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'monthly_form_id'=>$this->monthly_form_id,
            'donation_category_id'=>$this->donation_category_id,
            'donation_type'=>$this->donation_type,
            'item_name'=>$this->item_name,
            'amount'=>$this->amount,
            'notes'=>$this->notes,
            'status'=>$this->status,
        ];
    }
}
