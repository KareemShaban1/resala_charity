<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonorPhone extends Model
{
    use HasFactory;
    // SoftDeletes;

    protected $fillable = [
        'donor_id',
        'phone_number',
        'phone_type',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
