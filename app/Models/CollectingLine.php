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
        'collecting_date',
        'number',
    ];

    protected $casts = [
        'collecting_date' => 'date',
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

    

    public static function generateUniqueNumber()
    {
        $year = date('Y'); // Get current year
        $latestEntry = CollectingLine::where('number', 'LIKE', "%-$year")->orderBy('id', 'desc')->first();
    
        if ($latestEntry) {
            // Extract the numeric part before the hyphen
            $lastNumber = (int) explode('-', $latestEntry->number)[0];
            $number = $lastNumber + 1;
        } else {
            // Start from 1 if no entry exists for the current year
            $number = 1;
        }
    
        return "$number-$year";
    }
    
   
}
