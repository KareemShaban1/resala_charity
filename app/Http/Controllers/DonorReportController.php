<?php

namespace App\Http\Controllers;

use App\Models\ActivityStatus;
use App\Models\Department;
use App\Models\DonorActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $statuses = ActivityStatus::pluck('name');
        $statistics = [];
        $activityTypes = [];

        // Initialize all status counts to 0
        foreach ($statuses as $status) {
            $statistics[$status] = 0;
        }

        foreach ($users as $user) {
            $query = $user->activities()
                ->with('donor', 'callType', 'activityStatus');

            if (isset($request->start_date) && isset($request->end_date)) {
                $startDate = Carbon::parse($request->input('start_date'));
                $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            $activities = $query->get();

            foreach ($activities as $activity) {
                // Count activities by status
                if (!empty($activity->activityStatus->name) && isset($statistics[$activity->activityStatus->name])) {
                    $statistics[$activity->activityStatus->name]++;
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
        $query = User::with([
            'department',
            'activities'
        ])->withCount(['activities' => function ($q) use ($request) {
            if ($request->start_date && $request->end_date) {
                $q->whereBetween('created_at', [
                    Carbon::parse($request->start_date),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            }
        }]);


        if (!Auth::user()->is_super_admin) {
            $query->where('id', Auth::id());
        }

        if ($request->user_id && $request->user_id != 'all') {
            $query->where('id', $request->user_id);
        }

        if ($request->department_id && $request->department_id != 'all') {
            $query->where('department_id', $request->department_id);
        }

        $users = $query->get();

        $departments = Department::all();
        return $request->ajax() ? DataTables::of($users)->addColumn('department', fn($user) => $user->department->name ?? 'N/A')->make(true)
            : view('backend.pages.reports.donor-random-calls.index', compact('users', 'departments'));
    }


    public function donorRandomCallsStatistics(Request $request)
    {
        $statuses = ActivityStatus::pluck('name')->toArray();
        $statistics = array_fill_keys($statuses, 0);
        $activityTypes = [];

        // Fetch users with activities
        $usersQuery = User::with([
            'activities'
        ]);

        if (!Auth::user()->is_super_admin) {
            $usersQuery->where('id', Auth::id());
        }

        // Filter by user ID if provided
        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $usersQuery->where('id', $request->user_id);
        }

        if ($request->department_id && $request->department_id != 'all') {
            $usersQuery->where('department_id', $request->department_id);
        }


        $users = $usersQuery->get();

        foreach ($users as $user) {
            $query = $user->activities()
                ->with('donor', 'callType', 'activityStatus');

            // Apply date filtering if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->input('start_date'));
                $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            $activities = $query->get();

            foreach ($activities as $activity) {
                if (!empty($activity->activityStatus->name) && isset($statistics[$activity->activityStatus->name])) {
                    $statistics[$activity->activityStatus->name]++;
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
