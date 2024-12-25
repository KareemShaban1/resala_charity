<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyDonationsDonate extends Model
{
    use HasFactory;

    protected $table = "monthly_donations_donates";

    protected $fillable = [
        'monthly_donation_id',
        'donation_category_id',
        'amount',
        'item_name',
        'donation_type'
    ];

    public function donationCategory()
    {
        return $this->belongsTo(DonationCategory::class, 'donation_category_id');
    }
}
