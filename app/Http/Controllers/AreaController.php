<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\City;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AreaController extends BaseController
{
    public function __construct()
    {
        $this->model = Area::class;
        $this->viewPath = 'backend.pages.areas';
        $this->routePrefix = 'areas';
        $this->validationRules = [
            'name' => 'required|string|max:255|unique:areas,name',
            'city_id' => 'required|exists:cities,id'
        ];
    }

    public function data()
    {
        $query = $this->model::with('city.governorate');
        
        return DataTables::of($query)
            ->addColumn('city', function ($item) {
                return $item->city->name;
            })
            ->addColumn('governorate', function ($item) {
                return $item->city->governorate->name;
            })
            ->addColumn('action', function ($item) {
                return '
                    <div class="d-flex gap-2">
                        <a href="javascript:void(0);" onclick="editArea('.$item->id.', \''.$item->name.'\', '.$item->city_id.')"
                        class="btn btn-sm btn-info">
                            <i class="mdi mdi-square-edit-outline"></i>
                        </a>
                        <a href="javascript:void(0);" onclick="deleteRecord('.$item->id.', \'areas\')"
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
            'name' => 'required|string|max:255|unique:areas,name,' . $id,
            'city_id' => 'required|exists:cities,id'
        ];
    }

    public function edit($id)
    {
        $area = $this->model::with('city.governorate')->findOrFail($id);
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $area,
                'governorate_id' => $area->city->governorate_id
            ]);
        }
        return view($this->viewPath . '.edit', compact('area'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $area = Area::findOrFail($id);
            $area->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Area deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting area: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get areas by city id.
     */
    public function getAreasByCity(Request $request)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id'
        ]);

        $areas = Area::where('city_id', $request->city_id)->get();

        return response()->json([
            'success' => true,
            'data' => $areas
        ]);
    }
}
