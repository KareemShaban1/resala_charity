<?php

namespace App\Http\Controllers;

use App\Models\MonthlyDonation;
use App\Http\Requests\StoreMonthlyDonationRequest;
use App\Http\Requests\UpdateMonthlyDonationRequest;
use App\Models\Department;
use App\Models\DonationCategory;
use App\Models\Donor;
use App\Models\Employee;
use App\Models\MonthlyDonationsDonate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MonthlyDonationController extends Controller
{
    public function index()
    {
        // Check if the user is authorized to view monthly donation
        $this->authorize('view', MonthlyDonation::class);

        $donors = Donor::all();
        $donationCategories = DonationCategory::all();
        $departments = Department::all();
        $employees = Employee::all();
        return view(
            'backend.pages.monthly_donations.index',
            compact('donors', 'donationCategories', 'departments', 'employees')
        );
    }

    public function cancelledMonthlyDonations()
    {
        $donors = Donor::all();
        $donationCategories = DonationCategory::all();
        $departments = Department::all();
        $employees = Employee::all();
        return view(
            'backend.pages.monthly_donations.cancelled',
            compact('donors', 'donationCategories', 'departments', 'employees')
        );
    }



    public function data()
    {

        $query = MonthlyDonation::query()
            ->selectRaw('
        monthly_donations.id,
        monthly_donations.donor_id,
        monthly_donations.collecting_donation_way,
        monthly_donations.created_at,
        monthly_donations.status,
        monthly_donations.cancellation_reason,
        monthly_donations.cancellation_date,
        donors.name as donor_name,
        areas.name as area_name,
        donors.address,
        GROUP_CONCAT(DISTINCT donor_phones.phone_number SEPARATOR ", ") as phone_numbers
    ')
            ->leftJoin('donors', 'monthly_donations.donor_id', '=', 'donors.id')
            ->leftJoin('areas', 'donors.area_id', '=', 'areas.id')
            ->leftJoin('donor_phones', 'donors.id', '=', 'donor_phones.donor_id')
            ->with('donor', 'donates')
            ->groupBy(
                'monthly_donations.donor_id',
                'donors.name',
                'areas.name',
                'donors.address',
                'monthly_donations.id',
                'monthly_donations.created_at',
                'monthly_donations.collecting_donation_way',
                'monthly_donations.status',
                'monthly_donations.cancellation_reason',
                'monthly_donations.cancellation_date'
            );

        if (request()->has('status')) {
            $status = request('status');
            if ($status === 'ongoing') {
                $query->where('monthly_donations.status', 'ongoing');
            } elseif ($status === 'cancelled') {
                $query->where('monthly_donations.status', 'cancelled');
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
                    <a href="javascript:void(0);" onclick="editMonthlyDonation(' . $item->id . ')"
                    class="btn btn-sm btn-info">
                        <i class="mdi mdi-square-edit-outline"></i>
                    </a>
                    <a href="javascript:void(0);" onclick="deleteRecord(' . $item->id . ', \'monthly-donations\')"
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
            ->addColumn('donates', function ($item) {
                return $item->donates->map(function ($donate) {
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
            ->addColumn('cancellation_reason', function ($item) {
                return $item->cancellation_reason;
            })
            ->addColumn('cancellation_date', function ($item) {
                return $item->cancellation_date;
            })
            ->rawColumns(['action', 'donates'])
            ->make(true);
    }


    protected function getUpdateValidationRules($id)
    {
        return [
            'donor_id' => 'required|exists:donors,id',
            // 'date' => 'required|string',
            'notes' => 'nullable|string',
            'collecting_donation_way' => 'required|string',
        ];
    }


    public function getMonthlyDonation($id)
    {
        $monthlyDonation = MonthlyDonation::with('donor', 'donates')->findOrFail($id);
        return response()->json($monthlyDonation);
    }


    public function store(StoreMonthlyDonationRequest $request)
    {

        $this->authorize('create', MonthlyDonation::class);

        // Validate the incoming request data
        $validatedData = $request->validated();

        DB::beginTransaction(); // Start a transaction

        try {
            // Create the MonthlyDonation
            $monthlyDonation = MonthlyDonation::create([
                'donor_id' => $validatedData['donor_id'],
                'collecting_donation_way' => $validatedData['collecting_donation_way'],
                'status' => $validatedData['status'],
                'department_id' => $validatedData['department_id'],
                'employee_id' => $validatedData['employee_id'],
                'cancellation_reason' => $validatedData['cancellation_reason'],
                'cancellation_date' => $validatedData['cancellation_date'],
            ]);

            $donatesAdded = false; // Flag to check if any donates were added

            // Process and save donates
            foreach ($validatedData['donates'] as $donateData) {
                if (
                    $donateData['financial_donation_type'] === 'Financial'
                    && !empty($donateData['financial_donation_categories_id'])
                    && !empty($donateData['financial_amount'])
                ) {

                    $monthlyDonation->donates()->create([
                        'donation_type' => $donateData['financial_donation_type'],
                        'donation_category_id' => $donateData['financial_donation_categories_id'],
                        'amount' => $donateData['financial_amount'],
                    ]);

                    $donatesAdded = true;
                }

                if (
                    $donateData['inKind_donation_type'] === 'inKind'
                    && !empty($donateData['in_kind_item_name'])
                    && !empty($donateData['in_kind_quantity'])
                ) {

                    $monthlyDonation->donates()->create([
                        'donation_type' => $donateData['inKind_donation_type'],
                        'donation_category_id' => null,
                        'item_name' => $donateData['in_kind_item_name'],
                        'amount' => $donateData['in_kind_quantity'],
                    ]);

                    $donatesAdded = true;
                }
            }

            // Check if no donates were added
            if (!$donatesAdded) {
                DB::rollBack(); // Rollback the transaction
                return response()->json([
                    'success' => false,
                    'message' => __('validation.no_valid_donations_provided'),
                ], 400);
            }

            DB::commit(); // Commit the transaction

            return response()->json([
                'success' => true,
                'message' => __('messages.Monthly Donation created successfully'),
                'data' => $monthlyDonation->load('donates'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function edit(MonthlyDonation $monthlyDonation)
    {
        $this->authorize('update', MonthlyDonation::class);

        return response()->json($monthlyDonation->load(['donates', 'donor']));
    }
    public function update(UpdateMonthlyDonationRequest $request, MonthlyDonation $monthlyDonation)
    {

        $this->authorize('update', MonthlyDonation::class);

        // Validate the incoming request data
        $validatedData = $request->validated();

        DB::beginTransaction(); // Start a transaction

        try {
            // Update the MonthlyDonation
            $monthlyDonation->update([
                'donor_id' => $validatedData['donor_id'],
                'collecting_donation_way' => $validatedData['collecting_donation_way'],
                'status' => $validatedData['status'],
                'department_id' => $validatedData['department_id'],
                'employee_id' => $validatedData['employee_id'],
                'cancellation_reason' => $validatedData['cancellation_reason'],
                'cancellation_date' => $validatedData['cancellation_date'],
            ]);

            $donatesUpdated = false; // Flag to check if any donations were processed

            // Process donations
            foreach ($validatedData['donates'] as $donateData) {
                // Update or create financial donations
                if (
                    isset($donateData['financial_donation_type']) && $donateData['financial_donation_type'] === 'Financial'
                    && !empty($donateData['financial_donation_categories_id'])
                    && !empty($donateData['financial_amount'])
                ) {

                    $donation = $monthlyDonation->donates()->updateOrCreate(
                        ['id' => $donateData['financial_monthly_donation_id'] ?? null], // Match by ID if it exists
                        [
                            'donation_type' => $donateData['financial_donation_type'],
                            'donation_category_id' => $donateData['financial_donation_categories_id'],
                            'amount' => $donateData['financial_amount'],
                            'item_name' => null, // Ensure financial donations do not have item names
                        ]
                    );

                    $donatesUpdated = true;
                }

                // Update or create in-kind donations
                if (
                    isset($donateData['inKind_donation_type']) && $donateData['inKind_donation_type'] === 'inKind'
                    && !empty($donateData['in_kind_item_name'])
                    && !empty($donateData['in_kind_quantity'])
                ) {

                    $donation = $monthlyDonation->donates()->updateOrCreate(
                        ['id' => $donateData['inkind_monthly_donation_id'] ?? null], // Match by ID if it exists
                        [
                            'donation_type' => $donateData['inKind_donation_type'],
                            'donation_category_id' => null, // In-kind donations do not have a category
                            'item_name' => $donateData['in_kind_item_name'],
                            'amount' => $donateData['in_kind_quantity'],
                        ]
                    );

                    $donatesUpdated = true;
                }
            }

            // Check if no donations were updated or added
            if (!$donatesUpdated) {
                DB::rollBack(); // Rollback the transaction
                return response()->json([
                    'success' => false,
                    'message' => __('validation.no_valid_donations_provided'),
                ], 400);
            }

            DB::commit(); // Commit the transaction

            return response()->json([
                'success' => true,
                'message' => __('messages.Monthly Donation updated successfully'),
                'data' => $monthlyDonation->load('donates'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(MonthlyDonation $monthlyDonation)
    {
        DB::beginTransaction(); // Start a transaction

        try {
            $monthlyDonation->donates()->delete();
            $monthlyDonation->delete();
            DB::commit(); // Commit the transaction
            return response()->json([
                'success' => true,
                'message' => __('messages.Monthly Donation deleted successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function deleteDonate($id)
    {
        $this->authorize('delete', MonthlyDonation::class);

        $donation = MonthlyDonationsDonate::find($id);

        if (!$donation) {
            return response()->json(['message' => 'Donation not found.'], 404);
        }

        try {
            $donation->delete(); // Soft delete or permanent delete based on your setup
            return response()->json(['message' => 'Donation deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete donation.'], 500);
        }
    }
}
