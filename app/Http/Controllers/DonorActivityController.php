<?php

namespace App\Http\Controllers;

use App\Models\DonorActivity;
use App\Http\Requests\StoreDonorActivityRequest;
use App\Http\Requests\UpdateDonorActivityRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class DonorActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'call_type_id' => 'required|exists:call_types,id',
            'activity_type' => 'required|in:call,whatsapp_chat',
            'notes' => 'nullable|string',
            'activity_status_id' => 'required|exists:activity_statuses,id',
            'activity_reason_id' => 'nullable|exists:activity_reasons,id',
            'date_time' => 'required|date',
            'response' => 'nullable|string',
        ]);
        // Convert `date_time` to proper format
        $validatedData['date_time'] = Carbon::parse($validatedData['date_time'])->format('Y-m-d H:i:s');
        // dd($validatedData);
        DonorActivity::create([
            'donor_id' => $request->donor_id,
            'call_type_id' => $request->call_type_id,
            'activity_type' => $request->activity_type,
            'notes' => $request->notes,
            'activity_status_id' => $request->activity_status_id,
            'activity_reason_id' => $request->activity_reason_id,
            'response' => $request->response,
            'date_time' => $validatedData['date_time'],
            'created_by' => auth()->user()->id
        ]);
        return response()->json([
            'success' => true,
            'message' => __('messages.Donor activity added successfully'),
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(DonorActivity $donorActivity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DonorActivity $donorActivity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Find the donor activity
            $donorActivity = DonorActivity::find($id);

            if (!$donorActivity) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.Donor activity not found'),
                ], 404);
            }

            // Validate the request data
            $validatedData = $request->validate([
                'donor_id' => 'required|exists:donors,id',
                'call_type_id' => 'required|exists:call_types,id',
                'activity_type' => 'required|in:call,whatsapp_chat',
                'notes' => 'nullable|string',
                'activity_status_id' => 'required|exists:activity_statuses,id',
                'activity_reason_id' => 'nullable|exists:activity_reasons,id',
                'date_time' => 'required|date',
                'response' => 'nullable|string',
            ]);

            // Convert `date_time` to proper format
            $validatedData['date_time'] = Carbon::parse($validatedData['date_time'])->format('Y-m-d H:i:s');

            // Update the donor activity
            $updated = $donorActivity->update($validatedData);

            // Check if update was successful
            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.Donor activity update failed'),
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => __('messages.Donor activity updated successfully'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.Donor activity update failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        try {
            $donorActivity = DonorActivity::findOrFail($id);
            $donorActivity->delete();

            return response()->json([
                'success' => true,
                'message' => __('messages.Donor activity deleted successfully'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.Donor activity deleted failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
