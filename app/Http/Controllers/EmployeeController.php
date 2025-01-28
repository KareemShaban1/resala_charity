<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends BaseController
{
    
    public function __construct()
    {
        $this->model = Employee::class;
        $this->viewPath = 'backend.pages.employees';
        $this->routePrefix = 'employees';
        $this->validationRules = [
            'name' => 'required|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id'
        ];
    }

    public function data()
    {
        $query = $this->model::with('department');
        
        return DataTables::of($query)
            ->addColumn('department', function ($item) {
                return $item->department->name;
            })
            ->addColumn('action', function ($item) {
                return '
                    <div class="d-flex gap-2">
                        <a href="javascript:void(0);"  onclick="editEmployee('.$item->id.', \''.$item->name.'\', 
                        '.$item->department_id.',  \''.$item->job_title.'\' )"
                        class="btn btn-sm btn-info">
                            <i class="mdi mdi-square-edit-outline"></i>
                        </a>
                        <a href="javascript:void(0);"  onclick="deleteRecord('.$item->id.', \'employees\')"
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
            'name' => 'required|string|max:255|unique:employees,name,' . $id,
            'job_title' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id'
        ];
    }

    public function edit($id)
    {
        $employee = $this->model::findOrFail($id);
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $employee
            ]);
        }
        return view($this->viewPath . '.edit', compact('employee'));
    }

    public function getEmployeesByDepartment(Request $request)
    {
        $employees = Employee::where('department_id', $request->department_id)
            ->select('id', 'name')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }
}
