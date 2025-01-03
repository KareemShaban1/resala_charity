<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'donation_category_id',
        // 'donation_type',
        'item_name',
        'amount',
    ];
    public function donation(){
        return $this->belongsTo(Donation::class);
    }
    public function donationCategory(){
        return $this->belongsTo(DonationCategory::class);
    }
}
