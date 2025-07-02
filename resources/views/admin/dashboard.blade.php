@extends('admin.layout.layout')

@section('content')
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Dashboard</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">

                <!-- Stats Summary -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-primary">
                            <div class="inner">
                                <h3>{{ $totalUsers }}</h3>
                                <p>Total Users</p>
                            </div>
                            <a href="{{ url('admin/users') }}" class="small-box-footer">More info <i
                                    class="bi bi-link-45deg"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-success">
                            <div class="inner">
                                <h3>{{ $totalRoles }}</h3>
                                <p>Total Roles</p>
                            </div>
                            <a href="{{ url('admin/roles') }}" class="small-box-footer">More info <i
                                    class="bi bi-link-45deg"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-warning">
                            <div class="inner">
                                <h3>{{ $totalPermissions }}</h3>
                                <p>Total Permissions</p>
                            </div>
                            <a href="{{ url('admin/permissions') }}" class="small-box-footer">More info <i
                                    class="bi bi-link-45deg"></i></a>
                        </div>
                    </div>

                    @if (!is_null($totalCustomers))
                        <div class="col-lg-3 col-6">
                            <div class="small-box text-bg-danger">
                                <div class="inner">
                                    <h3>{{ $totalCustomers }}</h3>
                                    <p>Total Customers</p>
                                </div>
                                <a href="{{ url('admin/customers') }}" class="small-box-footer">
                                    More info <i class="bi bi-link-45deg"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Logged-in User Info Table -->

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mt-4">
                            <div class="card-header">
                                <h3 class="card-title">Online Users Details</h3>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Roles</th>
                                            <th>Permissions</th>
                                            <th>Last Seen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($onlineUsers as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->mobile ?? 'N/A' }}</td>
                                                <td>
                                                    @foreach ($user->getRoleNames() as $role)
                                                        <span class="badge bg-success">{{ $role }}</span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                        data-bs-target="#permissionsModal-{{ $user->id }}">
                                                        View
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="permissionsModal-{{ $user->id }}"
                                                        tabindex="-1"
                                                        aria-labelledby="permissionsModalLabel-{{ $user->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-scrollable">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="permissionsModalLabel-{{ $user->id }}">
                                                                        Permissions for {{ $user->name }}
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    @if (count($user->getAllPermissionsList()))
                                                                        @foreach ($user->getAllPermissionsList() as $permission)
                                                                            <span
                                                                                class="badge bg-info mb-1">{{ $permission }}</span>
                                                                        @endforeach
                                                                    @else
                                                                        <p>No permissions assigned.</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>{{ $user->last_seen ? $user->last_seen->diffForHumans() : 'N/A' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No users online currently.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Logged-in User Info Table -->

                <!-- User Sessions Log Table -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">User Login & Logout History</h3>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>First Login</th>
                                    <th>Last Logout</th>
                                    <th>Total Session Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    use App\Models\UserSession;
                                    use Illuminate\Support\Facades\Auth;

                                    $loggedInUser = Auth::user();
                                    $isMasterAdmin = $loggedInUser->hasRole('master_admin');

                                    $sessionsQuery = UserSession::with('user')
                                        ->whereDate('login_at', now());

                                    if (!$isMasterAdmin) {
                                        $sessionsQuery->whereHas('user', function ($q) use ($loggedInUser) {
                                            $q->where('company_id', $loggedInUser->company_id);
                                        });
                                    }

                                    $sessionsGrouped = $sessionsQuery->get()->groupBy('user_id');
                                @endphp

                                @forelse ($sessionsGrouped as $userId => $userSessions)
                                    @php
                                        $user = $userSessions->first()->user;
                                        $firstLogin = $userSessions->sortBy('login_at')->first()->login_at;

                                        $lastLogoutSession = $userSessions
                                            ->whereNotNull('logout_at')
                                            ->sortByDesc('logout_at')
                                            ->first();
                                        $lastLogout = $lastLogoutSession ? $lastLogoutSession->logout_at : null;

                                        $totalDuration = $userSessions->sum('session_duration');
                                    @endphp
                                    <tr>
                                        <td>
                                            @if ($user)
                                                <a href="javascript:void(0);"
                                                    class="view-sessions-link text-primary text-decoration-underline"
                                                    data-user-id="{{ $user->id }}"
                                                    data-user-name="{{ $user->name }}">
                                                    {{ $user->name }}
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $user?->email ?? 'N/A' }}</td>
                                        <td>{{ $firstLogin ? $firstLogin->format('d-m-Y H:i:s') : 'N/A' }}</td>
                                        <td>{{ $lastLogout ? $lastLogout->format('d-m-Y H:i:s') : 'Active' }}</td>
                                        <td>{{ $totalDuration ? gmdate('H:i:s', $totalDuration) : 'Active' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No session records found.</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- End User Sessions Log Table -->

                <!-- Session History Modal -->
                <div class="modal fade" id="sessionHistoryModal" tabindex="-1" aria-labelledby="sessionHistoryModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="sessionHistoryModalLabel">Session History</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="sessionHistoryContent">Loading...</div>
                            </div>
                        </div>
                    </div>
                </div>

                @push('scripts')
                @endpush

            </div>
        </div>
    </main>
@endsection
