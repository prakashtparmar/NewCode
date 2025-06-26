@extends('admin.layout.layout')

@section('content')
    <main class="app-main">
        {{-- Header Section --}}
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Trips</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Trip Management</a></li>
                            <li class="breadcrumb-item active">All Trips</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content Section --}}
        <div class="app-content">
            <div class="container-fluid">
                <div class="card mb-4">
                    {{-- Card Header Section --}}
                    <div class="card-header">
                        <h5 class="card-title">Trip List</h5>
                        <a href="{{ route('trips.create') }}" class="btn btn-primary float-end">Add Trip</a>
                    </div>

                    {{-- Card Body Section --}}
                    <div class="card-body table-responsive">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                <strong>Success:</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                <strong>Error:</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <table id="trips-table" class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#ID</th>
                                    <th>Created By</th>
                                    <th>Date</th>
                                    <th>Mode</th>
                                    <th>Distance (km)</th>
                                    <th>Status</th>
                                    <th>Approval</th>
                                    <th>Logs</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trips as $trip)
                                    <tr>
                                        <td>{{ $trip->id }}</td>
                                        <td>{{ $trip->user->name ?? 'N/A' }}</td>
                                        <td>{{ $trip->trip_date }}</td>
                                        <td>{{ $trip->travel_mode }}</td>
                                        <td>{{ $trip->total_distance_km }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $trip->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
                                                {{ ucfirst($trip->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($trip->approval_status === 'pending')
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                        id="approvalDropdown{{ $trip->id }}" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        Pending
                                                    </button>
                                                    <ul class="dropdown-menu"
                                                        aria-labelledby="approvalDropdown{{ $trip->id }}">
                                                        <li>
                                                            <form method="POST"
                                                                action="{{ route('trips.approve', $trip->id) }}">
                                                                @csrf
                                                                <input type="hidden" name="status" value="approved">
                                                                <button type="submit" class="dropdown-item text-success">
                                                                    Approve
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#denyModal{{ $trip->id }}">
                                                                Deny
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @else
                                                <span
                                                    class="badge 
                                                    @if ($trip->approval_status == 'approved') bg-success
                                                    @elseif($trip->approval_status == 'denied') bg-danger
                                                    @else bg-secondary @endif">
                                                    {{ ucfirst($trip->approval_status) }}
                                                </span>
                                                @if ($trip->approval_status === 'denied' && $trip->approval_reason)
                                                    <br><small class="text-muted">Reason:
                                                        {{ $trip->approval_reason }}</small>
                                                @endif
                                                @if ($trip->approvedByUser)
                                                    <br><small class="text-muted">By:
                                                        {{ $trip->approvedByUser->name }}</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {{ $trip->tripLogs->count() }} logs<br>
                                            <a href="#" class="text-primary" data-bs-toggle="modal"
                                                data-bs-target="#logModal{{ $trip->id }}">view</a>

                                            {{-- Log Modal --}}
                                            <div class="modal fade" id="logModal{{ $trip->id }}" tabindex="-1"
                                                aria-labelledby="logModalLabel{{ $trip->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Trip Logs for Trip #{{ $trip->id }}
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            @if ($trip->tripLogs->count())
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>#</th>
                                                                                <th>Latitude</th>
                                                                                <th>Longitude</th>
                                                                                <th>Recorded At</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($trip->tripLogs as $index => $log)
                                                                                <tr>
                                                                                    <td>{{ $index + 1 }}</td>
                                                                                    <td>{{ $log->latitude }}</td>
                                                                                    <td>{{ $log->longitude }}</td>
                                                                                    <td>{{ $log->recorded_at }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @else
                                                                <p>No logs available for this trip.</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('trips.show', $trip) }}" class="text-info me-2"
                                                title="View">
                                                <i class="fas fa-eye"></i></a>&nbsp;
                                            <a href="{{ route('trips.edit', $trip) }}" class="text-warning me-2"
                                                title="Edit">
                                                <i class="fas fa-edit"></i></a>&nbsp;
                                            <form action="{{ route('trips.destroy', $trip) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this trip?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link p-0 text-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No trips found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Deny Modals --}}
                        @foreach ($trips as $trip)
                            @if ($trip->approval_status === 'pending')
                                <div class="modal fade" id="denyModal{{ $trip->id }}" tabindex="-1"
                                    aria-labelledby="denyModalLabel{{ $trip->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('trips.approve', $trip->id) }}"
                                            class="modal-content">
                                            @csrf
                                            <input type="hidden" name="status" value="denied">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Deny Trip #{{ $trip->id }}</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Date:</strong> {{ $trip->trip_date }}</p>
                                                <p><strong>Distance:</strong> {{ $trip->total_distance_km }} km</p>
                                                <p><strong>Created by:</strong> {{ $trip->user->name ?? 'N/A' }}</p>
                                                <p><strong>Company:</strong> {{ $trip->company->name ?? 'N/A' }}</p>
                                                <div class="mb-3">
                                                    <label for="reason-{{ $trip->id }}" class="form-label">Reason for
                                                        Denial</label>
                                                    <textarea name="reason" id="reason-{{ $trip->id }}" class="form-control" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-danger">Submit Denial</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
