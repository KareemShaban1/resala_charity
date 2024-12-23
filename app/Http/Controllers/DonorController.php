<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\DonorPhone;
use App\Http\Requests\StoreDonorRequest;
use App\Http\Requests\UpdateDonorRequest;
use App\Imports\DonorsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class DonorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.pages.donors.index');
    }

    /**
     * Get donors data for DataTable.
     */
    public function data()
    {
        $query = Donor::with(['governorate', 'city', 'area', 'phones']);

        return datatables()->of($query)
            ->addColumn('phones', function ($donor) {
                return $donor->phones->map(function ($phone) {
                    return $phone->phone_number . ' (' . ucfirst($phone->phone_type) . ')';
                })->implode(', ');
            })
            ->addColumn('action', function ($donor) {
                return '
                    <a href="javascript:void(0)" onclick="editDonor(' . $donor->id . ')" class="btn btn-sm btn-info">
                        <i class="mdi mdi-pencil"></i>
                    </a>
                    <a href="javascript:void(0)" onclick="deleteDonor(' . $donor->id . ')" class="btn btn-sm btn-danger">
                        <i class="mdi mdi-trash-can"></i>
                    </a>';
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
            ->rawColumns(['active', 'action'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDonorRequest $request)
    {
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
                'message' => 'Donor created successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating donor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Donor $donor)
    {
        return response()->json($donor->load(['governorate', 'city', 'area', 'phones']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Donor $donor)
    {
        return response()->json($donor->load(['governorate', 'city', 'area', 'phones']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDonorRequest $request, Donor $donor)
    {
        try {
            DB::beginTransaction();

            $donor->update($request->validated());

            if ($request->has('phones')) {
                $existingPhones = $donor->phones->pluck('id', 'phone_number'); // Map phone_number to ID

                foreach ($request->phones as $phone) {
                    if (!empty($phone['number'])) {
                        $normalizedPhone = preg_replace('/\D/', '', $phone['number']); // Normalize phone number

                        if ($existingPhones->has($normalizedPhone)) {
                            // Update existing phone
                            $donor->phones()->where('id', $existingPhones[$normalizedPhone])->update([
                                'phone_number' => $phone['number'],
                                'phone_type' => $phone['type'],
                                'is_primary' => $phone['is_primary'] ?? false,
                            ]);
                        } else {
                            // Create new phone
                            $donor->phones()->create([
                                'phone_number' => $phone['number'],
                                'phone_type' => $phone['type'],
                                'is_primary' => $phone['is_primary'] ?? false,
                            ]);
                        }
                    }
                }

                // Handle primary phone: ensure only one phone is marked as primary
                $donor->phones()->update(['is_primary' => false]); // Reset all to non-primary
                if ($primaryPhone = collect($request->phones)->firstWhere('is_primary', true)) {
                    $donor->phones()->where('phone_number', $primaryPhone['number'])->update(['is_primary' => true]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Donor updated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating donor: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Donor $donor)
    {
        try {
            $donor->delete(); // This will also delete related phones due to cascadeOnDelete

            return response()->json([
                'success' => true,
                'message' => 'Donor deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting donor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importDonors(Request $request)
    {
        ini_set('max_execution_time', 600); // Extends execution time to 300 seconds
        ini_set('memory_limit', '512M');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        try {
            // Import the donors using the DonorsImport class
            $import = new DonorsImport();
            Excel::import($import, $request->file('file'));

            $skippedRows = $import->getSkippedRows(); // Retrieve skipped rows for feedback

            // Return the result with skipped rows if any
            return response()->json([
                'success' => true,
                'message' => 'Donors imported successfully!',
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
                'message' => 'Some rows failed validation.',
                'errors' => $errorDetails,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing donors: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        // Validate that query is provided
        if (empty($query)) {
            return response()->json([], 200); // Return an empty array if no query is provided
        }

        // Normalize the query to remove non-numeric characters
        $normalizedQuery = preg_replace('/\D/', '', $query); // Remove non-numeric characters

        // Search donors by ID or related phone numbers
        $donors = Donor::with('phones') // Load phones relationship
            ->where('id', 'like', "%$query%")
            ->orWhereHas('phones', function ($q) use ($normalizedQuery) {
                // Normalize phone numbers in the database as well for accurate matching
                $q->whereRaw('REPLACE(REPLACE(REPLACE(phone_number, "-", ""), "(", ""), ")", "") LIKE ?', ['%' . $normalizedQuery . '%']);
            })
            ->get();

        // Loop through donors and filter for phone number match
        $donors->each(function ($donor) use ($normalizedQuery) {
            // Get all phones that match the search query
            $matchedPhones = $donor->phones->filter(function ($phone) use ($normalizedQuery) {
                // Normalize the phone number and compare
                $normalizedPhone = preg_replace('/\D/', '', $phone->phone_number);
                return strpos($normalizedPhone, $normalizedQuery) !== false;
            });

            // Add matched phones to the donor object
            $donor->matched_phones = $matchedPhones->pluck('phone_number');
        });

        // Return the modified donor data with the matched phones
        return response()->json([
            'results' => $donors->map(function ($donor) {
                return [
                    'id' => $donor->id,
                    'text' => $donor->name . ' (' . implode(', ', $donor->matched_phones->toArray()) . ')', // Show all matched phones
                    'matched_phones' => $donor->matched_phones, // Pass all matched phones
                ];
            })
        ], 200);
    }
}
