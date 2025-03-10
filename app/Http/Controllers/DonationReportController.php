<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationReportController extends Controller
{
    public function collectedDonations(Request $request)
    {
        $donationQuery = Donation::query();
        $collectedDonationQuery = Donation::with(['donateItems.donationCategory'])
            ->whereHas('collectingDonation');



        if (request()->ajax()) {


            if (isset($request->start_date) && isset($request->end_date)) {
                $startDate = request('start_date');
                $endDate = request('end_date');
                $donationQuery->whereBetween('created_at', [$startDate, $endDate]);
                $collectedDonationQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            if (isset($request->employee_id)) {
                $donationQuery->whereHas('collectingDonation', function ($query) {
                    $query->where('employee_id', request('employee_id'));
                });
                $collectedDonationQuery->whereHas('collectingDonation', function ($query) {
                    $query->where('employee_id', request('employee_id'));
                });
            }

            if (isset($request->area_id)) {
                $donationQuery->whereHas('donor', function ($q) {
                    $q->where('area_id', request('area_id'));
                });
                $collectedDonationQuery->whereHas('donor', function ($q) {
                    $q->where('area_id', request('area_id'));
                });
            }

            if (isset($request->user_id)) {
                $donationQuery->where('created_by', request('user_id'));
                $collectedDonationQuery->where('created_by', request('user_id'));
            }

            if (isset($request->department_id)) {
                $donationQuery->whereHas('collectingDonation', function ($query) use ($request) {
                    $query->whereHas('employee', function ($q) use ($request) {
                        $q->where('department_id', $request->department_id);
                    });
                });
                $collectedDonationQuery->whereHas('collectingDonation', function ($query) use ($request) {
                    $query->whereHas('employee', function ($q) use ($request) {
                        $q->where('department_id', $request->department_id);
                    });
                });
            }

            if (isset($request->reporting_way)) {
                $donationQuery->where('reporting_way', $request->reporting_way);
                $collectedDonationQuery->where('reporting_way', $request->reporting_way);
            }

            if (isset($request->collecting_way)) {
                $donationQuery->whereHas('collectingDonation', function ($query) use ($request) {
                    $query->where('collecting_way', $request->collecting_way);
                });
                $collectedDonationQuery->whereHas('collectingDonation', function ($query) use ($request) {
                    $query->where('collecting_way', $request->collecting_way);
                });
            }

            // Fetch all available donation categories
            $allCategories = \App\Models\DonationCategory::pluck('name')->toArray();

            // Process the collected donation query
            $donationsByCategory = $collectedDonationQuery
                ->whereHas('donateItems', function ($query) {
                    $query->where('donation_type', 'financial');
                })->get()->pluck('donateItems')->flatten()
                ->groupBy('donationCategory.name')
                ->map(function ($items) {
                    return [
                        'count' => $items->count(),
                        'total_amount' => $items->sum('amount'),
                    ];
                })->filter(function ($value, $key) {
                    return !empty($key); // Remove empty keys
                });

            // Ensure all categories exist with default values
            foreach ($allCategories as $category) {
                if (!isset($donationsByCategory[$category])) {
                    $donationsByCategory[$category] = [
                        'count' => 0,
                        'total_amount' => 0,
                    ];
                }
            }

            // Get all donations
            $allDonations = (clone $donationQuery)->get();
            $allDonationsAmount = (clone $donationQuery)->withSum('donateItems', 'amount')->get()->sum('donate_items_sum_amount');


            $collectedDonations = (clone $collectedDonationQuery)->get();
            $collectedDonationsAmount = (clone $collectedDonationQuery)->withSum('donateItems', 'amount')->get()->sum('donate_items_sum_amount');

            // Get financial and in-kind donations
            $financialDonations = (clone $donationQuery)->whereHas('donateItems', function ($query) {
                $query->where('donation_type', 'financial');
            })->get();
            $inKindDonations = (clone $donationQuery)->whereHas('donateItems', function ($query) {
                $query->where('donation_type', 'inKind');
            })->get();

            // Get financial and in-kind collected donations
            $financialCollectedDonations = (clone $collectedDonationQuery)
                ->whereHas('donateItems', function ($query) {
                    $query->where('donation_type', 'financial');
                })->get();

            $inKindCollectedDonations = (clone $collectedDonationQuery)
                ->whereHas('donateItems', function ($query) {
                    $query->where('donation_type', 'inKind');
                })->get();


            // Get sum of financial and in-kind donations
            $financialDonationsAmount = $financialDonations->sum(fn($donation) => $donation->donateItems->sum('amount'));

            // Get sum of financial and in-kind collected donations
            $financialCollectedDonationsAmount = $financialCollectedDonations->sum(fn($donation) => $donation->donateItems->sum('amount'));


            return response()->json([
                'allDonationsCount' => $allDonations->count(),
                'collectedDonationsCount' => $collectedDonations->count(),
                'financialDonationsCount' => $financialDonations->count(),
                'inKindDonationsCount' => $inKindDonations->count(),
                'financialCollectedDonationsCount' => $financialCollectedDonations->count(),
                'inKindCollectedDonationsCount' => $inKindCollectedDonations->count(),

                // Sum amounts
                'allDonationsAmount' => $allDonationsAmount,
                'collectedDonationsAmount' => $collectedDonationsAmount,
                'financialDonationsAmount' => $financialDonationsAmount,
                'financialCollectedDonationsAmount' => $financialCollectedDonationsAmount,
                'donationsByCategory' => $donationsByCategory,

            ]);
        }

        return view('backend.pages.reports.donations.collected');
    }

    public function notCollectedDonations(Request $request)
    {

        $donationQuery = Donation::query();

        if (request()->ajax()) {
            if (isset($request->start_date) && isset($request->end_date)) {
                $startDate = request('start_date');
                $endDate = request('end_date');
                $donationQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            if (isset($request->area_id)) {
                $donationQuery->whereHas('donor', function ($q) {
                    $q->where('area_id', request('area_id'));
                });
            }
            if (isset($request->user_id)) {
                $donationQuery->where('created_by', request('user_id'));
            }

            $allDonations = (clone $donationQuery)->get();


            $notCollectedDonations = (clone $donationQuery)->whereDoesntHave('collectingDonation')->get();
            $notCollectedDonationsAmount = (clone $donationQuery)->whereDoesntHave('collectingDonation')->withSum('donateItems', 'amount')->get()->sum('donate_items_sum_amount');

            $financialNotCollectedDonations = (clone $donationQuery)
                ->whereDoesntHave('collectingDonation')
                ->whereHas('donateItems', function ($query) {
                    $query->where('donation_type', 'financial');
                })->get();

            $inKindNotCollectedDonations = (clone $donationQuery)
                ->whereDoesntHave('collectingDonation')
                ->whereHas('donateItems', function ($query) {
                    $query->where('donation_type', 'inKind');
                })->get();

            // Get sum of financial and in-kind not collected donations
            $financialNotCollectedDonationsAmount = $financialNotCollectedDonations->sum(fn($donation) => $donation->donateItems->sum('amount'));

            $allDonationsAmount = (clone $donationQuery)->withSum('donateItems', 'amount')->get()->sum('donate_items_sum_amount');

            return response()->json([
                'allDonationsCount' => $allDonations->count(),
                'allDonationsAmount' => $allDonationsAmount,
                'notCollectedDonationsCount' => $notCollectedDonations->count(),
                'financialNotCollectedDonationsCount' => $financialNotCollectedDonations->count(),
                'inKindNotCollectedDonationsCount' => $inKindNotCollectedDonations->count(),
                'notCollectedDonationsAmount' => $notCollectedDonationsAmount,
                'financialNotCollectedDonationsAmount' => $financialNotCollectedDonationsAmount,
            ]);
        }

        return view('backend.pages.reports.donations.not-collected');
    }
}
