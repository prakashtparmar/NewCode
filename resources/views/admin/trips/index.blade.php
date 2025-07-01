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
                <div class="card-header">
                    <h5 class="card-title">Trip List</h5>
                    @can('create_trips')
                        <a href="{{ route('trips.create') }}" class="btn btn-primary float-end">Add Trip</a>
                    @endcan
                </div>

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

                    @can('view_trips')
                        <table id="trips-table" class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#ID</th>
                                    <th>Created By</th>
                                    <th>Date</th>
                                    <th>Mode</th>
                                    <th>Distance (km)</th>
                                    <th>Place To Visit</th>
                                    <th>Start KM</th>
                                    <th>Start Photo</th>
                                    <th>End KM</th>
                                    <th>End Photo</th>
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
                                        <td>{{ $trip->place_to_visit ?? '-' }}</td>
                                        <td>{{ $trip->starting_km ?? '-' }}</td>
                                        <td>
                                            @if ($trip->start_km_photo)
                                                <a href="{{ asset('storage/' . $trip->start_km_photo) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $trip->start_km_photo) }}" alt="Start Photo" width="50">
                                                </a>
                                            @else -
                                            @endif
                                        </td>
                                        <td>{{ $trip->end_km ?? '-' }}</td>
                                        <td>
                                            @if ($trip->end_km_photo)
                                                <a href="{{ asset('storage/' . $trip->end_km_photo) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $trip->end_km_photo) }}" alt="End Photo" width="50">
                                                </a>
                                            @else -
                                            @endif
                                        </td>

                                        {{-- Status --}}
                                        <td>
                                            <form method="POST" action="{{ route('trips.status.toggle', $trip->id) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $trip->status === 'completed' ? 'pending' : 'completed' }}">
                                                <button type="submit" class="badge {{ $trip->status === 'completed' ? 'bg-success' : 'bg-warning' }}"
                                                    onclick="return confirm('Are you sure you want to mark this trip as {{ $trip->status === 'completed' ? 'Pending' : 'Completed' }}?')">
                                                    {{ ucfirst($trip->status) }}
                                                </button>
                                            </form>
                                        </td>

                                        {{-- Approval --}}
                                      <td>
    @if(auth()->user()->can('trip_approvals') && $trip->approval_status === 'pending')
        <div class="dropdown">
            <button class="badge bg-warning text-dark dropdown-toggle border-0"
                type="button" id="approvalDropdown{{ $trip->id }}"
                data-bs-toggle="dropdown">
                Pending
            </button>
            <ul class="dropdown-menu">
                <li>
                    <form method="POST" action="{{ route('trips.approve', $trip->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="dropdown-item text-success">
                            <i class="fas fa-check-circle me-2"></i> Approve
                        </button>
                    </form>
                </li>
                <li>
                    <a class="dropdown-item text-danger" href="#"
                        data-bs-toggle="modal"
                        data-bs-target="#denyModal{{ $trip->id }}">
                        <i class="fas fa-times-circle me-2"></i> Deny
                    </a>
                </li>
            </ul>
        </div>
    @else
        <span class="badge 
            @if ($trip->approval_status == 'approved') bg-success
            @elseif($trip->approval_status == 'denied') bg-danger
            @else bg-secondary @endif">
            {{ ucfirst($trip->approval_status) }}
        </span>
        @if ($trip->approval_status === 'denied' && $trip->approval_reason)
            <br><small class="text-muted">Reason: {{ $trip->approval_reason }}</small>
        @endif
        @if ($trip->approval_status === 'denied' && $trip->approvedByUser)
            <br><small class="text-muted">By: {{ $trip->approvedByUser->name }}</small>
        @endif
    @endif
</td>


                                        {{-- Logs --}}
                                        <td>
                                            @can('view_trip_logs')
                                                {{ $trip->tripLogs->count() }} logs<br>
                                                <a href="#" class="text-primary" data-bs-toggle="modal"
                                                    data-bs-target="#logsModal{{ $trip->id }}">view</a>
                                            @endcan
                                        </td>

                                        {{-- Actions --}}
                                        <td>
                                            <a href="{{ route('trips.show', $trip) }}" class="text-info me-2" title="View">
                                                <i class="fas fa-eye"></i></a>
                                            @can('edit_trips')
                                                <a href="{{ route('trips.edit', $trip) }}" class="text-warning me-2" title="Edit">
                                                    <i class="fas fa-edit"></i></a>
                                            @endcan
                                            @can('delete_trips')
                                                <form action="{{ route('trips.destroy', $trip) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this trip?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link p-0 text-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="text-center">No trips found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    {{-- Modals (placed outside table) --}}
    @foreach($trips as $trip)
        @can('trip_approvals')
            @if ($trip->approval_status === 'pending')
                <div class="modal fade" id="denyModal{{ $trip->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('trips.approve', $trip->id) }}" class="modal-content">
                            @csrf
                            <input type="hidden" name="status" value="denied">
                            <div class="modal-header">
                                <h5 class="modal-title">Deny Trip #{{ $trip->id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Date:</strong> {{ $trip->trip_date }}</p>
                                <p><strong>Distance:</strong> {{ $trip->total_distance_km }} km</p>
                                <p><strong>Created by:</strong> {{ $trip->user->name ?? 'N/A' }}</p>
                                <p><strong>Company:</strong> {{ $trip->company->name ?? 'N/A' }}</p>
                                <div class="mb-3">
                                    <label for="reason-{{ $trip->id }}" class="form-label">Reason for Denial</label>
                                    <textarea name="reason" id="reason-{{ $trip->id }}" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">Submit Denial</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endcan
    @endforeach

</main>
@endsection
