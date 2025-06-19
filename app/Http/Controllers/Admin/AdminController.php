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
    // public function index()
    // {
    //     $totalUsers = User::count();
    //     $totalRoles = \Spatie\Permission\Models\Role::count();
    //     $totalPermissions = \Spatie\Permission\Models\Permission::count();
    //     $totalCustomers = \App\Models\Customer::count();

    //     $onlineTimeout = now()->subMinutes(10);

    //     // Eager-load roles and permissions
    //     $onlineUsers = User::where('last_seen', '>=', $onlineTimeout)
    //         ->with(['roles', 'permissions']) // Eager load for performance
    //         ->get();

    //     return view('admin.dashboard', compact(
    //         'totalUsers',
    //         'totalRoles',
    //         'totalPermissions',
    //         'totalCustomers',
    //         'onlineUsers'
    //     ));
    // }


    public function index()
    {
        $user = Auth::user();
        $onlineTimeout = now()->subMinutes(10);

        // Check if logged-in user is a master_admin
        $isMasterAdmin = $user->hasRole('master_admin');

        // Gather data accordingly
        if ($isMasterAdmin) {
            $totalUsers = User::count();
            $totalRoles = \Spatie\Permission\Models\Role::count();
            $totalPermissions = \Spatie\Permission\Models\Permission::count();

            // Temporarily disable customer stats without error
            $totalCustomers = null;

            $onlineUsers = User::where('last_seen', '>=', $onlineTimeout)
                ->with(['roles', 'permissions'])
                ->get();
        } else {
            $companyId = $user->company_id;

            $totalUsers = User::where('company_id', $companyId)->count();
            $totalRoles = \Spatie\Permission\Models\Role::where('company_id', $companyId)->count();
            $totalPermissions = \Spatie\Permission\Models\Permission::where('company_id', $companyId)->count();

            // Disable customer stats but safely
            $totalCustomers = null;

            $onlineUsers = User::where('company_id', $companyId)
                ->where('last_seen', '>=', $onlineTimeout)
                ->with(['roles', 'permissions'])
                ->get();
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalRoles',
            'totalPermissions',
            'totalCustomers',
            'onlineUsers'
        ));
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
    // public function store(LoginRequest $request)
    // {
    //     $credentials = $request->only('email', 'password');


    //     // Find user by email first
    //     $user = User::where('email', $credentials['email'])->first();

    //     // Check if user exists
    //     if (!$user) {
    //         return redirect()->back()->with('error_message', 'Invalid Email or Password');
    //     }

    //     // Check if user is active
    //     if ($user->is_active == 0) {
    //         return redirect()->back()->with('error_message', 'Your account is inactive. Please contact support.');
    //     }

    //     // Ensure user has at least one role
    //     if ($user->roles()->count() === 0) {
    //         return redirect()->back()->with('error_message', 'You do not have any assigned role. Please contact administrator.');
    //     }

    //     // Attempt login using default guard (change to 'admin' if using admin guard)
    //     if (Auth::attempt($credentials, $request->filled('remember'))) {
    //         // Regenerate session to prevent fixation attacks
    //         $request->session()->regenerate();

    //         return redirect()->route('admin.dashboard');
    //     } else {
    //         return redirect()->back()->with('error_message', 'Invalid Email or Password');
    //     }
    // }

    public function store(LoginRequest $request)
    {
        // Get the credentials (email, password, and company code)
        $credentials = $request->only('email', 'password', 'company_id');

        // Step 1: Check if the user has the "master_admin" role
        $isMasterUser = User::where('email', $credentials['email'])
            ->whereHas('roles', function ($query) {
                // Check if the user has the 'master_admin' role
                $query->where('name', 'master_admin');  // Updated role name
            })->exists();

        // If the user is a master_admin, skip the company validation
        if (!$isMasterUser) {
            // Step 2: Check if company code exists in the companies table
            $company = \App\Models\Company::where('code', $credentials['company_id'])->first();

            if (!$company) {
                return redirect()->back()->with('error_message', 'Invalid Company Code.');
            }
        } else {
            // For master_admin user, we can skip setting a company (or set it to null)
            $company = null;
        }

        // Step 3: Find the user by email and check if they belong to the specified company (if not a master_admin)
        $userQuery = User::where('email', $credentials['email']);

        // If not master_admin, ensure the user belongs to the specific company
        if (!$isMasterUser) {
            $userQuery->where('company_id', $company->id);
        }

        $user = $userQuery->first();

        // Check if the user exists and is active
        if (!$user) {
            return redirect()->back()->with('error_message', 'Invalid Email or Password.');
        }

        if ($user->is_active == 0) {
            return redirect()->back()->with('error_message', 'Your account is inactive. Please contact support.');
        }

        // Ensure the user has at least one role
        if ($user->roles()->count() === 0) {
            return redirect()->back()->with('error_message', 'You do not have any assigned role. Please contact the administrator.');
        }

        // Attempt login using the default guard
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {

            // ✅ Cookie Logic - Remember me
            if (!empty($request->remember)) {
                setcookie("email", $credentials["email"], time() + 3600); // 1 hour
                setcookie("password", $credentials["password"], time() + 3600);
            } else {
                setcookie("email", "", time() - 3600);
                setcookie("password", "", time() - 3600);
            }
            // ✅ END -- Cookie Logic - Remember me --  END ✅

            // Regenerate the session to prevent session fixation attacks
            $request->session()->regenerate();

            // Redirect to the admin dashboard after successful login
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->back()->with('error_message', 'Invalid Email or Password.');
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
        $user = Auth::user(); // get currently authenticated user

        if ($user) {
            $user->last_seen = null; // Set last_seen to null so they appear offline immediately
            $user->save();
        }

        Auth::logout(); // Logout user
        return redirect()->route('admin.login'); // Redirect to login
    }
}
