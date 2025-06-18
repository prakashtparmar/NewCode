<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->route('user')->id;  // Get user id from route

        return [
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$userId}",
            'password' => 'nullable|string|min:6|confirmed',
        ];
    }
}
