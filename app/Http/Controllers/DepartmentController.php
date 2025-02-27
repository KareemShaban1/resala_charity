<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends BaseController
{
    use DataTableTrait;

    public function __construct()
    {
        $this->model = Department::class;
        $this->viewPath = 'backend.pages.departments';
        $this->routePrefix = 'departments';
        $this->validationRules = [
            'name' => 'required|string|max:255|unique:departments,name'
        ];
    }

    public function data()
    {
        $query = $this->model::query();
        
        return DataTables::of($query)
            ->addColumn('action', function ($item) {
                $editButton = '';
                $deleteButton = '';

                if (auth()->user()->can('update department')) {
                    $editButton = '
                        <a href="javascript:void(0);" onclick="editDepartment('.$item->id.', \''.$item->name.'\')"
                        class="btn btn-sm btn-info">
                            <i class="mdi mdi-square-edit-outline"></i>
                        </a>
                    ';
                }

                if (auth()->user()->can('delete department')) {
                    $deleteButton = '
                        <a href="javascript:void(0);" onclick="deleteRecord('.$item->id.', \'departments\')"
                        class="btn btn-sm btn-danger">
                            <i class="mdi mdi-delete"></i>
                        </a>
                    ';
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
            'name' => 'required|string|max:255|unique:departments,name,' . $id
        ];
    }
}
