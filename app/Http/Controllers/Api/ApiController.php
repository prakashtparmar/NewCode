<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Company, State, District, City, Tehsil, Pincode};
use Illuminate\Support\Facades\{Hash, Validator};
use Spatie\Permission\Models\{Role, Permission};

class ApiController extends Controller
{
    // -------------------------
    // Public Endpoints
    // -------------------------

    /**
     * Handle user registration
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

        if (auth('sanctum')->check()) {
            $validated['company_id'] = auth('sanctum')->user()->company_id;
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('users', 'public');
        }

        $user = User::create($validated);

        if (!empty($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        $user->tokens()->where('name', 'api-token')->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        $user->image = $user->image ? asset('storage/' . $user->image) : null;

        return response()->json([
            'message' => 'User registered successfully.',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'      => 'required|email|exists:users,email',
            'password'   => 'required|string|min:6',
            'company_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => false, 'message' => 'Invalid credentials'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['status' => false, 'message' => 'Your account is inactive. Please contact the administrator.'], 403);
        }

        if ($user->roles()->count() === 0) {
            return response()->json(['status' => false, 'message' => 'Role not assigned. Contact admin.'], 403);
        }

        if (!$user->hasRole('master_admin')) {
            $company = Company::where('code', $request->company_id)->first();
            if (!$company || $user->company_id !== $company->id) {
                return response()->json(['status' => false, 'message' => 'Invalid or mismatched company code!'], 403);
            }
        }

        $user->tokens()->where('name', 'api-token')->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'user' => $user->only(['id', 'name', 'email', 'company_id']),
            'token' => $token,
        ]);
    }

    // -------------------------
    // Protected Endpoints
    // -------------------------

    /**
     * List all users
     */
    public function indexUsers(Request $request)
    {
        $query = User::with(['roles', 'permissions', 'state', 'district', 'city', 'tehsil'])->latest();

        if (!$request->user()->hasRole('master_admin')) {
            $query->where('company_id', $request->user()->company_id);
        }

        $users = $query->get();

        foreach ($users as $user) {
            $user->image = $user->image ? asset('storage/' . $user->image) : null;
        }

        return response()->json(['status' => true, 'data' => $users]);
    }

    /**
     * Create a new user
     */
    public function storeUser(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_users')) {
            return response()->json(['status' => false, 'message' => 'Unauthorized to create users.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'roles' => 'array|nullable',
            'image' => 'file|image|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['password'] = Hash::make($data['password']);
        $data['company_id'] = $request->user()->company_id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('users', 'public');
        }

        $user = User::create($data);

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        $user->image = $user->image ? asset('storage/' . $user->image) : null;

        return response()->json(['status' => true, 'message' => 'User created successfully.', 'data' => $user], 201);
    }

    /**
     * Get a single user by ID
     */
    public function showUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        if (!$request->user()->hasRole('master_admin') && $user->company_id !== $request->user()->company_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $user->image = $user->image ? asset('storage/' . $user->image) : null;

        return response()->json(['status' => true, 'data' => $user]);
    }

    /**
     * Update a user
     */
    public function updateUser(Request $request, User $user)
    {
        if (!$request->user()->hasRole('master_admin') && $user->company_id !== $request->user()->company_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|min:6',
            'roles' => 'sometimes|array',
            'permissions' => 'sometimes|array',
            'image' => 'sometimes|file|image|nullable',
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

        $user->image = $user->image ? asset('storage/' . $user->image) : null;

        return response()->json(['status' => true, 'data' => $user]);
    }

    /**
     * Delete a user
     */
    public function deleteUser(Request $request, User $user)
    {
        if (!$request->user()->hasPermissionTo('delete_users')) {
            return response()->json(['status' => false, 'message' => 'Unauthorized to delete users.'], 403);
        }

        if (!$request->user()->hasRole('master_admin') && $user->company_id !== $request->user()->company_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized to delete this user.'], 403);
        }

        $user->delete();

        return response()->json(['status' => true, 'message' => 'User deleted successfully.']);
    }

    /**
     * Toggle user active/inactive
     */
    public function toggleUser(Request $request, User $user)
    {
        if (!$request->user()->hasRole('master_admin') && $user->company_id !== $request->user()->company_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $user->image = $user->image ? asset('storage/' . $user->image) : null;

        return response()->json(['status' => true, 'data' => $user]);
    }

    /**
     * Get profile of logged-in user
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load(['roles', 'permissions', 'state', 'district', 'city', 'tehsil', 'pincode']);

        $user->image = $user->image ? asset('storage/' . $user->image) : null;

        return response()->json(['status' => true, 'data' => $user]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => true, 'message' => 'Logged out']);
    }

    /**
     * Get districts by state
     */
    public function getDistricts(Request $request, $state_id)
    {
        return response()->json(District::where('state_id', $state_id)->get());
    }

    /**
     * Get cities by district
     */
    public function getCities(Request $request, $district_id)
    {
        return response()->json(City::where('district_id', $district_id)->get());
    }

    /**
     * Get tehsils by city
     */
    public function getTehsils(Request $request, $city_id)
    {
        return response()->json(Tehsil::where('city_id', $city_id)->get());
    }

    /**
     * Get pincodes by city
     */
    public function getPincodes(Request $request, $city_id)
    {
        return response()->json(Pincode::where('city_id', $city_id)->get());
    }
}
