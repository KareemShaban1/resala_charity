<?php

namespace App\Http\Controllers;

use App\Models\MonthlyDonation;
use App\Http\Requests\StoreMonthlyDonationRequest;
use App\Http\Requests\UpdateMonthlyDonationRequest;
use App\Models\Department;
use App\Models\DonationCategory;
use App\Models\Donor;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MonthlyDonationController extends Controller
{
    // public function __construct()
    // {
    //     $this->model = MonthlyDonation::class;
    //     $this->viewPath = 'backend.pages.monthly_donations';
    //     $this->routePrefix = 'monthly_donations';
    //     $this->validationRules = [
    //         'donor_id' => 'required|exists:donors,id',
    //         // 'date' => 'required|string',
    //         'notes' => 'nullable|string',
    //         'collecting_donation_way' => 'required|string',
    //     ];
    // }

    public function index()
    {
        $donors = Donor::all();
        $donationCategories = DonationCategory::all();
        $departments = Department::all();
        $employees = Employee::all();
        return view( 'backend.pages.monthly_donations.index', 
        compact('donors', 'donationCategories', 'departments', 'employees'));
    }

    public function data()
    {
        $query = MonthlyDonation::with('donor','donates');

        return DataTables::of($query)
            ->addColumn('action', function ($item) {
                return '
                    <div class="d-flex gap-2">
                        <a href="javascript:void(0);" onclick="editMonthlyDonation(' . $item->id . ')"
                        class="btn btn-sm btn-info">
                            <i class="mdi mdi-square-edit-outline"></i>
                        </a>
                        <a href="javascript:void(0);" onclick="deleteRecord(' . $item->id . ', \'monthly_donations\')"
                        class="btn btn-sm btn-danger">
                            <i class="mdi mdi-delete"></i>
                        </a>
                    </div>
                ';
            })
            ->addColumn('monthly_donation_day', function ($item) {
                return $item->donor?->monthly_donation_day ?? 0;
            })
            ->addColumn('name', function ($item) {
                return $item->donor?->name ?? 'N/A';
            })
            ->addColumn('area', function ($item) {
                return $item->donor?->area?->name ?? 'N/A';
            })
            ->addColumn('address', function ($item) {
                return $item->donor?->address ?? 'N/A';
            })
            ->addColumn('phones', function ($item) {
                return $item->donor?->phones->isNotEmpty() ?
                    $item->donor->phones->map(function ($phone) {
                        return $phone->phone_number . ' (' . ucfirst($phone->phone_type) . ')';
                    })->implode(', ') : 'N/A';
            })
            ->addColumn('collecting_donation_way', function ($item) {
                switch ($item->collecting_donation_way) {
                    case $item->collecting_donation_way === 'location':
                        return __('Location');
                        break;
                    case $item->collecting_donation_way === 'online':
                        return __('Online');
                        break;
                    case $item->collecting_donation_way === 'representative':
                        return __('Representative');
                        break;
                    default:
                        return 'N/A';
                        break;
                }
            })
            ->addColumn('donates', function ($item) {
                return $item->donates->map(function ($donate) {
                    // Check donation type and format accordingly
                    if ($donate->donation_type === 'Financial') {
                        // Show donation_type, category name, and amount with bold donation type
                        return '<strong class="donation-type financial">' . __('Financial Donation') . ':' . '</strong> ' . 
                               ($donate->donationCategory->name ?? 'N/A') . ' - ' . $donate->amount;
                    } elseif ($donate->donation_type === 'inKind') {
                        // Show donation_type, item_name, and amount with bold donation type
                        return '<strong class="donation-type in-kind">' . __('inKind Donation') . ':' . '</strong> ' . 
                               ($donate->item_name ?? 'N/A') . ' - ' . $donate->amount;
                    }
                    return ''; // Return empty string if no matching donation type
                })->implode('<br>'); // Display each donation on a new line
            })
            
            ->editColumn('created_at', function ($item) {
                return $item->created_at->format('Y-m-d H:i:s');
            })
            ->rawColumns(['action','donates'])
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

    // public function store(Request $request)
    // {
    //     // Validate the incoming request data
    //     $validatedData = $request->validate($this->validationRules());

    //     // Create the MonthlyDonation
    //     $monthlyDonation = MonthlyDonation::create($validatedData);

    //     // Handle the related monthly_donation_donates data
    //     if ($request->has('donates')) {
    //         foreach ($request->input('donates') as $donateData) {
    //             $monthlyDonation->donates()->create($donateData);
    //         }
    //     }

    //     return response()->json($monthlyDonation, 201);
    // }


    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'department_id' => 'required|exists:departments,id',
            'employee_id' => 'required|exists:employees,id',
            'collecting_donation_way' => 'required|string',
            'donates' => 'required|array',
            'donates.*.financial_donation_type' => 'required|in:Financial',
            'donates.*.inKind_donation_type' => 'required|in:inKind',
            'donates.*.financial_donation_categories_id' => 'nullable|exists:donation_categories,id',
            'donates.*.financial_amount' => 'nullable|numeric|min:0',
            'donates.*.in_kind_item_name' => 'nullable|string',
            'donates.*.in_kind_quantity' => 'nullable|integer|min:1',
        ]);
    
        DB::beginTransaction(); // Start a transaction
    
        try {
            // Create the MonthlyDonation
            $monthlyDonation = MonthlyDonation::create([
                'donor_id' => $validatedData['donor_id'],
                'collecting_donation_way' => $validatedData['collecting_donation_way'],
                'department_id' => $validatedData['department_id'],
                'employee_id' => $validatedData['employee_id'],
            ]);
    
            $donatesAdded = false; // Flag to check if any donates were added
    
            // Process and save donates
            foreach ($validatedData['donates'] as $donateData) {
                if ($donateData['financial_donation_type'] === 'Financial' 
                    && !empty($donateData['financial_donation_categories_id']) 
                    && !empty($donateData['financial_amount'])) {
                    
                    $monthlyDonation->donates()->create([
                        'donation_type' => $donateData['financial_donation_type'],
                        'donation_category_id' => $donateData['financial_donation_categories_id'],
                        'amount' => $donateData['financial_amount'],
                    ]);
    
                    $donatesAdded = true;
                } 
                
                if ($donateData['inKind_donation_type'] === 'inKind' 
                    && !empty($donateData['in_kind_item_name']) 
                    && !empty($donateData['in_kind_quantity'])) {
                    
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
                    'message' => 'No valid donations were provided.',
                ], 400);
            }
    
            DB::commit(); // Commit the transaction
    
            return response()->json([
                'success' => true,
                'message' => 'Donors imported successfully!',
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
    return response()->json($monthlyDonation->load(['donates', 'donor']));

}
public function update(Request $request, MonthlyDonation $monthlyDonation)
{
    // Validate the incoming request data
    $validatedData = $request->validate([
        'donor_id' => 'required|exists:donors,id',
        'department_id' => 'required|exists:departments,id',
        'employee_id' => 'required|exists:employees,id',
        'collecting_donation_way' => 'required|string',
        'donates' => 'required|array',
        'donates.*.id' => 'nullable|exists:donates,id',
        'donates.*.financial_monthuly_donation_id' => 'nullable|exists:monthly_donations_donates,id',
        'donates.*.inkind_monthuly_donation_id' => 'nullable|exists:monthly_donations_donates,id',
        'donates.*.financial_donation_type' => 'required|in:Financial',
        'donates.*.inKind_donation_type' => 'required|in:inKind',
        'donates.*.financial_donation_categories_id' => 'nullable|exists:donation_categories,id',
        'donates.*.financial_amount' => 'nullable|numeric|min:0',
        'donates.*.in_kind_item_name' => 'nullable|string',
        'donates.*.in_kind_quantity' => 'nullable|integer|min:1',
    ]);

    DB::beginTransaction(); // Start a transaction

    try {
        // Update the MonthlyDonation
        $monthlyDonation->update([
            'donor_id' => $validatedData['donor_id'],
            'collecting_donation_way' => $validatedData['collecting_donation_way'],
            'department_id' => $validatedData['department_id'],
            'employee_id' => $validatedData['employee_id'],
        ]);

        $donatesUpdated = false; // Flag to check if any donations were processed

        // Process donations
        foreach ($validatedData['donates'] as $donateData) {
            // Update or create financial donations
            if ($donateData['financial_donation_type'] === 'Financial' 
                && !empty($donateData['financial_donation_categories_id']) 
                && !empty($donateData['financial_amount'])) {

                $donation = $monthlyDonation->donates()->updateOrCreate(
                    ['id' => $donateData['financial_monthuly_donation_id'] ?? null], // Match by ID if it exists
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
            if ($donateData['inKind_donation_type'] === 'inKind' 
                && !empty($donateData['in_kind_item_name']) 
                && !empty($donateData['in_kind_quantity'])) {

                $donation = $monthlyDonation->donates()->updateOrCreate(
                    ['id' => $donateData['inkind_monthuly_donation_id'] ?? null], // Match by ID if it exists
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
                'message' => 'No valid donations were provided.',
            ], 400);
        }

        DB::commit(); // Commit the transaction

        return response()->json([
            'success' => true,
            'message' => 'Monthly donation updated successfully!',
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

    
}
