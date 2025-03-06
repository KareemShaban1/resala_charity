<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationReportController extends Controller
{
    public function index()
    {
        $donationQuery = Donation::query();

        if (request()->ajax()) {
            if (request()->has('start_date') && request()->has('end_date')) {
                $startDate = request('start_date');
                $endDate = request('end_date');
                $donationQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            // Get all donations
            $allDonations = (clone $donationQuery)->get();
            $collectedDonations = (clone $donationQuery)->whereHas('collectingDonation')->get();

            // Get sum of donation amounts
            $allDonationsAmount = (clone $donationQuery)->withSum('donateItems', 'amount')->get()->sum('donate_items_sum_amount');
            $collectedDonationsAmount = (clone $donationQuery)->whereHas('collectingDonation')->withSum('donateItems', 'amount')->get()->sum('donate_items_sum_amount');

            // Get financial and in-kind donations
            $financialDonations = (clone $donationQuery)->whereHas('donateItems', function ($query) {
                $query->where('donation_type', 'financial');
            })->get();
            $inKindDonations = (clone $donationQuery)->whereHas('donateItems', function ($query) {
                $query->where('donation_type', 'inKind');
            })->get();

            // Get financial and in-kind collected donations
            $financialCollectedDonations = (clone $donationQuery)
                ->whereHas('collectingDonation')
                ->whereHas('donateItems', function ($query) {
                    $query->where('donation_type', 'financial');
                })->get();

            $inKindCollectedDonations = (clone $donationQuery)
                ->whereHas('collectingDonation')
                ->whereHas('donateItems', function ($query) {
                    $query->where('donation_type', 'inKind');
                })->get();


            // Get sum of financial and in-kind donations
            $financialDonationsAmount = $financialDonations->sum(fn($donation) => $donation->donateItems->sum('amount'));
            $inKindDonationsAmount = $inKindDonations->sum(fn($donation) => $donation->donateItems->sum('amount'));

            // Get sum of financial and in-kind collected donations
            $financialCollectedDonationsAmount = $financialCollectedDonations->sum(fn($donation) => $donation->donateItems->sum('amount'));
            $inKindCollectedDonationsAmount = $inKindCollectedDonations->sum(fn($donation) => $donation->donateItems->sum('amount'));


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
            ]);
        }

        return view('backend.pages.reports.donations.index');
    }

    public function notCollectedDonations()
    {

        $donationQuery = Donation::query();

        if (request()->ajax()) {
            if (request()->has('start_date') && request()->has('end_date')) {
                $startDate = request('start_date');
                $endDate = request('end_date');
                $donationQuery->whereBetween('created_at', [$startDate, $endDate]);
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
            $inKindNotCollectedDonationsAmount = $inKindNotCollectedDonations->sum(fn($donation) => $donation->donateItems->sum('amount'));

            return response()->json([
                'notCollectedDonationsCount' => $notCollectedDonations->count(),
                'financialNotCollectedDonationsCount' => $financialNotCollectedDonations->count(),
                'inKindNotCollectedDonationsCount' => $inKindNotCollectedDonations->count(),

                // Sum amounts
                'notCollectedDonationsAmount' => $notCollectedDonationsAmount,
                'financialNotCollectedDonationsAmount' => $financialNotCollectedDonationsAmount,
            ]);
        }
    }
}
