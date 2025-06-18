<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Session::put('page', 'customers');
        $admin = Auth::user();

        $customers = $admin->role === 'executive'
            ? Customer::where('executive_id', $admin->id)->get()
            : Customer::all();

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Session::put('page', 'add-customer');
        $executives = User::where('role', 'executive')->get();

        return view('admin.customers.edit', compact('executives'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:customers',
            'phone' => 'required',
            'address' => 'required',
        ]);

        $admin = Auth::user();
        $validated['executive_id'] = $admin->role === 'executive' ? $admin->id : $request->input('executive_id');

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        $executives = User::where('role', 'executive')->get();

        return view('admin.customers.edit', compact('customer', 'executives'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $customer = Customer::findOrFail($id);

    $validated = $request->validate([
        'name'    => 'required',
        'email'   => 'required|email|unique:customers,email,' . $id,
        'phone'   => 'required',
        'address' => 'required',
    ]);

    $admin = Auth::user();

    // Only assign executive_id if admin is executive or value is sent
    if ($admin->role === 'executive') {
        $validated['executive_id'] = $admin->id;
    } elseif ($request->has('executive_id')) {
        $validated['executive_id'] = $request->input('executive_id');
    }

    $customer->update($validated);

    return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
