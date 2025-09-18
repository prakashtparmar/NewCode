<?php

namespace App\Http\Requests\Auth;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Everyone is allowed to attempt login.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for login request.
     */
    public function rules(): array
    {
        // Get the email from the request
        $email = $this->input('email');
        dd(DB::connection()->getDatabaseName());
        // Step 1: Check if the user has the "master_admin" role
        // $isMasterUser = \App\Models\User::where('email', $email)
        //                                 ->whereHas('roles', function ($query) {
        //                                     $query->where('name', 'master_admin');  // Check for master_admin role
        //                                 })
        //                                 ->exists();

        // Step 2: Define validation rules
        $rules = [
            'email' => 'required|email|max:255|exists:users,email',
            'password' => 'required|string|min:6',
        ];

        dd(Company::all());

        // Step 3: Add conditional rule for company_id if the user is not a master user
        // if (!$isMasterUser) {
            $rules['company_id'] = 'required|string|exists:companies,code'; // Only required if not a master user
        // }

        return $rules;
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email is required!',
            'email.email' => 'Please provide a valid email address!',
            'email.exists' => 'No user found with this email address!',
            'password.required' => 'Password is required!',
            'password.min' => 'Password must be at least 6 characters!',
            'company_id.required' => 'Company code is required!',
            'company_id.exists' => 'Invalid company code!',
        ];
    }
}
