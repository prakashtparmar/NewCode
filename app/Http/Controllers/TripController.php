<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Services\TripService;
use App\Http\Requests\Admin\TripRequest;

class TripController extends Controller
{
    protected $tripService;

    public function __construct(TripService $tripService)
    {
        $this->tripService = $tripService;
    }

    public function index()
    {
        Session::put('page', 'trips');
        $trips = Trip::all(); // or: Trip::where('user_id', Auth::id())->get();
        return view('admin.trips.index', compact('trips'));
    }

    public function create()
    {
        return view('admin.trips.add_edit_trip')->with('title', 'Add Trip');
    }

    public function store(TripRequest $request)
    {
        $message = $this->tripService->addEditTrip($request);
        return redirect()->route('trips.index')->with('success_message', $message['message']);
    }

    // public function show(Trip $trip)
    // {
    //     return view('admin.trips.show', compact('trip')); // Ensure this view exists
    // }

    public function destroy(string $id)
    {
        $result = $this->tripService->deleteTrip($id);
        return redirect()->back()->with('success_message', $result['message']);
    }

    // public function show($id)
    // {
    //     $trip = Trip::with('user')->findOrFail($id);
    //     return view('admin.trips.show', compact('trip'));
    // }

    // Show Trip Details In PopUp

//     public function show($id)
// {
//     $trip = Trip::with('approvedByAdmin')->findOrFail($id);

//     return response()->json($trip);
// }

public function show($id)
    {
        $trip = Trip::with(['approvedByAdmin', 'createdByAdmin'])->findOrFail($id);

        return response()->json($trip);
    }



    public function edit($id)
    {
        $trip = Trip::findOrFail($id);
        return view('admin.trips.edit', compact('trip'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'trip_date' => 'required|date',
            'travel_mode' => 'required',
            'purpose' => 'required|string|max:255',
            // Add other validations...
        ]);

        $trip = Trip::findOrFail($id);
        $trip->update($request->all());

        return redirect()->route('trips.index')->with('success_message', 'Trip updated successfully.');
    }

    public function approve($id)
    {
        $trip = Trip::findOrFail($id);
        $trip->approval_status = 'approved';
        // $trip->approved_by = auth()->user()->id;
        $trip->approved_by = Auth::guard('admin')->user()->id;
        $trip->approved_at = now();
        $trip->save();

        return redirect()->back()->with('success_message', 'Trip approved successfully.');
    }

    public function deny(Request $request, TripService $tripService)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'reason' => 'required|string|max:255',
        ]);

        $trip = Trip::findOrFail($validated['trip_id']);

        $tripService->denyTrip($trip, $validated['reason']);

        return response()->json(['status' => 'success', 'message' => 'Trip denied successfully.']);
    }
}
