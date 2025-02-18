<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function changeDonorsData(){
        $randomDonors = Donor::whereBetween('id', [11390, 12,387])->get();
        foreach ($randomDonors as $donor) {
            $donor->donor_category = 'random';
            $donor->save();
        }
        return 'done';
    } 
}
