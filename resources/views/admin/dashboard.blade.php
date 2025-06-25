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
                    {{-- <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-danger">
                            <div class="inner">
                                <h3>{{ $totalCustomers }}</h3>
                                <p>Total Customers</p>
                            </div>
                            <a href="{{ url('admin/customers') }}" class="small-box-footer">More info <i
                                    class="bi bi-link-45deg"></i></a>
                        </div>
                    </div> --}}

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

            </div>
        </div>
    </main>
@endsection
