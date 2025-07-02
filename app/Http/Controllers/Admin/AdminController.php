<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\Admin\AdminService;
use App\Models\UserSession;
use Session;

class AdminController extends Controller
{
    protected $adminService;

    // ✅ Inject AdminService using Constructor
    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function index()
    {
        $user = Auth::user();
        $onlineTimeout = now()->subMinutes(10);

        $isMasterAdmin = $user->hasRole('master_admin');

        if ($isMasterAdmin) {
            $totalUsers = User::count();
            $totalRoles = \Spatie\Permission\Models\Role::count();
            $totalPermissions = \Spatie\Permission\Models\Permission::count();
            $totalCustomers = null;

            $onlineUsers = User::where('last_seen', '>=', $onlineTimeout)
                ->with(['roles', 'permissions'])
                ->get();
        } else {
            $companyId = $user->company_id;

            $totalUsers = User::where('company_id', $companyId)->count();
            $totalRoles = \Spatie\Permission\Models\Role::where('company_id', $companyId)->count();
            $totalPermissions = \Spatie\Permission\Models\Permission::where('company_id', $companyId)->count();
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

    public function create()
    {
        return view('admin.login');
    }

    public function store(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password', 'company_id');

        $isMasterUser = User::where('email', $credentials['email'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'master_admin');
            })->exists();

        if (!$isMasterUser) {
            $company = \App\Models\Company::where('code', $credentials['company_id'])->first();
            if (!$company) {
                return redirect()->back()->with('error_message', 'Invalid Company Code.');
            }
            if ($company->status !== 'Active') {
                return redirect()->back()->with('error_message', 'Your company is currently inactive.');
            }
        } else {
            $company = null;
        }

        $userQuery = User::where('email', $credentials['email']);
        if (!$isMasterUser) {
            $userQuery->where('company_id', $company->id);
        }
        $user = $userQuery->first();

        if (!$user) {
            return redirect()->back()->with('error_message', 'Invalid Email or Password.');
        }

        if ($user->is_active == 0) {
            return redirect()->back()->with('error_message', 'Your account is inactive. Please contact support.');
        }

        if ($user->roles()->count() === 0) {
            return redirect()->back()->with('error_message', 'You do not have any assigned role. Please contact the administrator.');
        }

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {

            if (!empty($request->remember)) {
                setcookie("email", $credentials["email"], time() + 3600);
                setcookie("password", $credentials["password"], time() + 3600);
            } else {
                setcookie("email", "", time() - 3600);
                setcookie("password", "", time() - 3600);
            }

            $request->session()->regenerate();

            // ✅ Log the user login session
            UserSession::create([
                'user_id'    => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'login_at'   => now(),
            ]);

            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->back()->with('error_message', 'Invalid Email or Password.');
        }
    }

    public function edit(Admin $admin)
    {
        //
    }

    public function update(Request $request, Admin $admin)
    {
        //
    }

    public function destroy()
    {
        $user = Auth::user();

        if ($user) {
            $user->last_seen = null;
            $user->save();

            $session = UserSession::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest()
                ->first();

            if ($session) {
                $session->update([
                    'logout_at'        => now(),
                    'session_duration' => $session->login_at->diffInSeconds(now()),
                ]);
            }
        }

        Auth::logout();
        return redirect()->route('admin.login');
    }

    /**
     * ✅ Fetch user session logs and total logged-in time for today (for dashboard modal popup)
     */
    public function getUserSessionHistory(Request $request, $userId)
{
    $loggedInUser = Auth::user();
    $targetUser   = User::find($userId);

    if (!$targetUser) {
        return '<p class="text-danger">User not found.</p>';
    }

    $isMasterAdmin = $loggedInUser->hasRole('master_admin');

    // ✅ Restrict company admins from viewing other companies' user sessions
    if (!$isMasterAdmin && $loggedInUser->company_id !== $targetUser->company_id) {
        return '<p class="text-danger">Unauthorized access. You can only view session logs of your own company\'s users.</p>';
    }

    // ✅ Fetch sessions for this user
    $sessions = UserSession::where('user_id', $userId)
        ->orderByDesc('login_at')
        ->get();

    if ($sessions->isEmpty()) {
        return '<p class="text-muted">No session records found.</p>';
    }

    // ✅ Calculate today's total session duration for this user
    $todayTotalSeconds = UserSession::where('user_id', $userId)
        ->whereNotNull('session_duration')
        ->whereDate('login_at', now()->toDateString())
        ->sum('session_duration');

    $html = '<p><strong>Total Active Time Today:</strong> ' . gmdate('H:i:s', $todayTotalSeconds) . '</p>';

    $html .= '<table class="table table-bordered table-striped">';
    $html .= '<thead><tr>
                <th>Login Time</th>
                <th>Logout Time</th>
                <th>Duration</th>
              </tr></thead><tbody>';

    foreach ($sessions as $session) {
        $login    = $session->formatted_login_at;
        $logout   = $session->formatted_logout_at;
        $duration = $session->formatted_duration;

        $html .= "<tr>
                    <td>{$login}</td>
                    <td>{$logout}</td>
                    <td>{$duration}</td>
                  </tr>";
    }

    $html .= '</tbody></table>';

    return $html;
}


}
