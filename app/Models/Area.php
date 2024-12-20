<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'city_id'];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
