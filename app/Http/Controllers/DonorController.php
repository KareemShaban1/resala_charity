<?php

namespace App\Http\Controllers;

use App\Exports\NonMatchingNumbersExport;
use App\Models\Donor;
use App\Http\Requests\StoreDonorRequest;
use App\Http\Requests\UpdateDonorRequest;
use App\Imports\DonorsImport;
use App\Imports\PhoneNumbersImport;
use App\Jobs\ImportDonorsJob;
use App\Models\DonationCategory;
use App\Models\DonorPhone;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class DonorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Donor::class);

        $donors = Donor::all();
        $donationCategories = DonationCategory::all();
        $employees = Employee::all();
        return view('backend.pages.donors.index', compact('donors', 'donationCategories', 'employees'));
    }

    // randomDonors

    public function randomDonors()
    {
        $this->authorize('view', Donor::class);

        $donors = Donor::all();
        $donationCategories = DonationCategory::all();
        $employees = Employee::all();
        return view('backend.pages.donors.random_donors', compact('donors', 'donationCategories', 'employees'));
    }
    /**
     * Get donors data for DataTable.
     */
    public function data(Request $request)
    {
        $query = Donor::with([
            'governorate',
            'city',
            'area',
            'phones',
            'activities' => function ($query) {
                $query->with('activityStatus')->latest();
            }
        ])
            ->selectRaw(
                'donors.*, 
            CASE WHEN donors.parent_id IS NOT NULL THEN donors.parent_id ELSE donors.id END as parent_donor_group_id,
            CASE WHEN donors.parent_id IS NOT NULL THEN 1 ELSE 0 END as is_child_order,
            (SELECT COUNT(*) FROM donor_activities WHERE donor_activities.donor_id = donors.id) as activities_count'
            )
            ->orderBy('parent_donor_group_id', 'asc')
            ->orderBy('is_child_order', 'asc')
            ->orderBy('donors.created_at', 'desc');




        if ($request->has('columns')) {
            foreach ($request->get('columns') as $column) {
                $searchValue = $column['search']['value'];
                $columnName = $column['name'];

                if ($searchValue) {
                    if ($columnName === 'phones') {
                        $query->whereHas('phones', function ($q) use ($searchValue) {
                            $q->where('phone_number', 'like', "%{$searchValue}%");
                        });
                    } elseif ($columnName === 'city.name') {
                        $query->whereHas('city', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%{$searchValue}%");
                        });
                    } elseif ($columnName === 'area.name') {
                        $query->whereHas('area', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%{$searchValue}%");
                        });
                    } elseif ($columnName === 'donors.active') {
                        $query->where('active', strtolower($searchValue));
                    } elseif ($columnName === 'has_activities') {
                        if (strtolower($searchValue) === 'yes') {
                            $query->having('activities_count', '>', 0);
                        } elseif (strtolower($searchValue) === 'no') {
                            $query->having('activities_count', '=', 0);
                        }
                    } elseif ($columnName === 'last_activity_status') {
                        $query->whereHas('activities', function ($q) use ($searchValue) {
                            $q->whereHas('activityStatus', function ($q2) use ($searchValue) {
                                $q2->where('name', 'like', "%{$searchValue}%");
                            });
                        });
                    } else {
                        $query->where($columnName, 'like', "%{$searchValue}%");
                    }
                }
            }
        }

        $query->orderBy('donors.id', 'desc');

        // Date filter
        if (request()->has('date_filter')) {
            $dateFilter = request('date_filter');
            $startDate = request('start_date');
            $endDate = request('end_date');

            if ($dateFilter === 'today') {
                $query->whereDate('donors.created_at', operator: today());
            } elseif ($dateFilter === 'week') {
                $query->whereBetween('donors.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($dateFilter === 'month') {
                $query->whereBetween('donors.created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            } elseif ($columnName === 'last_activity_status') {
                // Filter based on the donor's last activity status name
                $query->whereHas('activities', function ($q) use ($searchValue) {
                    $q->whereHas('activityStatus', function ($q2) use ($searchValue) {
                        $q2->where('name', 'like', "%{$searchValue}%");
                    });
                });
            } elseif ($dateFilter === 'range' && $startDate && $endDate) {
                $query->whereBetween('donors.created_at', [$startDate, $endDate]);
            }
        }

        if (request()->has('category')) {
            $category = request('category');
            if ($category === 'normal') {
                $query->whereIn('donors.donor_category', ['normal', 'special']);
            } elseif ($category === 'random') {
                $query->where('donors.donor_category', 'random');
            }
        }


        return datatables()->of($query)
            ->filterColumn('phones', function ($query, $keyword) {
                $query->whereHas('phones', function ($q) use ($keyword) {
                    $q->where('phone_number', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('phones', function ($donor) {
                // return $donor->phones->isNotEmpty() ?
                //     $donor->phones->map(function ($phone) {
                //         return $phone->phone_number . ' (' . ucfirst($phone->phone_type) . ')';
                //     })->implode(', ') : 'N/A';
                return $donor->phones->isNotEmpty() ?
                    $donor->phones->map(function ($phone) {
                        return $phone->phone_number;
                    })->implode(', ') : 'N/A';
            })
            ->addColumn('name', function ($donor) {
                return '<a href="' . route('donor-history.show', [$donor->id]) . '" class="text-info">'
                    . $donor->name .
                    '</a>';
            })
            ->addColumn('action', function ($donor) {
                $btn = '<div class="d-flex gap-2">';

                // View Donor Button (assuming all users can view details)
                if (auth()->user()->can('view donors')) {
                    $btn .= '<a href="javascript:void(0)" onclick="donorDetails(' . $donor->id . ')" 
                         class="btn btn-sm btn-light">
                            <i class="mdi mdi-eye"></i>
                        </a>';
                }

                // Edit Donor Button (only for users with 'update donor' permission)
                if (auth()->user()->can('update donor')) {
                    $btn .= '<a href="javascript:void(0)" onclick="editDonor(' . $donor->id . ')" 
                             class="btn btn-sm btn-info">
                                <i class="mdi mdi-pencil"></i>
                            </a>';
                }

                // Delete Donor Button (only for users with 'delete donor' permission)
                if (auth()->user()->can('delete donor')) {
                    $btn .= '<a href="javascript:void(0)" onclick="deleteDonor(' . $donor->id . ')" 
                             class="btn btn-sm btn-danger">
                                <i class="mdi mdi-trash-can"></i>
                            </a>';
                }

                // Assign Donor Button (only for 'monthly' donors & if user has 'assign donor' permission)
                if ($donor->donor_type === 'monthly' && auth()->user()->can('assign donor')) {
                    $btn .= '<a href="javascript:void(0)" onclick="assignDonor(' . $donor->id . ')" 
                             class="btn btn-sm btn-success">
                                <i class="mdi mdi-account-plus"></i>
                            </a>';
                }

                // Add Activity Button (only if user has 'add activity' permission)
                if (auth()->user()->can('create activity')) {
                    $btn .= '<a href="javascript:void(0)" onclick="addActivity(' . $donor->id . ')" 
                             class="btn btn-sm btn-dark">
                                <i class="uil-outgoing-call"></i>
                            </a>';
                }

                $btn .= '</div>';
                return $btn;
            })


            ->editColumn('active', function ($donor) {
                return $donor->active ?
                    '<span class="badge bg-success">' . __("Active") . '</span>' :
                    '<span class="badge bg-danger">' . __("Inactive") . '</span>';
            })
            ->editColumn('area', function ($donor) {
                return $donor->area ? $donor->area->name : '';
            })
            ->editColumn('city', function ($donor) {
                return $donor->city ? $donor->city->name : '';
            })
            ->editColumn('governorate', function ($donor) {
                return $donor->governorate ? $donor->governorate->name : '';
            })
            ->editColumn('donor_type', function ($donor) {
                return $donor->donor_type ? $donor->donor_type : '';
            })
            ->editColumn('donor_category', function ($donor) {
                return $donor->donor_category ? $donor->donor_category : '';
            })
            ->addColumn('is_child', function ($item) {
                // Check if this donor is referenced as a parent by any other donor
                $isParent = Donor::where('parent_id', $item->id)->exists();

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
            ->addColumn('has_activities', function ($donor) {
                return $donor->activities_count > 0 ?
                    '<span class="badge bg-success">' . __("Yes") . '</span>'
                    : '<span class="badge bg-danger">' . __("No") . '</span>';
            })
            ->addColumn('last_activity_status', function ($donor) {
                $lastActivity = $donor->activities->first(); // Get the most recent activity
                $status = $lastActivity?->activityStatus->name;

                return $status ? '<span class="badge bg-primary">' . ucfirst($status) . '</span>'
                    : '<span class="badge bg-secondary">' . __("No Activity") . '</span>';
            })
            ->rawColumns(['active', 'action', 'name', 'has_activities', 'last_activity_status'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDonorRequest $request)
    {
        $this->authorize('create', Donor::class);

        try {
            DB::beginTransaction();

            $donor = Donor::create($request->validated());

            // Handle phones
            if ($request->has('phones')) {
                foreach ($request->phones as $index => $phone) {
                    if (!empty($phone['number'])) {
                        $donor->phones()->create([
                            'phone_number' => $phone['number'],
                            'phone_type' => $phone['type'],
                            'is_primary' => $index === 0 // First phone is primary
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('messages.Donor created successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('messages.Donor created failed'),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Donor $donor)
    {
        $this->authorize('view', Donor::class);

        $childrenDonors = Donor::where('parent_id', $donor->id)->get();

        return response()->json($donor->load([
            'department',
            'governorate',
            'city',
            'area',
            'phones',
            'childrenDonors',
            'activities'
        ]));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Donor $donor)
    {
        $this->authorize('update', Donor::class);

        return response()->json($donor->load(['governorate', 'city', 'area', 'phones']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDonorRequest $request, Donor $donor)
    {
        $this->authorize('update', Donor::class);

        try {
            DB::beginTransaction();

            $donor->update($request->validated());

            if ($request->has('phones')) {
                $existingPhones = $donor->phones->pluck('id', 'phone_number'); // Map phone_number to ID

                foreach ($request->phones as $phone) {
                    if (!empty($phone['number'])) {
                        // Normalize and convert Arabic numerals to Western numerals
                        $normalizedPhone = $this->normalizePhoneNumber($phone['number']);

                        if ($existingPhones->has($normalizedPhone)) {
                            // Update existing phone
                            $donor->phones()->where('id', $existingPhones[$normalizedPhone])->update([
                                'phone_number' => $normalizedPhone,
                                'phone_type' => $phone['type'],
                                'is_primary' => $phone['is_primary'] ?? false,
                            ]);
                        } else {
                            // Create new phone
                            $donor->phones()->create([
                                'phone_number' => $normalizedPhone,
                                'phone_type' => $phone['type'],
                                'is_primary' => $phone['is_primary'] ?? false,
                            ]);
                        }
                    }
                }

                // Handle primary phone: ensure only one phone is marked as primary
                $donor->phones()->update(['is_primary' => false]); // Reset all to non-primary
                if ($primaryPhone = collect($request->phones)->firstWhere('is_primary', true)) {
                    $donor->phones()->where('phone_number', $this->normalizePhoneNumber($primaryPhone['number']))->update(['is_primary' => true]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('messages.Donor updated successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update donor failed: ', [$e, $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => __('messages.Donor update failed'),
            ], 500);
        }
    }

    /**
     * Normalize phone numbers by converting Arabic numerals to Western numerals.
     */
    private function normalizePhoneNumber($phone)
    {
        $arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $westernNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        // Remove non-digit characters and replace Arabic numbers with Western numbers
        return str_replace($arabicNumbers, $westernNumbers, preg_replace('/\D/', '', $phone));
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Donor $donor)
    {
        $this->authorize('delete', arguments: Donor::class);

        try {
            $donor->delete(); // This will also delete related phones due to cascadeOnDelete

            return response()->json([
                'success' => true,
                'message' => __('messages.Donor deleted successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.Donor deleted failed'),
            ], 500);
        }
    }

    public function importDonors(Request $request)
    {
        ini_set('max_execution_time', 3600); // Extends execution time to 300 seconds
        ini_set('memory_limit', '1024M');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        try {
            // Import the donors using the DonorsImport class
            $import = new DonorsImport();
            // Excel::import($import, $request->file('file'));

            Excel::queueImport($import, $request->file('file'));


            $skippedRows = $import->getSkippedRows(); // Retrieve skipped rows for feedback

            // Return the result with skipped rows if any
            return response()->json([
                'success' => true,
                'message' => __('messages.Donors imported successfully'),
                'skipped_rows' => $skippedRows,
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorDetails = [];

            // Collect errors for each failed row
            foreach ($failures as $failure) {
                $errorDetails[] = [
                    'row' => $failure->row(), // Row number
                    'errors' => $failure->errors(), // Array of error messages
                    'values' => $failure->values(), // Data that caused the failure
                ];
                Log::error("Row {$failure->row()} failed validation: " . json_encode($failure->errors()));
            }

            return response()->json([
                'success' => false,
                'message' => __('messages.Some rows failed validation.'),
                'errors' => $errorDetails,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.Error importing donors: ' . $e->getMessage()),
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $query = trim($request->get('query'));

        // Return an empty response if no query is provided
        if (empty($query)) {
            return response()->json([], 200);
        }

        // Normalize the query for name search
        $query = preg_replace('/\s+/', ' ', $query); // Normalize spaces
        $query = str_replace(['%', '_'], ['\%', '\_'], $query); // Escape special characters

        // Normalize the query for phone search
        $normalizedQuery = preg_replace('/\D/', '', $query);

        // Build the search query
        $donorsQuery = Donor::with('phones');

        if (is_numeric($query)) {
            // Search by phone if the query is numeric
            $donorsQuery->whereHas('phones', function ($q) use ($normalizedQuery) {
                $q->whereRaw('REPLACE(REPLACE(REPLACE(phone_number, "-", ""), "(", ""), ")", "") LIKE ?', ['%' . $normalizedQuery . '%']);
            });
        } else {
            // Search by name if the query is not numeric
            $donorsQuery->where('name', 'like', '%' . $query . '%');
        }

        if ($request->has('monthly')) {
            // dd("test");
            $donorsQuery->where('donor_type', 'monthly');
        }

        // Get the donors matching the query
        $donors = $donorsQuery->get();

        // Filter and normalize matched phones for each donor
        $donors->each(function ($donor) use ($normalizedQuery) {
            $donor->matched_phones = $donor->phones->filter(function ($phone) use ($normalizedQuery) {
                // Normalize the phone number and check if it contains the query
                $normalizedPhone = preg_replace('/\D/', '', $phone->phone_number);
                return strpos($normalizedPhone, $normalizedQuery) !== false;
            })->pluck('phone_number');
        });

        // Prepare the response with matched phones
        return response()->json([
            'results' => $donors->map(function ($donor) {
                return [
                    'id' => $donor->id,
                    'text' => $donor->name . ' (' . $donor->matched_phones->implode(', ') . ')',
                    'matched_phones' => $donor->matched_phones,
                ];
            }),
        ], 200);
    }

    // public function search(Request $request)
    // {
    //     $query = trim($request->get('query'));

    //     // Return an empty response if no query is provided
    //     if (empty($query)) {
    //         return response()->json([], 200);
    //     }

    //     // Normalize the query for name search
    //     $query = preg_replace('/\s+/', ' ', $query); // Normalize spaces
    //     $query = str_replace(['%', '_'], ['\%', '\_'], $query); // Escape special characters

    //     // Normalize the query for phone search
    //     $normalizedQuery = preg_replace('/\D/', '', $query);

    //     // Build the search query
    //     $donorsQuery = Donor::with('phones');

    //     if (is_numeric($query)) {
    //         if ((int)$query > 0) {
    //             // Search by ID
    //             $donorsQuery->where('id', (int)$query);
    //         } else {
    //             // Search by phone if the query is numeric
    //             $donorsQuery->orWhereHas('phones', function ($q) use ($normalizedQuery) {
    //                 $q->whereRaw('REPLACE(REPLACE(REPLACE(phone_number, "-", ""), "(", ""), ")", "") LIKE ?', ['%' . $normalizedQuery . '%']);
    //             });
    //         }
    //     } else {
    //         // Search by name if the query is not numeric
    //         $donorsQuery->where('name', 'like', '%' . $query . '%');
    //     }

    //     if ($request->has('monthly')) {
    //         $donorsQuery->where('donor_type', 'monthly');
    //     }

    //     // Get the donors matching the query
    //     $donors = $donorsQuery->get();

    //     // Filter and normalize matched phones for each donor
    //     $donors->each(function ($donor) use ($normalizedQuery) {
    //         $donor->matched_phones = $donor->phones->filter(function ($phone) use ($normalizedQuery) {
    //             // Normalize the phone number and check if it contains the query
    //             $normalizedPhone = preg_replace('/\D/', '', $phone->phone_number);
    //             return strpos($normalizedPhone, $normalizedQuery) !== false;
    //         })->pluck('phone_number');
    //     });

    //     // Prepare the response with matched phones
    //     return response()->json([
    //         'results' => $donors->map(function ($donor) {
    //             return [
    //                 'id' => $donor->id,
    //                 'text' => $donor->name . ' (' . $donor->matched_phones->implode(', ') . ')',
    //                 'matched_phones' => $donor->matched_phones,
    //             ];
    //         }),
    //     ], 200);
    // }


    public function assignDonors(Request $request)
    {
        $request->validate([
            'parent_donor_id' => 'required|exists:donors,id',
            'donor_id' => 'required|exists:donors,id',
        ]);
        $parentDonor = Donor::where('id', $request->parent_donor_id)->first();
        $chirdernDonor = Donor::where('id', $request->donor_id)->first();

        if ($parentDonor) {
            $chirdernDonor->parent_id = $parentDonor->id;
            $chirdernDonor->save();
        }
        return response()->json([
            'success' => true,
            'message' => __('messages.Donor assigned successfully.'),
        ]);
    }

    public function reAssignDonors(Request $request)
    {
        $request->validate([
            'parent_donor_id' => 'required|exists:donors,id',
            'donor_id' => 'required|exists:donors,id',
        ]);
        $parentDonor = Donor::where('id', $request->parent_donor_id)->first();
        $chirdernDonor = Donor::where('id', $request->donor_id)->first();

        if ($parentDonor) {
            $chirdernDonor->parent_id = null;
            $chirdernDonor->save();
        }
        return response()->json([
            'success' => true,
            'message' => __('messages.Donor re assigned successfully.'),
        ]);
    }



    public function donorChildren(Request $request)
    {
        $parentDonor = Donor::where('id', $request->parent_donor_id)->first();
        $childrenDonors = Donor::where('parent_id', $parentDonor->id)->get();
        return response()->json($childrenDonors);
    }

    public function notAssignedDonors(Request $request)
    {
        // Get all donors where:
        // 1. Donor is not the parent donor we are working with
        // 2. Donor is top-level (parent_id is null)
        // 3. Donor is not a parent of other donors (exclude donors who appear as parent_id)
        $childrenDonors = Donor::where('id', '<>', $request->parent_donor_id)
            ->whereNull('parent_id')  // Ensure it's a top-level donor
            ->where('donor_type', 'monthly')
            ->whereNotIn('id', Donor::whereNotNull('parent_id')->pluck('parent_id'))  // Exclude those who are parents for other donors
            ->get();

        return response()->json($childrenDonors);
    }


    public function uploadPhoneNumbers(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Import phone numbers from the Excel file
        $uploadedNumbers = Excel::toCollection(new PhoneNumbersImport, $request->file('file'))->first();

        // Log the uploaded numbers for debugging
        Log::info('Uploaded numbers:', $uploadedNumbers->toArray());

        // Get existing phone numbers from the database
        $existingNumbers = DonorPhone::pluck('phone_number')->toArray();

        // Log the existing numbers for debugging
        Log::info('Existing numbers:', $existingNumbers);

        // Flatten the uploaded numbers and normalize data types
        $uploadedNumbers = $uploadedNumbers->flatten()->map(function ($number) {
            return (string) $number; // Convert to string
        });

        // Convert existing numbers to strings
        $existingNumbers = array_map('strval', $existingNumbers);

        // Find phone numbers that do not exist in the database
        $nonMatchingNumbers = $uploadedNumbers->diff($existingNumbers);

        // Log the non-matching numbers for debugging
        Log::info('Non-matching numbers:', $nonMatchingNumbers->toArray());

        // Generate a new Excel file with the non-matching numbers
        $fileName = 'non_matching_numbers_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new NonMatchingNumbersExport($nonMatchingNumbers), $fileName);
    }

    public function deleteDonorPhone($id){
        $this->authorize('delete', Donor::class);

        $donorPhone = DonorPhone::find($id);

        if (!$donorPhone) {
            return response()->json(['message' => 'Donor Phone not found.'], 404);
        }

        try {
            $donorPhone->delete(); // Soft delete or permanent delete based on your setup
            return response()->json(['message' => 'Donor Phone deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete donor phone.'], 500);
        }
    }
}
