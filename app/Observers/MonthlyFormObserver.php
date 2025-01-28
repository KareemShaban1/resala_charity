<?php

namespace App\Observers;

use App\Models\MonthlyForm;

class MonthlyFormObserver
{
    //
    public function creating(MonthlyForm $monthlyForm)
    {
        //
        $user = auth()->user();

        $monthlyForm->created_by = $user->id; 
    }

    protected function generateUniqueNumber()
    {
        // Example logic for generating a unique number
        return 'MD-' . date('Ymd') . '-' . rand(0, 100);
    }
}
