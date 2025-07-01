<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    /**
     * API Login — revoke old token, create new, update last_seen
     */
    public function login(Request $request)
{
    // Validate input
    $validator = Validator::make($request->all(), [
        'login_id'   => 'required|string',
        'password'   => 'required',
        'company_id' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => false,
            'message' => 'Validation errors',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $credentials = $request->only('login_id', 'password', 'company_id');

    // Detect if login_id is email or mobile
    if (filter_var($credentials['login_id'], FILTER_VALIDATE_EMAIL)) {
        // Email login
        $user = User::where('email', $credentials['login_id'])->first();
    } else {
        // Mobile login
        $user = User::where('mobile', $credentials['login_id'])->first();
    }

    if (!$user) {
        return response()->json(['status' => false, 'message' => 'Invalid Login ID or Password.'], 401);
    }

    $isMasterAdmin = $user->hasRole('master_admin');

    // Master admin cannot pass company_id
    if ($isMasterAdmin && !empty($credentials['company_id'])) {
        return response()->json([
            'status'  => false,
            'message' => 'Master Admin login should not include Company Code.',
        ], 400);
    }

    // Validate company if not master_admin
    if (!$isMasterAdmin) {
        $company = Company::where('code', $credentials['company_id'])->first();

        if (!$company) {
            return response()->json(['status' => false, 'message' => 'Invalid Company Code.'], 404);
        }
        if ($company->status !== 'Active') {
            return response()->json(['status' => false, 'message' => 'Your company is inactive.'], 403);
        }
        if ($user->company_id != $company->id) {
            return response()->json(['status' => false, 'message' => 'User not linked to this company.'], 403);
        }
    }

    // Validate password
    if (!Hash::check($credentials['password'], $user->password)) {
        return response()->json(['status' => false, 'message' => 'Invalid Login ID or Password.'], 401);
    }

    // Check active
    if ($user->is_active == 0) {
        return response()->json(['status' => false, 'message' => 'Account inactive. Contact support.'], 403);
    }

    // Check role presence
    if ($user->roles()->count() === 0) {
        return response()->json(['status' => false, 'message' => 'No role assigned. Contact admin.'], 403);
    }

    // ✅ Revoke old tokens
    $user->tokens()->delete();

    // ✅ Create new token
    $token = $user->createToken('mobile-token')->plainTextToken;

    // ✅ Update last_seen
    $user->last_seen = now();
    $user->save();

    return response()->json([
        'status'  => true,
        'message' => 'Login successful',
        'data'    => [
            'token'       => $token,
            'user'        => $user,
            'roles'       => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ],
    ], 200);
}

    /**
     * API Logout — revoke tokens + clear last_seen
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->last_seen = null;
            $user->save();
            $user->tokens()->delete();
        }

        return response()->json(['status' => true, 'message' => 'Logged out successfully.'], 200);
    }

    /**
     * Get API Authenticated User Profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'status' => true,
            'data'   => [
                'user'        => $user,
                'roles'       => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
        ], 200);
    }
}
