<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['id','name'];

    public function areas()
    {
        return $this->belongsToMany(Area::class, 'area_group_members');
    }
}
