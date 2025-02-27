<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('view', Role::class);

        $roles = Role::all();
        $permissions = Permission::all();
        return view('backend.pages.roles.index', compact('roles', 'permissions'));
    }
    public function data()
{
    $roles = Role::with('permissions')->get(); // Include permissions in the query
    return DataTables::of($roles)
        ->addColumn('actions', function ($role) {
            $permissions = $role->permissions->pluck('id')->toArray(); // Extract permission IDs
            $permissionsJson = htmlspecialchars(json_encode($permissions), ENT_QUOTES, 'UTF-8');
            
            $btn = '<div class="d-flex gap-2">';
            if (auth()->user()->can('update role' || auth()->user()->isSuperAdmin())) {
                $btn .= '<a href="javascript:void(0)" onclick="editRole(' . $role->id . ', \'' . $role->name . '\', ' . $permissionsJson . ')" class="btn btn-sm btn-info">
                        <i class="mdi mdi-pencil"></i>
                    </a>';
            }
            if (auth()->user()->can('delete role') || auth()->user()->isSuperAdmin()) {
                $btn .= '<a href="javascript:void(0)" onclick="deleteRole(' . $role->id . ')" class="btn btn-sm btn-danger">
                        <i class="mdi mdi-trash-can"></i>
                    </a>';
            }
            $btn .= '</div>';

        })
        ->addColumn('permissions_count', function ($role) {
            return $role->permissions->count();
        })
        ->rawColumns(['actions', 'permissions_count'])
        ->make(true);
}


    public function create()
    {
        $this->authorize('create', Role::class);

        $permissions = Permission::all();
        return view('backend.pages.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.' . class_basename(Role::class) . ' created successfully'),
                'data' => $role
            ]);
        }

        return redirect()->route('roles.index');
    }

    public function edit(Role $role)
    {
        $this->authorize('update', Role::class);

        $permissions = Permission::all(); // Retrieve all permissions
        $role->load('permissions'); // Eager load the role's permissions
        return response()->json([
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }
    

    public function update(Request $request, Role $role)
    {
        $this->authorize('update', Role::class);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.' . class_basename(Role::class) . ' updated successfully'),
                'data' => $role
            ]);
        }

        return redirect()->route('roles.index');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', Role::class);

        $role->delete();
        return redirect()->route('roles.index');
    }
}
