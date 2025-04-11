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

    public function index(Request $request)
    {

        $monthYear = now()->format('Y-m');
        $departmentId = $request->input('department_id');
        $followUpDepartmentId = $request->input('follow_up_department_id');
        $status = $request->input('status');
        $areaId = $request->input('area_id');

        $donorQuery = Donor::query();

        // Filter by month
        $monthlyFormsDonationsQuery = MonthlyFormDonation::query();

        if ($monthYear) {
            $monthlyFormsDonationsQuery
                ->where('month', substr($monthYear, 5, 2));
            $donorQuery->whereYear('created_at', substr($monthYear, 0, 4))
                ->whereMonth('created_at', substr($monthYear, 5, 2));
        }
        if ($departmentId) {
            $monthlyFormsDonationsQuery->whereHas('monthlyForm', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });
            $donorQuery->where('department_id', operator: $departmentId);
        }

        if ($followUpDepartmentId) {
            $monthlyFormsDonationsQuery->whereHas('monthlyForm', function ($query) use ($followUpDepartmentId) {
                $query->where('follow_up_department_id', $followUpDepartmentId);
            });
            $donorQuery->whereHas('monthlyForms', function ($query) use ($followUpDepartmentId) {
                $query->where('follow_up_department_id', $followUpDepartmentId);
            });
        }

        if ($status) {
            $monthlyFormsDonationsQuery->whereHas('donation', function ($query) use ($status) {
                $query->where('status', $status);
            });
        }

        $monthlyFormsDonations = $monthlyFormsDonationsQuery->whereHas('donation', function ($query) {
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
        $allMonthlyFormsAmount = $monthlyFormsQuery->withSum(['items as total_amount' => function ($query) {
            $query->where('donation_type', 'financial');
        }], 'amount')->get()->sum('total_amount');

        $cancelledMonthlyFormsCount = MonthlyForm::where('status', 'cancelled')
            ->count();
        $cancelledMonthlyFormsAmount = MonthlyForm::where('status', 'cancelled')
            ->whereHas('items', function ($query) {
                $query->where('donation_type', 'financial');
            })->withSum(['items as total_amount' => function ($query) {
                $query->where('donation_type', 'financial');
            }], 'amount')->get()->sum('total_amount');


        // Collected and Not Collected Forms Count
        $monthlyFormsNotCollectedCount = (clone $monthlyFormsQuery)->whereNotIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))->count();
        $monthlyFormsNotCollectedAmount = (clone $monthlyFormsQuery)->whereNotIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))
            ->withSum(['items as total_amount' => function ($query) {
                $query->where('donation_type', 'financial');
            }], 'amount')->get()->sum('total_amount');


        $monthlyFormsCollectedCount = (clone $monthlyFormsQuery)->whereIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))->count();
        $monthlyFormsCollectedAmount = (clone $monthlyFormsQuery)->whereIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))
            ->withSum(['items as total_amount' => function ($query) {
                $query->where('donation_type', 'financial');
            }], 'amount')->get()->sum('total_amount');


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


        // Add a collected_status attribute
        $donorsWithForms->transform(function ($donor) {
            $donor->collected_status = $donor->monthlyForms->flatMap->donations->isNotEmpty() ? 'collected' : 'not_collected';
            return $donor;
        });


        return view('backend.pages.reports.monthly-forms.index', compact(
            'allMonthlyFormsCount',
            'allMonthlyFormsAmount',
            'cancelledMonthlyFormsCount',
            'cancelledMonthlyFormsAmount',
            'monthlyFormsCollectedCount',
            'monthlyFormsCollectedAmount',
            'monthlyFormsNotCollectedCount',
            'monthlyFormsNotCollectedAmount',
            'donorsWithForms',
            'donorsCount',
            'monthYear'
        ));
    }

    public function filter(Request $request)
    {
        $monthYear = $request->input('month_year');
        $departmentId = $request->input('department_id');
        $followUpDepartmentId = $request->input('follow_up_department_id');
        $status = $request->input('status');
        $areaId = $request->input('area_id');


        // Filter by month
        $monthlyFormsDonationsQuery = MonthlyFormDonation::query();

        $monthlyFormsQuery = MonthlyForm::whereHas('items', function ($query) {
            $query->where('donation_type', 'financial');
        });

        if ($monthYear) {
            $monthlyFormsDonationsQuery
                ->where('month', substr($monthYear, 5, 2));
        }


        if ($departmentId) {
            $monthlyFormsDonationsQuery->whereHas('monthlyForm', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });
        }

        if ($followUpDepartmentId) {
            $monthlyFormsDonationsQuery->whereHas('monthlyForm', function ($query) use ($followUpDepartmentId) {
                $query->where('follow_up_department_id', $followUpDepartmentId);
            });
        }

        if ($status) {
            $monthlyFormsDonationsQuery->whereHas('donation', function ($query) use ($status) {
                $query->where('status', $status);
            });
        }




        // Total Forms
        $allMonthlyFormsCount = $monthlyFormsQuery->count();
        $allMonthlyFormsAmount = $monthlyFormsQuery->withSum(['items as total_amount' => function ($query) {
            $query->where('donation_type', 'financial');
        }], 'amount')->get()->sum('total_amount');

        $cancelledMonthlyFormsCount = MonthlyForm::where('status', 'cancelled')
            ->count();
        $cancelledMonthlyFormsAmount = MonthlyForm::where('status', 'cancelled')
            ->whereHas('items', function ($query) {
                $query->where('donation_type', 'financial');
            })->withSum(['items as total_amount' => function ($query) {
                $query->where('donation_type', 'financial');
            }], 'amount')->get()->sum('total_amount');



        $collectedMonthlyFormsDonations = $monthlyFormsDonationsQuery->whereHas('donation', function ($query) {
            $query->whereHas('collectingDonation');
        })->get();

        // Not Collected Forms Count and Amount
        $monthlyFormsNotCollectedCount = (clone $monthlyFormsQuery)->whereNotIn('id', $collectedMonthlyFormsDonations->pluck('monthly_form_id'))->count();
        $monthlyFormsNotCollectedAmount = (clone $monthlyFormsQuery)->whereNotIn('id', $collectedMonthlyFormsDonations->pluck('monthly_form_id'))
            ->withSum(['items as total_amount' => function ($query) {
                $query->where('donation_type', 'financial');
            }], 'amount')->get()->sum('total_amount');


        // Collected Forms Count and Amount
        $monthlyFormsCollectedCount = (clone $monthlyFormsQuery)->whereIn('id', $collectedMonthlyFormsDonations->pluck('monthly_form_id'))->count();
        $monthlyFormsCollectedAmount = (clone $monthlyFormsQuery)->whereIn('id', $collectedMonthlyFormsDonations->pluck('monthly_form_id'))
            ->withSum(['items as total_amount' => function ($query) {
                $query->where('donation_type', 'financial');
            }], 'amount')->get()->sum('total_amount');


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


        // Add a collected_status attribute
        $donorsWithForms->transform(function ($donor) {
            $donor->collected_status = $donor->monthlyForms->flatMap->donations->isNotEmpty() ? 'collected' : 'not_collected';
            return $donor;
        });


        return response()->json([
            // 'filteredTable' => view('backend.pages.reports.monthly-forms.partials.monthly_forms_table', [
            //     'allMonthlyFormsCount' => $allMonthlyFormsCount,
            //     'allMonthlyFormsAmount' => $allMonthlyFormsAmount,
            //     'cancelledMonthlyFormsCount' => $cancelledMonthlyFormsCount,
            //     'cancelledMonthlyFormsAmount' => $cancelledMonthlyFormsAmount,
            //     'monthlyFormsCollectedCount' => $monthlyFormsCollectedCount,
            //     'monthlyFormsCollectedAmount' => $monthlyFormsCollectedAmount,
            //     'monthlyFormsNotCollectedCount' => $monthlyFormsNotCollectedCount,
            //     'monthlyFormsNotCollectedAmount' => $monthlyFormsNotCollectedAmount,
            // ])->render(),
            'allMonthlyFormsCount' => $allMonthlyFormsCount,
            'allMonthlyFormsAmount' => $allMonthlyFormsAmount,
            'cancelledMonthlyFormsCount' => $cancelledMonthlyFormsCount,
            'cancelledMonthlyFormsAmount' => $cancelledMonthlyFormsAmount,
            'monthlyFormsCollectedCount' => $monthlyFormsCollectedCount,
            'monthlyFormsCollectedAmount' => $monthlyFormsCollectedAmount,
            'monthlyFormsNotCollectedCount' => $monthlyFormsNotCollectedCount,
            'monthlyFormsNotCollectedAmount' => $monthlyFormsNotCollectedAmount,
            'donorsWithFormsTable' => view('backend.pages.reports.monthly-forms.partials.donors_with_forms_table', compact('donorsWithForms'))->render(),
            'donorsPaginationLinks' => (string) $donorsWithForms->links('pagination::bootstrap-4'),
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
