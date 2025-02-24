<?php

namespace App\Http\Controllers;

use App\Models\ActivityStatus;
use App\Http\Requests\StoreActivityStatusRequest;
use App\Http\Requests\UpdateActivityStatusRequest;
use Yajra\DataTables\Facades\DataTables;

class ActivityStatusController  extends BaseController
{
    public function __construct()
    {
        $this->model = ActivityStatus::class;
        $this->viewPath = 'backend.pages.activity-statuses';
        $this->routePrefix = 'call-types';
        $this->validationRules = [
            'name' => 'required|string|max:255|unique:activity_statuses,name',
        ];
    }

    public function data()
    {
        $query = $this->model::query();
        
        return DataTables::of($query)
            ->addColumn('action', function ($item) {
                return '
                    <div class="d-flex gap-2">
                        <a href="javascript:void(0);" onclick="editActivityStatus('.$item->id.', \''.$item->name.'\')"
                        class="btn btn-sm btn-info">
                            <i class="mdi mdi-square-edit-outline"></i>
                        </a>
                        <a href="javascript:void(0);" onclick="deleteRecord('.$item->id.', \'call-types\')"
                        class="btn btn-sm btn-danger">
                            <i class="mdi mdi-delete"></i>
                        </a>
                    </div>
                ';
            })
            ->editColumn('created_at', function($item) {
                return $item->created_at->format('Y-m-d H:i:s');
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    protected function getUpdateValidationRules($id)
    {
        return [
            'name' => 'required|string|max:255|unique:activity_statuses,name,' . $id
        ];
    }
}
