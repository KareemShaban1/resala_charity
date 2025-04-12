<?php

namespace App\Http\Controllers;

use App\Models\CollectingLine;
use App\Http\Requests\StoreCollectingLineRequest;
use App\Http\Requests\UpdateCollectingLineRequest;
use App\Models\Area;
use App\Models\AreaGroup;
use App\Models\Department;
use App\Models\Donation;
use App\Models\DonationCategory;
use App\Models\Donor;
use App\Models\Employee;
use App\Models\MonthlyForm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;

class CollectingLineController extends Controller
{

    public function index()
    {

        $representatives = Employee::whereHas('department', function ($q) {
            $q->where('name', 'المناديب');
        })->get();
        $drivers = Employee::whereHas('department', function ($q) {
            $q->where('name', 'السائقين');
        })->get();
        // $employees = Employee::whereHas(
        //     'department',
        //     function ($q) {
        //         $q->where('name', 'استمارات وعناوين');
        //     }
        // )->get();
        $employees = Employee::all();
        $areaGroups = AreaGroup::all();
        $areas = Area::all();
        $donationCategories = DonationCategory::all();
        $donors = Donor::all();

        return view(
            'backend.pages.collecting-lines.allCollectingLines',
            compact('representatives', 'drivers', 'donors','employees', 'areas', 'areaGroups', 'donationCategories')
        );
    }
    /**
     * Display a listing of the resource.
     */
    public function addCollectingLines()
    {

        $representatives = Employee::whereHas('department', function ($q) {
            $q->where('name', 'المناديب');
        })->get();
        $drivers = Employee::whereHas('department', function ($q) {
            $q->where('name', 'السائقين');
        })->get();
        // $employees = Employee::whereHas(
        //     'department',
        //     function ($q) {
        //         $q->where('name', 'استمارات وعناوين');
        //     }
        // )->get();
        $employees = Employee::all();
        $areaGroups = AreaGroup::all();
        $areas = Area::all();
        $donationCategories = DonationCategory::all();
        $donors = Donor::all();
        $followUpDepartments = Department::all();

        return view(
            'backend.pages.collecting-lines.addCollectingLines',
            compact('representatives', 'drivers', 'donors', 'followUpDepartments', 'employees', 'areaGroups', 'areas', 'donationCategories')
        );
    }

