<?php

namespace App\Http\Controllers;

use App\Models\TravelMode;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TravelModeController extends Controller
{
    public function index()
    {
        Session::put('page', 'travelmode');

        $authUser = auth()->user();
        $travelModes = $authUser->user_level === 'master_admin'
            ? TravelMode::with('company')->latest()->get()
            : TravelMode::with('company')->where('company_id', $authUser->company_id)->latest()->get();

        return view('admin.trips.travelmode.index', compact('travelModes'));
    }

    public function create()
    {
        $authUser = auth()->user();
        $companies = $authUser->user_level === 'master_admin' ? Company::all() : collect();

        return view('admin.trips.travelmode.create', compact('companies', 'authUser'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->only('name');
        $authUser = auth()->user();

        $data['company_id'] = $authUser->user_level === 'master_admin'
            ? $request->company_id
            : $authUser->company_id;

        TravelMode::create($data);

        return redirect()->route('travelmode.index')->with('success', 'Travel Mode created successfully.');
    }

    public function edit(TravelMode $travelMode)
    {
        $authUser = auth()->user();
        if ($authUser->user_level !== 'master_admin' && $travelMode->company_id !== $authUser->company_id) {
            abort(403);
        }

        $companies = $authUser->user_level === 'master_admin' ? Company::all() : collect();

        return view('admin.trips.travelmode.edit', compact('travelMode', 'companies', 'authUser'));
    }

    public function update(Request $request, TravelMode $travelMode)
    {
        $authUser = auth()->user();
        if ($authUser->user_level !== 'master_admin' && $travelMode->company_id !== $authUser->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->only('name');
        $data['company_id'] = $authUser->user_level === 'master_admin'
            ? $request->company_id
            : $authUser->company_id;

        $travelMode->update($data);

        return redirect()->route('travelmode.index')->with('success', 'Travel Mode updated successfully.');
    }

    public function destroy(TravelMode $travelMode)
    {
        $authUser = auth()->user();
        if ($authUser->user_level !== 'master_admin' && $travelMode->company_id !== $authUser->company_id) {
            abort(403);
        }

        $travelMode->delete();

        return redirect()->route('travelmode.index')->with('success', 'Travel Mode deleted.');
    }
}
