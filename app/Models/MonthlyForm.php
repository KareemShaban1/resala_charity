<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyForm extends Model
{
    use HasFactory;
    protected $fillable = [
        'donor_id',
        'employee_id',
        'department_id',
        'created_by',
        'status',
        'notes',
        'collecting_donation_way',
        'cancellation_reason',
        'cancellation_date',
        'donation_type'
        // 'collected_by'
    ];
    protected $casts = [
        'donor_id' => 'integer',
        'created_by' => 'integer',
        'collected_by' => 'integer',
        'collecting_donation_way' => 'string',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->with('department');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function donor()
    {
        return $this->belongsTo(Donor::class, 'donor_id')->withTrashed();;
    }
    public function items(){
        return $this->hasMany(MonthlyFormItem::class,'monthly_form_id')
        ->with('donationCategory');
    }
    public function donations(){
        return $this->belongsToMany(Donation::class, 'monthly_form_donations', 'monthly_form_id', 'donation_id')
        ->withPivot('donation_date', 'month')
        ->withTimestamps();
    }
}

