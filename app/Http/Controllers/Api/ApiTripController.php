<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiTripController extends Controller
{
    // Store a new trip log point
    public function logPoint(Request $request)
    {
        $validated = $request->validate([
            'trip_id'     => 'required|exists:trips,id',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
            'recorded_at' => 'nullable|date',
        ]);

        $log = TripLog::create([
            'trip_id'     => $validated['trip_id'],
            'latitude'    => $validated['latitude'],
            'longitude'   => $validated['longitude'],
            'recorded_at' => $validated['recorded_at'] ?? now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Log point recorded.',
            'data'    => $log
        ], 201);
    }

    // Get all logs for a trip
    public function logs($tripId)
    {
        $trip = Trip::with('tripLogs')->findOrFail($tripId);

        return response()->json([
            'status' => 'success',
            'trip'   => $trip->only(['id', 'trip_date', 'start_time', 'end_time', 'status']),
            'logs'   => $trip->tripLogs->sortBy('recorded_at')->values()
        ]);
    }

    // Mark a trip as completed
    public function completeTrip($tripId)
    {
        $trip = Trip::findOrFail($tripId);
        $endLog = TripLog::where('trip_id', $tripId)->orderByDesc('recorded_at')->first();

        if ($endLog) {
            $trip->end_lat = $endLog->latitude;
            $trip->end_lng = $endLog->longitude;
            $trip->end_time = now();
        }

        $trip->total_distance_km = $this->calculateDistanceFromLogs($tripId);
        $trip->status = 'completed';
        $trip->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Trip marked as completed.',
            'trip'    => $trip
        ]);
    }

    // Create a new trip via API
    public function storeTrip(Request $request)
{
    $validated = $request->validate([
        'trip_date'      => 'nullable|date',
        'start_time'     => 'nullable',
        'end_time'       => 'nullable',
        'start_lat'      => 'required|numeric',
        'start_lng'      => 'required|numeric',
        'end_lat'        => 'nullable|numeric',
        'end_lng'        => 'nullable|numeric',
        'travel_mode'    => 'required|string',
        'purpose'        => 'nullable|string',
        'tour_type'      => 'nullable|string',
        'place_to_visit' => 'nullable|string',
        'starting_km'    => 'nullable|string',
        'end_km'         => 'nullable|string',
        'start_km_photo' => 'nullable|mimes:jpeg,jpg,png,bmp,gif,svg,webp,tiff,ico|max:5120',
        'end_km_photo'   => 'nullable|mimes:jpeg,jpg,png,bmp,gif,svg,webp,tiff,ico|max:5120',
    ]);

    $user = Auth::user();

    // handle photo uploads if any
    $startKmPhoto = $request->hasFile('start_km_photo')
        ? $request->file('start_km_photo')->store('trip_photos', 'public')
        : null;

    $endKmPhoto = $request->hasFile('end_km_photo')
        ? $request->file('end_km_photo')->store('trip_photos', 'public')
        : null;

    // If end_lat/lng provided, calculate distance
    $distance = null;
    if (!empty($validated['end_lat']) && !empty($validated['end_lng'])) {
        $distance = $this->calculateDistance(
            $validated['start_lat'],
            $validated['start_lng'],
            $validated['end_lat'],
            $validated['end_lng']
        );
    }

    $trip = Trip::create([
        'user_id'           => $user->id,
        'company_id'        => $user->company_id,
        'trip_date'         => $validated['trip_date'] ?? now()->toDateString(),
        'start_time'        => $validated['start_time'] ?? now()->toTimeString(),
        'end_time'          => $validated['end_time'] ?? null,
        'start_lat'         => $validated['start_lat'],
        'start_lng'         => $validated['start_lng'],
        'end_lat'           => $validated['end_lat'] ?? null,
        'end_lng'           => $validated['end_lng'] ?? null,
        'total_distance_km' => $distance,
        'travel_mode'       => $validated['travel_mode'],
        'purpose'           => $validated['purpose'] ?? null,
        'tour_type'         => $validated['tour_type'] ?? null,
        'place_to_visit'    => $validated['place_to_visit'] ?? null,
        'starting_km'       => $validated['starting_km'] ?? null,
        'end_km'            => $validated['end_km'] ?? null,
        'start_km_photo'    => $startKmPhoto,
        'end_km_photo'      => $endKmPhoto,
        'status'            => 'Started',
        'approval_status'   => 'pending',
    ]);

    return response()->json([
        'status'  => 'success',
        'message' => 'Trip created successfully.',
        'trip'    => $trip
    ], 201);
}


    // Calculate total distance from trip logs
    private function calculateDistanceFromLogs($tripId)
    {
        $logs = TripLog::where('trip_id', $tripId)->orderBy('recorded_at')->get();
        if ($logs->count() < 2) return 0;

        $distance = 0;
        for ($i = 1; $i < $logs->count(); $i++) {
            $distance += $this->calculateDistance(
                $logs[$i - 1]->latitude,
                $logs[$i - 1]->longitude,
                $logs[$i]->latitude,
                $logs[$i]->longitude
            );
        }

        return round($distance, 2);
    }

    // Calculate distance between two geo-points
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist  = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
                 cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist  = acos($dist);
        $dist  = rad2deg($dist);
        $km    = $dist * 111.13384;
        return round($km, 2);
    }
}
