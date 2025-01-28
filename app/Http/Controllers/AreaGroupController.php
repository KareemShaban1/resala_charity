<?php

namespace App\Http\Controllers;

use App\Models\AreaGroup;
use App\Http\Requests\StoreAreaGroupRequest;
use App\Http\Requests\UpdateAreaGroupRequest;
use Yajra\DataTables\Facades\DataTables;

class AreaGroupController extends Controller
{
    public function index()
    {
        // Fetch all area groups with their associated areas
        $areaGroups = AreaGroup::with('areas')->get();

        // Return the view with the data
        return view('backend.pages.area-groups.index', compact('areaGroups'));
    }

    public function data()
    {
        $query =   AreaGroup::with('areas');

        return DataTables::of($query)

            ->addColumn('areas', function ($item) {
                return implode(', ', $item->areas->pluck('name')->toArray());
            })
            ->addColumn('action', function ($item) {
                return '
                    <div class="d-flex gap-2">
                        <a href="javascript:void(0);" onclick="editAreaGroup(' . $item->id . ', \'' . $item->name . '\', ' . $item->city_id . ')"
                        class="btn btn-sm btn-info">
                            <i class="mdi mdi-square-edit-outline"></i>
                        </a>
                        <a href="javascript:void(0);" onclick="deleteRecord(' . $item->id . ', \'areas\')"
                        class="btn btn-sm btn-danger">
                            <i class="mdi mdi-delete"></i>
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAreaGroupRequest $request)
    {
        // Validate the request data
        $validated = $request->validated();

        // Create the area group
        $areaGroup = AreaGroup::create([
            'name' => $validated['name'],
        ]);

        // Attach selected areas to the area group
        if (isset($validated['areas'])) {
            $areaGroup->areas()->attach($validated['areas']);
        }


        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.' . class_basename(AreaGroup::class) . ' created successfully'),
                'data' => $areaGroup
            ]);
        }

        // Redirect with success message
        return redirect()->route('areas-groups.index')->with('success', 'Area group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AreaGroup $areaGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $areaGroup = AreaGroup::with('areas')->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $areaGroup,
            ]);
        }
        return view('backend.pages.area-groups.edit', compact('areaGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAreaGroupRequest $request, $id)
    {
        $areaGroup = AreaGroup::findOrFail($id);

        // Validate the request data
        $validated = $request->validated();

        // Update the area group
        $areaGroup->update([
            'name' => $validated['name'],
        ]);


        // Sync the associated areas (remove old associations and add new ones)
        if (isset($validated['areas'])) {

            $areaGroup->areas()->sync($validated['areas']);
        } else {
            // If no areas are selected, detach all
            $areaGroup->areas()->detach();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.' . class_basename(AreaGroup::class) . ' updated successfully'),
                'data' => $areaGroup
            ]);
        }

        // Redirect with success message
        return redirect()->route('areas-groups.index')->with('success', 'Area group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AreaGroup $areaGroup)
    {
        //
    }
}
