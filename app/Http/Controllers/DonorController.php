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
                return $donor->phones->map(function($phone) {
                    return $phone->phone_number . ' (' . ucfirst($phone->phone_type) . ')';
                })->implode(', ');
            })
            ->addColumn('action', function ($donor) {
                return '
                    <a href="javascript:void(0)" onclick="editDonor('.$donor->id.')" class="btn btn-sm btn-info">
                        <i class="mdi mdi-pencil"></i>
                    </a>
                    <a href="javascript:void(0)" onclick="deleteDonor('.$donor->id.')" class="btn btn-sm btn-danger">
                        <i class="mdi mdi-trash-can"></i>
                    </a>';
            })
            ->editColumn('active', function ($donor) {
                return $donor->active ? 
                    '<span class="badge bg-success">Active</span>' : 
                    '<span class="badge bg-danger">Inactive</span>';
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
            
            // Handle phones
            if ($request->has('phones')) {
                // Delete existing phones
                $donor->phones()->delete();
                
                // Add new phones
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
    }
    catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error importing donors: ' . $e->getMessage(),
        ], 500);
    }
}

}
