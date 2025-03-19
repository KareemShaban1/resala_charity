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
        $donorQuery = Donor::query();

        // monthly forms donations query
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


        $monthlyFormsDonations = $monthlyFormsDonationsQuery->whereHas('donation', function ($query) {
            $query->whereHas('collectingDonation');
        })->get();




        $monthlyFormsQuery = MonthlyForm::whereHas('items', function ($query) {
            $query->where('donation_type', 'financial');
        });
        if ($departmentId) {
            $monthlyFormsQuery->where('department_id', $departmentId);
        }

        if ($followUpDepartmentId) {
            $monthlyFormsQuery->where('follow_up_department_id', $followUpDepartmentId);
        }

        // all monthly forms that is all monthly forms that has items with donation type "financial" 
        // Total Forms
        $allMonthlyFormsCount = $monthlyFormsQuery->count();
        $allMonthlyFormsAmount = $monthlyFormsQuery->withSum(['items as total_amount' => function ($query) {
            $query->where('donation_type', 'financial');
        }], 'amount')->get()->sum('total_amount');

        // cancelled monthly forms that is all monthly forms that cancelled and has items with donation type "financial" 
        $cancelledMonthlyFormsCount = MonthlyForm::where('status', 'cancelled')
            ->count();
        $cancelledMonthlyFormsAmount = MonthlyForm::where('status', 'cancelled')
            ->whereHas('items', function ($query) {
                $query->where('donation_type', 'financial');
            })->withSum(['items as total_amount' => function ($query) {
                $query->where('donation_type', 'financial');
            }], 'amount')->get()->sum('total_amount');


        // Not Collected Forms Count and Amount
        $monthlyFormsNotCollectedCount = (clone $monthlyFormsQuery)->whereNotIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))->count();
        $monthlyFormsNotCollectedAmount = (clone $monthlyFormsQuery)->whereNotIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))
            ->withSum(['items as total_amount' => function ($query) {
                $query->where('donation_type', 'financial');
            }], 'amount')->get()->sum('total_amount');


        // Collected Forms Count and Amount
        $monthlyFormsCollectedCount = (clone $monthlyFormsQuery)->whereIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))->count();
        $monthlyFormsCollectedAmount = (clone $monthlyFormsQuery)->whereIn('id', $monthlyFormsDonations->pluck('monthly_form_id'))
            ->withSum(['items as total_amount' => function ($query) {
                $query->where('donation_type', 'financial');
            }], 'amount')->get()->sum('total_amount');


        $donorsWithForms = Donor::whereHas('monthlyForms', function ($query) use ($departmentId, $followUpDepartmentId, $monthYear) {
            $query->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
                ->when($followUpDepartmentId, fn($q) => $q->where('follow_up_department_id', $followUpDepartmentId));
                // ->whereHas('donations', function ($donationQuery) use ($monthYear) {
                //     $donationQuery
                //         ->when(
                //             $monthYear,
                //             fn($q) =>
                //             $q->whereYear('date', substr($monthYear, 0, 4))
                //                 ->whereMonth('date', substr($monthYear, 5, 2))
                //         );
                // });
        })
            ->with([
                'phones',
                'monthlyForms' => function ($query) use ($monthYear, $departmentId, $followUpDepartmentId,) {
                    $query->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
                        ->when($followUpDepartmentId, fn($q) => $q->where('follow_up_department_id', $followUpDepartmentId));
                        // ->with([
                        //     'donations' => function ($donationQuery) use ($monthYear) {
                        //         $donationQuery->with('donateItems.donationCategory')
                        //             ->whereHas('collectingDonation')
                        //             ->when(
                        //                 $monthYear,
                        //                 fn($q) =>
                        //                 $q->whereYear('date', substr($monthYear, 0, 4))
                        //                     ->whereMonth('date', substr($monthYear, 5, 2))
                        //             );
                        //     }
                        // ]);
                }
            ])
            ->paginate(10);

        // ✅ Optimized transformation logic
        $donorsWithForms->each(function ($donor) {
            $donor->collected_status = optional($donor->monthlyForms)
                ->flatMap->donations
                ->isNotEmpty() ? 'collected' : 'not_collected';
        });



        return response()->json([
            'filteredTable' => view(
                'backend.pages.reports.monthly-forms.partials.monthly_forms_table',
                compact(
                    'allMonthlyFormsCount',
                    'allMonthlyFormsAmount',
                    'cancelledMonthlyFormsCount',
                    'cancelledMonthlyFormsAmount',
                    'monthlyFormsCollectedCount',
                    'monthlyFormsCollectedAmount',
                    'monthlyFormsNotCollectedCount',
                    'monthlyFormsNotCollectedAmount',
                )
            )->render(),
            'donorsWithFormsTable' => view('backend.pages.reports.monthly-forms.partials.donors_with_forms_table', compact('donorsWithForms'))->render(),
            'donorsPaginationLinks' => $donorsWithForms->links('pagination::bootstrap-4')->toHtml(),

        ]);
    }


    // public function index(Request $request)
    // {
    //     $monthYear = now()->format('Y-m');
    //     $filters = $request->only(['department_id', 'follow_up_department_id', 'status', 'area_id']);

    //     // Get Donors Count
    //     $donorQuery = $this->applyDonorFilters(Donor::query(), $filters, $monthYear);
    //     $donorsCount = $donorQuery->count();

    //     // Get Monthly Forms Data
    //     $monthlyFormsQuery = $this->applyMonthlyFormFilters(MonthlyForm::query(), $filters, $monthYear);
    //     $monthlyFormsDonations = $this->getMonthlyFormsDonations($filters, $monthYear);

    //     $allMonthlyFormsCount = $monthlyFormsQuery->count();
    //     $allMonthlyFormsAmount = $monthlyFormsQuery->withSum('items', 'amount')->get()->sum('items_sum_amount');

    //     $cancelledMonthlyFormsCount = MonthlyForm::where('status', 'cancelled')->count();
    //     $cancelledMonthlyFormsAmount = MonthlyForm::where('status', 'cancelled')
    //         ->whereHas('items', fn($query) => $query->where('donation_type', 'financial'))
    //         ->withSum('items', 'amount')->get()->sum('items_sum_amount');

    //     $collectedFormsIds = $monthlyFormsDonations->pluck('monthly_form_id');
    //     $monthlyFormsCollectedCount = $monthlyFormsQuery->whereIn('id', $collectedFormsIds)->count();
    //     $monthlyFormsCollectedAmount = $monthlyFormsQuery->whereIn('id', $collectedFormsIds)
    //     ->withSum('items', 'amount')->get()->sum('items_sum_amount');

    //     $monthlyFormsNotCollectedCount = $monthlyFormsQuery->whereNotIn('id', $collectedFormsIds)->count();
    //     $monthlyFormsNotCollectedAmount = $monthlyFormsQuery->whereNotIn('id', $collectedFormsIds)
    //     ->withSum('items', 'amount')->get()->sum('items_sum_amount');

    //     // Fetch Donors with Forms
    //     $donorsWithForms = $this->getDonorsWithForms($filters, $monthYear);

    //     return view('backend.pages.reports.monthly-forms.index', compact(
    //         'allMonthlyFormsCount',
    //         'allMonthlyFormsAmount',
    //         'cancelledMonthlyFormsCount',
    //         'cancelledMonthlyFormsAmount',
    //         'monthlyFormsCollectedCount',
    //         'monthlyFormsCollectedAmount',
    //         'monthlyFormsNotCollectedCount',
    //         'monthlyFormsNotCollectedAmount',
    //         'donorsWithForms',
    //         'donorsCount',
    //         'monthYear'
    //     ));
    // }

    // /**
    //  * Apply filters to Donor Query
    //  */
    // private function applyDonorFilters($query, $filters, $monthYear)
    // {
    //     if ($monthYear) {
    //         $query->whereYear('created_at', substr($monthYear, 0, 4))
    //             ->whereMonth('created_at', substr($monthYear, 5, 2));
    //     }
    //     if (!empty($filters['department_id'])) {
    //         $query->where('department_id', $filters['department_id']);
    //     }
    //     if (!empty($filters['follow_up_department_id'])) {
    //         $query->whereHas('monthlyForms', fn($q) => $q->where('follow_up_department_id', $filters['follow_up_department_id']));
    //     }
    //     return $query;
    // }

    // /**
    //  * Apply filters to Monthly Forms Query
    //  */
    // private function applyMonthlyFormFilters($query, $filters, $monthYear)
    // {
    //     $query->whereHas('items', fn($q) => $q->where('donation_type', 'financial'));

    //     if ($monthYear) {
    //         $query->whereYear('created_at', substr($monthYear, 0, 4))
    //             ->whereMonth('created_at', substr($monthYear, 5, 2));
    //     }
    //     if (!empty($filters['department_id'])) {
    //         $query->where('department_id', $filters['department_id']);
    //     }
    //     if (!empty($filters['follow_up_department_id'])) {
    //         $query->where('follow_up_department_id', $filters['follow_up_department_id']);
    //     }
    //     return $query;
    // }

    // /**
    //  * Get Monthly Forms Donations
    //  */
    // private function getMonthlyFormsDonations($filters, $monthYear)
    // {
    //     $query = MonthlyFormDonation::query()->whereHas('donation', fn($q) => $q->whereHas('collectingDonation'));

    //     if ($monthYear) {
    //         $query->whereYear('created_at', substr($monthYear, 0, 4))
    //             ->whereMonth('created_at', substr($monthYear, 5, 2));
    //     }
    //     if (!empty($filters['department_id'])) {
    //         $query->whereHas('monthlyForm', fn($q) => $q->where('department_id', $filters['department_id']));
    //     }
    //     if (!empty($filters['follow_up_department_id'])) {
    //         $query->whereHas('monthlyForm', fn($q) => $q->where('follow_up_department_id', $filters['follow_up_department_id']));
    //     }

    //     return $query->get();
    // }

    // /**
    //  * Get Donors with Forms and Collected Status
    //  */
    // private function getDonorsWithForms($filters, $monthYear)
    // {
    //     $query = Donor::whereHas('monthlyForms', function ($q) use ($filters) {
    //         if (!empty($filters['department_id'])) {
    //             $q->where('department_id', $filters['department_id']);
    //         }
    //         if (!empty($filters['follow_up_department_id'])) {
    //             $q->where('follow_up_department_id', $filters['follow_up_department_id']);
    //         }
    //     })->with([
    //         'phones',
    //         'monthlyForms' => function ($q) use ($monthYear) {
    //             $q->with(['followUpDepartment', 'donations' => function ($donationQuery) use ($monthYear) {
    //                 $donationQuery->whereHas('collectingDonation');
    //                 if ($monthYear) {
    //                     $donationQuery->whereYear('date', substr($monthYear, 0, 4))
    //                         ->whereMonth('date', substr($monthYear, 5, 2));
    //                 }
    //             }]);
    //         }
    //     ]);

    //     $donors = $query->paginate(10);

    //     $donors->transform(function ($donor) {
    //         $donor->collected_status = $donor->monthlyForms->flatMap->donations->isNotEmpty() ? 'collected' : 'not_collected';
    //         return $donor;
    //     });

    //     return $donors;
    // }


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
