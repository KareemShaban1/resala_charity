<?php

namespace App\Http\Controllers;

use App\Models\ActivityReason;
use App\Http\Requests\StoreActivityReasonRequest;
use App\Http\Requests\UpdateActivityReasonRequest;
use Yajra\DataTables\Facades\DataTables;

class ActivityReasonController extends BaseController
{
    public function __construct()
    {
        $this->model = ActivityReason::class;
        $this->viewPath = 'backend.pages.activity-reasons';
        $this->routePrefix = 'call-types';
        $this->validationRules = [
            'name' => 'required|string|max:255|unique:activity_reasons,name',
            'active' => 'nullable|boolean',

        ];
    }
    public function data()
    {
        $query = $this->model::query();
    
        return DataTables::of($query)
            ->addColumn('action', function ($item) {
                $editButton = '';
                $deleteButton = '';
    
                if (auth()->user()->can('update activity reason')) {
                    $editButton = '<a href="javascript:void(0);" onclick="editActivityReason(' . $item->id . ', ' . htmlspecialchars(json_encode($item->name)) . ', ' . $item->active . ')" 
                                   class="btn btn-sm btn-info">
                                   <i class="mdi mdi-square-edit-outline"></i>
                               </a>';
                }
    
                if (auth()->user()->can('delete activity reason')) {
                    $deleteButton = '<a href="javascript:void(0);" onclick="deleteRecord(' . $item->id . ', \'activity-reasons\')" 
                                     class="btn btn-sm btn-danger">
                                     <i class="mdi mdi-delete"></i>
                                 </a>';
                }
    
                return '<div class="d-flex gap-2">' . $editButton . $deleteButton . '</div>';
            })
            ->editColumn('created_at', function ($item) {
                return $item->created_at->format('Y-m-d H:i:s');
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    

    protected function getUpdateValidationRules($id)
    {
        return [
            'name' => 'required|string|max:255|unique:activity_reasons,name,' . $id,
            'active' => 'nullable|boolean',

        ];
    }
}
