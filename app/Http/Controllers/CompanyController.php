<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies.
     */
    public function index()
    {
        $user = Auth::user();

        // master_admin sees all companies, others only their own
        $companies = $user->hasRole('master_admin')
            ? Company::all()
            : Company::where('id', $user->company_id)->get();

        return view('admin.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create()
    {
        // $this->authorizeMaster(); // Only allow master_admin to create

        return view('admin.companies.create');
    }

    /**
     * Store a newly created company in storage.
     */
    // public function store(Request $request)
    // {
    //     $this->authorizeMaster(); // Only master_admin can store companies

    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'code' => 'nullable|string|unique:companies,code',
    //         'email' => 'nullable|email',
    //         'address' => 'nullable|string',
    //     ]);

    //     Company::create($validated);

    //     return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    // }

    public function store(Request $request)
    {
        //$this->authorizeMaster(); // Only master_admin can store companies

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:companies,code',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'owner_name' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:50',
            'contact_no' => 'nullable|string|max:20',
            'contact_no2' => 'nullable|string|max:20',
            'telephone_no' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'state' => 'nullable|string|max:100',
            'product_name' => 'nullable|string|max:255',
            'subscription_type' => 'nullable|string|max:100',
            'tally_configuration' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:png|max:2048', // only PNG
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        Company::create($validated);

        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }


    /**
     * Display the specified company.
     */
    public function show(Company $company)
    {
        $this->authorizeCompanyAccess($company);

        return view('admin.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company)
    {
        $this->authorizeCompanyAccess($company);

        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified company in storage.
     */
    public function update(Request $request, Company $company)
    {
        $this->authorizeCompanyAccess($company);

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
        $this->authorizeMaster(); // Only master_admin can delete companies

        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Company deleted successfully.');
    }

    /**
     * Toggle active/inactive status of a company.
     */
    public function toggle($id)
    {
        $company = Company::findOrFail($id);
        $this->authorizeCompanyAccess($company);

        $company->is_active = !$company->is_active;
        $company->status = $company->is_active ? 'Active' : 'Inactive';
        $company->save();

        return redirect()->route('companies.index')->with('success', 'Company status updated.');
    }

    /**
     * Authorize only master_admin for certain actions.
     */
    private function authorizeMaster()
    {
        $user = Auth::user();
        if (!$user->hasRole('master_admin')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Authorize access to specific company.
     */
    private function authorizeCompanyAccess(Company $company)
    {
        $user = Auth::user();

        if ($user->hasRole('master_admin')) {
            return; // master_admin has access to all companies
        }

        if ($company->id !== $user->company_id) {
            abort(403, 'Unauthorized access to this company.');
        }
    }
}
