<?php

namespace App\Http\Controllers;

use App\Models\TourType;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TourTypeController extends Controller
{
    public function index()
    {
        Session::put('page', 'travel_modes');

        $authUser = auth()->user();
        $TourTypes = $authUser->user_level === 'master_admin'
            ? TourType::with('company')->latest()->get()
            : TourType::with('company')->where('company_id', $authUser->company_id)->latest()->get();

        return view('admin.travel_modes.index', compact('TourTypes'));
    }

    public function create()
    {
        $authUser = auth()->user();
        $companies = $authUser->user_level === 'master_admin' ? Company::all() : collect();

        return view('admin.travel_modes.create', compact('companies', 'authUser'));
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

        TourType::create($data);

        return redirect()->route('travel-modes.index')->with('success', 'Travel Mode created successfully.');
    }

    public function edit(TourType $TourType)
    {
        $authUser = auth()->user();
        if ($authUser->user_level !== 'master_admin' && $TourType->company_id !== $authUser->company_id) {
            abort(403);
        }

        $companies = $authUser->user_level === 'master_admin' ? Company::all() : collect();

        return view('admin.travel_modes.edit', compact('TourType', 'companies', 'authUser'));
    }

    public function update(Request $request, TourType $TourType)
    {
        $authUser = auth()->user();
        if ($authUser->user_level !== 'master_admin' && $TourType->company_id !== $authUser->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->only('name');
        $data['company_id'] = $authUser->user_level === 'master_admin'
            ? $request->company_id
            : $authUser->company_id;

        $TourType->update($data);

        return redirect()->route('travel-modes.index')->with('success', 'Travel Mode updated successfully.');
    }

    public function destroy(TourType $TourType)
    {
        $authUser = auth()->user();
        if ($authUser->user_level !== 'master_admin' && $TourType->company_id !== $authUser->company_id) {
            abort(403);
        }

        $TourType->delete();

        return redirect()->route('travel-modes.index')->with('success', 'Travel Mode deleted.');
    }
}
