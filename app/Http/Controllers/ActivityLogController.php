<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Http\Requests\StoreActivityLogRequest;
use App\Http\Requests\UpdateActivityLogRequest;
use App\Models\MonthlyFormItem;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends Controller
{

    public function index()
    {
        return view('backend.pages.activity-logs.index');
    }

    public function data(Request $request)
    {
        $query = ActivityLog::class::with('user');

        // Filter by Action
        if ($request->has('action') && !empty($request->action)) {
            $query->where('action', $request->action);
        }

        // Filter by Model
        if ($request->has('model') && !empty($request->model)) {
            $query->where('model', $request->model);
        }

        // Filter by User
        if ($request->has('user') && !empty($request->user)) {
            $query->where('user_id', $request->user);
        }

        // Filter by Date Range
        if ($request->has('date_range') && !empty($request->date_range)) {
            [$startDate, $endDate] = explode(' - ', $request->date_range);
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        return DataTables::of($query)
            ->editColumn('created_at', function ($item) {
                return $item->created_at->format('Y-m-d H:i:s');
            })
            ->editColumn('action', function ($item) {
                return match ($item->action) {
                    'created' => __('created'),
                    'updated' => __('updated'),
                    'deleted' => __('deleted'),
                    default => __('Unknown'),
                };
            })
            ->editColumn('model', function ($item) {
                return match ($item->model) {
                    'App\Models\Donor' => __('Donor'),
                    'App\Models\DonorPhone' => __('Donor Phone'),
                    'App\Models\DonorActivity' => __('Donor Activity'),
                    'App\Models\MonthlyForm' => __('Monthly Form'),
                    'App\Models\Donation' => __('Donation'),
                    'App\Models\DonationCategory' => __('Donation Category'),
                    'App\Models\DonationItem' => __('Donation Item'),
                    'App\Models\Employee' => __('Employee'),
                    'App\Models\CallType' => __('Call Type'),
                    'App\Models\Event' => __('Event'),
                    'App\Models\Notification' => __('Notification'),
                    'App\Models\ActivityLog' => __('Activity Log'),
                    'App\Models\MonthlyFormDonation' => __('Monthly Form Donation'),
                    'App\Models\ActivityStatus' => __('Activity Statuses'),
                    'App\Models\Governorate' => __('Governorates'),
                    'App\Models\City' => __('Cities'),
                    'App\Models\Area' => __('Areas'),
                    'App\Models\AreaGroup' => __('Area Group'),
                    'App\Models\MonthlyFormItem' => __('Monthly Form Item'),
                    'App\Models\DonationCollecting' => __('Donation Collecting'),
                    'Spatie\Permission\Models\Role' => __('Roles'),
                    default => $item->model, // Fallback for unexpected values
                };
            })
            ->editColumn('changes', function ($item) {
                $monthlyFormItemType = null; // Ensure it's defined before usage

                if ($item->model == 'App\Models\MonthlyFormItem' && $item->action == 'updated') {
                    $monthlyFormItem = MonthlyFormItem::find($item->model_id);
                    if ($monthlyFormItem) {
                        $monthlyFormItemType = $monthlyFormItem->donation_type;
                    }
                }

                if (!$item->changes) {
                    return __('N/A');
                }

                try {
                    $parsedChanges = json_decode($item->changes, true);
                    if (!is_array($parsedChanges)) {
                        return __('Invalid Data');
                    }

                    $changesList = '<ul>';
                    foreach ($parsedChanges as $key => $value) {
                        if ($key === 'updated_at') {
                            continue; // Skip 'updated_at'
                        }
                        if ($key === 'password') {
                            return __('Password Updated');
                        }

                        $translatedKey = match ($key) {
                            'address' => __('Address'),
                            'name' => __('Name'),
                            'email' => __('Email'),
                            'date' => __('Date'),
                            'donor_type' => __('Donor Type'),
                            'monthly_donation_day' => __('Monthly Donation Day'),
                            'title' => __('Title'),
                            'status' => __('Status'),
                            'parent_id' => __('Parent Donor'),
                            'department_id' => __('Department ID'),
                            'amount' => $monthlyFormItemType === 'financial'
                                ? __('Amount') . ' (' . __('Financial Donation') . ')'
                                : __('Quantity') . ' (' . __('In Kind Donation') . ')',
                            default => ucfirst(str_replace('_', ' ', $key)),
                        };

                        // Handle specific model-related changes
                        switch ($item->model) {
                            case 'App\Models\Donor':
                                if ($key === 'status') {
                                    $value = $value == 1 ? __('Active') : __('Inactive');
                                }
                                if ($key === 'parent_id') {
                                    $value = $value !== null
                                        ? __('Assign Donor') . ' - (' . $value . ')'
                                        : __('Re-Assign Donor');
                                }
                                break;

                            case 'App\Models\Donation':
                                if ($key === 'amount') {
                                    $value = number_format($value, 2) . ' ' . __('Currency');
                                }
                                break;

                            case 'App\Models\Employee':
                                if ($key === 'role_id') {
                                    $roles = [
                                        1 => __('Admin'),
                                        2 => __('Manager'),
                                        3 => __('Staff'),
                                    ];
                                    $value = $roles[$value] ?? $value;
                                }
                                break;
                        }

                        if ($translatedKey !== '') {
                            $changesList .= "<li><strong>{$translatedKey}:</strong> {$value}</li>";
                        }
                    }
                    $changesList .= '</ul>';

                    return $changesList;
                } catch (\Exception $e) {
                    return __('Invalid JSON');
                }
            })
            ->rawColumns(['changes'])
            ->make(true);
    }
}
