<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use App\Models\Tehsil;
use App\Models\Pincode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ApiController extends Controller
{
    // --------------------------------------------
    // Public Endpoints
    // --------------------------------------------

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'roles'    => 'nullable|array',
            'roles.*'  => 'exists:roles,name',
            'image'    => 'nullable|image|max:2048',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        // Assign company_id if authenticated (admin creating user)
        if (auth('sanctum')->check()) {
            $validated['company_id'] = auth('sanctum')->user()->company_id;
        }

        // Store image if provided
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('users', 'public');
        }

        // Create user and assign roles
        $user = User::create($validated);

        if (!empty($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        // Generate API token
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully.',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    /**
     * Authenticate user and return token
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $email = $request->input('email');
        $password = $request->input('password');
        $companyCode = $request->input('company_id');

        $user = User::where('email', $email)->first();

        // Check if credentials match
        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Deny access if user is inactive
        if (!$user->is_active) {
            return response()->json([
                'status'  => false,
                'message' => 'Your account is inactive. Please contact the administrator.',
            ], 403);
        }

        // Deny access if no role is assigned
        if ($user->roles()->count() === 0) {
            return response()->json([
                'status' => false,
                'message' => 'Role not assigned to this user. Please contact admin.',
            ], 403);
        }

        // If not master_admin, validate company association
        $isMasterAdmin = $user->hasRole('master_admin');

        if (!$isMasterAdmin) {
            if (!$companyCode) {
                return response()->json([
                    'status' => false,
                    'message' => 'Company code is required!',
                ], 422);
            }

            $company = Company::where('code', $companyCode)->first();

            if (!$company || $user->company_id !== $company->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or mismatched company code!',
                ], 403);
            }
        }

        // Generate API token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'user'    => $user->only(['id', 'name', 'email', 'company_id']),
            'token'   => $token,
        ]);
    }

    // --------------------------------------------
    // Protected Endpoints
    // --------------------------------------------

    /**
     * List all users (company filtered if not master admin)
     */
    public function indexUsers(Request $request)
    {
        $query = User::with(['roles', 'permissions', 'state', 'district', 'city', 'tehsil'])
                     ->latest();

        if (!$request->user()->hasRole('master_admin')) {
            $query->where('company_id', $request->user()->company_id);
        }

        return response()->json([
            'status' => true,
            'data'   => $query->get()
        ]);
    }

    /**
     * Store a new user (with image, roles, password confirmation)
     */
    public function storeUser(Request $request)
{
    // ✅ Check if the logged-in user has 'create_user' permission
    if (!$request->user()->hasPermissionTo('create_users')) {
        return response()->json([
            'status'  => false,
            'message' => 'You do not have permission to create a user.',
        ], 403);
    }

    $validator = Validator::make($request->all(), [
        'name'                  => 'required|string|max:255',
        'email'                 => 'required|email|unique:users,email',
        'password'              => 'required|min:6|confirmed', // uses `password_confirmation`
        'password_confirmation' => 'required',
        'roles'                 => 'array|nullable',
        'image'                 => 'file|image|nullable',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $data = $validator->validated();
    $data['password'] = Hash::make($data['password']);
    $data['company_id'] = $request->user()->company_id;

    // Upload profile image if available
    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('users', 'public');
    }

    // ✅ Create the user
    $user = User::create($data);

    // ✅ Assign roles if provided
    if (!empty($data['roles'])) {
        $user->syncRoles($data['roles']);
    }

    return response()->json([
        'status'  => true,
        'message' => 'User created successfully.',
        'data'    => $user
    ], 201);
}

    /**
     * Show a single user's detail
     */
    public function showUser(Request $request, User $user)
    {
        if (!$request->user()->hasRole('master_admin') &&
            $user->company_id !== $request->user()->company_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json(['status' => true, 'data' => $user]);
    }

    /**
     * Update a user's data (with optional roles & permissions)
     */
    public function updateUser(Request $request, User $user)
    {
        if (!$request->user()->hasRole('master_admin') &&
            $user->company_id !== $request->user()->company_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|required',
            'email'       => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password'    => 'sometimes|nullable|min:6',
            'roles'       => 'sometimes|array',
            'permissions' => 'sometimes|array',
            'image'       => 'sometimes|file|image|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('users', 'public');
        }

        $user->update($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        if (isset($data['permissions'])) {
            $user->syncPermissions($data['permissions']);
        }

        return response()->json(['status' => true, 'data' => $user]);
    }

    /**
     * Delete a user
     */
    public function deleteUser(Request $request, User $user)
{
    // ✅ Check if user has permission to delete users
    if (!$request->user()->hasPermissionTo('delete_users')) {
        return response()->json([
            'status'  => false,
            'message' => 'You do not have permission to delete users.',
        ], 403);
    }

    // ✅ Optional: Restrict deletion to same company unless master_admin
    if (!$request->user()->hasRole('master_admin') &&
        $user->company_id !== $request->user()->company_id) {
        return response()->json([
            'status'  => false,
            'message' => 'Unauthorized to delete this user.',
        ], 403);
    }

    // ✅ Soft delete or force delete as needed
    $user->delete();

    return response()->json([
        'status'  => true,
        'message' => 'User deleted successfully.',
    ]);
}

    /**
     * Toggle user's active/inactive status
     */
    public function toggleUser(Request $request, User $user)
    {
        if (!$request->user()->hasRole('master_admin') &&
            $user->company_id !== $request->user()->company_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json(['status' => true, 'data' => $user]);
    }

    // --------------------------------------------
    // Profile & Logout
    // --------------------------------------------

    /**
     * Get logged-in user's profile with relationships
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load([
            'roles', 'permissions', 'state', 'district', 'city', 'tehsil', 'pincode'
        ]);

        return response()->json(['status' => true, 'data' => $user]);
    }

    /**
     * Logout and revoke current access token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['status' => true, 'message' => 'Logged out']);
    }

    // --------------------------------------------
    // Geographic Selectors (AJAX helpers)
    // --------------------------------------------

    public function getDistricts(Request $request, $state_id)
    {
        return response()->json(District::where('state_id', $state_id)->get());
    }

    public function getCities(Request $request, $district_id)
    {
        return response()->json(City::where('district_id', $district_id)->get());
    }

    public function getTehsils(Request $request, $city_id)
    {
        return response()->json(Tehsil::where('city_id', $city_id)->get());
    }

    public function getPincodes(Request $request, $city_id)
    {
        return response()->json(Pincode::where('city_id', $city_id)->get());
    }
}
