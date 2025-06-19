<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies.
     */
    public function index()
    {
        $companies = Company::all();
        return view('admin.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create()
    {
        return view('admin.companies.create');
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:companies,code',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        Company::create($validated);
        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company)
    {
        return view('admin.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified company in storage.
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:companies,code,' . $company->id,
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $company->update($validated);
        return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Company deleted successfully.');
    }

    public function toggle($id)
{
    $company = Company::findOrFail($id);
    $company->is_active = !$company->is_active;
    $company->status = $company->is_active ? 'Active' : 'Inactive';
    $company->save();

    return redirect()->route('companies.index')->with('success', 'Company status updated.');
}
}
