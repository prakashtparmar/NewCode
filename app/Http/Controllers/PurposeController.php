<?php

namespace App\Http\Controllers;

use App\Models\Purpose;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PurposeController extends Controller
{
    public function index()
    {
        Session::put('page', 'travel_modes');

        $authUser = auth()->user();
        $Purposes = $authUser->user_level === 'master_admin'
            ? Purpose::with('company')->latest()->get()
            : Purpose::with('company')->where('company_id', $authUser->company_id)->latest()->get();

        return view('admin.travel_modes.index', compact('Purposes'));
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

        Purpose::create($data);

        return redirect()->route('travel-modes.index')->with('success', 'Travel Mode created successfully.');
    }

    public function edit(Purpose $Purpose)
    {
        $authUser = auth()->user();
        if ($authUser->user_level !== 'master_admin' && $Purpose->company_id !== $authUser->company_id) {
            abort(403);
        }

        $companies = $authUser->user_level === 'master_admin' ? Company::all() : collect();

        return view('admin.travel_modes.edit', compact('Purpose', 'companies', 'authUser'));
    }

    public function update(Request $request, Purpose $Purpose)
    {
        $authUser = auth()->user();
        if ($authUser->user_level !== 'master_admin' && $Purpose->company_id !== $authUser->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->only('name');
        $data['company_id'] = $authUser->user_level === 'master_admin'
            ? $request->company_id
            : $authUser->company_id;

        $Purpose->update($data);

        return redirect()->route('travel-modes.index')->with('success', 'Travel Mode updated successfully.');
    }

    public function destroy(Purpose $Purpose)
    {
        $authUser = auth()->user();
        if ($authUser->user_level !== 'master_admin' && $Purpose->company_id !== $authUser->company_id) {
            abort(403);
        }

        $Purpose->delete();

        return redirect()->route('travel-modes.index')->with('success', 'Travel Mode deleted.');
    }
}
