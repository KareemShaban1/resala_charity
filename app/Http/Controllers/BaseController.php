<?php

namespace App\Http\Controllers;

use App\Traits\DataTableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests, DataTableTrait;

    protected $model;
    protected $viewPath;
    protected $routePrefix;
    protected $validationRules = [];

    public function index()
    {
        return view($this->viewPath . '.index');
    }

    public function data()
    {
        $query = $this->model::query();
        return $this->getDataTable($query);
    }

    public function create()
    {
        return view($this->viewPath . '.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate($this->validationRules);
            if($data['date']){
                $convertedDateTime = str_replace('T', ' ', $data['date']) . ':00';
                $data['date'] = $convertedDateTime;
            }
            $item = $this->model::create($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => class_basename($this->model) . ' created successfully',
                    'data' => $item
                ]);
            }

            return redirect()->route($this->routePrefix . '.index')
                ->with('success', class_basename($this->model) . ' created successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating ' . class_basename($this->model),
                    'errors' => $e->getMessage()
                ], 422);
            }

            return back()->withErrors(['error' => 'Error creating ' . class_basename($this->model)])->withInput();
        }
    }

    public function edit($id)
    {
        $item = $this->model::findOrFail($id);
        return view($this->viewPath . '.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        try {
            $item = $this->model::findOrFail($id);
            
            // If validation rules need to be modified for update (like unique rule)
            $rules = $this->getUpdateValidationRules($id);
            
            $data = $request->validate($rules);
            $item->update($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => class_basename($this->model) . ' updated successfully',
                    'data' => $item
                ]);
            }

            return redirect()->route($this->routePrefix . '.index')
                ->with('success', class_basename($this->model) . ' updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating ' . class_basename($this->model),
                    'errors' => $e->getMessage()
                ], 422);
            }

            return back()->withErrors(['error' => 'Error updating ' . class_basename($this->model)])->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $item = $this->model::findOrFail($id);
            $item->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => class_basename($this->model) . ' deleted successfully'
                ]);
            }

            return redirect()->route($this->routePrefix . '.index')
                ->with('success', class_basename($this->model) . ' deleted successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting ' . class_basename($this->model),
                    'errors' => $e->getMessage()
                ], 422);
            }

            return back()->withErrors(['error' => 'Error deleting ' . class_basename($this->model)]);
        }
    }

    protected function getUpdateValidationRules($id)
    {
        return $this->validationRules;
    }
}
