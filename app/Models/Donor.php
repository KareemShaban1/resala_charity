<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donor extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'street',
        'governorate_id',
        'city_id',
        'area_id',
        'active',
        'donor_type',
        'monthly_donation_day',
        'department_id',
        'notes',
        'donor_category'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function phones()
    {
        return $this->hasMany(DonorPhone::class);
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
    public function donations()
    {
        return $this->hasMany(Donation::class)->with('donateItems');
    }
    public function monthlyForms()
    {
        return $this->hasMany(MonthlyForm::class)->with('items');
    }

    public function activities()
    {
        return $this->hasMany(DonorActivity::class)->with(['callType','createdBy']);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }
    public function childrenDonors(){
        return $this->hasMany(Donor::class,'parent_id');
    }
}
