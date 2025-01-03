<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationCollecting extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'collecting_date',
        'employee_id',
        'receipt_number',
    ];

    protected $casts = [
        'collecting_date' => 'date',
    ];
    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
