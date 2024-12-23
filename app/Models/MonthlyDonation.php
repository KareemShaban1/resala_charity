<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyDonation extends Model
{
    use HasFactory;
    protected $fillable = [
        'number',
        'donor_id',
        'created_by',
        'date',
        'notes',
        'collecting_donation_way',
        // 'collected_by'
    ];
    protected $casts = [
        'donor_id' => 'integer',
        'created_by' => 'integer',
        'collected_by' => 'integer',
        'date' => 'date',
        'collecting_donation_way' => 'string',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class, 'donor_id');
    }
}
