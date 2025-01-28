<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectingLineDonation extends Model
{
    use HasFactory;

    protected $table = 'collecting_line_donations';

    protected $fillable = [
        'collecting_line_id',
        'donation_id',
    ];
}
