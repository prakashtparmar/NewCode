<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use App\Models\Tehsil;
use App\Models\Pincode;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     // Apply permissions to methods just like in the first controller
    //     $this->middleware(['permission:view_users|create_users|edit_users|delete_users'], ['only' => ["index", "show"]]);
    //     $this->middleware(['permission:create_users'], ['only' => ["create", "store"]]);
    //     $this->middleware(['permission:edit_users'], ['only' => ["edit", "update"]]);
    //     $this->middleware(['permission:delete_users'], ['only' => ["destroy"]]);
    // }

    /**
     * Display a paginated list of users.
     */
    public function index()
    {
        Session::put('page', 'dashboard');

        // $users = User::with(['roles', 'permissions'])->latest()->paginate(10);
        // $users = User::with(['roles', 'permissions'])->latest()->get();
        $users = User::with(['roles', 'permissions', 'state', 'district', 'tehsil', 'city'])->latest()->get();



        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form to create a new user.
     */
    public function create()
    {
        return view('admin.users.create', [
            'roles' => Role::all(),
            'permissions' => Permission::all(),
            'states' => State::all(), // ✅ Add this
        ]);
    }
    /**
     * Store a newly created user in the database.
     */
    // public function store(StoreUserRequest $request)
    // {
    //     $data = $request->validated();

    //     if (!empty($data['password'])) {
    //         $data['password'] = bcrypt($data['password']);
    //     }

    //     $user = User::create($data);

    //     // Assign roles and permissions
    //     // $user->syncRoles($request->input('roles', []));
    //     $user->syncRoles($request->roles); // Sync roles with the user
    //     return redirect()->route('users.index')->with('success', 'User created successfully.');
    // }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        // Optional: Handle file upload for image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('users', 'public');
        }

        $user = User::create($data);

        $user->syncRoles($request->roles);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }


    /**
     * Display a specific user's details.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form to edit an existing user.
     */
    // public function edit(User $user)
    // {
    //     return view('admin.users.edit', [
    //         'user' => $user,
    //         'roles' => Role::all(),
    //         'permissions' => Permission::all()
    //     ]);
    // }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::all(),
            'permissions' => Permission::all(),
            'states' => State::all(),
            'districts' => District::where('state_id', $user->state_id)->get(),
            'cities' => City::where('district_id', $user->district_id)->get(),
            'tehsils' => Tehsil::where('city_id', $user->city_id)->get(),
            'pincodes' => Pincode::where('city_id', $user->city_id)->get(),
        ]);
    }


    /**
     * Update a specific user in the database.
     */
    // public function update(UpdateUserRequest $request, User $user)
    // {
    //     $data = $request->validated();

    //     if (!empty($data['password'])) {
    //         $data['password'] = bcrypt($data['password']);
    //     } else {
    //         unset($data['password']);
    //     }

    //     $user->update($data);

    //     // Sync roles and permissions
    //     $user->syncRoles($request->input('roles', []));
    //     $user->syncPermissions($request->input('permissions', []));

    //     return redirect()->route('users.index')->with('success', 'User updated successfully.');
    // }

    public function update(UpdateUserRequest $request, User $user)
    {
        
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('users', 'public');
        }

        $user->update($data);

        $user->syncRoles($request->input('roles', []));
        $user->syncPermissions($request->input('permissions', []));

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }


    /**
     * Delete a specific user.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user active/inactive status.
     */
    public function toggle(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User status updated.');
    }


    public function getDistricts($state_id)
    {
        $districts = District::where('state_id', $state_id)->get();
        return response()->json($districts);
    }

    public function getCities($district_id)
    {
        $cities = City::where('district_id', $district_id)->get();
        return response()->json($cities);
    }

    public function getTehsils($city_id)
    {
        $tehsils = Tehsil::where('city_id', $city_id)->get();
        return response()->json($tehsils);
    }

    public function getPincodes($city_id)
{
    $pincodes = Pincode::where('city_id', $city_id)->get();
    return response()->json($pincodes);
}
}
