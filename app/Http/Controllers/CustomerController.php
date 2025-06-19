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

        if ($admin->hasRole('master_admin')) {
            $customers = Customer::all();
        } elseif ($admin->hasRole('executive')) {
            $customers = Customer::where('executive_id', $admin->id)
                ->where('company_id', $admin->company_id)
                ->get();
        } else {
            $customers = Customer::where('company_id', $admin->company_id)->get();
        }

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Session::put('page', 'add-customer');
        $admin = Auth::user();

        $executives = User::where('role', 'executive')
            ->where('company_id', $admin->company_id)
            ->get();

        return view('admin.customers.edit', compact('executives'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required',
            'email'      => 'required|email|unique:customers',
            'phone'      => 'required',
            'address'    => 'required',
            'is_active'  => 'nullable|boolean',
        ]);

        $admin = Auth::user();
        $validated['company_id'] = $admin->company_id;
        $validated['executive_id'] = $admin->hasRole('executive')
            ? $admin->id
            : $request->input('executive_id');

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        $this->authorizeCustomerAccess($customer);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        $this->authorizeCustomerAccess($customer);

        $executives = User::where('role', 'executive')
            ->where('company_id', Auth::user()->company_id)
            ->get();

        return view('admin.customers.edit', compact('customer', 'executives'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);
        $this->authorizeCustomerAccess($customer);

        $validated = $request->validate([
            'name'    => 'required',
            'email'   => 'required|email|unique:customers,email,' . $id,
            'phone'   => 'required',
            'address' => 'required',
        ]);

        $admin = Auth::user();

        if ($admin->hasRole('executive')) {
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
        $this->authorizeCustomerAccess($customer);

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    /**
     * Restrict access to customers outside user's company (except for master_admin).
     */
    private function authorizeCustomerAccess(Customer $customer)
    {
        $admin = Auth::user();

        if ($admin->hasRole('master_admin')) {
            return;
        }

        if ($customer->company_id !== $admin->company_id) {
            abort(403, 'Unauthorized access to this customer.');
        }
    }
}
