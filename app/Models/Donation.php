<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    // use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'created_by',
        'date',
        'status',
        'donation_type',
        'donation_category',
        'collecting_time',
        'notes',
        'alternate_date',
        'reporting_way',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->with('department');
    }

    public function donateItems()
    {
        return $this->hasMany(DonationItem::class, 'donation_id')->with('donationCategory');
    }


    public function collectingDonation()
    {
        return $this->hasOne(DonationCollecting::class, 'donation_id');
    }

    public function collectingLines()
    {
        return $this->belongsToMany(CollectingLine::class, 'collecting_line_donations', 'donation_id', 'collecting_line_id');
    }

    public function monthlyForms()
    {
        return $this->belongsToMany(MonthlyForm::class, 'monthly_form_donations', 'donation_id', 'monthly_form_id')
            ->withPivot('donation_date', 'month')
            ->withTimestamps();
    }
}
