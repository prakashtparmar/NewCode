<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripLog; // ✅ Include TripLog
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    public function index()
    {
        $trips = Trip::with('user')->latest()->get();
        return view('admin.trips.index', compact('trips'));
    }

    public function create()
    {
        return view('admin.trips.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_date'      => 'required|date',
            'start_time'     => 'required',
            'end_time'       => 'required',
            'start_lat'      => 'required|numeric',
            'start_lng'      => 'required|numeric',
            'end_lat'        => 'required|numeric',
            'end_lng'        => 'required|numeric',
            'travel_mode'    => 'required|string',
            'purpose'        => 'nullable|string',
        ]);

        $distance = $this->calculateDistance(
            $request->start_lat,
            $request->start_lng,
            $request->end_lat,
            $request->end_lng
        );

        Trip::create([
            'user_id'           => Auth::id(),
            'company_id'        => Auth::user()->company_id,
            'trip_date'         => $request->trip_date,
            'start_time'        => $request->start_time,
            'end_time'          => $request->end_time,
            'start_lat'         => $request->start_lat,
            'start_lng'         => $request->start_lng,
            'end_lat'           => $request->end_lat,
            'end_lng'           => $request->end_lng,
            'total_distance_km' => $distance,
            'travel_mode'       => $request->travel_mode,
            'purpose'           => $request->purpose,
            'status'            => 'pending',
            'approval_status'   => 'pending',
        ]);

        return redirect()->route('trips.index')->with('success', 'Trip added successfully.');
    }

    public function show(Trip $trip)
    {
        return view('admin.trips.show', compact('trip'));
    }

    public function edit(Trip $trip)
    {
        return view('admin.trips.edit', compact('trip'));
    }

    public function update(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'trip_date'      => 'required|date',
            'start_time'     => 'required',
            'end_time'       => 'required',
            'start_lat'      => 'required|numeric',
            'start_lng'      => 'required|numeric',
            'end_lat'        => 'required|numeric',
            'end_lng'        => 'required|numeric',
            'travel_mode'    => 'required|string',
            'purpose'        => 'nullable|string',
        ]);

        $trip->update([
            'trip_date'         => $request->trip_date,
            'start_time'        => $request->start_time,
            'end_time'          => $request->end_time,
            'start_lat'         => $request->start_lat,
            'start_lng'         => $request->start_lng,
            'end_lat'           => $request->end_lat,
            'end_lng'           => $request->end_lng,
            'travel_mode'       => $request->travel_mode,
            'purpose'           => $request->purpose,
            'total_distance_km' => $this->calculateDistance(
                $request->start_lat,
                $request->start_lng,
                $request->end_lat,
                $request->end_lng
            ),
        ]);

        return redirect()->route('trips.index')->with('success', 'Trip updated successfully.');
    }

    public function destroy(Trip $trip)
    {
        $trip->delete();
        return redirect()->route('trips.index')->with('success', 'Trip deleted successfully.');
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'status'          => 'required|in:approved,denied',
            'approval_reason' => 'nullable|string|max:255',
        ]);

        $trip = Trip::findOrFail($id);

        $trip->update([
            'approval_status' => $request->status,
            'approval_reason' => $request->approval_reason,
            'approved_by'     => Auth::id(),
            'approved_at'     => now(),
        ]);

        return redirect()->back()->with('success', 'Trip approval updated.');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $km = $dist * 60 * 1.1515 * 1.609344;

        return round($km, 2);
    }

    // ✅ ADDITION 1: Store GPS points
    public function logPoint(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'recorded_at' => 'nullable|date',
        ]);

        $log = TripLog::create([
            'trip_id' => $request->trip_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'recorded_at' => $request->recorded_at ?? now(),
        ]);

        return response()->json(['status' => 'success', 'log' => $log]);
    }

    // ✅ ADDITION 2: Show route on map
    public function showRoute($tripId)
{
    $trip = Trip::findOrFail($tripId);

    $logs = TripLog::where('trip_id', $tripId)
        ->orderBy('recorded_at')
        ->get(['latitude', 'longitude']);

    return view('admin.trips.map', [
        'trip' => $trip,
        'logs' => $logs,
    ]);
}
}
