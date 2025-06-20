@extends('admin.layout.layout')

@section('content')
<main class="app-main">
    <!-- Page Header -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Trip Management</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('trips.index') }}">Trips</a></li>
                        <li class="breadcrumb-item active">Add Trip</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Add New Trip</h5>
                        </div>

                        <!-- Flash & Validation Errors -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                <strong>Success:</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                <strong>Error:</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if ($errors->any())
                            @foreach ($errors->all() as $error)
                                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                    <strong>Error:</strong> {{ $error }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endforeach
                        @endif

                        <!-- Trip Form -->
                        <form method="POST" action="{{ route('trips.store') }}">
                            @csrf
                            <div class="card-body row">

                                <div class="mb-3 col-md-6">
                                    <label for="trip_date" class="form-label">Trip Date</label>
                                    <input type="date" name="trip_date" class="form-control" value="{{ old('trip_date') }}" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="start_time" class="form-label">Start Time</label>
                                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="end_time" class="form-label">End Time</label>
                                    <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="travel_mode" class="form-label">Travel Mode</label>
                                    <select name="travel_mode" class="form-select" required>
                                        <option value="">-- Select Mode --</option>
                                        <option value="car">Car</option>
                                        <option value="bike">Bike</option>
                                        <option value="walk">Walk</option>
                                    </select>
                                </div>

                                <div class="mb-3 col-md-12">
                                    <label for="purpose" class="form-label">Purpose</label>
                                    <input type="text" name="purpose" class="form-control" value="{{ old('purpose') }}">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Start Location</label>
                                    <div class="input-group">
                                        <input type="text" name="start_lat" id="start_lat" class="form-control me-2" placeholder="Latitude" required>
                                        <input type="text" name="start_lng" id="start_lng" class="form-control" placeholder="Longitude" required>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="setCurrentLocation('start')">Use Current Location</button>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">End Location</label>
                                    <div class="input-group">
                                        <input type="text" name="end_lat" id="end_lat" class="form-control me-2" placeholder="Latitude" required>
                                        <input type="text" name="end_lng" id="end_lng" class="form-control" placeholder="Longitude" required>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="setCurrentLocation('end')">Use Current Location</button>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="total_distance_km" class="form-label">Calculated Distance (km)</label>
                                    <input type="text" name="total_distance_km" id="total_distance_km" class="form-control" readonly>
                                </div>

                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Map Preview</label>
                                    <div id="map" style="height: 400px; border: 1px solid #ccc;"></div>
                                </div>

                            </div>

                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-success">Save Trip</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
