<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
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
        return $this->sendResponse([
            'token' => $token,
            'user' => $user
        ], 'User registered successfully.');
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
            return $this->sendError('Unauthorized access.', ['error' => 'Invalid credentials']);
        }

        if (!$user->is_active) {
            return $this->sendError('Unauthorized access.', ['error' => 'Your account is inactive. Please contact the administrator.']);
        }

        if ($user->roles()->count() === 0) {
            return $this->sendError('Unauthorized Role.', ['error' => 'Role not assigned. Contact admin.']);
        }

        if (!$user->hasRole('master_admin')) {
            $company = Company::where('code', $request->company_id)->first();
            if (!$company || $user->company_id !== $company->id) {
                return $this->sendError('Unauthorized domain.', ['error' => 'Domain does not match user company']);
            }
        }

        $user->tokens()->where('name', 'api-token')->delete();
        $token = $user->createToken('api-token')->plainTextToken;
        return $this->sendResponse([
            'token' => $token,
            'user' => $user
        ], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse(null, 'Log out successful');
    }
}
