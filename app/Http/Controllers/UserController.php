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
    /**
     * Display a paginated list of users.
     */
    public function index()
    {
        Session::put('page', 'dashboard');

        $query = User::with(['roles', 'permissions', 'state', 'district', 'tehsil', 'city'])->latest();

        // 🔐 Restrict users to current user's company unless master_admin
        if (auth()->user()->user_level !== 'master_admin') {
            $query->where('company_id', auth()->user()->company_id);
        }

        $users = $query->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form to create a new user.
     */
    public function create()
    {
        // ✅ Show only roles for the logged-in user's company (unless master_admin)
        $roles = auth()->user()->user_level === 'master_admin'
            ? Role::all()
            : Role::where('company_id', auth()->user()->company_id)->get();

        return view('admin.users.create', [
            'roles' => $roles,
            'permissions' => Permission::all(),
            'states' => State::all(),
        ]);
    }

    /**
     * Store a newly created user in the database.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        // 📌 Assign the company ID of the currently logged-in user
        $data['company_id'] = auth()->user()->company_id;

        // 📷 Optional: Handle file upload for image
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
        // 🔐 Restrict show access to same company
        if (auth()->user()->user_level !== 'master_admin' && $user->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access to user.');
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form to edit an existing user.
     */
    public function edit(User $user)
    {
        // 🔐 Restrict edit access to same company
        if (auth()->user()->user_level !== 'master_admin' && $user->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access to user.');
        }

        // ✅ Show only roles for the logged-in user's company (unless master_admin)
        $roles = auth()->user()->user_level === 'master_admin'
            ? Role::all()
            : Role::where('company_id', auth()->user()->company_id)->get();

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $roles,
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
    public function update(UpdateUserRequest $request, User $user)
    {
        // 🔐 Restrict update to same company
        if (auth()->user()->user_level !== 'master_admin' && $user->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized update attempt.');
        }

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
        // 🔐 Restrict delete to same company
        if (auth()->user()->user_level !== 'master_admin' && $user->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized delete attempt.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user active/inactive status.
     */
    public function toggle(User $user)
    {
        // 🔐 Restrict status toggle to same company
        if (auth()->user()->user_level !== 'master_admin' && $user->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized toggle attempt.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User status updated.');
    }

    /**
     * AJAX: Get districts for selected state.
     */
    public function getDistricts($state_id)
    {
        $districts = District::where('state_id', $state_id)->get();
        return response()->json($districts);
    }

    /**
     * AJAX: Get cities for selected district.
     */
    public function getCities($district_id)
    {
        $cities = City::where('district_id', $district_id)->get();
        return response()->json($cities);
    }

    /**
     * AJAX: Get tehsils for selected city.
     */
    public function getTehsils($city_id)
    {
        $tehsils = Tehsil::where('city_id', $city_id)->get();
        return response()->json($tehsils);
    }

    /**
     * AJAX: Get pincodes for selected city.
     */
    public function getPincodes($city_id)
    {
        $pincodes = Pincode::where('city_id', $city_id)->get();
        return response()->json($pincodes);
    }
}
