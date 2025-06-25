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
                        <span class="badge {{ $trip->status === 'completed' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($trip->status) }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Approval:</strong><br>
                        <span class="badge 
                            @if($trip->approval_status == 'approved') bg-success
                            @elseif($trip->approval_status == 'denied') bg-danger
                            @else bg-secondary
                            @endif">
                            {{ ucfirst($trip->approval_status) ?? 'Pending' }}
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

                    <!-- Hidden Fields for JS -->
                    <input type="hidden" id="start_lat" value="{{ $trip->start_lat }}">
                    <input type="hidden" id="start_lng" value="{{ $trip->start_lng }}">
                    <input type="hidden" id="end_lat" value="{{ $trip->end_lat }}">
                    <input type="hidden" id="end_lng" value="{{ $trip->end_lng }}">
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

{{-- @section('scripts')
<script>
    function initMap() {
        const startLat = parseFloat(document.getElementById('start_lat').value);
        const startLng = parseFloat(document.getElementById('start_lng').value);
        const endLat = parseFloat(document.getElementById('end_lat').value);
        const endLng = parseFloat(document.getElementById('end_lng').value);

        const centerLat = (startLat + endLat) / 2;
        const centerLng = (startLng + endLng) / 2;

        const map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: { lat: centerLat, lng: centerLng }
        });

        const startMarker = new google.maps.Marker({
            position: { lat: startLat, lng: startLng },
            map: map,
            label: 'S'
        });

        const endMarker = new google.maps.Marker({
            position: { lat: endLat, lng: endLng },
            map: map,
            label: 'E'
        });

        const tripPath = new google.maps.Polyline({
            path: [
                { lat: startLat, lng: startLng },
                { lat: endLat, lng: endLng }
            ],
            geodesic: true,
            strokeColor: '#007bff',
            strokeOpacity: 1.0,
            strokeWeight: 4
        });

        tripPath.setMap(map);
    }

    window.onload = initMap;
</script>
@endsection --}}
