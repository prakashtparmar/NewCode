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
            'email' => 'required|email|max:255',
            'password' => 'required|string',
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email is required!',
            'email.email' => 'Please provide a valid email address!',
            'password.required' => 'Password is required!',
        ];
    }
}
