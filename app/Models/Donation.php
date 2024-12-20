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
        'amount',
        'date',
        'notes',
        'payment_method',
        'receipt',
        'active',
        'donate_date',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
