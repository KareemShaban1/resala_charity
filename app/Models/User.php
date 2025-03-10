<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{

    // use SoftDeletes;
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['is_admin', 'is_super_admin'];


    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function activities()
    {
        return $this->hasMany(DonorActivity::class, 'created_by')
            ->with('donor', 'callType', 'activityStatus');
    }

    public function getIsSuperAdminAttribute()
    {
        return $this->hasRole('Super Admin');
    }

    public function getIsAdminAttribute()
    {
        return $this->hasRole('admin');
    }

}
