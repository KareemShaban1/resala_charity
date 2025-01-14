<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;

class DonorHistoryController extends Controller
{
    //
    public function show($id)
    {
        $donor = Donor::find($id);
        $donor->load('donations', 'monthlyDonations');
        return view('backend.pages.donor-history.index', compact('donor'));
    }

    public function getDonations(Request $request, $id)
    {
        $donor = Donor::findOrFail($id);

        $donations = $donor->donations();

        // Apply date filter if provided
        if ($request->start_date && $request->end_date) {
            $donations->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $donations = $donations->get();
        $donations->load('collectingDonation','donateItems');


        return view('backend.pages.donor-history.partials.donations-table', compact('donations'))->render();
    }

    public function getMonthlyDonations(Request $request, $id)
    {
        $donor = Donor::findOrFail($id);

        $monthlyDonations = $donor->monthlyDonations();

        // Apply date filter if provided
        if ($request->start_date && $request->end_date) {
            $monthlyDonations->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $monthlyDonations = $monthlyDonations->get();

        return view('backend.pages.donor-history.partials.monthly-donations-table', compact('monthlyDonations'))->render();
    }

    public function getActivities(Request $request, $id)
    {
        $donor = Donor::findOrFail($id);

        $activities = $donor->activities();

        // Apply date filter if provided
        if ($request->start_date && $request->end_date) {
            $activities->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $activities = $activities->get();

        return view('backend.pages.donor-history.partials.activities-table', compact('activities'))->render();
    }
}
