<?php

namespace App\Observers;

use App\Models\Donation;

class DonationObserver
{

    public function creating(Donation $donation): void
    {
        $donation->created_by = auth()->user()->id; 
    }
    /**
     * Handle the Donation "created" event.
     */
    public function created(Donation $donation): void
    {
        //
    }

    /**
     * Handle the Donation "updated" event.
     */
    public function updated(Donation $donation): void
    {
        //
    }

    /**
     * Handle the Donation "deleted" event.
     */
    public function deleted(Donation $donation): void
    {
        //
    }

    /**
     * Handle the Donation "restored" event.
     */
    public function restored(Donation $donation): void
    {
        //
    }

    /**
     * Handle the Donation "force deleted" event.
     */
    public function forceDeleted(Donation $donation): void
    {
        //
    }
}
