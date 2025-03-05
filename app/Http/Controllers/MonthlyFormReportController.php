<?php

namespace App\Http\Controllers;

use App\Exports\MonthlyFormsExport;
use App\Models\Donor;
use App\Models\MonthlyForm;
use App\Models\MonthlyFormDonation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MonthlyFormReportController extends Controller
{
    //
    public function index(Request $request)
    {

        $monthYear = now()->format('Y-m');
        $departmentId = $request->input('department_id');

        $donorQuery = Donor::query();

        // Filter by month
        $monthlyFormsQuery = MonthlyFormDonation::query();
        if ($monthYear) {
            $monthlyFormsQuery->whereYear('created_at', substr($monthYear, 0, 4))
                ->whereMonth('created_at', substr($monthYear, 5, 2));

            $donorQuery->whereYear('created_at', substr($monthYear, 0, 4))
                ->whereMonth('created_at', substr($monthYear, 5, 2));
        }

        if ($departmentId) {
            $monthlyFormsQuery->whereHas('monthlyForm', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });
            $donorQuery->where('department_id', operator: $departmentId);
        }

        $monthlyFormsDonations = $monthlyFormsQuery->whereHas('donation', function ($query) {
            $query->whereHas('collectingDonation');
        })->get();

        $donorsCount = $donorQuery->count();


        $monthlyFormsQuery = MonthlyForm::query();
        if ($departmentId) {
            $monthlyFormsQuery->where('department_id', $departmentId);
        }

        // Total Forms
        $allMonthlyFormsCount = $monthlyFormsQuery->count();

        // Total Amount for all Monthly Forms
        $allMonthlyFormsAmount = $monthlyFormsQuery->whereHas('items', function ($query) {
            $query->where('donation_type', 'financial');
        })->withSum(['items as total_amount' => function ($query) {
            $query->where('donation_type', 'financial');
        }], 'amount')->get()->sum('total_amount');

        // Collected and Not Collected Forms Count
        $monthlyFormsNotCollectedCount = $monthlyFormsQuery->whereNotIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))->count();
        $monthlyFormsCollectedCount = $monthlyFormsQuery->whereIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))->count();

        // Collected and Not Collected Amount
        $monthlyFormsNotCollectedAmount = $monthlyFormsQuery->whereNotIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))
            ->whereHas('items', function ($query) {
                $query->where('donation_type', 'financial');
            })
            ->withSum(['items as total_amount' => function ($query) {
                $query->where('donation_type', 'financial');
            }], 'amount')->get()->sum('total_amount');

        $monthlyFormsCollectedAmount = $monthlyFormsQuery->whereIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))
            ->whereHas('items', function ($query) {
                $query->where('donation_type', 'financial');
            })
            ->withSum(['items as total_amount' => function ($query) {
                $query->where('donation_type', 'financial');
            }], 'amount')->get()->sum('total_amount');

        // Get Donors with Collected Monthly Forms
        $donorsWithCollectedForms = Donor::whereHas('monthlyForms', function ($query) {
            $query->whereHas('donations', function ($donationQuery) {
                $donationQuery->whereHas('collectingDonation');
            });
        })->get();

        // Get Donors with Not Collected Monthly Forms
        $donorsWithNotCollectedForms = Donor::whereHas('monthlyForms', function ($query) {
            $query->whereDoesntHave('donations', function ($donationQuery) {
                $donationQuery->whereHas('collectingDonation');
            });
        })->get();

        $donorsWithForms = Donor::whereHas('monthlyForms')
            ->with(['phones', 'monthlyForms' => function ($query) use ($monthYear) {
                $query->with(['followUpDepartment', 'donations' => function ($donationQuery) use ($monthYear) {
                    $donationQuery->whereHas('collectingDonation'); // Ensures donation is collected

                    // Apply Date Filters on Donations
                    if ($monthYear) {
                        $donationQuery->whereYear('date', substr($monthYear, 0, 4))
                            ->whereMonth('date', substr($monthYear, 5, 2));
                    }
                }]);
            }])->paginate(10); // Paginate with 10 records per page
        ;


        // Add a collected_status attribute
        $donorsWithForms->transform(function ($donor) {
            $donor->collected_status = $donor->monthlyForms->flatMap->donations->isNotEmpty() ? 'collected' : 'not_collected';
            return $donor;
        });


        return view('backend.pages.reports.monthly-forms.index', compact(
            'allMonthlyFormsCount',
            'allMonthlyFormsAmount',
            'monthlyFormsCollectedCount',
            'monthlyFormsCollectedAmount',
            'monthlyFormsNotCollectedCount',
            'monthlyFormsNotCollectedAmount',
            'donorsWithCollectedForms',
            'donorsWithNotCollectedForms',
            'donorsWithForms',
            'donorsCount',
            // 'fromDate',
            // 'toDate',
            'monthYear'
        ));
    }

    public function filter(Request $request)
    {
        // $fromDate = $request->input('from_date');
        // $toDate = $request->input('to_date');
        $monthYear = $request->input('month_year');
        $departmentId = $request->input('department_id');

        $donorQuery = Donor::query();

        // Filter by month
        $monthlyFormsQuery = MonthlyFormDonation::query();
        if ($monthYear) {
            $monthlyFormsQuery->whereYear('created_at', substr($monthYear, 0, 4))
                ->whereMonth('created_at', substr($monthYear, 5, 2));

            $donorQuery->whereYear('created_at', substr($monthYear, 0, 4))
                ->whereMonth('created_at', substr($monthYear, 5, 2));
        }

        if ($departmentId) {
            $monthlyFormsQuery->whereHas('monthlyForm', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });
            $donorQuery->where('department_id', operator: $departmentId);
        }


        $monthlyFormsDonations = $monthlyFormsQuery->whereHas('donation', function ($query) {
            $query->whereHas('collectingDonation');
        })->get();

        $donorsCount = $donorQuery->count();

        $monthlyFormsQuery = MonthlyForm::whereHas('items', function ($query) {
            $query->where('donation_type', 'financial');
        });
        if ($departmentId) {
            $monthlyFormsQuery->where('department_id', $departmentId);
        }

        // Total Forms
        $allMonthlyFormsCount = $monthlyFormsQuery->count();

        // Total Amount for all Monthly Forms
        $allMonthlyFormsAmount = $monthlyFormsQuery->withSum(['items as total_amount' => function ($query) {
            $query->where('donation_type', 'financial');
        }], 'amount')->get()->sum('total_amount');

        // Collected and Not Collected Forms Count
        $monthlyFormsNotCollectedCount = $monthlyFormsQuery->whereNotIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))->count();
        $monthlyFormsCollectedCount = $monthlyFormsQuery->whereIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))->count();

        // Collected and Not Collected Amount
        $monthlyFormsNotCollectedAmount = $monthlyFormsQuery->whereNotIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))
            ->get()->sum('total_amount');
            dd($monthlyFormsQuery);


        $monthlyFormsCollectedAmount = $monthlyFormsQuery->whereIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))
            ->withSum(['items as total_amount' => function ($query) {
                $query->where('donation_type', 'financial');
            }], 'amount')->get()->sum('total_amount');

        // Donors with Collected Monthly Forms (Considering Date Filters)
        $donorsWithCollectedForms = Donor::whereHas('monthlyForms', function ($query) use ($monthYear) {
            $query->whereHas('donations', function ($donationQuery) use ($monthYear) {
                $donationQuery->whereHas('collectingDonation');

                // Apply Date Filters on Monthly Forms
                if ($monthYear) {
                    $donationQuery->whereYear('date', substr($monthYear, 0, 4))
                        ->whereMonth('date', substr($monthYear, 5, 2));
                }
            });
        })
            ->with('phones')
            ->get();

        // Donors with Not Collected Monthly Forms (Considering Date Filters)
        $donorsWithNotCollectedForms = Donor::whereHas('monthlyForms', function ($query) use ($monthYear) {
            $query->whereDoesntHave('donations', function ($donationQuery)  use ($monthYear) {
                $donationQuery->whereHas('collectingDonation');

                // Apply Date Filters on Monthly Forms
                if ($monthYear) {
                    $donationQuery->whereYear('date', substr($monthYear, 0, 4))
                        ->whereMonth('date', substr($monthYear, 5, 2));
                }
            });
        })
            ->with('phones')
            ->get();


        $donorsWithForms = Donor::whereHas('monthlyForms')
            ->with(['phones', 'monthlyForms' => function ($query) use ($monthYear) {
                $query->with(['donations' => function ($donationQuery) use ($monthYear) {
                    $donationQuery->whereHas('collectingDonation'); // Ensures donation is collected

                    // Apply Date Filters on Donations
                    if ($monthYear) {
                        $donationQuery->whereYear('date', substr($monthYear, 0, 4))
                            ->whereMonth('date', substr($monthYear, 5, 2));
                    }
                }]);
            }])->get();

        // Add a collected_status attribute
        $donorsWithForms->transform(function ($donor) {
            $donor->collected_status = $donor->monthlyForms->flatMap->donations->isNotEmpty() ? 'collected' : 'not_collected';
            return $donor;
        });


        return response()->json([
            'filteredTable' => view(
                'backend.pages.reports.monthly-forms.partials.monthly_forms_table',
                compact(
                    'allMonthlyFormsCount',
                    'allMonthlyFormsAmount',
                    'monthlyFormsCollectedCount',
                    'monthlyFormsCollectedAmount',
                    'monthlyFormsNotCollectedCount',
                    'monthlyFormsNotCollectedAmount',
                    'donorsWithCollectedForms',
                )
            )->render(),
            'donorsWithFormsTable' => view('backend.pages.reports.monthly-forms.partials.donors_with_forms_table', compact('donorsWithForms'))->render(),
            'collectedDonorsTable' => view('backend.pages.reports.monthly-forms.partials.donors_collected_table', compact('donorsWithCollectedForms'))->render(),
            'notCollectedDonorsTable' => view('backend.pages.reports.monthly-forms.partials.donors_not_collected_table', compact('donorsWithNotCollectedForms'))->render(),
        ]);
    }



    public function exportMonthlyForms(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $monthYear = $request->input('month_year'); // Format: YYYY-MM

        $query = MonthlyFormDonation::query();

        if ($monthYear) {
            $query->whereYear('created_at', substr($monthYear, 0, 4))
                ->whereMonth('created_at', substr($monthYear, 5, 2));
        }

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $monthlyFormsDonations = $query->pluck('monthly_form_id');

        $data = [
            'all_monthly_formscount' => MonthlyForm::count(),

            'all_monthly_forms_amount' => MonthlyForm::whereHas('items', function ($query) {
                $query->where('donation_type', 'financial');
            })->withSum(['items as total_amount' => function ($query) {
                $query->where('donation_type', 'financial');
            }], 'amount')->get()->sum('total_amount'),

            'monthly_forms_not_collected_count' => MonthlyForm::whereNotIn('id', $monthlyFormsDonations)->count(),

            'monthly_forms_collected_count' => MonthlyForm::whereIn('id', $monthlyFormsDonations)->count(),

            'monthly_forms_not_collected_amount' => MonthlyForm::whereNotIn('id', $monthlyFormsDonations)
                ->whereHas('items', function ($query) {
                    $query->where('donation_type', 'financial');
                })
                ->withSum(['items as total_amount' => function ($query) {
                    $query->where('donation_type', 'financial');
                }], 'amount')->get()->sum('total_amount'),

            'monthly_forms_collected_amount' => MonthlyForm::whereIn('id', $monthlyFormsDonations)
                ->whereHas('items', function ($query) {
                    $query->where('donation_type', 'financial');
                })
                ->withSum(['items as total_amount' => function ($query) {
                    $query->where('donation_type', 'financial');
                }], 'amount')->get()->sum('total_amount')
        ];

        return Excel::download(new MonthlyFormsExport($data), 'monthly_forms.xlsx');
    }
}
