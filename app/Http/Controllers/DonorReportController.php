<?php

namespace App\Http\Controllers;

use App\Models\DonorActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DonorReportController extends Controller
{
    //
    public function donorActivities(Request $request)
    {
        $users = User::with('department')
            ->withCount(['activities' => function ($query) use ($request) {
                if (isset($request->start_date) && isset($request->end_date)) {
                    $startDate = Carbon::parse($request->input('start_date'));
                    $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }])
            ->get();

        if ($request->ajax()) {
            return DataTables::of($users)
                ->addColumn('department', function ($user) {
                    return $user->department ? $user->department->name : 'N/A';
                })
                ->make(true);
        }

        return view('backend.pages.reports.donor-activities.index', compact('users'));
    }

    public function donorStatistics(Request $request)
    {
        $users = User::with('activities')->get();
        $statuses = ["ReplyAndDonate", "ReplyAndNotDonate", "NoReply", "PhoneNotAvailable"];
        $statistics = [];
        $activityTypes = [];
    
        // Initialize all status counts to 0
        foreach ($statuses as $status) {
            $statistics[$status] = 0;
        }
    
        foreach ($users as $user) {
            $query = $user->activities();
    
            if (isset($request->start_date) && isset($request->end_date)) {
                $startDate = Carbon::parse($request->input('start_date'));
                $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
    
            $activities = $query->get();
    
            foreach ($activities as $activity) {
                // Count activities by status
                if (!empty($activity->status) && isset($statistics[$activity->status])) {
                    $statistics[$activity->status]++;
                } else {
                    // Count activities without a status by activity_type
                    $activityType = $activity->activity_type ?? 'Unknown';
                    if (!isset($activityTypes[$activityType])) {
                        $activityTypes[$activityType] = 0;
                    }
                    $activityTypes[$activityType]++;
                }
            }
        }
    
        return response()->json([
            'statistics' => $statistics,
            'activity_types' => $activityTypes
        ]);
    }
    
}
