<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectingLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'driver_id',
        'employee_id',
        'area_group_id',
        'collecting_date'
    ];

    public function representative()
    {
        return $this->belongsTo(Employee::class, 'representative_id');
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }
    
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function areaGroup()
    {
        return $this->belongsTo(AreaGroup::class, 'area_group_id');
    }

    public function donations(){
        return $this->belongsToMany(Donation::class, 'collecting_line_donations');

    }
   
}
