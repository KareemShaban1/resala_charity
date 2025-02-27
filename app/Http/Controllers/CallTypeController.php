<?php

namespace App\Http\Controllers;

use App\Models\CallType;
use App\Http\Requests\StoreCallTypeRequest;
use App\Http\Requests\UpdateCallTypeRequest;
use Yajra\DataTables\Facades\DataTables;

class CallTypeController extends BaseController
{
    public function __construct()
    {
        $this->model = CallType::class;
        $this->viewPath = 'backend.pages.call-types';
        $this->routePrefix = 'call-types';
        $this->validationRules = [
            'name' => 'required|string|max:255|unique:call_types,name'
        ];
    }

    public function data()
    {
        $query = $this->model::query();
        
        return DataTables::of($query)
            ->addColumn('action', function ($item) {
                $editButton = '';
                $deleteButton = '';

                if (auth()->user()->can('update call type')) {
                    $editButton = '<a href="javascript:void(0);" onclick="editCallType('.$item->id.', \''.$item->name.'\')"
                                    class="btn btn-sm btn-info">
                                    <i class="mdi mdi-square-edit-outline"></i>
                                </a>';
                }

                if (auth()->user()->can('delete call type')) {
                    $deleteButton = '<a href="javascript:void(0);" onclick="deleteRecord('.$item->id.', \'call-types\')"
                                    class="btn btn-sm btn-danger">
                                    <i class="mdi mdi-delete"></i>
                                </a>';
                }

                return '<div class="d-flex gap-2">' . $editButton . $deleteButton . '</div>';
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
            'name' => 'required|string|max:255|unique:call_types,name,' . $id
        ];
    }
}
