<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\UpdateDonationRequest;
use App\Models\DonationCategory;
use App\Models\DonationItem;
use App\Models\Donor;
use App\Models\Employee;
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



    public function data()
    {

        $query = Donation::query()
            ->selectRaw('
        donations.id,
        donations.donor_id,
        donations.status,
        donations.created_by,
        donations.created_at,
        donation_collectings.collecting_date,
        donation_collectings.receipt_number,
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
                'donation_collectings.collecting_date',
                'donation_collectings.receipt_number'
            );

        if (request()->has('status')) {
            $status = request('status');
            if ($status === 'collected') {
                $query->where('donations.status', 'collected');
            } elseif ($status === 'not_collected') {
                $query->where('donations.status', 'not_collected');
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
            ->addColumn('donateItems', function ($item) {
                return $item->donateItems->map(function ($donate) {
                    if ($donate->donation_type === 'Financial') {
                        return '<strong class="donation-type financial">' . __('Financial Donation') . ':</strong> ' .
                            ($donate->donationCategory->name ?? 'N/A') . ' - ' . $donate->amount;
                    } elseif ($donate->donation_type === 'inKind') {
                        return '<strong class="donation-type in-kind">' . __('inKind Donation') . ':</strong> ' .
                            ($donate->item_name ?? 'N/A') . ' - ' . $donate->amount;
                    }
                    return '';
                })->implode('<br>');
            })
            ->addColumn('receipt_number', function ($item) {
                return $item->collectingDonation?->receipt_number ?? 'N/A';
            })
            ->rawColumns(['action', 'donateItems'])
            ->make(true);
    }



    public function getDonationDetails($id)
    {
        $donation = Donation::with('donor', 'donateItems', 'collectingDonation')->findOrFail($id);
        return response()->json($donation);
    }


    // public function store(StoreDonationRequest $request)
    // {

    //     $this->authorize('create', Donation::class);

    //     // Validate the incoming request data
    //     $validatedData = $request->validated();

    //     DB::beginTransaction(); // Start a transaction

    //     try {

    //         // Create the MonthlyDonation
    //         $donation = Donation::create([
    //             'donor_id' => $validatedData['donor_id'],
    //             'status' => $validatedData['status'],
    //             'date' => $validatedData['date'],
    //         ]);



    //         $donatesAdded = false; // Flag to check if any donates were added

    //         // Process and save donates
    //         foreach ($validatedData['donates'] as $donateData) {
    //             if (
    //                 isset($donateData['financial_donation_type']) && $donateData['financial_donation_type'] === 'Financial'
    //                 && !empty($donateData['financial_donation_categories_id'])
    //                 && !empty($donateData['financial_amount'])
    //             ) {

    //                 $donation->donateItems()->create([
    //                     'donation_type' => $donateData['financial_donation_type'],
    //                     'donation_category_id' => $donateData['financial_donation_categories_id'],
    //                     'amount' => $donateData['financial_amount'],
    //                 ]);

    //                 $donatesAdded = true;
    //             }

    //             if (
    //                 isset($donateData['inKind_donation_type']) && $donateData['inKind_donation_type'] === 'inKind'
    //                 && !empty($donateData['in_kind_item_name'])
    //                 && !empty($donateData['in_kind_quantity'])
    //             ) {

    //                 $donation->donateItems()->create([
    //                     'donation_type' => $donateData['inKind_donation_type'],
    //                     'donation_category_id' => null,
    //                     'item_name' => $donateData['in_kind_item_name'],
    //                     'amount' => $donateData['in_kind_quantity'],
    //                 ]);

    //                 $donatesAdded = true;
    //             }
    //         }

    //         if($donation->status === "collected"){

    //             $donation->collectingDonation()->create([
    //                 "employee_id"=> $validatedData["employee_id"],
    //                 "donation_id"=>$donation->id,
    //                 "collecting_date"=>$validatedData["collecting_date"],
    //                 'receipt_number'=>$validatedData["receipt_number"],
    //             ]);

    //         }

    //         // Check if no donates were added
    //         if (!$donatesAdded) {
    //             DB::rollBack(); // Rollback the transaction
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => __('validation.no_valid_donations_provided'),
    //             ], 400);
    //         }

    //         DB::commit(); // Commit the transaction

    //         return response()->json([
    //             'success' => true,
    //             'message' => __('messages.donation_added_successfully'),
    //             'data' => $donation->load('donateItems'),
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack(); // Rollback the transaction on error
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An error occurred while processing the request.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        $this->authorize('create', Donation::class);

        // Validate the incoming request data
        $validatedData = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'date' => 'required|date',
            'donation_type' => 'required|string|in:Financial,inKind',
            'status' => 'required|in:collected,not_collected',
            'employee_id' => 'nullable|exists:employees,id',
            'collecting_date' => 'nullable|date',
            'receipt_number' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if (!empty($value)) {
                        $financialExists = DB::table('donations')
                            ->join('donation_items', 'donations.id', '=', 'donation_items.donation_id')
                            ->join('donation_collectings', 'donations.id', '=', 'donation_collectings.donation_id')
                            ->where('donation_collectings.receipt_number', $value)
                            ->where('donation_items.donation_type', 'Financial')
                            ->exists();

                        $inKindExists = DB::table('donations')
                            ->join('donation_items', 'donations.id', '=', 'donation_items.donation_id')
                            ->join('donation_collectings', 'donations.id', '=', 'donation_collectings.donation_id')
                            ->where('donation_collectings.receipt_number', $value)
                            ->where('donation_items.donation_type', 'inKind')
                            ->exists();

                        $donates = $request->get('donates', []);

                        $hasFinancial = collect($donates)
                            ->contains(fn($d) =>
                            isset($d['financial_donation_type']) && $d['financial_donation_type'] === 'Financial' &&
                                !empty($d['financial_donation_categories_id']) &&
                                !empty($d['financial_amount']));
                        $hasInKind = collect($donates)->contains(
                            fn($d) =>
                            isset($d['inKind_donation_type']) && $d['inKind_donation_type'] === 'inKind' &&
                                !empty($d['in_kind_item_name']) &&
                                !empty($d['in_kind_quantity'])
                        );


                        // Fail validation if financial donation type exists and receipt number is already used
                        if ($hasFinancial && $financialExists) {
                            $fail(__('validation.unique_receipt_number_financial'));
                        }

                        // Fail validation if in-kind donation type exists and receipt number is already used
                        if ($hasInKind && $inKindExists) {
                            $fail(__('validation.unique_receipt_number_inKind'));
                        }
                    }
                },
            ],
            'donates' => 'required|array',
            'donates.*.financial_donation_type' => 'nullable|in:Financial',
            'donates.*.financial_donation_categories_id' => 'nullable|exists:donation_categories,id',
            'donates.*.financial_amount' => 'nullable|numeric|min:0',
            'donates.*.inKind_donation_type' => 'nullable|in:inKind',
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
            ]);

            $donatesAdded = false;

            // Process and save donates
            foreach ($validatedData['donates'] as $donateData) {
                if (
                    isset($donateData['financial_donation_type']) && $donateData['financial_donation_type'] === 'Financial'
                    && !empty($donateData['financial_donation_categories_id'])
                    && !empty($donateData['financial_amount'])
                ) {
                    $donation->donateItems()->create([
                        // 'donation_type' => $donateData['financial_donation_type'],
                        'donation_category_id' => $donateData['financial_donation_categories_id'],
                        'amount' => $donateData['financial_amount'],
                    ]);

                    $donatesAdded = true;
                }

                if (
                    isset($donateData['inKind_donation_type']) && $donateData['inKind_donation_type'] === 'inKind'
                    && !empty($donateData['in_kind_item_name'])
                    && !empty($donateData['in_kind_quantity'])
                ) {
                    $donation->donateItems()->create([
                        // 'donation_type' => $donateData['inKind_donation_type'],
                        'donation_category_id' => null,
                        'item_name' => $donateData['in_kind_item_name'],
                        'amount' => $donateData['in_kind_quantity'],
                    ]);

                    $donatesAdded = true;
                }
            }

            if ($donation->status === "collected") {
                $donation->collectingDonation()->create([
                    "employee_id" => $validatedData["employee_id"],
                    "donation_id" => $donation->id,
                    "collecting_date" => $validatedData["collecting_date"],
                    'receipt_number' => $validatedData["receipt_number"],
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
                'message' => __('messages.donation_added_successfully'),
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
    // public function update(UpdateDonationRequest $request, Donation $donation)
    // {
    //     $this->authorize('update', Donation::class);

    //     // Validate the incoming request data
    //     $validatedData = $request->validated();

    //     DB::beginTransaction(); // Start a transaction

    //     try {
    //         // Update the Donation
    //         $donation->update([
    //             'donor_id' => $validatedData['donor_id'],
    //             'status' => $validatedData['status'],
    //             'date' => $validatedData['date'],
    //         ]);

    //         $donatesUpdated = false; // Flag to check if any donations were processed

    //         // Process donations
    //         foreach ($validatedData['donates'] as $donateData) {
    //             // Update or create financial donations
    //             if (
    //                 isset($donateData['financial_donation_type']) && $donateData['financial_donation_type'] === 'Financial'
    //                 && !empty($donateData['financial_donation_categories_id'])
    //                 && !empty($donateData['financial_amount'])
    //             ) {
    //                 $donation->donateItems()->updateOrCreate(
    //                     ['id' => $donateData['financial_donation_id'] ?? null], // Match by ID if it exists
    //                     [
    //                         'donation_type' => $donateData['financial_donation_type'],
    //                         'donation_category_id' => $donateData['financial_donation_categories_id'],
    //                         'amount' => $donateData['financial_amount'],
    //                         'item_name' => null, // Ensure financial donations do not have item names
    //                     ]
    //                 );

    //                 $donatesUpdated = true;
    //             }

    //             // Update or create in-kind donations
    //             if (
    //                 isset($donateData['inKind_donation_type']) && $donateData['inKind_donation_type'] === 'inKind'
    //                 && !empty($donateData['in_kind_item_name'])
    //                 && !empty($donateData['in_kind_quantity'])
    //             ) {
    //                 $donation->donateItems()->updateOrCreate(
    //                     ['id' => $donateData['inkind_donation_id'] ?? null], // Match by ID if it exists
    //                     [
    //                         'donation_type' => $donateData['inKind_donation_type'],
    //                         'donation_category_id' => null, // In-kind donations do not have a category
    //                         'item_name' => $donateData['in_kind_item_name'],
    //                         'amount' => $donateData['in_kind_quantity'],
    //                     ]
    //                 );

    //                 $donatesUpdated = true;
    //             }
    //         }

    //         // Check if no donations were updated or added
    //         if (!$donatesUpdated) {
    //             DB::rollBack(); // Rollback the transaction
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => __('validation.no_valid_donations_provided'),
    //             ], 400);
    //         }

    //         DB::commit(); // Commit the transaction

    //         return response()->json([
    //             'success' => true,
    //             'message' => __('messages.donation_updated_successfully'),
    //             'data' => $donation->load('donateItems'),
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack(); // Rollback the transaction on error
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An error occurred while processing the request.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }


    public function update(Request $request, Donation $donation)
    {
        $this->authorize('update', $donation);

        // Validate the incoming request data
        $validatedData = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'date' => 'required|date',
            'status' => 'required|in:collected,not_collected',
            'employee_id' => 'nullable|exists:employees,id',
            'collecting_date' => 'nullable|date',
            'receipt_number' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request, $donation) {
                    if (!empty($value)) {
                        $financialExists = DB::table('donations')
                            ->join('donation_items', 'donations.id', '=', 'donation_items.donation_id')
                            ->join('donation_collectings', 'donations.id', '=', 'donation_collectings.donation_id')
                            ->where('donation_collectings.receipt_number', $value)
                            ->where('donation_items.donation_type', 'Financial')
                            ->where('donations.id', '!=', $donation->id)
                            ->exists();

                        $inKindExists = DB::table('donations')
                            ->join('donation_items', 'donations.id', '=', 'donation_items.donation_id')
                            ->join('donation_collectings', 'donations.id', '=', 'donation_collectings.donation_id')
                            ->where('donation_collectings.receipt_number', $value)
                            ->where('donation_items.donation_type', 'inKind')
                            ->where('donations.id', '!=', $donation->id)
                            ->exists();

                        $donates = $request->get('donates', []);

                        $hasFinancial = collect($donates)
                            ->contains(fn($d) =>
                            isset($d['financial_donation_type']) && $d['financial_donation_type'] === 'Financial' &&
                                !empty($d['financial_donation_categories_id']) &&
                                !empty($d['financial_amount']));

                        $hasInKind = collect($donates)->contains(
                            fn($d) =>
                            isset($d['inKind_donation_type']) && $d['inKind_donation_type'] === 'inKind' &&
                                !empty($d['in_kind_item_name']) &&
                                !empty($d['in_kind_quantity'])
                        );

                        if ($hasFinancial && $financialExists) {
                            $fail(__('validation.unique_receipt_number_financial'));
                        }

                        if ($hasInKind && $inKindExists) {
                            $fail(__('validation.unique_receipt_number_inKind'));
                        }
                    }
                },
            ],
            'donates' => 'required|array',
            'donates.*.financial_donation_type' => 'nullable|in:Financial',
            'donates.*.financial_donation_categories_id' => 'nullable|exists:donation_categories,id',
            'donates.*.financial_amount' => 'nullable|numeric|min:0',
            'donates.*.inKind_donation_type' => 'nullable|in:inKind',
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
            ]);

            $donatesAdded = false;

            // Update donateItems
            $donation->donateItems()->delete();

            foreach ($validatedData['donates'] as $donateData) {
                if (
                    isset($donateData['financial_donation_type']) && $donateData['financial_donation_type'] === 'Financial' &&
                    !empty($donateData['financial_donation_categories_id']) &&
                    !empty($donateData['financial_amount'])
                ) {
                    $donation->donateItems()->create([
                        'donation_type' => $donateData['financial_donation_type'],
                        'donation_category_id' => $donateData['financial_donation_categories_id'],
                        'amount' => $donateData['financial_amount'],
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
                        'receipt_number' => $validatedData["receipt_number"],
                    ]
                );
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
                'message' => __('messages.donation_updated_successfully'),
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


    public function destroy(Donation $donation)
    {
        $donation->collectingDonation()->delete();
        $donation->donateItems()->delete();
        $donation->delete();
        return response()->json([
            'success' => true,
            'message' => __('messages.donation_deleted_successfully'),
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
            return response()->json(['message' => 'Donation item deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete donation item.'], 500);
        }
    }
}
