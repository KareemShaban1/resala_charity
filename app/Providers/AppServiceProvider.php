<?php

namespace App\Providers;

use App\Observers\GenericObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        $models = [
            'App\Models\User','App\Models\Governorate',
            'App\Models\City','App\Models\Area','App\Models\DonationCategory', 
            'App\Models\Donor', 'App\Models\Department', 'App\Models\Employee', 
            'App\Models\Donation','App\Models\MonthlyDonation'];
        foreach ($models as $model) {
            $model::observe(GenericObserver::class);
        }
    }
}
