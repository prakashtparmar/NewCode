<div class="mb-3">
    <label>Date</label>
    <input type="date" name="trip_date" class="form-control" value="{{ old('trip_date', $trip->trip_date ?? '') }}">
</div>
<div class="mb-3">
    <label>Start Time</label>
    <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $trip->start_time ?? '') }}">
</div>
<div class="mb-3">
    <label>End Time</label>
    <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $trip->end_time ?? '') }}">
</div>
<div class="mb-3">
    <label>Start Location</label>
    <input type="text" name="start_lat" class="form-control" placeholder="Latitude" value="{{ old('start_lat', $trip->start_lat ?? '') }}">
    <input type="text" name="start_lng" class="form-control mt-1" placeholder="Longitude" value="{{ old('start_lng', $trip->start_lng ?? '') }}">
</div>
<div class="mb-3">
    <label>End Location</label>
    <input type="text" name="end_lat" class="form-control" placeholder="Latitude" value="{{ old('end_lat', $trip->end_lat ?? '') }}">
    <input type="text" name="end_lng" class="form-control mt-1" placeholder="Longitude" value="{{ old('end_lng', $trip->end_lng ?? '') }}">
</div>
<div class="mb-3">
    <label>Travel Mode</label>
    <select name="travel_mode" class="form-control">
        <option value="car" {{ (old('travel_mode', $trip->travel_mode ?? '') == 'car') ? 'selected' : '' }}>Car</option>
        <option value="bike" {{ (old('travel_mode', $trip->travel_mode ?? '') == 'bike') ? 'selected' : '' }}>Bike</option>
        <option value="walk" {{ (old('travel_mode', $trip->travel_mode ?? '') == 'walk') ? 'selected' : '' }}>Walk</option>
    </select>
</div>
<div class="mb-3">
    <label>Purpose</label>
    <textarea name="purpose" class="form-control">{{ old('purpose', $trip->purpose ?? '') }}</textarea>
</div>
