<?php

namespace App\Observers;

use App\Models\MonthlyDonation;

class MonthlyDonationObserver
{
    //
    public function creating(MonthlyDonation $monthlyDonation)
    {
        //
        $user = auth()->user();

        $monthlyDonation->created_by = $user->id; 
        $monthlyDonation->number = $this->generateUniqueNumber();
    }

    protected function generateUniqueNumber()
    {
        // Example logic for generating a unique number
        return 'MD-' . date('Ymd') . '-' . rand(0, 100);
    }
}
