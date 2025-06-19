<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

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
    return [
        // Email validation: Required, valid email format, maximum length of 255, and should exist in the 'users' table
        'email' => 'required|email|max:255|exists:users,email',  // **Added 'exists' rule to check if email exists in the 'users' table**

        // Password validation: Required, string type, and minimum length of 6 characters
        'password' => 'required|string|min:6',                     // **Added 'min' rule to check for minimum password length (6 characters)**

        // Company code validation: Required, string type, and should exist in the 'companies' table
        'company_id' => 'required|string|exists:companies,code', // **Added validation for 'company_id' to check if it exists in the 'companies' table**
    ];
}

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            // Custom error messages for each validation rule
            'email.required' => 'Email is required!',
            'email.email' => 'Please provide a valid email address!',
            'email.exists' => 'No user found with this email address!',  // **Change: Added custom error message for when email doesn't exist in the 'users' table**

            'password.required' => 'Password is required!',
            'password.min' => 'Password must be at least 6 characters!',  // **Change: Added custom error message for minimum password length**

            'company_id.required' => 'Company code is required!',
            'company_id.exists' => 'Invalid company code!',           // **Change: Added custom error message for invalid company code**
        ];
    }
}
