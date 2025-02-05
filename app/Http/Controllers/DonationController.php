<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\UpdateDonationRequest;
use App\Models\DonationCategory;
use App\Models\DonationItem;
use App\Models\Donor;
use App\Models\Employee;
use App\Models\MonthlyForm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DonationController extends Controller
{
    public function index()
    {
        // Check if the user is authorized to view donation
        $this->authorize('view', Donation::class);

        $donors = Donor::all();
        $donationCategories = DonationCategory::all();
        $employees = Employee::all();
        return view(
            'backend.pages.donations.index',
            compact('donors', 'donationCategories', 'employees')
        );
    }

    public function monthlyDonations()
    {
        // Check if the user is authorized to view donation
        $this->authorize('view', Donation::class);

        $donors = Donor::all();
        $donationCategories = DonationCategory::all();
        $employees = Employee::all();
        return view(
            'backend.pages.donations.monthly_donation',
            compact('donors', 'donationCategories', 'employees')
        );
    }

    public function gatheredDonations()
    {
        // Check if the user is authorized to view donation
        $this->authorize('view', Donation::class);

        $donors = Donor::all();
        $donationCategories = DonationCategory::all();
        $employees = Employee::all();
        return view(
            'backend.pages.donations.gathered_donation',
            compact('donors', 'donationCategories', 'employees')
        );
    }



    public function data()
    {

        $query = Donation::query()
            ->selectRaw('
        donations.id,
        donations.donor_id,
        donations.status,
        donations.created_by,
        donations.created_at,
        donations.donation_type,
        donations.donation_category,
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
                'donations.donation_category',
                'donation_collectings.collecting_date',
                'donation_collectings.in_kind_receipt_number'
            );

        if (request()->has('status')) {
            $status = request('status');
            if ($status === 'collected') {
                $query->where('donations.status', 'collected');
            } elseif ($status === 'not_collected') {
                $query->where('donations.status', 'not_collected');
            }elseif($status === 'followed_up'){
                $query->where('donations.status', 'followed_up');
            }elseif($status === 'cancelled'){
                $query->where('donations.status', 'cancelled');
            }

            // followed_up,cancelled
        }

        // Date filter
        if (request()->has('date_filter')) {
            $dateFilter = request('date_filter');
            $startDate = request('start_date');
            $endDate = request('end_date');

            if ($dateFilter === 'today') {
                $query->whereDate('donations.date', operator: today());
            } elseif ($dateFilter === 'week') {
                $query->whereBetween('donations.date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($dateFilter === 'month') {
                $query->whereBetween('donations.date', [now()->startOfMonth(), now()->endOfMonth()]);
            } elseif ($dateFilter === 'range' && $startDate && $endDate) {
                $query->whereBetween('donations.date', [$startDate, $endDate]);
            }
        }

        // Donation category filter
        if (request()->has('donation_category') && request('donation_category') !== 'all') {
            $query->where('donations.donation_category', request('donation_category'));
            if (request('donation_category') === 'monthly') {
                $query->whereIn('donations.donation_category', [
                    'monthly',
                    'normal_and_monthly'
                ]);
            }
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
                 <a href="javascript:void(0);" onclick="donationDetails(' . $item->id . ')"
                    class="btn btn-sm btn-light">
                        <i class="mdi mdi-eye"></i>
                    </a>
                    <a href="javascript:void(0);" onclick="editDonation(' . $item->id . ')"
                    class="btn btn-sm btn-info">
                        <i class="mdi mdi-square-edit-outline"></i>
                    </a>
                    <a href="javascript:void(0);" onclick="deleteRecord(' . $item->id . ', \'donations\')"
                    class="btn btn-sm btn-danger">
                        <i class="mdi mdi-delete"></i>
                    </a>
                     <a href="javascript:void(0)" onclick="addActivity(' . $item->donor->id . ')" class="btn btn-sm btn-dark">
                        <i class="uil-outgoing-call"></i>
                    </a>
                </div>
            ';
            })
            ->addColumn('name', function ($item) {
                // return $item->donor->name;
                return '<a href="' . route('donor-history.show', [$item->donor->id]) . '" class="text-info">'
                . $item->donor->name .
                '</a>';
            })
            ->addColumn('area', function ($item) {
                return $item->donor->area->name;
            })
            ->addColumn('address', function ($item) {
                return $item->donor->address;
            })
            ->addColumn('donation_category', function ($item) {
                content:
                if ($item->donation_category === 'normal') {
                    return __('Normal Donation');
                } elseif ($item->donation_category === 'monthly') {
                    return __('Monthly Donation');
                } elseif ($item->donation_category === 'gathered') {
                    return __('Gathered Donation');
                } elseif ($item->donation_category === 'normal_and_monthly') {
                    return __('Normal and Monthly Donation');
                }
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
            ->addColumn('donation_status', function ($item) {
                if ($item->status === 'collected') {
                    return ' <span class="text-white badge bg-success">' .  __('Collected') . '</span>';
                } elseif ($item->status === 'not_collected') {
                    return ' <span class="text-white badge bg-primary">' .  __('Not Collected') . '</span><br>' . __('') . '';
                } elseif ($item->status === 'followed_up') {
                    return ' <span class="text-white badge bg-warning">' .  __('Followed Up') . '</span><br>' . __('') . '';
                } elseif ($item->status === 'cancelled') {
                    return ' <span class="text-white badge bg-danger">' .  __('Cancelled Donation') . '</span><br>' . __('') . '';
                }
            })
            ->addColumn('group_key', function ($item) {
                if ($item->donation_category === 'gathered') {
                    return $item->donor_id . '-' . $item->created_at;
                }
                return null;
            })
            ->rawColumns(['name','action', 'donateItems', 'receipt_number', 'donation_status'])
            ->make(true);
    }



    public function getDonationDetails($id)
    {
        $donation = Donation::with('donor', 'donateItems', 'collectingDonation', 'createdBy')->findOrFail($id);

        // Check if the donation category is "gathered"
        if ($donation->donation_category === 'gathered') {
            // Get all donations with the same donor_id, category, and created_at
            $relatedDonations = Donation::where('donor_id', $donation->donor_id)
                ->where('donation_category', 'gathered')
                ->whereDate('created_at', $donation->created_at) // Ensuring same creation date
                ->pluck('id'); // Get only the IDs

            // Calculate the total amount of donation items for these donations
            $totalAmount = DonationItem::whereIn('donation_id', $relatedDonations)
                ->sum('amount');

            // Add total amount to the response
            $donation->total_gathered_amount = $totalAmount;
        }

        return response()->json($donation);
    }


    public function store(Request $request)
    {
        $this->authorize('create', Donation::class);

        // Validate the incoming request data
        $validatedData = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'date' => 'required|date',
            'donation_type' => 'required|string|in:financial,inKind,both',
            'status' => 'required|in:collected,not_collected,followed_up,cancelled',
            'reporting_way' => 'required|string|in:call,whatsapp_chat,other',
            'alternate_date' => 'sometime|date',
            'donation_category' => 'required|string|in:normal,monthly',
            'employee_id' => 'nullable|exists:employees,id',
            'collecting_date' => 'nullable|date',
            'collecting_time' => 'nullable|string',
            'collecting_way' => 'nullable|string|in:representative,location,online',
            'notes' => 'nullable|string',
            'in_kind_receipt_number' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if (!empty($value)) {
                        $inKindExists = DB::table('donations')
                            ->join('donation_items', 'donations.id', '=', 'donation_items.donation_id')
                            ->join('donation_collectings', 'donations.id', '=', 'donation_collectings.donation_id')
                            ->where('donation_collectings.in_kind_receipt_number', $value)
                            ->where('donation_items.donation_type', 'inKind')
                            ->exists();

                        $donates = $request->get('donates', []);
                        $hasInKind = collect($donates)->contains(
                            fn($d) =>
                            isset($d['inKind_donation_type']) && ($d['inKind_donation_type'] === 'inKind' || $d['financial_donation_type'] === 'Both') &&
                                !empty($d['in_kind_item_name']) &&
                                !empty($d['in_kind_quantity'])
                        );

                        // Fail validation if in-kind donation type exists and receipt number is already used
                        if ($hasInKind && $inKindExists) {
                            $fail(__('validation.unique_receipt_number_in_kind'));
                        }
                    }
                },
            ],
            'donates' => 'required|array',
            'donates.*.financial_donation_item_type' => 'nullable|in:normal,monthly',
            'donates.*.financial_donation_type' => 'nullable|in:financial',
            'donates.*.financial_donation_categories_id' => 'nullable|exists:donation_categories,id',
            'donates.*.financial_amount' => 'nullable|numeric|min:0',
            'donates.*.financial_receipt_number' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if (!empty($value)) {
                        // Check if the receipt number already exists in the database
                        $financialExists = DB::table('donation_items')
                            // ->join('donation_items', 'donations.id', '=', 'donation_items.donation_id')
                            ->where('donation_items.financial_receipt_number', $value)
                            ->where('donation_items.donation_type', 'financial')
                            ->exists();

                        if ($financialExists) {
                            $fail(__('validation.unique_receipt_number_financial'));
                        }

                        // Check for duplicate receipt numbers within the same request
                        $donates = $request->get('donates', []);
                        $receiptNumbers = [];

                        foreach ($donates as $donate) {
                            if (isset($donate['financial_receipt_number']) && !empty($donate['financial_receipt_number'])) {
                                if (in_array($donate['financial_receipt_number'], $receiptNumbers)) {
                                    $fail(__('validation.duplicate_receipt_number_in_request'));
                                }
                                $receiptNumbers[] = $donate['financial_receipt_number'];
                            }
                        }
                    }
                },
            ],
            'donates.*.inKind_donation_type' => 'nullable|in:inKind',
            'donates.*.in_kind_donation_item_type' => 'nullable|in:normal,monthly',
            'donates.*.in_kind_item_name' => 'nullable|string',
            'donates.*.in_kind_quantity' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Create the Donation
            $donation = Donation::create([
                'donor_id' => $validatedData['donor_id'],
                'status' => $validatedData['status'],
                'date' => $validatedData['date'],
                'donation_type' => $validatedData['donation_type'],
                'donation_category' => $validatedData['donation_category'],
                'collecting_time' => $validatedData['collecting_time'],
                'notes' => $validatedData['notes'],
                'alternate_date' => $validatedData['alternate_date'] ?? null,
                'reporting_way' => $validatedData['reporting_way'] ?? null,
            ]);

            $donatesAdded = false;

            // Process and save donates
            foreach ($validatedData['donates'] as $donateData) {
                if (
                    isset($donateData['financial_donation_type']) && $donateData['financial_donation_type'] === 'financial'
                    && !empty($donateData['financial_donation_categories_id'])
                    && !empty($donateData['financial_amount'])
                ) {
                    $donation->donateItems()->create([
                        'donation_type' => $donateData['financial_donation_type'],
                        'donation_category_id' => $donateData['financial_donation_categories_id'],
                        'financial_receipt_number' => $donateData["financial_receipt_number"] ?? null,
                        'amount' => $donateData['financial_amount'],
                        'donation_item_type' => $donateData['financial_donation_item_type'],
                    ]);

                    $donatesAdded = true;
                }

                if (
                    isset($donateData['inKind_donation_type']) && $donateData['inKind_donation_type'] === 'inKind'
                    && !empty($donateData['in_kind_item_name'])
                    && !empty($donateData['in_kind_quantity'])
                ) {
                    $donation->donateItems()->create([
                        'donation_type' => $donateData['inKind_donation_type'],
                        'donation_category_id' => null,
                        'item_name' => $donateData['in_kind_item_name'],
                        'amount' => $donateData['in_kind_quantity'],
                        'donation_item_type' => $donateData['in_kind_donation_item_type'],
                    ]);

                    $donatesAdded = true;
                }
            }

            if ($donation->status === "collected") {
                $donation->collectingDonation()->create([
                    "employee_id" => $validatedData["employee_id"],
                    "donation_id" => $donation->id,
                    "collecting_date" => $validatedData["collecting_date"],
                    // 'financial_receipt_number' => $validatedData["financial_receipt_number"] ?? null,
                    'in_kind_receipt_number' => $validatedData["in_kind_receipt_number"] ?? null,
                    "collecting_way" => $validatedData["collecting_way"]
                ]);
            }

            if (!$donatesAdded) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => __('validation.no_valid_donations_provided'),
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('messages.Donation created successfully'),
                'data' => $donation->load('donateItems'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function edit(Donation $donation)
    {
        $this->authorize('update', Donation::class);

        return response()->json($donation->load(['donateItems', 'donor', 'collectingDonation']));
    }

    public function update(Request $request, Donation $donation)
    {
        $this->authorize('update', $donation);

        // Validate the incoming request data
        $validatedData = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'date' => 'required|date',
            'status' => 'required|in:collected,not_collected,followed_up,cancelled',
            'reporting_way' => 'required|string|in:call,whatsapp_chat,other',
            'alternate_date' => 'sometime|date',
            'employee_id' => 'nullable|exists:employees,id',
            'collecting_date' => 'nullable|date',
            'donation_type' => 'required|string|in:financial,inKind,both',
            'donation_category' => 'required|string|in:normal,monthly',
            'collecting_time' => 'nullable|string',
            'collecting_way' => 'nullable|string|in:representative,location,online',
            'notes' => 'nullable|string',
            'in_kind_receipt_number' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request, $donation) {
                    if (!empty($value)) {

                        $inKindExists = DB::table('donations')
                            ->join('donation_items', 'donations.id', '=', 'donation_items.donation_id')
                            ->join('donation_collectings', 'donations.id', '=', 'donation_collectings.donation_id')
                            ->where('donation_collectings.in_kind_receipt_number', $value)
                            ->where('donation_items.donation_type', 'inKind')
                            ->where('donations.id', '!=', $donation->id)
                            ->exists();

                        $donates = $request->get('donates', []);

                        $hasInKind = collect($donates)->contains(
                            fn($d) =>
                            isset($d['inKind_donation_type']) && $d['inKind_donation_type'] === 'inKind' &&
                                !empty($d['in_kind_item_name']) &&
                                !empty($d['in_kind_quantity'])
                        );

                        if ($hasInKind && $inKindExists) {
                            $fail(__('validation.unique_receipt_number_in_kind'));
                        }
                    }
                },
            ],
            'donates' => 'required|array',
            'donates.*.financial_donation_item_type' => 'nullable|in:normal,monthly',
            'donates.*.financial_donation_type' => 'nullable|in:financial',
            'donates.*.financial_donation_categories_id' => 'nullable|exists:donation_categories,id',
            'donates.*.financial_amount' => 'nullable|numeric|min:0',
            'donates.*.financial_receipt_number' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if (!empty($value)) {
                        // Get the current donation item ID being updated (if any)
                        $currentDonationItemId = $this->getCurrentDonationItemId($attribute, $request);

                        // Check if the receipt number already exists in the database, excluding the current record
                        $query = DB::table('donation_items')
                            ->where('financial_receipt_number', $value)
                            ->where('donation_type', 'financial');

                        // Exclude the current record if it's being updated
                        if (!empty($currentDonationItemId)) {
                            $query->where('id', '!=', $currentDonationItemId);
                        }

                        $financialExists = $query->exists();


                        if ($financialExists) {
                            $fail(__('validation.unique_receipt_number_financial'));
                        }

                        // Check for duplicate receipt numbers within the same request
                        $donates = $request->get('donates', []);
                        $receiptNumbers = [];

                        foreach ($donates as $donate) {
                            if (isset($donate['financial_receipt_number']) && !empty($donate['financial_receipt_number'])) {
                                if (in_array($donate['financial_receipt_number'], $receiptNumbers)) {
                                    $fail(__('validation.duplicate_receipt_number_in_request'));
                                }
                                $receiptNumbers[] = $donate['financial_receipt_number'];
                            }
                        }
                    }
                },
            ],
            'donates.*.inKind_donation_type' => 'nullable|in:inKind',
            'donates.*.in_kind_donation_item_type' => 'nullable|in:normal,monthly',
            'donates.*.in_kind_item_name' => 'nullable|string',
            'donates.*.in_kind_quantity' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Update the Donation record
            $donation->update([
                'donor_id' => $validatedData['donor_id'],
                'status' => $validatedData['status'],
                'date' => $validatedData['date'],
                'donation_type' => $validatedData['donation_type'],
                'donation_category' => $validatedData['donation_category'],
                'collecting_time' => $validatedData['collecting_time'],
                'notes' => $validatedData['notes'],
                'alternate_date' => $validatedData['alternate_date'] ?? null,
                'reporting_way' => $validatedData['reporting_way'] ?? null,
            ]);

            $donatesAdded = false;

            // Update donateItems
            $donation->donateItems()->delete();

            foreach ($validatedData['donates'] as $donateData) {
                if (
                    isset($donateData['financial_donation_type'])
                    && $donateData['financial_donation_type'] === 'financial' &&
                    !empty($donateData['financial_donation_categories_id']) &&
                    !empty($donateData['financial_amount'])
                ) {
                    $donation->donateItems()->create([
                        'donation_type' => $donateData['financial_donation_type'],
                        'donation_category_id' => $donateData['financial_donation_categories_id'],
                        'amount' => $donateData['financial_amount'],
                        'financial_receipt_number' => $donateData["financial_receipt_number"],
                        'donation_item_type' => $donateData['financial_donation_item_type'],
                    ]);

                    $donatesAdded = true;
                }

                if (
                    isset($donateData['inKind_donation_type']) && $donateData['inKind_donation_type'] === 'inKind' &&
                    !empty($donateData['in_kind_item_name']) &&
                    !empty($donateData['in_kind_quantity'])
                ) {
                    $donation->donateItems()->create([
                        'donation_type' => $donateData['inKind_donation_type'],
                        'donation_category_id' => null,
                        'item_name' => $donateData['in_kind_item_name'],
                        'amount' => $donateData['in_kind_quantity'],
                        'donation_item_type' => $donateData['in_kind_donation_item_type'],
                    ]);

                    $donatesAdded = true;
                }
            }

            // Update or delete collectingDonation
            if ($donation->status === "collected") {
                $donation->collectingDonation()->updateOrCreate(
                    ['donation_id' => $donation->id],
                    [
                        "employee_id" => $validatedData["employee_id"],
                        "collecting_date" => $validatedData["collecting_date"],
                        // 'financial_receipt_number' => $validatedData["financial_receipt_number"],
                        'in_kind_receipt_number' => $validatedData["in_kind_receipt_number"],
                        "collecting_way" => $validatedData["collecting_way"],
                    ]
                );
                // Get the latest monthly_form_donations entry
                $latestMonthlyFormDonation = DB::table('monthly_form_donations')
                    ->where('donation_id', $donation->id)
                    ->latest('donation_date')
                    ->first();

                    // dd($latestMonthlyFormDonation);

                    // \Log::info('latestMonthlyFormDonation : ' . $latestMonthlyFormDonation);

                // If an entry exists, update the monthly_donation_day in monthly_forms
                if ($latestMonthlyFormDonation) {
                    $donationDate = Carbon::parse($latestMonthlyFormDonation->donation_date);
                    $monthlyDonationDay = $donationDate->day; // Extract the day

                    // \Log::info('monthlyDonationDay : ' . $monthlyDonationDay);
                    // Update the monthly_forms table
                    DB::table('donors')
                        ->where('id', $donation->donor_id)
                        ->update(['monthly_donation_day' => $monthlyDonationDay]);

                        \Log::info('updated monthly_forms');
                }
            } else {
                $donation->collectingDonation()->delete();
            }

            if (!$donatesAdded) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => __('validation.no_valid_donations_provided'),
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('messages.Donation updated successfully'),
                'data' => $donation->load('donateItems'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the donation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    protected function getCurrentDonationItemId($attribute, $request)
    {
        // Extract the index from the attribute name (e.g., 'donates.0.financial_receipt_number' => 0)
        preg_match('/donates\.(\d+)/', $attribute, $matches);
        $index = $matches[1] ?? null;

        if ($index !== null) {
            // Get the donation item ID from the request (if provided)
            return $request->input("donates.{$index}.financial_donation_item_id");
        }

        return null;
    }
    public function destroy(Donation $donation)
    {
        $donation->collectingDonation()->delete();
        $donation->donateItems()->delete();
        $donation->delete();
        return response()->json([
            'success' => true,
            'message' => __('messages.Donation deleted successfully'),
        ]);
    }

    public function deleteDonatationItem($id)
    {
        $this->authorize('delete', Donation::class);

        $donation = DonationItem::find($id);

        if (!$donation) {
            return response()->json(['message' => 'Donation item not found.'], 404);
        }

        try {
            $donation->delete(); // Soft delete or permanent delete based on your setup
            return response()->json(['message' => 'Donation item deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete donation item.'], 500);
        }
    }



    public function storeGatheredDonation(Request $request)
    {
        $this->authorize('create', Donation::class);

        // Validate the incoming request data
        $validatedData = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            // 'date' => 'required|date',
            'donation_type' => 'required|string|in:financial,inKind,both',
            'status' => 'required|in:collected,not_collected',
            'reporting_way' => 'required|string|in:call,whatsapp_chat,other',
            'donation_category' => 'required|string|in:normal,monthly,gathered',
            'employee_id' => 'nullable|exists:employees,id',
            'collecting_date' => 'nullable|date',
            'collecting_time' => 'nullable|string',
            'collecting_way' => 'nullable|string|in:representative,location,online',
            'notes' => 'nullable|string',
            'donates' => 'required|array',
            'donates.*.financial_donation_type' => 'nullable|in:financial',
            'donates.*.financial_donation_categories_id' => 'nullable|exists:donation_categories,id',
            'donates.*.financial_amount' => 'nullable|numeric|min:0',
            'donates.*.date' => 'required|date',
            'financial_receipt_number' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if (!empty($value)) {
                        // Check if the receipt number already exists in the database
                        $financialExists = DB::table('donation_items')
                            // ->join('donation_items', 'donations.id', '=', 'donation_items.donation_id')
                            ->where('donation_items.financial_receipt_number', $value)
                            ->where('donation_items.donation_type', 'financial')
                            ->exists();

                        if ($financialExists) {
                            $fail(__('validation.unique_receipt_number_financial'));
                        }

                        // Check for duplicate receipt numbers within the same request
                        $donates = $request->get('donates', []);
                        $receiptNumbers = [];

                        foreach ($donates as $donate) {
                            if (isset($donate['financial_receipt_number']) && !empty($donate['financial_receipt_number'])) {
                                if (in_array($donate['financial_receipt_number'], $receiptNumbers)) {
                                    $fail(__('validation.duplicate_receipt_number_in_request'));
                                }
                                $receiptNumbers[] = $donate['financial_receipt_number'];
                            }
                        }
                    }
                },
            ],
        ]);

        DB::beginTransaction();

        try {

            // Process and save donates
            foreach ($validatedData['donates'] as $donateData) {

                // Create the Donation
                $donation = Donation::create([
                    'donor_id' => $validatedData['donor_id'],
                    'status' => $validatedData['status'],
                    'date' => $donateData['date'],
                    'donation_type' => $validatedData['donation_type'],
                    'donation_category' => $validatedData['donation_category'],
                    'collecting_time' => $validatedData['collecting_time'],
                    'notes' => $validatedData['notes'],
                    'alternate_date' => $validatedData['alternate_date'] ?? null,
                    'reporting_way' => $validatedData['reporting_way'] ?? null,
                ]);

                $donatesAdded = false;
                if (
                    isset($donateData['financial_donation_type']) && $donateData['financial_donation_type'] === 'financial'
                    && !empty($donateData['financial_donation_categories_id'])
                    && !empty($donateData['financial_amount'])
                ) {
                    $donation->donateItems()->create([
                        'donation_type' => $donateData['financial_donation_type'],
                        'donation_category_id' => $donateData['financial_donation_categories_id'],
                        'financial_receipt_number' => $validatedData["financial_receipt_number"] ?? null,
                        'amount' => $donateData['financial_amount'],
                    ]);

                    $donatesAdded = true;
                }

                if ($donation->status === "collected") {
                    $donation->collectingDonation()->create([
                        "employee_id" => $validatedData["employee_id"],
                        "donation_id" => $donation->id,
                        "collecting_date" => $validatedData["collecting_date"],
                        // 'financial_receipt_number' => $validatedData["financial_receipt_number"] ?? null,
                        'in_kind_receipt_number' => $validatedData["in_kind_receipt_number"] ?? null,
                        "collecting_way" => $validatedData["collecting_way"]
                    ]);
                }

                if (!$donatesAdded) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => __('validation.no_valid_donations_provided'),
                    ], 400);
                }
            }


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('messages.Donation created successfully.'),
                'data' => $donation->load('donateItems'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
