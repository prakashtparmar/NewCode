<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
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

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => true, 'message' => 'Logged out']);
    }
}
