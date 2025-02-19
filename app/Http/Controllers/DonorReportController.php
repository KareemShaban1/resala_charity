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
                    $activityType = $activity->activity_type . '  ( ' . ($activity->callType?->name) . ' )' ?? 'Unknown';
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


    public function donorRandomCalls(Request $request)
    {
        $users = User::with([
            'department',
            'activities' => function ($query) use ($request) {
                $query
                    ->with('callType', 'donor');
            }
        ])
            ->withCount(['activities' => function ($query) use ($request) {
                $query->where('call_type_id', 1);
                if (isset($request->start_date) && isset($request->end_date)) {
                    $startDate = Carbon::parse($request->input('start_date'));
                    $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }])
            ->get();
        if (isset($request->user_id) && $request->user_id != 'all') {
            $users = $users->where('id', $request->user_id);
        }

        if ($request->ajax()) {
            return DataTables::of($users)
                ->addColumn('department', function ($user) {
                    return $user->department ? $user->department->name : 'N/A';
                })
                ->make(true);
        }

        return view('backend.pages.reports.donor-random-calls.index', compact('users'));
    }

    public function donorRandomCallsStatistics(Request $request)
    {
        $statuses = ["ReplyAndDonate", "ReplyAndNotDonate", "NoReply", "PhoneNotAvailable"];
        $statistics = array_fill_keys($statuses, 0);
        $activityTypes = [];

        // Fetch users with activities
        $usersQuery = User::with([
            'activities' => function ($query) use ($request) {
                $query
                    ->where('call_type_id', 1);
            }
        ]);

        // Filter by user ID if provided
        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $usersQuery->where('id', $request->user_id);
        }

        $users = $usersQuery->get();

        foreach ($users as $user) {
            $query = $user->activities();

            // Apply date filtering if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->input('start_date'));
                $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            $activities = $query->get();

            foreach ($activities as $activity) {
                if (!empty($activity->status) && isset($statistics[$activity->status])) {
                    $statistics[$activity->status]++;
                } else {
                    // Handle activities without a status
                    $activityType = trim(($activity->activity_type ?? 'Unknown') . ' ( ' . ($activity->callType->name ?? 'Unknown') . ' )');
                    $activityTypes[$activityType] = ($activityTypes[$activityType] ?? 0) + 1;
                }
            }
        }

        return response()->json([
            'statistics' => $statistics,
            // 'activity_types' => $activityTypes
        ]);
    }
}
