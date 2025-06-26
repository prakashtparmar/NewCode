<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApiTripController extends Controller
{
    /**
     * Get trips for authenticated user based on their role.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Trip::with(['user', 'company', 'approvedByUser', 'tripLogs']);

        if ($user->hasRole('master_admin')) {
            $trips = $query->latest()->get();
        } elseif ($user->hasRole('sub_admin')) {
            $trips = $query->where('company_id', $user->company_id)->latest()->get();
        } else {
            $trips = $query->where('user_id', $user->id)->latest()->get();
        }

        return response()->json(['status' => 'success', 'trips' => $trips]);
    }

    /**
     * Store a new trip.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_date'    => 'required|date',
            'start_time'   => 'required',
            'end_time'     => 'required',
            'start_lat'    => 'required|numeric',
            'start_lng'    => 'required|numeric',
            'end_lat'      => 'required|numeric',
            'end_lng'      => 'required|numeric',
            'travel_mode'  => 'required|string',
            'purpose'      => 'nullable|string',
        ]);

        $distance = $this->calculateDistance(
            $request->start_lat,
            $request->start_lng,
            $request->end_lat,
            $request->end_lng
        );

        $trip = Trip::create([
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

        return response()->json(['status' => 'success', 'trip' => $trip]);
    }

    /**
     * Show a specific trip with logs.
     */
    public function show($id)
    {
        $trip = Trip::with('tripLogs')->findOrFail($id);
        return response()->json(['status' => 'success', 'trip' => $trip]);
    }

    /**
     * Update a trip.
     */
    public function update(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);

        $validated = $request->validate([
            'trip_date'       => 'required|date',
            'start_time'      => 'required',
            'end_time'        => 'required',
            'start_lat'       => 'required|numeric',
            'start_lng'       => 'required|numeric',
            'end_lat'         => 'required|numeric',
            'end_lng'         => 'required|numeric',
            'travel_mode'     => 'required|string',
            'purpose'         => 'nullable|string',
            'approval_status' => 'required|in:pending,approved,denied',
            'approval_reason' => 'nullable|string|max:255',
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
            'approval_status'   => $request->approval_status,
            'approval_reason'   => $request->approval_status === 'denied' ? $request->approval_reason : null,
            'approved_by'       => in_array($request->approval_status, ['approved', 'denied']) ? auth()->id() : null,
            'approved_at'       => in_array($request->approval_status, ['approved', 'denied']) ? now() : null,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Trip updated']);
    }

    /**
     * Delete a trip.
     */
    public function destroy($id)
    {
        $trip = Trip::findOrFail($id);
        $trip->delete();

        return response()->json(['status' => 'success', 'message' => 'Trip deleted']);
    }

    /**
     * Approve or deny a trip.
     */
    public function approve(Request $request, $id)
    {
        $trip = Trip::findOrFail($id);
        $status = $request->input('status', 'approved');
        $reason = $request->input('reason');

        if ($status === 'denied') {
            $request->validate(['reason' => 'required|string|max:255']);
        }

        $calculatedDistance = $this->calculateDistanceFromLogs($trip->id);

        $trip->update([
            'approval_status'   => $status,
            'approval_reason'   => $status === 'denied' ? $reason : null,
            'approved_by'       => Auth::id(),
            'approved_at'       => now(),
            'total_distance_km' => $calculatedDistance,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Trip approval updated']);
    }

    /**
     * Log a GPS point for a trip.
     */
    public function logPoint(Request $request)
    {
        $request->validate([
            'trip_id'     => 'required|exists:trips,id',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
            'recorded_at' => 'nullable|date',
        ]);

        $log = TripLog::create([
            'trip_id'     => $request->trip_id,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'recorded_at' => $request->recorded_at ?? now(),
        ]);

        return response()->json(['status' => 'success', 'log' => $log]);
    }

    /**
     * Return logs for a trip.
     */
    public function logs($id)
    {
        $trip = Trip::findOrFail($id);
        $logs = $trip->tripLogs()->select('latitude', 'longitude', 'recorded_at')->get();

        return response()->json(['status' => 'success', 'logs' => $logs]);
    }

    /**
     * Update trip start/end coordinates using logs.
     */
    public function updateTripCoordinates($tripId)
    {
        $startLog = DB::table('trip_logs')
            ->where('trip_id', $tripId)
            ->orderBy('recorded_at', 'asc')
            ->first();

        $endLog = DB::table('trip_logs')
            ->where('trip_id', $tripId)
            ->orderBy('recorded_at', 'desc')
            ->first();

        if ($startLog && $endLog) {
            DB::table('trips')->where('id', $tripId)->update([
                'start_lat' => $startLog->latitude,
                'start_lng' => $startLog->longitude,
                'end_lat'   => $endLog->latitude,
                'end_lng'   => $endLog->longitude,
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Trip coordinates updated']);
    }

    /**
     * Calculate direct distance using coordinates.
     */
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

    /**
     * Calculate total distance from logs.
     */
    private function calculateDistanceFromLogs($tripId)
    {
        $logs = TripLog::where('trip_id', $tripId)->orderBy('recorded_at')->get();

        if ($logs->count() < 2) return 0;

        $distance = 0;
        for ($i = 1; $i < $logs->count(); $i++) {
            $distance += $this->calculateDistance(
                $logs[$i - 1]->latitude, $logs[$i - 1]->longitude,
                $logs[$i]->latitude, $logs[$i]->longitude
            );
        }

        return round($distance, 2);
    }
}
