<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Governorate;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class CityController extends BaseController
{
    public function __construct()
    {
        $this->model = City::class;
        $this->viewPath = 'backend.pages.cities';
        $this->routePrefix = 'cities';
        $this->validationRules = [
            'name' => 'required|string|max:255|unique:cities,name',
            'governorate_id' => 'required|exists:governorates,id'
        ];
    }

    public function data()
    {
        $query = $this->model::with('governorate');
        
        return DataTables::of($query)
            ->addColumn('governorate', function ($item) {
                return $item->governorate->name;
            })
            ->addColumn('action', function ($item) {

                $editButton = '';
                $deleteButton = '';

                if (auth()->user()->can('update city')) {
                    $editButton = ' <a href="javascript:void(0);"  onclick="editCity('.$item->id.', \''.$item->name.'\', '.$item->governorate_id.')"
                        class="btn btn-sm btn-info">
                            <i class="mdi mdi-square-edit-outline"></i>
                        </a>';
                    
                }

                if (auth()->user()->can('delete city')) {
                    $deleteButton = ' <a href="javascript:void(0);"  onclick="deleteRecord('.$item->id.', \'cities\')"
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
            'name' => 'required|string|max:255|unique:cities,name,' . $id,
            'governorate_id' => 'required|exists:governorates,id'
        ];
    }

    public function edit($id)
    {
        $city = $this->model::findOrFail($id);
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $city
            ]);
        }
        return view($this->viewPath . '.edit', compact('city'));
    }

    public function getCitiesByGovernorate(Request $request)
    {
        $cities = City::where('governorate_id', $request->governorate_id)
            ->select('id', 'name')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }
}
