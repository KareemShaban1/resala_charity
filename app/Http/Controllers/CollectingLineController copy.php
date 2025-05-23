<?php

namespace App\Http\Controllers;

use App\Models\CollectingLine;
use App\Http\Requests\StoreCollectingLineRequest;
use App\Http\Requests\UpdateCollectingLineRequest;
use App\Models\AreaGroup;
use App\Models\Donation;
use App\Models\DonationCategory;
use App\Models\Donor;
use App\Models\Employee;
use App\Models\MonthlyForm;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CollectingLineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $representatives = Employee::whereHas('department', function ($q) {
            $q->where('name', 'Representatives');
        })->get();
        $drivers = Employee::whereHas('department', function ($q) {
            $q->where('name', 'Drivers');
        })->get();
        $employees = Employee::whereHas(
            'department',
            function ($q) {
                $q->whereNotIn('name', ['Representatives', 'Drivers']);
            }
        )->get();
        $areaGroups = AreaGroup::all();
        $donationCategories = DonationCategory::all();
        $donors = Donor::all();

        return view(
            'backend.pages.collecting-lines.index',
            compact('representatives', 'drivers', 'donors', 'employees', 'areaGroups', 'donationCategories')
        );
    }

    /**
     * Fetch collecting lines data for DataTables.
     */
    public function getCollectingLinesData(Request $request)
    {
        if ($request->ajax()) {
            $data = CollectingLine::query();

            // Apply filters
            if ($request->has('date') && $request->date != '') {
                $data->whereDate('collecting_date', '=', $request->date);
            }
            // if ($request->has('end_date') && $request->end_date != '') {
            //     $data->whereDate('created_at', '<=', $request->end_date);
            // }
            if ($request->has('area_group') && $request->area_group != '') {
                $data->where('area_group', $request->area_group);
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
                    $btn = '<button class="edit-btn btn btn-sm btn-primary" 
                    data-id="' . $row->id .
                        '" data-area-group-id="' . $row->area_group_id .
                        '" data-representative-id="' . $row->representative_id .
                        '" data-driver-id="' . $row->driver_id .
                        '" data-employee-id="' . $row->employee_id .
                        '" data-collecting-date="' . $row->collecting_date . '">' . __('Edit') . '</button>';
                    $btn .= ' <button class="delete-btn btn btn-sm btn-danger" data-id="' . $row->id . '">' . __('Delete') . '</button>';
                    $btn .= '<button class="btn btn-sm btn-info view-donations-btn" data-id="' . $row->id . '">' . __('View Donations') . '</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function getCollectingLinesByDate(Request $request)
    {

        $data = CollectingLine::with('areaGroup');
        if ($request->has('date') && $request->date != '') {
            $data->whereDate('collecting_date', '=', $request->date);
        }
        if ($request->has('area_group') && $request->area_group != '') {
            $data->where('area_group', $request->area_group);
        }
        $data = $data->get();

        return response()->json([
            'success' => true,
            'message' => __('messages.' . class_basename(CollectingLine::class) . ' retrieved successfully'),
            'data' => $data
        ]);
    }

    /**
     * Fetch donations data for DataTables.
     */
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
                ->with('donor', 'donateItems')
                ->whereDoesntHave('collectingLines') // Exclude donations already assigned to a collecting line
                ->where('donations.status', 'not_collected')
                ->groupBy(
                    'donations.donor_id',
                    'donors.name',
                    'areas.name',
                    'donors.address',
                    'donations.id',
                    'donations.created_at',
                    'donations.status',
                    'donations.created_by',
                    'donations.donation_type',
                    'donation_collectings.collecting_date',
                    'donation_collectings.in_kind_receipt_number'
                );

            // Apply filters
            if ($request->has('date') && $request->date != '') {
                $data->whereDate('donations.date',  $request->date);
            }
            if ($request->has('area_group') && $request->area_group != '') {
                $data->whereHas('donor.area.areaGroups', function ($q) use ($request) {
                    $q->where('area_groups.id', $request->area_group);
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
                     <button class="btn btn-sm btn-primary assign-btn" data-id="' . $item->id . '">Select</button>
                </div>
            ';
                })
                ->addColumn('name', function ($item) {
                    return $item->donor->name;
                })
                ->addColumn('area', function ($item) {
                    return $item->donor->area->name;
                })
                ->addColumn('address', function ($item) {
                    return $item->donor->address;
                })
                ->addColumn('monthly_donation_day', function ($item) {
                    return $item->donor?->monthly_donation_day ?? 0;
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
                                    ($donate->donationCategory->name ?? 'N/A') . ' - ' . $donate->amount . '<br>';
                            }
                            if (isset($donate->item_name) && isset($donate->amount)) {
                                return  '<strong class="donation-type in-kind">' . __('inKind Donation') . ':</strong> ' .
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
        GROUP_CONCAT(DISTINCT donor_phones.phone_number SEPARATOR ", ") as phone_numbers
    ')
            ->leftJoin('donors', 'monthly_forms.donor_id', '=', 'donors.id')
            ->leftJoin('areas', 'donors.area_id', '=', 'areas.id')
            ->leftJoin('donor_phones', 'donors.id', '=', 'donor_phones.donor_id')
            ->leftJoin('monthly_form_donations', 'monthly_forms.id', '=', 'monthly_form_donations.monthly_form_id')
            ->where('monthly_forms.status', 'ongoing')
            ->with('donor', 'items')
            ->groupBy(
                'monthly_forms.donor_id',
                'donors.name',
                'areas.name',
                'donors.address',
                'donors.monthly_donation_day',
                'monthly_forms.id',
                'monthly_forms.created_at',
                'monthly_forms.collecting_donation_way',
                'monthly_forms.status',
                'monthly_forms.cancellation_reason',
                'monthly_forms.cancellation_date'
            );
        // Apply filters
        if ($request->has('date') && $request->date != '') {
            // Extract the day, month, and year from the selected date
            $day = date('d', strtotime($request->date));
            $month = date('m', strtotime($request->date));
            $year = date('Y', strtotime($request->date));

            // Filter by the extracted day
            $query->where('donors.monthly_donation_day', $day);

            // Filter out monthly_forms that have donations in the same month
            $query->whereDoesntHave('donations', function ($q) use ($month, $year) {
                $q->whereMonth('donations.created_at', $month)
                    ->whereYear('donations.created_at', $year);
            });
        }

        if ($request->has('area_group') && $request->area_group != '') {
            $query->whereHas('donor.area.areaGroups', function ($q) use ($request) {
                $q->where('area_groups.id', $request->area_group);
            });
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
                return $item->donor->name;
            })
            ->addColumn('area', function ($item) {
                return $item->donor->area->name;
            })
            ->addColumn('address', function ($item) {
                return $item->donor->address;
            })
            ->addColumn('monthly_donation_day', function ($item) {
                return $item->donor?->monthly_donation_day ?? 0;
            })
            ->addColumn('phones', function ($item) {
                return $item->donor?->phones->isNotEmpty() ?
                    $item->donor->phones->map(function ($phone) {
                        return $phone->phone_number . ' (' . ucfirst($phone->phone_type) . ')';
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
            ->rawColumns(['action', 'items'])
            ->make(true);
    }

    public function getDonationsByCollectingLine(Request $request)
    {
        $collectingLine = CollectingLine::find($request->collecting_line_id);
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
                ->with('donor', 'donateItems')
                ->groupBy(
                    'donations.donor_id',
                    'donors.name',
                    'areas.name',
                    'donors.address',
                    'donations.id',
                    'donations.created_at',
                    'donations.status',
                    'donations.created_by',
                    'donations.donation_type',
                    'donation_collectings.collecting_date',
                    'donation_collectings.in_kind_receipt_number'
                );


            // // Apply additional filters (if needed)
            // if ($request->has('date') && $request->date != '') {
            //     $data->whereDate('created_at', '>=', $request->date);
            // }
            // if ($request->has('area_group') && $request->area_group != '') {
            //     $data->where('area_group_id', $request->area_group);
            // }

            return DataTables::of($data)
                ->addColumn('name', function ($item) {
                    return $item->donor->name;
                })
                ->addColumn('area', function ($item) {
                    return $item->donor->area->name;
                })
                ->addColumn('address', function ($item) {
                    return $item->donor->address;
                })
                ->addColumn('monthly_donation_day', function ($item) {
                    return $item->donor?->monthly_donation_day ?? 0;
                })
                ->addColumn('phones', function ($item) {
                    return $item->donor?->phones->isNotEmpty() ?
                        $item->donor->phones->map(function ($phone) {
                            return $phone->phone_number . ' (' . ucfirst($phone->phone_type) . ')';
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
                                    ($donate->donationCategory->name ?? 'N/A') . ' - ' . $donate->amount . '<br>';
                            }
                            if (isset($donate->item_name) && isset($donate->amount)) {
                                return  '<strong class="donation-type in-kind">' . __('inKind Donation') . ':</strong> ' .
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
                ->rawColumns(['donateItems', 'receipt_number', 'collected'])
                ->make(true);
        }
    }

    public function show($id) {}
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCollectingLineRequest $request)
    {
        CollectingLine::create($request->validated());
        return response()->json(['success' => 'Collecting Line created successfully.']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCollectingLineRequest $request, CollectingLine $collectingLine)
    {
        $collectingLine->update($request->validated());
        return response()->json(['success' => 'Collecting Line updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CollectingLine $collectingLine)
    {
        $collectingLine->delete();
        return response()->json(['success' => 'Collecting Line deleted successfully.']);
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

        return response()->json(['success' => 'Donation assigned successfully.']);
    }
}
