@extends('admin.layout.layout')

@section('content')
    <main class="app-main">

        <!-- Header Section -->
        <div class="app-content-header mb-3">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h1 class="app-title">Trip Details</h1>
                    <!-- Back Button -->
                    <div class="mt-3 text-end">
                        <a href="{{ route('trips.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Trips
                        </a>
                    </div>
                </div>


            </div>
        </div>

        <!-- Content Section -->
        <div class="app-content">
            <div class="container-fluid">
                <div class="card shadow-sm border-0">
                    <div class="card-body">



                        <!-- Trip Details Table -->
                        <div>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Trip Date</th>
                                        <td>{{ $trip->trip_date }}</td>
                                        <th>Travel Mode</th>
                                        <td>{{ ucfirst($trip->travel_mode) }}</td>
                                        <th>Status</th>
                                        <td>
                                            <span
                                                class="badge {{ $trip->status === 'completed' ? 'bg-success' : 'bg-warning' }} p-2">
                                                {{ ucfirst($trip->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Approval Status</th>
                                        <td>
                                            <span class="badge 
                                                @if ($trip->approval_status == 'approved') bg-success
                                                @elseif($trip->approval_status == 'denied') bg-danger
                                                @else bg-secondary @endif p-2">
                                                {{ ucfirst($trip->approval_status) ?? 'Pending' }}
                                            </span>
                                        </td>
                                        <th>Purpose</th>
                                        <td>{{ $trip->purpose }}</td>
                                        <th>Tour Type</th>
                                        <td>{{ $trip->tour_type }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Place To Visit</th>
                                        <td colspan="5">{{ $trip->place_to_visit }}</td>
                                    </tr>
                                    <tr>
                                        <th>Starting KM</th>
                                        <td>{{ $trip->starting_km }}</td>
                                        <th>End KM</th>
                                        <td>{{ $trip->end_km }}</td>
                                        <th>Traveled Distance (KM Reading)</th>
                                        <td>
                                            @if ($trip->starting_km !== null && $trip->end_km !== null)
                                                {{ $trip->end_km - $trip->starting_km }} km
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Total Distance (GPS) (km)</th>
                                        <td>{{ $trip->total_distance_km ?? 'Calculating...' }}</td>
                                        <th>Start Location</th>
                                        <td>{{ $trip->start_lat }}, {{ $trip->start_lng }}</td>
                                        <th>End Location</th>
                                        <td>{{ $trip->end_lat }}, {{ $trip->end_lng }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Map Section -->
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fas fa-map-marked-alt me-2"></i>Trip Route
                                Map</label>
                            <div class="border rounded shadow-sm" id="map" style="height: 500px;"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Pass logs to JS -->
        <script>
            const tripLogs = @json($tripLogs);
        </script>

    </main>
@endsection