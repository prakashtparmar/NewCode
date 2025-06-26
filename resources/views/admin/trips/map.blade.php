@extends('admin.layout.layout')

@section('content')
<main class="app-main">
    <!-- Header Section -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Trip Details</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('trips.index') }}">Trips</a></li>
                        <li class="breadcrumb-item active">View Trip</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="app-content">
        <div class="container-fluid">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Trip Information</h5>
                </div>

                <div class="card-body row">
                    <div class="col-md-6 mb-3">
                        <strong>Trip Date:</strong><br>
                        {{ $trip->trip_date }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Travel Mode:</strong><br>
                        {{ ucfirst($trip->travel_mode) }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Status:</strong><br>
                        <span class="badge {{ $trip->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($trip->status) }}
                        </span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Approval:</strong><br>
                        <span class="badge 
                            @if($trip->approval_status == 'approved') bg-success
                            @elseif($trip->approval_status == 'denied') bg-danger
                            @else bg-secondary
                            @endif">
                            {{ ucfirst($trip->approval_status ?? 'Pending') }}
                        </span>
                    </div>
                    <div class="col-md-12 mb-3">
                        <strong>Purpose:</strong><br>
                        {{ $trip->purpose }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Start Location:</strong><br>
                        {{ $trip->start_lat }}, {{ $trip->start_lng }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>End Location:</strong><br>
                        {{ $trip->end_lat }}, {{ $trip->end_lng }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Total Distance (km):</strong><br>
                        <span id="distance-display">{{ $trip->total_distance_km ?? 'Calculating...' }}</span>
                    </div>

                    <!-- Map Preview -->
                    <div class="col-md-12">
                        <label class="form-label">Trip Route Map</label>
                        <div id="map" style="height: 500px; border: 1px solid #ccc;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_API_KEY"></script>
<script>
    const tripPathCoordinates = @json($logs->map(fn($log) => ['lat' => (float)$log->latitude, 'lng' => (float)$log->longitude]));

    function initMap() {
        if (!tripPathCoordinates.length) {
            document.getElementById('map').innerHTML = '<p class="text-danger p-3">No location data available for this trip.</p>';
            return;
        }

        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 14,
            center: tripPathCoordinates[0],
        });

        // Start marker
        new google.maps.Marker({
            position: tripPathCoordinates[0],
            map: map,
            label: "S"
        });

        // End marker
        new google.maps.Marker({
            position: tripPathCoordinates[tripPathCoordinates.length - 1],
            map: map,
            label: "E"
        });

        // Draw path polyline
        const tripPath = new google.maps.Polyline({
            path: tripPathCoordinates,
            geodesic: true,
            strokeColor: "#28a745",
            strokeOpacity: 1.0,
            strokeWeight: 4,
        });

        tripPath.setMap(map);
    }

    window.onload = initMap;
</script>
@endsection





-------------------------------

@extends('admin.layout.layout')

@section('content')
<main class="app-main">
    <!-- Header Section -->
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

    <!-- Content Section -->
    <div class="app-content">
        <div class="container-fluid">
            <div class="card mb-4">
                <!-- Card Header -->
                <div class="card-header">
                    <h5 class="card-title">Trip List</h5>
                    <a href="{{ route('trips.create') }}" class="btn btn-primary float-end">Add Trip</a>
                </div>

                <!-- Card Body -->
                <div class="card-body table-responsive">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <strong>Success:</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <strong>Error:</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <table id="trips-table" class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Date</th>
                                <th>Mode</th>
                                <th>Distance (km)</th>
                                <th>Status</th>
                                <th>Approval</th>
                                <th>Created By</th>
                                <th>Company</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trips as $trip)
                                <tr>
                                    <td>{{ $trip->id }}</td>
                                    <td>{{ $trip->trip_date }}</td>
                                    <td>{{ $trip->travel_mode }}</td>
                                    <td>{{ $trip->total_distance_km }}</td>
                                    <td><span class="badge {{ $trip->status === 'completed' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($trip->status) }}</span></td>
                                    <td>
                                        <button class="btn btn-sm 
                                            @if($trip->approval_status == 'approved') btn-success 
                                            @elseif($trip->approval_status == 'denied') btn-danger 
                                            @else btn-secondary 
                                            @endif" 
                                            onclick="openApprovalModal({{ $trip->id }}, '{{ $trip->approval_status }}')">
                                            {{ ucfirst($trip->approval_status) ?? 'Pending' }}
                                        </button>
                                    </td>
                                    <td>{{ $trip->user->name ?? 'N/A' }}</td>
                                    <td>{{ $trip->user->company->name ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('trips.show', $trip) }}" class="text-info me-2" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('trips.edit', $trip) }}" class="text-warning me-2" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('trips.destroy', $trip) }}" method="POST" class="d-inline"
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
                </div> <!-- /.card-body -->
            </div> <!-- /.card -->
        </div> <!-- /.container-fluid -->
    </div> <!-- /.app-content -->

    <!-- Approval Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="approvalForm" method="POST" action="{{ route('trips.approve') }}">
                @csrf
                <input type="hidden" name="trip_id" id="modalTripId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approvalModalLabel">Trip Approval</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Select an action for this trip:</p>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="approval_status" value="approved" id="approveOption">
                            <label class="form-check-label" for="approveOption">Approve</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="approval_status" value="denied" id="denyOption">
                            <label class="form-check-label" for="denyOption">Deny</label>
                        </div>
                        <div class="mt-3" id="denyReasonBox" style="display: none;">
                            <label for="deny_reason" class="form-label">Reason for Denial:</label>
                            <textarea name="deny_reason" id="deny_reason" class="form-control" rows="3" placeholder="Enter reason..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

@push('scripts')
<script>
    function openApprovalModal(tripId, currentStatus) {
        document.getElementById('modalTripId').value = tripId;
        document.getElementById('denyReasonBox').style.display = 'none';
        document.getElementById('approveOption').checked = false;
        document.getElementById('denyOption').checked = false;
        document.getElementById('deny_reason').value = '';
        var modal = new bootstrap.Modal(document.getElementById('approvalModal'));
        modal.show();

        document.getElementById('approveOption').addEventListener('change', function () {
            document.getElementById('denyReasonBox').style.display = 'none';
        });
        document.getElementById('denyOption').addEventListener('change', function () {
            document.getElementById('denyReasonBox').style.display = 'block';
        });
    }
</script>
@endpush
@endsection

