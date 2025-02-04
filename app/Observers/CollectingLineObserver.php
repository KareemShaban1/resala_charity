<?php

namespace App\Observers;

use App\Models\CollectingLine;

class CollectingLineObserver
{
    //
    public function creating(CollectingLine $collectingLine): void
    {
        $collectingLine->number = CollectingLine::generateUniqueNumber(); 
    }
}
