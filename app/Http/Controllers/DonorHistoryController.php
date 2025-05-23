<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\DonorActivity;
use Illuminate\Http\Request;

class DonorHistoryController extends Controller
{
    //
    public function show($id)
    {
        $donor = Donor::find($id);
        $donor->load('donations', 'monthlyForms');
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
        $donations->load('collectingDonation', 'donateItems');


        return view('backend.pages.donor-history.partials.donations-table', compact('donations'))->render();
    }

    public function getMonthlyForms(Request $request, $id)
    {
        $donor = Donor::findOrFail($id);

        $monthlyForms = $donor->monthlyForms();

        // Apply date filter if provided
        if ($request->start_date && $request->end_date) {
            $monthlyForms->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $monthlyForms = $monthlyForms->get();

        return view('backend.pages.donor-history.partials.monthly-forms-table', compact('monthlyForms'))->render();
    }

    public function getActivities(Request $request, $id)
    {
        $donor = Donor::findOrFail($id);

        $activities = $donor->activities();

        if (request()->has('date_filter')) {
            $dateFilter = request('date_filter');
            $startDate = request('start_date');
            $endDate = request('end_date');

            if ($dateFilter === 'today') {
                $activities->whereDate('created_at', today());
            } elseif ($dateFilter === 'week') {
                $activities->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($dateFilter === 'month') {
                $activities->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            } elseif ($dateFilter === 'range' && $startDate && $endDate) {
                $activities->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        $activities = $activities->get();

        return view('backend.pages.donor-history.partials.activities-table', compact('activities'))->render();
    }

    public function showActivity($id)
    {
        $activity = DonorActivity::findOrFail($id);

        $activity->load('donor', 'callType', 'createdBy','activityStatus','activityReason');
        return response()->json($activity);
    }
}
