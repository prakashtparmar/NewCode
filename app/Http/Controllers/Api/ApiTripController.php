<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApiTripController extends Controller
{
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_date'      => 'required|date',
            'start_time'     => 'required',
            'end_time'       => 'nullable',
            'start_lat'      => 'required|numeric',
            'start_lng'      => 'required|numeric',
            'end_lat'        => 'required|numeric',
            'end_lng'        => 'required|numeric',
            'travel_mode'    => 'required|string',
        ]);

        $distance = $this->calculateDistance(
            $request->start_lat,
            $request->start_lng,
            $request->end_lat,
            $request->end_lng
        );

        $user = Auth::user();

        $trip = Trip::create([
            'user_id'           => $user->id,
            'company_id'        => $user->hasRole('master_admin') ? 1 : $user->company_id,
            'trip_date'         => $request->trip_date,
            'start_time'        => $request->start_time,
            'end_time'          => $request->end_time,
            'start_lat'         => $request->start_lat,
            'start_lng'         => $request->start_lng,
            'end_lat'           => $request->end_lat,
            'end_lng'           => $request->end_lng,
            'total_distance_km' => $distance,
            'travel_mode'       => $request->travel_mode,
            'status'            => 'pending',
            'approval_status'   => 'pending',
        ]);

        return response()->json(['status' => 'success', 'trip' => $trip]);
    }

    public function show(Trip $trip)
    {
        $trip->load('tripLogs');
        return response()->json(['status' => 'success', 'trip' => $trip]);
    }

    public function update(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'trip_date'       => 'required|date',
            'start_time'      => 'required',
            'start_lat'       => 'required|numeric',
            'start_lng'       => 'required|numeric',
            'end_lat'         => 'required|numeric',
            'end_lng'         => 'required|numeric',
            'travel_mode'     => 'required|string',
            'approval_status' => 'required|in:pending,approved,denied',
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

        return response()->json(['status' => 'success', 'trip' => $trip]);
    }

    public function destroy(Trip $trip)
    {
        $trip->delete();
        return response()->json(['status' => 'success']);
    }

    public function logPoint(Request $request)
    {
        $validated = $request->validate([
            'trip_id'     => 'required|exists:trips,id',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
        ]);

        $log = TripLog::create([
            'trip_id'     => $request->trip_id,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'recorded_at' => now(),
        ]);

        return response()->json(['status' => 'success', 'log' => $log]);
    }

    public function logs(Trip $trip)
    {
        return response()->json(['status' => 'success', 'logs' => $trip->tripLogs]);
    }

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

        return response()->json(['status' => 'success', 'trip' => $trip]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $km   = $dist * 111.13384;
        return round($km, 2);
    }

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

    public function getDropdownValues($type)
    {
        $tableMap = [
            'travel_mode' => 'travel_modes',
            'purpose'     => 'purposes',
            'tour_type'   => 'tour_types'
        ];

        if (!array_key_exists($type, $tableMap)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid type'], 400);
        }

        $user = Auth::user();
        $query = DB::table($tableMap[$type])->orderBy('name');

        if (!$user->hasRole('master_admin')) {
            $query->where('company_id', $user->company_id);
        }

        $values = $query->pluck('name');

        return response()->json(['status' => 'success', 'values' => $values]);
    }
}