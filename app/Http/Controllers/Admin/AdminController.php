<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\Admin\AdminService;
use Session;
// use App\Http\Requests\Auth\LoginRequest;

class AdminController extends Controller
{

    protected $adminService;

    // ✅ Inject AdminService using Constructor
    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.login');
    }


    
    /**
     * Handle the login attempt.
     */
    public function store(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');


        // Find user by email first
        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists
        if (!$user) {
            return redirect()->back()->with('error_message', 'Invalid Email or Password');
        }

        // Check if user is active
        if ($user->is_active == 0) {
            return redirect()->back()->with('error_message', 'Your account is inactive. Please contact support.');
        }

        // Ensure user has at least one role
        if ($user->roles()->count() === 0) {
            return redirect()->back()->with('error_message', 'You do not have any assigned role. Please contact administrator.');
        }

        // Attempt login using default guard (change to 'admin' if using admin guard)
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->back()->with('error_message', 'Invalid Email or Password');
        }
    }



    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