    /**
     * Fetch collecting lines data for DataTables.
     */
    public function getCollectingLinesData(Request $request)
    {
        if ($request->ajax()) {
            $data = CollectingLine::query();


            // Date filter
            if (request()->has('date_filter')) {
                $dateFilter = request('date_filter');
                $startDate = request('start_date');
                $endDate = request('end_date');

                if ($dateFilter === 'today') {
                    $data->whereDate('collecting_date',  today());
                } elseif ($dateFilter === 'week') {
                    $data->whereBetween('collecting_date', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($dateFilter === 'month') {
                    $data->whereBetween('collecting_date', [now()->startOfMonth(), now()->endOfMonth()]);
                } elseif ($dateFilter === 'range' && $startDate && $endDate) {
                    $data->whereBetween('collecting_date', [$startDate, $endDate]);
                }
            }

            if ($request->has('area_group') && $request->area_group != '') {
                $data->where('area_group_id', $request->area_group);
            }

            return DataTables::of($data)
                ->addColumn('areaGroup', function ($row) {
                    return $row->areaGroup->name;
                })
                ->addColumn('representative', function ($row) {
                    return $row->representative->name;
                })
                ->addColumn('driver', function ($row) {
                    return $row->driver->name;
                })
                ->addColumn('employee', function ($row) {
                    return $row->employee->name;
                })
                ->addColumn('action', function ($row) {
                    $btn = '';

                    // Edit Button (only for users with 'update collecting line' permission)
                    if (auth()->user()->can('update collecting line')) {
                        $btn .= '<button class="edit-btn btn btn-sm btn-primary" 
                                 data-id="' . $row->id . '"
                                 data-area-group-id="' . $row->area_group_id . '"
                                 data-representative-id="' . $row->representative_id . '"
                                 data-driver-id="' . $row->driver_id . '"
                                 data-employee-id="' . $row->employee_id . '"
                                 data-collecting-date="' . $row->collecting_date . '">' . __('Edit') . '</button>';
                    }

                    // Delete Button (only for users with 'delete collecting line' permission)
                    if (auth()->user()->can('delete collecting line')) {
                        $btn .= ' <button class="delete-btn btn btn-sm btn-danger" data-id="' . $row->id . '">' . __('Delete') . '</button>';
                    }

                    // View Donations Button (only for users with 'view donations' permission)
                    if (auth()->user()->can('view donations')) {
                        $btn .= ' <button class="btn btn-sm btn-info view-donations-btn" data-id="' . $row->id . '">' . __('View Donations') . '</button>';
                    }

                    // View Collecting Line Button (only for users with 'view collecting line' permission)
                    if (auth()->user()->can('view collecting lines')) {
                        $btn .= ' <a href="' . route('collecting-lines.export-pdf', ['collecting_line_id' => $row->id]) . '" 
                                  class="btn btn-sm btn-info">' . __('View Collecting Line') . '</a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function getCollectingLinesByDate(Request $request)
    {

        $data = CollectingLine::with('areaGroup');
        // if ($request->has('date') && $request->date != '') {
        //     $data->whereDate('collecting_date', '=', $request->date);
        // }
        if (request()->has('date_filter')) {
            $dateFilter = request('date_filter');
            $startDate = request('start_date');
            $endDate = request('end_date');

            if ($dateFilter === 'today') {
                $data->whereDate('collecting_date', operator: today());
            } elseif ($dateFilter === 'week') {
                $data->whereBetween('collecting_date', values: [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($dateFilter === 'month') {
                $data->whereBetween('collecting_date', [now()->startOfMonth(), now()->endOfMonth()]);
            } elseif ($dateFilter === 'range' && $startDate && $endDate) {
                $data->whereBetween('collecting_date', [$startDate, $endDate]);
            }
        }
        if ($request->has('area_group') && $request->area_group != '') {
            $data->where('area_group_id', $request->area_group);
        }
        $data = $data->get();

        return response()->json([
            'success' => true,
            'message' => __('messages.' . class_basename(CollectingLine::class) . ' retrieved successfully'),
            'data' => $data
        ]);
    }

    public function showCollectingLine($id)
    {
        $collectingLine = CollectingLine::with('areaGroup', 'representative', 'driver', 'employee')->findOrFail($id);

        $donors = Donor::all();
        $donationCategories = DonationCategory::all();
        $employees = Employee::all();
        
        return view(
            'backend.pages.collecting-lines.showCollectingLine',
            compact('collectingLine', 'donors', 'donationCategories', 'employees')
        );
    }

    public function getDonationsData(Request $request)
    {

        if ($request->ajax()) {
            $data = Donation::query()
                ->selectRaw('
                donations.id,
                donations.donor_id,
                donations.status,
                donations.created_by,
                donations.created_at,
                donations.notes,
                donations.donation_type,
                donation_collectings.collecting_date,
                donation_collectings.in_kind_receipt_number,
                donors.name as donor_name,
                donors.parent_id,
                CASE 
                    WHEN donors.parent_id IS NOT NULL THEN donors.parent_id
                    ELSE donors.id
                END as parent_donor_group_id,
                CASE 
                    WHEN donors.parent_id IS NOT NULL THEN 1  -- Child
                    ELSE 0  -- Parent
                END as is_child_order,
                areas.name as area_name,
                donors.address,
                GROUP_CONCAT(DISTINCT donor_phones.phone_number SEPARATOR ", ") as phone_numbers,
                (SELECT MAX(dc.collecting_date) 
                FROM donation_collectings dc 
                INNER JOIN donations d2 ON d2.id = dc.donation_id 
                WHERE d2.donor_id = donations.donor_id
                ) as last_donation_date
            ')
                ->leftJoin('donors', 'donations.donor_id', '=', 'donors.id')
                ->leftJoin('donation_collectings', 'donations.id', '=', 'donation_collectings.donation_id')
                ->leftJoin('areas', 'donors.area_id', '=', 'areas.id')
                ->leftJoin('donor_phones', 'donors.id', '=', 'donor_phones.donor_id')
                ->leftJoin('monthly_form_donations', 'donations.id', '=', 'monthly_form_donations.donation_id')
                ->leftJoin('departments', 'donors.department_id', '=', 'departments.id')
                ->with('donor', 'donateItems')
                ->whereDoesntHave('collectingLines') // Exclude donations already assigned to a collecting line
                ->whereNot('donations.status', 'collected')
                ->groupBy(
                    'donations.donor_id',
                    'donors.id',
                    'donors.name',
                    'donors.parent_id',
                    'areas.name',
                    'donors.address',
                    'donations.id',
                    'donations.notes',
                    'donations.created_at',
                    'donations.status',
                    'donations.created_by',
                    'donations.donation_type',
                    'donation_collectings.collecting_date',
                    'donation_collectings.in_kind_receipt_number'
                )
                ->orderBy('parent_donor_group_id', 'asc') // Group children under parents
                ->orderBy('is_child_order', 'asc'); // Ensure parents appear above their children; // Ensures Parent first, then Child under it



            // if ($request->has('date') && $request->date != '') {
            //     $data->whereDate('donations.date',  $request->date);
            // }
            if (request()->has('date_filter')) {
                $dateFilter = request('date_filter');
                $startDate = request('start_date');
                $endDate = request('end_date');

                if ($dateFilter === 'today') {
                    $data->whereDate('donations.date', operator: today());
                } elseif ($dateFilter === 'week') {
                    $data->whereBetween('donations.date', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($dateFilter === 'month') {
                    $data->whereBetween('donations.date', [now()->startOfMonth(), now()->endOfMonth()]);
                } elseif ($dateFilter === 'range' && $startDate && $endDate) {
                    $data->whereBetween('donations.date', [$startDate, $endDate]);
                }
            }

            if ($request->has('area_group') && $request->area_group != '') {
                $data->whereHas('donor.area.areaGroups', function ($q) use ($request) {
                    $q->where('area_groups.id', $request->area_group);
                });
            }
            if ($request->has('area') && $request->area != '') {
                $data->whereHas('donor.area', function ($q) use ($request) {
                    $q->where('areas.id', $request->area);
                });
            }

            return DataTables::of($data)
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('donors.name', 'LIKE', "%{$keyword}%");
                })
                ->filterColumn('area', function ($query, $keyword) {
                    $query->where('areas.name', 'LIKE', "%{$keyword}%");
                })
                ->filterColumn('address', function ($query, $keyword) {
                    $query->where('donors.address', 'LIKE', "%{$keyword}%");
                })
                ->filterColumn('phones', function ($query, $keyword) {
                    $query->where('donor_phones.phone_number', 'LIKE', "%{$keyword}%");
                })
                ->filterColumn('department', function ($query, $keyword) {
                    $query->where('donors.department.name', 'LIKE', "%{$keyword}%");
                })
                ->filterColumn('created_by', function ($query, $keyword) {
                    $query->whereHas('createdBy', function ($q) use ($keyword) {
                        $q->where('name', 'LIKE', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function ($item) {
                    return '
                    <div class="d-flex gap-2">
                        <a href="javascript:void(0);" onclick="donationDetails(' . $item->id . ')"
                        class="btn btn-sm btn-light">
                            <i class="mdi mdi-eye"></i>
                        </a>
                        <a href="javascript:void(0);" onclick="editDonation(' . $item->id . ')"
                        class="btn btn-sm btn-info">
                            <i class="mdi mdi-square-edit-outline"></i>
                        </a>
                        <button class="btn btn-sm btn-primary assign-btn" data-id="' . $item->id . '">
                            ' . __('Add To Collecting Line') . '
                        </button>
                    </div>
                ';
                })
                ->addColumn('name', function ($item) {
                    return $item->donor->name ?? '';
                })
                ->addColumn('area', function ($item) {
                    return $item->donor->area->name ?? '';
                })
                ->addColumn('address', function ($item) {
                    return $item->donor->address;
                })
                ->addColumn('monthly_donation_day', function ($item) {
                    return $item->donor?->monthly_donation_day ?? 0;
                })
                ->addColumn('department', function ($item) {
                    return $item->donor?->department->name ?? '';
                })
                ->addColumn('phones', function ($item) {
                    // return $item->donor?->phones->isNotEmpty() ?
                    //     $item->donor->phones->map(function ($phone) {
                    //         return $phone->phone_number . ' (' . ucfirst($phone->phone_type) . ')';
                    //     })->implode(', ') : 'N/A';
                    return $item->donor?->phones->isNotEmpty() ?
                        $item->donor->phones->map(function ($phone) {
                            return $phone->phone_number;
                        })->implode(', ') : 'N/A';
                })
                ->addColumn('donateItems', function ($item) {
                    return $item->donateItems->map(function ($donate) use ($item) {
                        if ($item->donation_type === 'financial') {
                            return '<strong class="donation-type financial">' . __('Financial Donation') . ':</strong> ' .
                                ($donate->donationCategory->name ?? 'N/A') . ' - ' . $donate->amount;
                        } elseif ($item->donation_type === 'inKind') {
                            return '<strong class="donation-type in-kind">' . __('inKind Donation') . ':</strong> ' .
                                ($donate->item_name ?? 'N/A') . ' - ' . $donate->amount;
                        } elseif ($item->donation_type === 'both') {
                            if (isset($donate->donation_category_id) && isset($donate->amount)) {
                                return '<strong class="donation-type financial">' . __('Financial Donation') . ':</strong> ' .
                                    ($donate->donationCategory->name ?? 'N/A') . ' - ' . $donate->amount;
                            }
                            if (isset($donate->item_name) && isset($donate->amount)) {
                                return '<strong class="donation-type in-kind">' . __('inKind Donation') . ':</strong> ' .
                                    ($donate->item_name ?? 'N/A') . ' - ' . $donate->amount;
                            }
                        }
                        return '';
                    })->implode('<br>');
                })
                ->addColumn('receipt_number', function ($item) {
                    if ($item->collectingDonation) {
                        if ($item->donation_type === 'inKind') {
                            return '<span class="text-danger">' . __('Financial Receipt Number') . ' : ' . $item->collectingDonation?->in_kind_receipt_number . '</span>';
                        } elseif ($item->donation_type === 'financial') {
                            return ' <span class="text-success">' .  __('In Kind Receipt Number') . ' : ' . $item->collectingDonation?->financial_receipt_number . '</span>';
                        } else {
                            return '<span class="text-danger">' . __('Financial Receipt Number') . ' : ' . $item->collectingDonation?->financial_receipt_number . '</span>
                        <br>
                        <span class="text-success">' .  __('In Kind Receipt Number') . ' : ' . $item->collectingDonation?->in_kind_receipt_number . '</span>';
                        }
                    }
                })
                ->addColumn('collected', function ($item) {
                    if ($item->collectingDonation) {
                        return ' <span class="text-success">' .  __('Collected') . '</span>';
                    } else {
                        return ' <span class="text-danger">' .  __('Not Collected') . '</span><br>' . __('') . '';
                    }
                })
                ->addColumn('is_child', function ($item) {
                    // Check if this donor is referenced as a parent by any other donor
                    $isParent = Donor::where('parent_id', $item->donor_id)->exists();

                    // If donor has a parent_id, they are a child
                    if (!is_null($item->parent_id)) {
                        return 'Child';
                    }

                    // If donor has no parent_id but is referenced as a parent by others, they are a Parent
                    if ($isParent) {
                        return 'Parent';
                    }

                    // If neither condition is met, return 'Other'
                    return 'Other';
                })
                ->addColumn('last_donation_date', function ($item) {
                    return $item->last_donation_date ? Carbon::parse($item->last_donation_date)->format('Y-m-d') : null;
                })
                ->addColumn('created_by', function ($item) {
                    return $item->createdBy->name ?? null;
                })
                ->rawColumns(['action', 'donateItems', 'receipt_number', 'collected'])
                ->make(true);
        }
    }

    public function getMonthlyFormsData(Request $request)
    {

        $query = MonthlyForm::query()
            ->selectRaw('
        monthly_forms.id,
        monthly_forms.donor_id,
        monthly_forms.collecting_donation_way,
        monthly_forms.created_at,
        monthly_forms.status,
        monthly_forms.cancellation_reason,
        monthly_forms.cancellation_date,
        donors.name as donor_name,
        donors.monthly_donation_day,
        areas.name as area_name,
        donors.address,
        donors.parent_id,
        GROUP_CONCAT(DISTINCT donor_phones.phone_number SEPARATOR ", ") as phone_numbers,
        (SELECT donation_collectings.collecting_date 
        FROM monthly_form_donations
        JOIN donations ON donations.id = monthly_form_donations.donation_id
        JOIN donation_collectings ON donation_collectings.donation_id = donations.id
        WHERE monthly_form_donations.monthly_form_id = monthly_forms.id
        ORDER BY donation_collectings.collecting_date DESC
        LIMIT 1) as last_donation_date,
        CASE 
            WHEN donors.parent_id IS NOT NULL THEN donors.parent_id
            ELSE donors.id
        END as parent_donor_group_id,
        CASE 
            WHEN donors.parent_id IS NOT NULL THEN 1  -- Child
            ELSE 0  -- Parent
        END as is_child_order
        ')
            ->leftJoin('donors', 'monthly_forms.donor_id', '=', 'donors.id')
            ->leftJoin('areas', 'donors.area_id', '=', 'areas.id')
            ->leftJoin('donor_phones', 'donors.id', '=', 'donor_phones.donor_id')
            ->leftJoin('monthly_form_donations', 'monthly_forms.id', '=', 'monthly_form_donations.monthly_form_id')
            ->where('monthly_forms.status', 'ongoing')
            ->with('donor', 'items')
            ->groupBy(
                'monthly_forms.donor_id',
                'donors.id',
                'donors.name',
                'areas.name',
                'donors.address',
                'donors.monthly_donation_day',
                'monthly_forms.id',
                'monthly_forms.created_at',
                'monthly_forms.collecting_donation_way',
                'monthly_forms.status',
                'monthly_forms.cancellation_reason',
                'monthly_forms.cancellation_date',
                'donors.parent_id'
            )
            ->orderBy('parent_donor_group_id', 'asc') // Group children under parents
            ->orderBy('is_child_order', 'asc'); // Ensure parents appear above their children; // Ensures Parent first, then Child under it


        // Apply date filter conditions
        if ($request->has('date_filter')) {
            $dateFilter = $request->date_filter;
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            if ($dateFilter === 'today') {
                $day = now()->format('j'); // Get today's day without leading zeros
                $month = now()->format('m'); // Get current month as two digits (e.g., '02' for February)

                $query->whereHas('donor', function ($q) use ($day) {
                    $q->where('donors.monthly_donation_day', $day);
                });

                // Exclude records where a donation exists in the same month
                $query->whereNotExists(function ($subQuery) use ($month) {
                    $subQuery->select(DB::raw(1))
                        ->from('monthly_form_donations')
                        ->whereColumn('monthly_form_donations.monthly_form_id', 'monthly_forms.id')
                        ->whereMonth('monthly_form_donations.donation_date', $month);
                });
            } elseif ($dateFilter === 'week') {
                $weekDays = collect(range(now()->startOfWeek()->format('j'), now()->endOfWeek()->format('j')))
                    ->map(fn($d) => (int) $d);
                $month = now()->format('m');

                $query->whereHas('donor', function ($q) use ($weekDays) {
                    $q->whereIn('donors.monthly_donation_day', $weekDays);
                });

                $query->whereNotExists(function ($subQuery) use ($month) {
                    $subQuery->select(DB::raw(1))
                        ->from('monthly_form_donations')
                        ->whereColumn('monthly_form_donations.monthly_form_id', 'monthly_forms.id')
                        ->whereMonth('monthly_form_donations.donation_date', $month);
                });
            } elseif ($dateFilter === 'month') {
                $monthDays = collect(range(now()->startOfMonth()->format('j'), now()->endOfMonth()->format('j')))->map(fn($d) => (int) $d);

                $month = now()->format('m');

                $query->whereHas('donor', function ($q) use ($monthDays) {
                    $q->whereIn('donors.monthly_donation_day', $monthDays);
                });

                $query->whereNotExists(function ($subQuery) use ($month) {
                    $subQuery->select(DB::raw(1))
                        ->from('monthly_form_donations')
                        ->whereColumn('monthly_form_donations.monthly_form_id', 'monthly_forms.id')
                        ->whereMonth('monthly_form_donations.donation_date', $month);
                });
            } elseif ($dateFilter === 'range' && $startDate && $endDate) {
                $startMonth = date('m', strtotime($startDate));
                $endMonth = date('m', strtotime($endDate));
                $daysInRange = collect(range((int)date('j', strtotime($startDate)), (int)date('j', strtotime($endDate))));

                $query->whereHas('donor', function ($q) use ($daysInRange) {
                    $q->whereIn('donors.monthly_donation_day', $daysInRange);
                });

                $query->whereNotExists(function ($subQuery) use ($startMonth, $endMonth) {
                    $subQuery->select(DB::raw(1))
                        ->from('monthly_form_donations')
                        ->whereColumn('monthly_form_donations.monthly_form_id', 'monthly_forms.id')
                        ->whereBetween(DB::raw('MONTH(monthly_form_donations.donation_date)'), [$startMonth, $endMonth]);
                });
            }
        }


        if ($request->has('area_group') && $request->area_group != '') {
            $query->whereHas('donor.area.areaGroups', function ($q) use ($request) {
                $q->where('area_groups.id', $request->area_group);
            });
        }

        if ($request->has('area') && $request->area != '') {
            $query->whereHas('donor.area', function ($q) use ($request) {
                $q->where('areas.id', $request->area);
            });
        }

        if ($request->has('follow_up_department_id') && $request->follow_up_department_id != '') {
            $query->where('follow_up_department_id', $request->follow_up_department_id);
        }

        return DataTables::of($query)
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('donors.name', 'LIKE', "%{$keyword}%");
            })
            ->filterColumn('area', function ($query, $keyword) {
                $query->where('areas.name', 'LIKE', "%{$keyword}%");
            })
            ->filterColumn('address', function ($query, $keyword) {
                $query->where('donors.address', 'LIKE', "%{$keyword}%");
            })
            ->filterColumn('phones', function ($query, $keyword) {
                $query->where('donor_phones.phone_number', 'LIKE', "%{$keyword}%");
            })
            ->addColumn('action', function ($item) {
                return '
            <div class="d-flex gap-2">
                <a href="javascript:void(0);" onclick="addMonthlyFormDonation(' . $item->id . ')"
                class="btn btn-sm btn-dark">
                    <i class="mdi mdi-plus"></i>
                </a>
            </div>
        ';
            })
            ->addColumn('name', function ($item) {
                return $item->donor->name ?? '';
            })
            ->addColumn('area', function ($item) {
                return $item->donor->area->name ?? '';
            })
            ->addColumn('address', function ($item) {
                return $item->donor->address ?? '';
            })
            ->addColumn('monthly_donation_day', function ($item) {
                return $item->donor?->monthly_donation_day ?? 0;
            })
            ->addColumn('phones', function ($item) {
                return $item->donor?->phones->isNotEmpty() ?
                    $item->donor->phones->map(function ($phone) {
                        return $phone->phone_number;
                    })->implode(', ') : 'N/A';
            })
            ->addColumn('collecting_donation_way', function ($item) {
                switch ($item->collecting_donation_way) {
                    case 'location':
                        return __('Location');
                    case 'online':
                        return __('Online');
                    case 'representative':
                        return __('Representative');
                    default:
                        return 'N/A';
                }
            })
            ->addColumn('notes', function ($item) {
                return $item->donor?->notes;
            })
            ->addColumn('items', function ($item) {
                return $item->items->map(function ($donate) {
                    if ($donate->donation_type === 'financial') {
                        return '<strong class="donation-type financial">' . __('Financial Donation') . ':</strong> ' .
                            ($donate->donationCategory->name ?? 'N/A') . ' - ' . $donate->amount;
                    } elseif ($donate->donation_type === 'inKind') {
                        return '<strong class="donation-type in-kind">' . __('inKind Donation') . ':</strong> ' .
                            ($donate->item_name ?? 'N/A') . ' - ' . $donate->amount;
                    } elseif ($donate->donation_type === 'both') {
                        return '<strong class="donation-type financial">' . __('Financial Donation') . ':</strong> ' .
                            ($donate->donationCategory->name ?? 'N/A') . ' - ' . $donate->amount . '<br>' .
                            '<strong class="donation-type in-kind">' . __('inKind Donation') . ':</strong> ' .
                            ($donate->item_name ?? 'N/A') . ' - ' . $donate->amount;
                    }
                    return '';
                })->implode('<br>');
            })
            ->addColumn('cancellation_reason', function ($item) {
                return $item->cancellation_reason;
            })
            ->addColumn('cancellation_date', function ($item) {
                return $item->cancellation_date;
            })
            ->addColumn('is_child', function ($item) {
                // Check if this donor is referenced as a parent by any other donor
                $isParent = Donor::where('parent_id', $item->donor_id)->exists();

                // If donor has a parent_id, they are a child
                if (!is_null($item->parent_id)) {
                    return 'Child';
                }

                // If donor has no parent_id but is referenced as a parent by others, they are a Parent
                if ($isParent) {
                    return 'Parent';
                }

                // If neither condition is met, return 'Other'
                return 'Other';
            })
            ->rawColumns(['action', 'items'])
            ->make(true);
    }


    public function getDonationsByCollectingLine(Request $request)
    {
        $collectingLine = CollectingLine::find($request->collecting_line_id);

        if (!$collectingLine) {
            return response()->json(['message' => 'Collecting Line not found'], 404);
        }

        if ($request->ajax()) {
            $data = $collectingLine->donations()
                ->selectRaw('
                donations.id,
                donations.donor_id,
                donations.status,
                donations.created_by,
                donations.created_at,
                donations.donation_type,
                donation_collectings.collecting_date,
                donation_collectings.in_kind_receipt_number,
                donors.name as donor_name,
                areas.name as area_name,
                donors.address,
                GROUP_CONCAT(DISTINCT donor_phones.phone_number SEPARATOR ", ") as phone_numbers
            ')
                ->leftJoin('donors', 'donations.donor_id', '=', 'donors.id')
                ->leftJoin('donation_collectings', 'donations.id', '=', 'donation_collectings.donation_id')
                ->leftJoin('areas', 'donors.area_id', '=', 'areas.id')
                ->leftJoin('donor_phones', 'donors.id', '=', 'donor_phones.donor_id')
                ->with(['donor', 'donateItems'])
                ->groupBy(
                    'donations.id',
                    'donations.donor_id',
                    'donations.status',
                    'donations.created_by',
                    'donations.created_at',
                    'donations.donation_type',
                    'donation_collectings.collecting_date',
                    'donation_collectings.in_kind_receipt_number',
                    'donors.name',
                    'areas.name',
                    'donors.address',

                );


                $totalFinancialAmount = $collectingLine->donations()
                ->where('donations.donation_type', '!=', 'inKind')
                ->join('donation_items', 'donations.id', '=', 'donation_items.donation_id')
                ->sum('donation_items.amount');

                
            return DataTables::of($data)
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('donors.name', 'LIKE', "%{$keyword}%");
                })
                ->filterColumn('area', function ($query, $keyword) {
                    $query->where('areas.name', 'LIKE', "%{$keyword}%");
                })
                ->filterColumn('address', function ($query, $keyword) {
                    $query->where('donors.address', 'LIKE', "%{$keyword}%");
                })
                ->filterColumn('phones', function ($query, $keyword) {
                    $query->where('donor_phones.phone_number', 'LIKE', "%{$keyword}%");
                })
                ->filterColumn('collected', function ($query, $keyword) {
                    $query->where('donations.status', 'LIKE', "%{$keyword}%");
                })
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="row-checkbox" value="' . $row->id . '">';
                })
                ->addColumn('collecting_line_id', function ($row) use ($request) {
                    return $request->collecting_line_id;
                })
                ->addColumn('name', fn($item) => $item->donor->name ?? 'N/A')
                ->addColumn('area', fn($item) => $item->donor->area->name ?? 'N/A')
                ->addColumn('address', fn($item) => $item->donor->address ?? 'N/A')
                ->addColumn('monthly_donation_day', fn($item) => $item->donor?->monthly_donation_day ?? 0)
                ->addColumn(
                    'phones',
                    fn($item) => $item->donor?->phones->isNotEmpty()
                        ? $item->donor->phones->map(fn($phone) => $phone->phone_number . ' (' . ucfirst($phone->phone_type) . ')')->implode(', ')
                        : 'N/A'
                )
                ->addColumn('donateItems', function ($item) {
                    return $item->donateItems->map(function ($donate) use ($item) {
                        return match ($item->donation_type) {
                            'financial' => '<strong class="donation-type financial">' . __('Financial Donation') . ':</strong> ' .
                                ($donate->donationCategory->name ?? 'N/A') . ' - ' . $donate->amount,
                            'inKind' => '<strong class="donation-type in-kind">' . __('inKind Donation') . ':</strong> ' .
                                ($donate->item_name ?? 'N/A') . ' - ' . $donate->amount,
                            'both' => (
                                ($donate->donation_category_id ? '<strong class="donation-type financial">' . __('Financial Donation') . ':</strong> ' .
                                    ($donate->donationCategory->name ?? 'N/A') . ' - ' . $donate->amount . '<br>' : '') .
                                ($donate->item_name ? '<strong class="donation-type in-kind">' . __('inKind Donation') . ':</strong> ' .
                                    ($donate->item_name ?? 'N/A') . ' - ' . $donate->amount : '')
                            ),
                            default => '',
                        };
                    })->implode('<br>');
                })
                ->addColumn('receipt_number', function ($item) {
                    if ($item->collectingDonation) {
                        return match ($item->donation_type) {
                            'inKind' => '<span class="text-danger">' . __('Financial Receipt Number') . ' : ' . $item->collectingDonation?->in_kind_receipt_number . '</span>',
                            'financial' => '<span class="text-success">' . __('In Kind Receipt Number') . ' : ' . $item->collectingDonation?->financial_receipt_number . '</span>',
                            'both' => '<span class="text-danger">' . __('Financial Receipt Number') . ' : ' . $item->collectingDonation?->financial_receipt_number . '</span><br>
                            <span class="text-success">' . __('In Kind Receipt Number') . ' : ' . $item->collectingDonation?->in_kind_receipt_number . '</span>',
                            default => '',
                        };
                    }
                    return 'N/A';
                })
                ->addColumn(
                    'collected',
                    fn($item) => $item->collectingDonation
                        ? '<span class="text-success">' . __('Collected') . '</span>'
                        : '<span class="text-danger">' . __('Not Collected') . '</span>'
                )
                ->addColumn('actions', function ($item) use ($request) {
                    return '
                    <div class="d-flex gap-2">
                    <a href="javascript:void(0);" onclick="editDonation(' . $item->id . ')"
                        class="btn btn-sm btn-info">
                            <i class="mdi mdi-square-edit-outline"></i>
                        </a>
                </div>';
                })
                ->addColumn('un_assign_actions', function ($item) use ($request) {
                    return '
                    <div class="d-flex gap-2">
                   
                    <button class="btn btn-sm btn-primary" 
                    onclick="unAssignCollectingLine(' . $item->id . ',' . $request->collecting_line_id . ')">
                        ' . __('Remove From Collecting Line') . '
                    </button>
                </div>';
                })
                ->with('total_financial_amount', $totalFinancialAmount)
                ->rawColumns(['donateItems', 'receipt_number', 'collected', 'actions', 'un_assign_actions','checkbox'])
                ->make(true);
        }

        return response()->json(['message' => 'Invalid request'], 400);
    }


    public function exportCollectingLineToPdf(Request $request)
    {
        // Fetch the collecting line
        $collectingLine = CollectingLine::find($request->collecting_line_id);

        // Fetch the donations data with parent-child relationships based on donors.parent_id
        $data = $collectingLine->donations()
            ->selectRaw('
            donations.id,
            donations.donor_id,
            donations.status,
            donations.created_by,
            donations.created_at,
            donations.collecting_time,
            donations.notes,
            donations.donation_type,
            donation_collectings.collecting_date,
            donation_collectings.in_kind_receipt_number,
            donors.name as donor_name,
            donors.parent_id as donor_parent_id,
            areas.name as area_name,
            donors.address,
            GROUP_CONCAT(DISTINCT donor_phones.phone_number SEPARATOR ", ") as phone_numbers
        ')
            ->leftJoin('donors', 'donations.donor_id', '=', 'donors.id')
            ->leftJoin('donation_collectings', 'donations.id', '=', 'donation_collectings.donation_id')
            ->leftJoin('areas', 'donors.area_id', '=', 'areas.id')
            ->leftJoin('donor_phones', 'donors.id', '=', 'donor_phones.donor_id')
            ->with('donor', 'donateItems.donationCategory')
            ->groupBy(
                'donations.id',
                'donations.donor_id',
                'donations.status',
                'donations.created_by',
                'donations.created_at',
                'donations.donation_type',
                'donations.collecting_time',
                'donations.notes',
                'donation_collectings.collecting_date',
                'donation_collectings.in_kind_receipt_number',
                'donors.name',
                'donors.parent_id',
                'areas.name',
                'donors.address',
                'collecting_line_donations.collecting_line_id',
                'collecting_line_donations.donation_id'
            )
            ->get();

        // Organize data based on donor.parent_id
        $organizedData = [];

        // First, initialize parents
        foreach ($data as $donation) {
            if ($donation->donor_parent_id === null) {
                // This is a parent donor
                $organizedData[$donation->donor_id] = [
                    'parent' => $donation,
                    'children' => [],
                ];
            }
        }

        // Second, attach children correctly
        foreach ($data as $donation) {
            if ($donation->donor_parent_id !== null) {
                // Ensure parent exists, if not initialize it
                if (!isset($organizedData[$donation->donor_parent_id])) {
                    $organizedData[$donation->donor_parent_id] = [
                        'parent' => null, // Parent might not be in data (edge case)
                        'children' => [],
                    ];
                }

                // Append the child donation correctly
                $organizedData[$donation->donor_parent_id]['children'][] = $donation;
            }
        }

        // Additional data for the PDF
        $additionalData = [
            'collecting_line_number' => $collectingLine->number,
            'representative' => $collectingLine->representative->name ?? '',
            'driver' => $collectingLine->driver->name ?? '',
            'employee' => $collectingLine->employee->name ?? '',
            'area_group' => $collectingLine->areaGroup->name ?? '',
            'collecting_line_date' => Carbon::parse($collectingLine->collecting_date)->format('Y-m-d'),
            'total_donations' => $data->count(),
        ];

        // Render the HTML view with the data
        $html = View::make('backend.pages.collecting-lines.collectingLineDetails', [
            'organizedData' => $organizedData,
            'additionalData' => $additionalData,
        ])->render();

        // Configure mpdf for Arabic support
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'xbriyaz',
            'tempDir' => storage_path('app/mpdf') // Set a custom temp directory

        ]);

        // Write the HTML content to the PDF
        $mpdf->WriteHTML($html);

        // Output the PDF as a download
        return response($mpdf->Output('donations_report.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="donations_report.pdf"',
        ]);
    }

    public function show($id) {}
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCollectingLineRequest $request)
    {
        $collectingLine = CollectingLine::create($request->validated());
        return response()->json([
            'success' => true,
            'message' => __('messages.Collecting Line  created successfully'),
            'data' => $collectingLine,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCollectingLineRequest $request, CollectingLine $collectingLine)
    {
        $collectingLine->update($request->validated());
        return response()->json([
            'success' => true,
            'message' => __('messages.Collecting Line updated successfully'),
            'data' => $collectingLine,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CollectingLine $collectingLine)
    {
        $collectingLine->delete();
        return response()->json([
            'success' => true,
            'message' => __('messages.Collecting Line deleted successfully'),
            'data' => null,
        ]);
    }


    public function assignDonation(Request $request)
    {
        $request->validate([
            'donation_id' => 'required|exists:donations,id',
            'collecting_line_id' => 'required|exists:collecting_lines,id',
        ]);

        $donation = Donation::find($request->donation_id);

        // Check if the donation is already assigned to the collecting line
        if ($donation->collectingLines()->where('collecting_line_id', $request->collecting_line_id)->exists()) {
            return response()->json(['error' => 'Donation is already assigned to this collecting line.'], 400);
        }

        // Assign the donation to the collecting line
        $donation->collectingLines()->attach($request->collecting_line_id);

        return response()->json([
            'success' => true,
            'message' => __('messages.Donation assigned successfully'),
            'data' => $donation,
        ]);
    }

    public function unAssignDonation(Request $request)
    {
        $request->validate([
            'donation_id' => 'required|exists:donations,id',
            'collecting_line_id' => 'required|exists:collecting_lines,id',
        ]);

        $donation = Donation::find($request->donation_id);


        // Assign the donation to the collecting line
        $donation->collectingLines()->detach($request->collecting_line_id);

        return response()->json([
            'success' => true,
            'message' => __('messages.Donation assigned successfully'),
            'data' => $donation,
        ]);
    }

    public function assignBulkDonations(Request $request)
    {
        $request->validate([
            'donation_ids' => 'required|array',
            'donation_ids.*' => 'exists:donations,id',
            'collecting_line_id' => 'required|exists:collecting_lines,id',
        ]);

        $donationIds = $request->donation_ids;
        $collectingLineId = $request->collecting_line_id;

        foreach ($donationIds as $donationId) {
            $donation = Donation::find($donationId);

            // Skip if the donation is already assigned
            if ($donation->collectingLines()->where('collecting_line_id', $collectingLineId)->exists()) {
                continue;
            }

            $donation->collectingLines()->attach($collectingLineId);
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.Donations assigned successfully'),
        ]);
    }

    public function unAssignBulkDonations(Request $request)
    {
        $request->validate([
            'donation_ids' => 'required|array',
            'donation_ids.*' => 'exists:donations,id',
            'collecting_line_id' => 'required|exists:collecting_lines,id',
        ]);

        $donationIds = $request->donation_ids;
        $collectingLineId = $request->collecting_line_id;

        foreach ($donationIds as $donationId) {
            $donation = Donation::find($donationId);
            $donation->collectingLines()->detach($collectingLineId);
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.Donations unassigned successfully'),
        ]);
    }


}
