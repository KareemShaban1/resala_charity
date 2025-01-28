<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Area;
use App\Models\City;
use App\Models\Department;
use App\Models\DonationCategory;
use App\Models\Donor;
use App\Models\Employee;
use App\Models\Governorate;
use App\Models\MonthlyForm;
use App\Models\User;
use App\Policies\AreaPolicy;
use App\Policies\CityPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\DonationCategoryPolicy;
use App\Policies\DonorPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\GovernoratePolicy;
use App\Policies\MonthlyFormPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        DonationCategory::class => DonationCategoryPolicy::class,
        MonthlyForm::class => MonthlyFormPolicy::class,
        Area::class => AreaPolicy::class,
        Governorate::class => GovernoratePolicy::class,
        City::class => CityPolicy::class,
        Donor::class => DonorPolicy::class,
        Role::class => RolePolicy::class,
        User::class => UserPolicy::class,
        Department::class => DepartmentPolicy::class,
        Employee::class => EmployeePolicy::class,




    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
