<?php

namespace App\Http\Controllers;

use App\Models\MonthlyForm;
use App\Http\Requests\StoreMonthlyFormRequest;
use App\Http\Requests\UpdateMonthlyFormRequest;
use App\Imports\MonthlyFormsImport;
use App\Imports\MonthlyFormsItemsImport;
use App\Models\Department;
use App\Models\DonationCategory;
use App\Models\Donor;
use App\Models\Employee;
use App\Models\MonthlyFormItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class MonthlyFormController extends Controller
{
    public function index()
    {
        // Check if the user is authorized to view monthly forms
        $this->authorize('view', MonthlyForm::class);

        $donors = Donor::all();
        $donationCategories = DonationCategory::all();
        $departments = Department::all();
        $employees = Employee::all();
        return view(
            'backend.pages.monthly_forms.index',
            compact('donors', 'donationCategories', 'departments', 'employees')
        );
    }

    public function cancelledMonthlyForms()
    {
        $donors = Donor::all();
        $donationCategories = DonationCategory::all();
        $departments = Department::all();
        $employees = Employee::all();
        return view(
            'backend.pages.monthly_forms.cancelled',
            compact('donors', 'donationCategories', 'departments', 'employees')
        );
    }



    public function data()
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
            areas.name as area_name,
            donors.address,
            GROUP_CONCAT(DISTINCT donor_phones.phone_number SEPARATOR ", ") as phone_numbers,
            SUM(CASE WHEN monthly_forms_items.donation_type = "financial" THEN monthly_forms_items.amount ELSE 0 END) as financial_amount
        ')
            ->leftJoin('donors', 'monthly_forms.donor_id', '=', 'donors.id')
            ->leftJoin('areas', 'donors.area_id', '=', 'areas.id')
            ->leftJoin('donor_phones', 'donors.id', '=', 'donor_phones.donor_id')
            ->leftJoin('monthly_forms_items', 'monthly_forms.id', '=', 'monthly_forms_items.monthly_form_id')
            ->with(['donor', 'items'])
            ->groupBy(
                'monthly_forms.id',
                'monthly_forms.donor_id',
                'monthly_forms.collecting_donation_way',
                'monthly_forms.created_at',
                'monthly_forms.status',
                'monthly_forms.cancellation_reason',
                'monthly_forms.cancellation_date',
                'donors.name',
                'areas.name',
                'donors.address'
            );

        if (request()->has('columns')) {
            foreach (request()->get('columns') as $column) {
                $searchValue = $column['search']['value'];
                $columnName = $column['name'];

                if ($searchValue) {
                    if ($columnName === 'phones') {
                        $query->whereHas('donor.phones', function ($q) use ($searchValue) {
                            $q->where('phone_number', 'like', "%{$searchValue}%");
                        });
                    } elseif ($columnName === 'name') {
                        $query->whereHas('donor', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%{$searchValue}%");
                        });
                    } elseif ($columnName === 'area') {
                        $query->whereHas('donor.area', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%{$searchValue}%");
                        });
                    } elseif ($columnName === 'monthly_donation_way') {
                        $query->where('monthly_forms.collecting_donation_way', 'like', "%{$searchValue}%");
                    } else {
                        $query->where($columnName, 'like', "%{$searchValue}%");
                    }
                }
            }
        }


        if (request()->has('status')) {
            $status = request('status');
            if ($status === 'ongoing') {
                $query->where('monthly_forms.status', 'ongoing');
            } elseif ($status === 'cancelled') {
                $query->where('monthly_forms.status', 'cancelled');
            }
        }

        // Date filter
        if (request()->has('date_filter')) {
            $dateFilter = request('date_filter');
            $startDate = request('start_date');
            $endDate = request('end_date');

            if ($dateFilter === 'today') {
                $query->whereDate('monthly_forms.form_date', operator: today());
            } elseif ($dateFilter === 'week') {
                $query->whereBetween('monthly_forms.form_date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($dateFilter === 'month') {
                $query->whereBetween('monthly_forms.form_date', [now()->startOfMonth(), now()->endOfMonth()]);
            } elseif ($dateFilter === 'range' && $startDate && $endDate) {
                $query->whereBetween('monthly_forms.form_date', [$startDate, $endDate]);
            }
        }

        // Donation category filter
        if (
            request()->has('department') &&
            request('department') != null && request('department') !== 'all'
        ) {
            $query->where('monthly_forms.department_id', request('department'));
        }

        if (
            request()->has('follow_up_department') &&
            request('follow_up_department') != null && request('follow_up_department') !== 'all'
        ) {
            $query->where('monthly_forms.follow_up_department_id', request('follow_up_department'));
        }

        if (
            request()->has('employee') &&
            request('employee') != null
            && request('employee') !== 'all'
        ) {
            $query->where('monthly_forms.employee_id', request('employee'));
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
                 <a href="javascript:void(0);" onclick="monthlyFormDetails(' . $item->id . ')"
                    class="btn btn-sm btn-light">
                        <i class="mdi mdi-eye"></i>
                    </a>
                    <a href="javascript:void(0);" onclick="editMonthlyForm(' . $item->id . ')"
                    class="btn btn-sm btn-info">
                        <i class="mdi mdi-square-edit-outline"></i>
                    </a>
                    <a href="javascript:void(0);" onclick="deleteRecord(' . $item->id . ', \'monthly-forms\')"
                    class="btn btn-sm btn-danger">
                        <i class="mdi mdi-delete"></i>
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
            ->addColumn('financial_amount', function ($item) {
                return $item->items->sum(function ($donate) {
                    return $donate->donation_type === 'financial' ? $donate->amount : 0;
                });
            })
            ->rawColumns(['action', 'items'])
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


    public function getMonthlyForm($id)
    {
        $monthlyForm = MonthlyForm::with('donor', 'items')->findOrFail($id);
        return response()->json($monthlyForm);
    }


    public function store(StoreMonthlyFormRequest $request)
    {

        $this->authorize('create', MonthlyForm::class);

        // Validate the incoming request data
        $validatedData = $request->validated();

        DB::beginTransaction(); // Start a transaction

        try {
            // Create the MonthlyForm
            $monthlyForm = MonthlyForm::create([
                'donor_id' => $validatedData['donor_id'],
                'collecting_donation_way' => $validatedData['collecting_donation_way'],
                'status' => $validatedData['status'],
                'notes' => $validatedData['notes'],
                'department_id' => $validatedData['department_id'],
                'employee_id' => $validatedData['employee_id'],
                'cancellation_reason' => $validatedData['cancellation_reason'],
                'cancellation_date' => $validatedData['cancellation_date'],
                'donation_type' => $validatedData['donation_type'],
                'form_date' => $validatedData['form_date'],
                'follow_up_department_id' => $validatedData['follow_up_department_id'],
            ]);

            $itemsAdded = false; // Flag to check if any items were added

            // Process and save items
            foreach ($validatedData['items'] as $donateData) {
                if (
                    isset($donateData['financial_donation_type']) && $donateData['financial_donation_type'] === 'financial'
                    && !empty($donateData['financial_donation_categories_id'])
                    && !empty($donateData['financial_amount'])
                ) {

                    $monthlyForm->items()->create([
                        'donation_type' => $donateData['financial_donation_type'],
                        'donation_category_id' => $donateData['financial_donation_categories_id'],
                        'amount' => $donateData['financial_amount'],
                    ]);

                    $itemsAdded = true;
                }

                if (
                    isset($donateData['inKind_donation_type']) &&  $donateData['inKind_donation_type'] === 'inKind'
                    && !empty($donateData['in_kind_item_name'])
                    && !empty($donateData['in_kind_quantity'])
                ) {

                    $monthlyForm->items()->create([
                        'donation_type' => $donateData['inKind_donation_type'],
                        'donation_category_id' => null,
                        'item_name' => $donateData['in_kind_item_name'],
                        'amount' => $donateData['in_kind_quantity'],
                    ]);

                    $itemsAdded = true;
                }
            }

            // Check if no items were added
            if (!$itemsAdded) {
                DB::rollBack(); // Rollback the transaction
                return response()->json([
                    'success' => false,
                    'message' => __('validation.no_valid_forms_provided'),
                ], 400);
            }

            DB::commit(); // Commit the transaction

            return response()->json([
                'success' => true,
                'message' => __('messages.Monthly Form created successfully'),
                'data' => $monthlyForm->load('items'),
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


    public function edit(MonthlyForm $monthlyForm)
    {
        $this->authorize('update', MonthlyForm::class);

        return response()->json($monthlyForm->load(['items', 'donor']));
    }
    public function update(UpdateMonthlyFormRequest $request, MonthlyForm $monthlyForm)
    {

        $this->authorize('update', MonthlyForm::class);

        // Validate the incoming request data
        $validatedData = $request->validated();

        DB::beginTransaction(); // Start a transaction

        try {
            // Update the MonthlyForm
            $monthlyForm->update([
                'donor_id' => $validatedData['donor_id'],
                'collecting_donation_way' => $validatedData['collecting_donation_way'],
                'status' => $validatedData['status'],
                'notes' => $validatedData['notes'],
                'department_id' => $validatedData['department_id'],
                'employee_id' => $validatedData['employee_id'],
                'cancellation_reason' => $validatedData['cancellation_reason'],
                'cancellation_date' => $validatedData['cancellation_date'],
                'donation_type' => $validatedData['donation_type'],
                'form_date' => $validatedData['form_date'],
                'follow_up_department_id' => $validatedData['follow_up_department_id'],
            ]);

            $itemsUpdated = false; // Flag to check if any forms were processed

            // Process forms
            foreach ($validatedData['items'] as $donateData) {
                // Update or create financial forms
                if (
                    isset($donateData['financial_donation_type']) && $donateData['financial_donation_type'] === 'financial'
                    && !empty($donateData['financial_donation_categories_id'])
                    && !empty($donateData['financial_amount'])
                ) {

                    $donation = $monthlyForm->items()->updateOrCreate(
                        ['id' => $donateData['financial_monthly_donation_id'] ?? null], // Match by ID if it exists
                        [
                            'donation_type' => $donateData['financial_donation_type'],
                            'donation_category_id' => $donateData['financial_donation_categories_id'],
                            'amount' => $donateData['financial_amount'],
                            'item_name' => null, // Ensure financial forms do not have item names
                        ]
                    );

                    $itemsUpdated = true;
                }

                // Update or create in-kind forms
                if (
                    isset($donateData['inKind_donation_type']) && $donateData['inKind_donation_type'] === 'inKind'
                    && !empty($donateData['in_kind_item_name'])
                    && !empty($donateData['in_kind_quantity'])
                ) {

                    $donation = $monthlyForm->items()->updateOrCreate(
                        ['id' => $donateData['inkind_monthly_donation_id'] ?? null], // Match by ID if it exists
                        [
                            'donation_type' => $donateData['inKind_donation_type'],
                            'donation_category_id' => null, // In-kind forms do not have a category
                            'item_name' => $donateData['in_kind_item_name'],
                            'amount' => $donateData['in_kind_quantity'],
                        ]
                    );

                    $itemsUpdated = true;
                }
            }

            // Check if no forms were updated or added
            if (!$itemsUpdated) {
                DB::rollBack(); // Rollback the transaction
                return response()->json([
                    'success' => false,
                    'message' => __('validation.no_valid_forms_provided'),
                ], 400);
            }

            DB::commit(); // Commit the transaction

            return response()->json([
                'success' => true,
                'message' => __('messages.Monthly Form updated successfully'),
                'data' => $monthlyForm->load('items'),
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

    public function destroy(MonthlyForm $monthlyForm)
    {
        DB::beginTransaction(); // Start a transaction

        try {
            $monthlyForm->items()->delete();
            $monthlyForm->delete();
            DB::commit(); // Commit the transaction
            return response()->json([
                'success' => true,
                'message' => __('messages.Monthly Form deleted successfully'),
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


    public function deleteItem($id)
    {
        $this->authorize('delete', MonthlyForm::class);

        $donation = MonthlyFormItem::find($id);

        if (!$donation) {
            return response()->json(['message' => 'Form not found.'], 404);
        }

        try {
            $donation->delete(); // Soft delete or permanent delete based on your setup
            return response()->json(['message' => 'Form deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete donation.'], 500);
        }
    }

    public function getMonthlyFormDetails($id)
    {
        $monthlyForm = MonthlyForm::with('donor', 'items', 'employee', 'department', 'createdBy')->findOrFail($id);
        return response()->json($monthlyForm);
    }

    public function importMonthlyForms(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048',
        ]);

        try {
            $import = new MonthlyFormsImport();
            Excel::import($import, $request->file('file'));

            $failures = $import->failures(); // Use built-in failures() method

            // Call afterImport manually after processing
            $import->afterImport();

            return response()->json([
                'success' => count($failures) === 0,
                'message' => count($failures) === 0 ? 'Monthly Forms imported successfully.' : 'Some records were skipped due to validation errors.',
                'errors' => $failures, // Return validation errors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during import.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function importMonthlyFormItems(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048',
        ]);

        try {
            Excel::import(new MonthlyFormsItemsImport, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Monthly Forms Items imported successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during import.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
