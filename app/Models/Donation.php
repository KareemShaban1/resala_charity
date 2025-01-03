<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'created_by',
        'date',
        'status',
        'donation_type'
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function donateItems() 
    {
        return $this->hasMany(DonationItem::class, 'donation_id')->with('donationCategory');
    }
    

    public function collectingDonation()
    {
        return $this->hasOne(DonationCollecting::class, 'donation_id');
    }
}
