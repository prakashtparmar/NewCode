@extends('admin.layout.layout')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Edit Trip</h4>
                    <a href="{{ route('trips.index') }}" class="btn btn-sm btn-secondary">Back to Trips</a>
                </div>

                <div class="card-body">
                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> Please fix the following errors:
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Trip Info --}}
                    <div class="mb-4">
                        <p><strong>Created By:</strong> {{ $trip->user->name ?? 'N/A' }}</p>
                        <p><strong>Company:</strong> {{ $trip->company->name ?? 'N/A' }}</p>
                    </div>

                    {{-- Edit Form --}}
                    <form action="{{ route('trips.update', $trip) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="trip_date" class="form-label">Trip Date</label>
                            <input type="date" name="trip_date" class="form-control" value="{{ old('trip_date', $trip->trip_date) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $trip->start_time) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $trip->end_time) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_lat" class="form-label">Start Latitude</label>
                                <input type="text" name="start_lat" class="form-control" value="{{ old('start_lat', $trip->start_lat) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="start_lng" class="form-label">Start Longitude</label>
                                <input type="text" name="start_lng" class="form-control" value="{{ old('start_lng', $trip->start_lng) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="end_lat" class="form-label">End Latitude</label>
                                <input type="text" name="end_lat" class="form-control" value="{{ old('end_lat', $trip->end_lat) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_lng" class="form-label">End Longitude</label>
                                <input type="text" name="end_lng" class="form-control" value="{{ old('end_lng', $trip->end_lng) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="travel_mode" class="form-label">Travel Mode</label>
                            <select name="travel_mode" class="form-select" required>
                                <option value="">-- Select Mode --</option>
                                <option value="car" {{ old('travel_mode', $trip->travel_mode) == 'car' ? 'selected' : '' }}>Car</option>
                                <option value="bike" {{ old('travel_mode', $trip->travel_mode) == 'bike' ? 'selected' : '' }}>Bike</option>
                                <option value="walk" {{ old('travel_mode', $trip->travel_mode) == 'walk' ? 'selected' : '' }}>Walk</option>
                                <option value="public" {{ old('travel_mode', $trip->travel_mode) == 'public' ? 'selected' : '' }}>Public Transport</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="purpose" class="form-label">Purpose</label>
                            <input type="text" name="purpose" class="form-control" value="{{ old('purpose', $trip->purpose) }}">
                        </div>

                        <div class="mb-3">
                            <label for="approval_status" class="form-label">Approval Status</label>
                            <select name="approval_status" id="approval_status" class="form-select" required onchange="toggleReasonField()">
                                <option value="pending" {{ old('approval_status', $trip->approval_status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('approval_status', $trip->approval_status) === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="denied" {{ old('approval_status', $trip->approval_status) === 'denied' ? 'selected' : '' }}>Denied</option>
                            </select>
                        </div>

                        <div class="mb-3" id="denial-reason-block" style="display: {{ old('approval_status', $trip->approval_status) === 'denied' ? 'block' : 'none' }};">
                            <label for="approval_reason" class="form-label">Reason for Denial</label>
                            <textarea name="approval_reason" id="approval_reason" class="form-control" rows="3">{{ old('approval_reason', $trip->approval_reason) }}</textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Trip</button>
                            <a href="{{ route('trips.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Script to toggle denial reason --}}
<script>
    function toggleReasonField() {
        const status = document.getElementById('approval_status').value;
        const reasonBlock = document.getElementById('denial-reason-block');
        reasonBlock.style.display = (status === 'denied') ? 'block' : 'none';
    }
</script>
@endsection
