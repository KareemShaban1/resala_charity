<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Http\Requests\StoreActivityLogRequest;
use App\Http\Requests\UpdateActivityLogRequest;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends BaseController
{
    public function __construct()
    {
        $this->model = ActivityLog::class;
        $this->viewPath = 'backend.pages.activity-logs';
        $this->routePrefix = 'activity-logs';
    }

    public function data()
    {
        $query = $this->model::With('user');
        
        return DataTables::of($query)
            ->editColumn('created_at', function($item) {
                return $item->created_at->format('Y-m-d H:i:s');
            })
            ->editColumn('changes', function ($item) {
                if ($item->changes) {
                    try {
                        $parsedChanges = json_decode($item->changes, true);
                        $changesList = '<ul>';
                        foreach ($parsedChanges as $key => $value) {
                            switch ($key) {
                                case 'address':
                                    $key = __('Address');
                                    break;
                                case 'updated_at':
                                    $key = __('Updated At');
                                    break;
                            }
                            $changesList .= "<li><strong>{$key}:</strong> {$value}</li>";
                        }
                        $changesList .= '</ul>';
                        return $changesList;
                    } catch (\Exception $e) {
                        return 'Invalid JSON';
                    }
                }
                return 'N/A';
            })
            ->rawColumns(['changes']) // Mark 'changes' as containing raw HTML
            ->make(true);
    }


}
