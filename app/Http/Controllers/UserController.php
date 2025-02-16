<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', User::class);

        $roles = Role::all();
        return view('backend.pages.users.index', compact('roles'));
    }

    /**
     * Fetch data for DataTables.
     */
    public function data()
    {
        $query = User::with('department');

        return DataTables::of($query)
            ->editColumn('name', function ($user) {
                return '<a href="' . route('users.details', $user->id) . '">' . $user->name . '</a>';
            })
            ->addColumn('action', function ($user) {
                return '
                    <button onclick="editUser(' . $user->id . ')" class="btn btn-sm btn-info">
                        <i class="mdi mdi-pencil"></i>
                    </button>
                    <button onclick="deleteUser(' . $user->id . ')" class="btn btn-sm btn-danger">
                        <i class="mdi mdi-trash-can"></i>
                    </button>';
            })
            ->addColumn('roles', function ($user) {
                $roles = $user->roles->pluck('name'); // Get roles as an array
                $rolesWithBadges = $roles->map(function ($role) {
                    return '<span class="badge bg-primary" style="font-size: 14px;">' . $role . '</span>';
                })->implode(' '); // Implode the badges with a space between them

                return $rolesWithBadges;
            })
            ->addColumn('department', function ($user) {
                return $user->department ? $user->department->name : '';
            })
            ->rawColumns(['action', 'roles', 'name'])
            ->make(true);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'department_id' => 'required|exists:departments,id',
            'roles' => 'required|array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles($request->roles);

        return response()->json(['success' => true, 'message' =>  __('messages.User created successfully')]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', User::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'department_id' => 'nullable|exists:departments,id',
            'roles' => 'required|array',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        $user->syncRoles($request->roles);

        return response()->json(['success' => true, 'message' =>  __('messages.User updated successfully')]);
    }


    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', User::class);

        $user->delete();

        return response()->json(['success' => true, 'message' =>  __('messages.User deleted successfully')]);
    }

    public function edit($id)
    {
        try {
            $this->authorize('update', User::class);

            // Retrieve the user by ID
            $user = User::findOrFail($id);

            // Return a view to edit the user details
            return response()->json($user->load('roles'));
        } catch (ModelNotFoundException $e) {
            // Handle the case where the user is not found
            return redirect()->route('users.index')->with('error', 'User not found.');
        }
    }

    public function show($id)
    {
        $this->authorize('view', User::class);

        $user = User::with('roles')->findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'department_id' => $user->department_id,
            'roles' => $user->roles->pluck('name'), // Send only role names
        ]);
    }

    public function changePassword(Request $request)
    {

        $this->authorize('update', User::class);

        $validated = $request->validate([
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);

        $user = User::findOrFail(auth()->user()->id);
        $user->password = Hash::make($request->password);
        $user->save();


        return redirect()->route('users.change-password.view')->with('success', __('messages.Password changed successfully'));
    }



    public function userDetails($id)
    {
        $user = User::findOrFail($id);
        $query = $user->activities();
    
        // Apply date filtering if start_date and end_date are provided
        if (request()->has('start_date') && request()->has('end_date')) {
            $startDate = request()->input('start_date');
            $endDate = Carbon::parse(request()->input('end_date'))->addDay(); // Add one day
        
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
    
        $activities = $query->get();
        $statuses = ["ReplyAndDonate", "ReplyAndNotDonate", "NoReply", "PhoneNotAvailable"];
        $statistics = $activities->groupBy('status');
    
        // Ensure all status keys exist with an empty collection if missing
        foreach ($statuses as $status) {
            $statistics[$status] = $statistics[$status] ?? collect([]);
        }
    
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }
    
        return view('backend.pages.users.user-details', compact('user','activities', 'statistics'));
    }
    
}
