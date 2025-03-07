<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonationCategory extends Model
{
    // use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        "name",
        "active",
        "description",
        ];
        protected $casts = [
            'active' => 'boolean',
        ];

}
