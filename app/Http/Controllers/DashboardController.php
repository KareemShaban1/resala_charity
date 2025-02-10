<?php

namespace App\Http\Controllers;

use App\Exports\MonthlyFormsExport;
use App\Models\Donor;
use App\Models\MonthlyForm;
use App\Models\MonthlyFormDonation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    //
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        // $monthYear = $request->input('month_year'); // Format: YYYY-MM
        // Get current month-year if not provided in request
        // $monthYear = $request->input('month_year', now()->format('Y-m')); // Default to current YYYY-MM

        $monthYear = now()->format('Y-m');

        $donorQuery = Donor::query();

        // Filter by month
        $monthlyFormsQuery = MonthlyFormDonation::query();
        if ($monthYear) {
            $donorQuery->whereYear('created_at', substr($monthYear, 0, 4))
                ->whereMonth('created_at', substr($monthYear, 5, 2));

        }

        // Filter by date range
        if ($fromDate && $toDate) {
            $donorQuery->whereBetween('created_at', [$fromDate, $toDate]);

        }

    
        $donorsCount = $donorQuery->count();

 
        return view('backend.pages.dashboard.index', compact(
            'donorsCount',
            'fromDate',
            'toDate',
            'monthYear'
        ));
    }

    public function filter(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $monthYear = $request->input('month_year'); // Format: YYYY-MM

        $donorQuery = Donor::query();

        // Filter by month
        $monthlyFormsQuery = MonthlyFormDonation::query();
        if ($monthYear) {     
                $donorQuery->whereYear('created_at', substr($monthYear, 0, 4))
                ->whereMonth('created_at', substr($monthYear, 5, 2));
            }

        // Filter by date range
        if ($fromDate && $toDate) {
            $donorQuery->whereBetween('created_at', [$fromDate, $toDate]);

        }

        $donorsCount = $donorQuery->count();

    
        return view('backend.pages.dashboard.partials.monthly_forms_table', compact(
            'donorsCount',
            'fromDate',
            'toDate',
            'monthYear'
        ));
    }
    


}
