<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonorActivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'donor_id',
        'call_type_id',
        'date_time',
        'activity_status_id',
        'activity_reason_id',
        'response',
        'notes',
        'activity_type',
        'created_by'
    ];

    public function donor(){
        return $this->belongsTo( Donor::class);
    }
    public function callType(){
        return $this->belongsTo( CallType::class);
    }
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }
    public function activityStatus(){
        return $this->belongsTo(ActivityStatus::class);
    }
    public function activityReason(){
        return $this->belongsTo(ActivityReason::class);
    }
}
