<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Designation;
use App\Models\Permission;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeCreatedException;

use App\Models\Tenant;

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
        $authUser = auth()->user();

        $roles = $authUser->user_level === 'master_admin'
            ? Role::all()
            : Role::where('company_id', $authUser->company_id)->get();

        $companies = $authUser->user_level === 'master_admin'
            ? Company::all()
            : collect(); 

        $users = User::when($authUser->user_level !== 'master_admin', function ($query) use ($authUser) {
                $query->where('company_id', $authUser->company_id);
            })->get();

        $designations = $authUser->user_level === 'master_admin'
        ? Designation::all()
        : Designation::where('company_id', $authUser->company_id)->get();
        return view('admin.companies.create',[
            'states' => State::all(),
            'designations' => $designations,
            'users' => $users, // ✅ added here
            'roles' => $roles,
            'permissions' => Permission::all(),
            'authUser' => $authUser,
            'companies' => $companies,
        ]);
    }
    


    

// public function store(Request $request)
// {
//     // 1️⃣ Validate Company + Admin fields
//     $validated = $request->validate([
//         // Company Fields
//         'name' => 'required|string|max:255',
//         'code' => 'nullable|string|unique:companies,code',
//         'email' => 'nullable|email',
//         'address' => 'nullable|string',
//         'owner_name' => 'nullable|string|max:255',
//         'gst_number' => 'nullable|string|max:50',
//         'contact_no' => 'nullable|string|max:20',
//         'contact_no2' => 'nullable|string|max:20',
//         'telephone_no' => 'nullable|string|max:20',
//         'website' => 'nullable|url',
//         'state' => 'nullable|string|max:100',
//         'product_name' => 'nullable|string|max:255',
//         'subscription_type' => 'nullable|string|max:100',
//         'tally_configuration' => 'nullable|boolean',
//         'logo' => 'nullable|image|mimes:png|max:2048',
//         'subdomain' => 'nullable|string|alpha_dash|unique:companies,subdomain',

//         // Admin User Fields
//         'user_name' => 'required|string|max:255',
//         'user_email' => 'required|email|unique:users,email',
//         'user_password' => 'required|string|min:6|confirmed',
//         'user_mobile' => 'nullable|string|max:20',
//         'user_dob' => 'nullable|date',
//         'user_gender' => 'nullable|string',
//         'user_marital_status' => 'nullable|string',
//         'user_address' => 'nullable|string',
//         'state_id' => 'nullable|exists:states,id',
//         'district_id' => 'nullable|exists:districts,id',
//         'city_id' => 'nullable|exists:cities,id',
//         'tehsil_id' => 'nullable|exists:tehsils,id',
//         'pincode_id' => 'nullable|exists:pincodes,id',
//         'postal_address' => 'nullable|string',
//         'latitude' => 'nullable|string',
//         'longitude' => 'nullable|string',
//         'user_type' => 'nullable|string',
//         'user_code' => 'nullable|string|unique:users,user_code',
//         'designation_id' => 'nullable|exists:designations,id',
//         'reporting_to' => 'nullable|exists:users,id',
//         'headquarter' => 'nullable|string',
//         'is_self_sale' => 'nullable|boolean',
//         'is_multi_day_start_end_allowed' => 'nullable|boolean',
//         'is_allow_tracking' => 'nullable|boolean',
//     ]);

//     // 2️⃣ Handle Company Logo
//     if ($request->hasFile('logo')) {
//         $validated['logo'] = $request->file('logo')->store('logos', 'public');
//     }

//     // 3️⃣ Subdomain + Tenant DB name
//     $subdomain = $validated['subdomain'] ?? Str::slug($validated['name'], '-');
//     $centralDomain = env('CENTRAL_DOMAIN', 'example.test');
//     $fullDomain = $subdomain . '.' . $centralDomain;
//     $tenancyDbName = 'tenant_' . Str::slug($subdomain, '_');

//     $company = null;
//     $tenant = null;

//     try {
//         // 4️⃣ Create the tenant database (outside transaction, as DDL cannot be rolled back)
//         DB::statement("CREATE DATABASE IF NOT EXISTS `$tenancyDbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

//         // 5️⃣ Start transaction for central DB operations
//         DB::beginTransaction();
//         try {
//             // 6️⃣ Create Company (central DB)
//             $company = Company::create([
//                 'name' => $validated['name'],
//                 'code' => $validated['code'] ?? null,
//                 'owner_name' => $validated['owner_name'] ?? null,
//                 'email' => $validated['email'] ?? null,
//                 'gst_number' => $validated['gst_number'] ?? null,
//                 'address' => $validated['address'] ?? null,
//                 'contact_no' => $validated['contact_no'] ?? null,
//                 'contact_no2' => $validated['contact_no2'] ?? null,
//                 'telephone_no' => $validated['telephone_no'] ?? null,
//                 'website' => $validated['website'] ?? null,
//                 'state' => $validated['state'] ?? null,
//                 'product_name' => $validated['product_name'] ?? null,
//                 'subscription_type' => $validated['subscription_type'] ?? null,
//                 'tally_configuration' => $validated['tally_configuration'] ?? 0,
//                 'logo' => $validated['logo'] ?? null,
//                 'subdomain' => $subdomain,
//             ]);

//             // 7️⃣ Create Tenant (tenant DB + domain)
//             $tenant = Tenant::create([
//                 'id' => (string) Str::uuid(),
//                 'data' => [
//                     'company_id' => $company->id,
//                     'database' => $tenancyDbName,
//                 ],
//             ]);

//             $tenant->domains()->create(['domain' => $fullDomain]);
//             $company->update(['tenant_id' => $tenant->id]);

//             // Commit central DB operations
//             DB::commit();
//         } catch (\Exception $e) {
//             // Safe rollback for central DB
//             try {
//                 DB::rollBack();
//             } catch (\Exception $rollbackEx) {
//                 Log::warning('Central DB rollback failed: ' . $rollbackEx->getMessage());
//             }
//             throw $e; // Rethrow to handle cleanup
//         }

//         // 8️⃣ Configure tenant connection
//         $tenantConnection = config('database.connections.tenant');
//         $tenantConnection['database'] = $tenancyDbName;
//         config(['database.connections.tenant' => $tenantConnection]);
//         DB::purge('tenant');
//         DB::reconnect('tenant');

//         // 9️⃣ Run tenant migrations (outside transaction, as DDL)
//         $exitCode = Artisan::call('migrate', [
//             '--database' => 'tenant',
//             '--path' => 'database/migrations/tenant',
//             '--force' => true,
//         ]);

//         if ($exitCode !== 0) {
//             throw new \Exception('Tenant migrations failed: ' . Artisan::output());
//         }

//         // 10️⃣ Insert company record in tenant DB (no transaction needed, cleanup will drop DB on error)
//         $tenantCompanyData = [
//             'name' => $company->name,
//             'code' => $company->code,
//             'owner_name' => $company->owner_name,
//             'email' => $company->email,
//             'gst_number' => $company->gst_number,
//             'address' => $company->address,
//             'contact_no' => $company->contact_no,
//             'contact_no2' => $company->contact_no2,
//             'telephone_no' => $company->telephone_no,
//             'website' => $company->website,
//             'state' => $company->state,
//             'product_name' => $company->product_name,
//             'subscription_type' => $company->subscription_type,
//             'tally_configuration' => $company->tally_configuration,
//             'logo' => $company->logo,
//             'subdomain' => $company->subdomain,
//             'created_at' => now(),
//             'updated_at' => now(),
//         ];
//         $existingTenantCompany = DB::connection('tenant')->table('companies')
//             ->where('code', $company->code)
//             ->first();
//         if (!$existingTenantCompany) {
//             DB::connection('tenant')->table('companies')->insert($tenantCompanyData);
//         }

//         // 11️⃣ Run seeders in correct order
//         $seedersPath = database_path('seeders');
//         $seederFiles = collect(\File::files($seedersPath))
//             ->filter(fn($file) => str_ends_with($file->getFilename(), 'Seeder.php'))
//             ->map(fn($file) => 'Database\\Seeders\\' . str_replace('.php', '', $file->getFilename()))
//             ->values();

//         $seederOrder = [
//             'StatesSeeder',
//             'DistrictsSeeder',
//             'CitiesSeeder',
//             'TehsilsSeeder',
//             'PincodesSeeder',
//         ];
//         $orderedSeederFiles = collect($seederOrder)
//             ->map(fn($class) => 'Database\\Seeders\\' . $class)
//             ->filter(fn($class) => $seederFiles->contains($class))
//             ->values();
//         $remainingSeeders = $seederFiles->diff($orderedSeederFiles)->values();
//         $finalSeederList = $orderedSeederFiles->merge($remainingSeeders);

//         DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=0;');
//         try {
//             foreach ($finalSeederList as $seederClass) {
//                 if ($seederClass === 'Database\\Seeders\\DatabaseSeeder') {
//                     continue; // Skip to avoid potential subdomain issues
//                 }
//                 Artisan::call('db:seed', [
//                     '--class' => $seederClass,
//                     '--database' => 'tenant',
//                     '--force' => true,
//                 ]);
//             }
//         } catch (\Exception $seederEx) {
//             Log::error("Seeder failed: {$seederClass} - " . $seederEx->getMessage());
//             throw new \Exception("Seeder failed: {$seederClass} - " . $seederEx->getMessage());
//         } finally {
//             DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=1;');
//         }

//         // 12️⃣ Insert admin user into tenant database
//         $userData = [
//             'name' => $validated['user_name'],
//             'email' => $validated['user_email'],
//             'password' => Hash::make($validated['user_password']),
//             'mobile' => $validated['user_mobile'] ?? null,
//             'date_of_birth' => $validated['user_dob'] ?? null,
//             'gender' => $validated['user_gender'] ?? null,
//             'marital_status' => $validated['user_marital_status'] ?? null,
//             'address' => $validated['user_address'] ?? null,
//             'state_id' => !empty($validated['state_id']) ? $validated['state_id'] : null,
//             'district_id' => !empty($validated['district_id']) ? $validated['district_id'] : null,
//             'city_id' => !empty($validated['city_id']) ? $validated['city_id'] : null,
//             'tehsil_id' => !empty($validated['tehsil_id']) ? $validated['tehsil_id'] : null,
//             'pincode_id' => !empty($validated['pincode_id']) ? $validated['pincode_id'] : null,
//             'postal_address' => $validated['postal_address'] ?? null,
//             'latitude' => $validated['latitude'] ?? null,
//             'longitude' => $validated['longitude'] ?? null,
//             'user_type' => $validated['user_type'] ?? null,
//             'user_code' => $validated['user_code'] ?? null,
//             'designation_id' => !empty($validated['designation_id']) ? $validated['designation_id'] : null,
//             'reporting_to' => !empty($validated['reporting_to']) ? $validated['reporting_to'] : null,
//             'headquarter' => $validated['headquarter'] ?? null,
//             'is_self_sale' => $validated['is_self_sale'] ?? 0,
//             'is_multi_day_start_end_allowed' => $validated['is_multi_day_start_end_allowed'] ?? 0,
//             'is_allow_tracking' => $validated['is_allow_tracking'] ?? 1,
//             'company_id' => 1, // Fixed: Use dynamic company ID
//             'user_level' => 'company_admin',
//             'is_active' => true,
//             'created_at' => now(),
//             'updated_at' => now(),
//         ];

//         foreach (['state_id', 'district_id', 'city_id', 'tehsil_id', 'pincode_id', 'designation_id', 'reporting_to'] as $idField) {
//             if (empty($userData[$idField]) || $userData[$idField] === '?' || $userData[$idField] === 0 || $userData[$idField] === '0') {
//                 $userData[$idField] = null;
//             }
//         }

//         DB::connection('tenant')->table('users')->insert($userData);

//         return redirect()->route('companies.index')
//             ->with('success', "Company & Admin created successfully. Domain: {$fullDomain}");

//     } catch (\Exception $e) {
//         Log::error('Company/Tenant creation failed: ' . $e->getMessage());

//         // Clean up
//         if ($tenant) {
//             try { $tenant->delete(); } catch (\Throwable $ex) {
//                 Log::warning('Failed to delete tenant: ' . $ex->getMessage());
//             }
//         }
//         if ($company) {
//             try { $company->delete(); } catch (\Throwable $ex) {
//                 Log::warning('Failed to delete company: ' . $ex->getMessage());
//             }
//         }
//         try { DB::statement("DROP DATABASE IF EXISTS `$tenancyDbName`"); } catch (\Throwable $ex) {
//             Log::warning('Failed to drop database: ' . $ex->getMessage());
//         }

//         // Provide detailed error message
//         $errorMsg = $e->getMessage();
//         if (str_contains($errorMsg, 'Integrity constraint violation')) {
//             $errorMsg = "A database seeder failed due to missing parent data or foreign key mismatch. Ensure parent tables (e.g., states, districts) are seeded before child tables (e.g., cities). Error: " . $errorMsg;
//         } elseif (str_contains($errorMsg, "Field 'subdomain' doesn't have a default value")) {
//             $errorMsg = "The companies table in the tenant database requires a 'subdomain' value. Ensure the DatabaseSeeder or other seeders include the subdomain field when inserting into the companies table. Error: " . $errorMsg;
//         } elseif (str_contains($errorMsg, 'no active transaction')) {
//             $errorMsg = "A transaction error occurred, likely due to a DDL operation (e.g., database creation or migration). Error: " . $errorMsg;
//         }

//         return back()->withInput()
//             ->withErrors(['error' => 'Onboarding failed: ' . $errorMsg]);
//     }
// }

    
public function store(Request $request)
{
    // 1️⃣ Validate Company + Admin fields
    $validated = $request->validate([
        // Company Fields
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
        'logo' => 'nullable|image|mimes:png|max:2048',
        'subdomain' => 'nullable|string|alpha_dash|unique:companies,subdomain',

        // Admin User Fields
        'user_name' => 'required|string|max:255',
        'user_email' => 'required|email|unique:users,email',
        'user_password' => 'required|string|min:6|confirmed',
        'user_mobile' => 'nullable|string|max:20',
        'user_dob' => 'nullable|date',
        'user_gender' => 'nullable|string',
        'user_marital_status' => 'nullable|string',
        'user_address' => 'nullable|string',
        'state_id' => 'nullable|exists:states,id',
        'district_id' => 'nullable|exists:districts,id',
        'city_id' => 'nullable|exists:cities,id',
        'tehsil_id' => 'nullable|exists:tehsils,id',
        'pincode_id' => 'nullable|exists:pincodes,id',
        'postal_address' => 'nullable|string',
        'latitude' => 'nullable|string',
        'longitude' => 'nullable|string',
        'user_type' => 'nullable|string',
        'user_code' => 'nullable|string|unique:users,user_code',
        'designation_id' => 'nullable|exists:designations,id',
        'reporting_to' => 'nullable|exists:users,id',
        'headquarter' => 'nullable|string',
        'is_self_sale' => 'nullable|boolean',
        'is_multi_day_start_end_allowed' => 'nullable|boolean',
        'is_allow_tracking' => 'nullable|boolean',
    ]);

    // 2️⃣ Handle Company Logo
    if ($request->hasFile('logo')) {
        $validated['logo'] = $request->file('logo')->store('logos', 'public');
    }

    // 3️⃣ Subdomain + Tenant DB name
    $subdomain = $validated['subdomain'] ?? Str::slug($validated['name'], '-');
    $centralDomain = env('CENTRAL_DOMAIN', 'test'); // Changed to 'test' as per your setup
    $fullDomain = $subdomain . '.' . $centralDomain;
    $tenancyDbName = 'tenant_' . Str::slug($subdomain, '_');

    $company = null;
    $tenant = null;

    try {
        // 4️⃣ Create the tenant database (outside transaction, as DDL cannot be rolled back)
        DB::statement("CREATE DATABASE IF NOT EXISTS `$tenancyDbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // 5️⃣ Start transaction for central DB operations
        DB::beginTransaction();
        try {
            // 6️⃣ Create Company (central DB)
            $company = Company::create([
                'name' => $validated['name'],
                'code' => $validated['code'] ?? null,
                'owner_name' => $validated['owner_name'] ?? null,
                'email' => $validated['email'] ?? null,
                'gst_number' => $validated['gst_number'] ?? null,
                'address' => $validated['address'] ?? null,
                'contact_no' => $validated['contact_no'] ?? null,
                'contact_no2' => $validated['contact_no2'] ?? null,
                'telephone_no' => $validated['telephone_no'] ?? null,
                'website' => $validated['website'] ?? null,
                'state' => $validated['state'] ?? null,
                'product_name' => $validated['product_name'] ?? null,
                'subscription_type' => $validated['subscription_type'] ?? null,
                'tally_configuration' => $validated['tally_configuration'] ?? 0,
                'logo' => $validated['logo'] ?? null,
                'subdomain' => $subdomain,
            ]);

            // 7️⃣ Create Tenant (tenant DB + domain)
            $tenant = Tenant::create([
                'id' => (string) Str::uuid(),
                'data' => [
                    'company_id' => $company->id,
                    'database' => $tenancyDbName,
                ],
                'tenancy_db_name' => $tenancyDbName,
            ]);

            $tenant->domains()->create(['domain' => $fullDomain]);
            $company->update(['tenant_id' => $tenant->id]);

            // Commit central DB operations
            DB::commit();
        } catch (\Exception $e) {
            // Safe rollback for central DB
            try {
                DB::rollBack();
            } catch (\Exception $rollbackEx) {
                Log::warning('Central DB rollback failed: ' . $rollbackEx->getMessage());
            }
            throw $e; // Rethrow to handle cleanup
        }

        // 8️⃣ Initialize tenancy before migrations
        tenancy()->initialize($tenant);
        $tenantConnection = config('database.connections.tenant');
        $tenantConnection['database'] = $tenancyDbName;
        config(['database.connections.tenant' => $tenantConnection]);
        DB::purge('tenant');
        DB::reconnect('tenant');

        // 9️⃣ Run tenant migrations (outside transaction, as DDL)
        $exitCode = Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);

        if ($exitCode !== 0) {
            throw new \Exception('Tenant migrations failed: ' . Artisan::output());
        }

        // 10️⃣ Insert tenant record in tenant DB
        $tenantData = [
            'id' => $tenant->id,
            'data' => $tenant->data,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $existingTenant = DB::connection('tenant')->table('tenants')
            ->where('id', $tenant->id)
            ->first();
        if (!$existingTenant) {
            DB::connection('tenant')->table('tenants')->insert($tenantData);
        }

        // 11️⃣ Insert company record in tenant DB
        $tenantCompanyData = [
            'name' => $company->name,
            'code' => $company->code,
            'owner_name' => $company->owner_name,
            'email' => $company->email,
            'gst_number' => $company->gst_number,
            'address' => $company->address,
            'contact_no' => $company->contact_no,
            'contact_no2' => $company->contact_no2,
            'telephone_no' => $company->telephone_no,
            'website' => $company->website,
            'state' => $company->state,
            'product_name' => $company->product_name,
            'subscription_type' => $company->subscription_type,
            'tally_configuration' => $company->tally_configuration,
            'logo' => $company->logo,
            'subdomain' => $company->subdomain,
            'tenant_id' => $tenant->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $existingTenantCompany = DB::connection('tenant')->table('companies')
            ->where('code', $company->code)
            ->first();
        if (!$existingTenantCompany) {
            DB::connection('tenant')->table('companies')->insert($tenantCompanyData);
        }

        // 12️⃣ Run seeders in correct order
        $seedersPath = database_path('seeders');
        $seederFiles = collect(\File::files($seedersPath))
            ->filter(fn($file) => str_ends_with($file->getFilename(), 'Seeder.php'))
            ->map(fn($file) => 'Database\\Seeders\\' . str_replace('.php', '', $file->getFilename()))
            ->values();

        $seederOrder = [
            'StatesSeeder',
            'DistrictsSeeder',
            'CitiesSeeder',
            'TehsilsSeeder',
            'PincodesSeeder',
        ];
        $orderedSeederFiles = collect($seederOrder)
            ->map(fn($class) => 'Database\\Seeders\\' . $class)
            ->filter(fn($class) => $seederFiles->contains($class))
            ->values();
        $remainingSeeders = $seederFiles->diff($orderedSeederFiles)->values();
        $finalSeederList = $orderedSeederFiles->merge($remainingSeeders);

        DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=0;');
        try {
            foreach ($finalSeederList as $seederClass) {
                if ($seederClass === 'Database\\Seeders\\DatabaseSeeder') {
                    continue; // Skip to avoid potential subdomain issues
                }
                Artisan::call('db:seed', [
                    '--class' => $seederClass,
                    '--database' => 'tenant',
                    '--force' => true,
                ]);
            }
        } catch (\Exception $seederEx) {
            Log::error("Seeder failed: {$seederClass} - " . $seederEx->getMessage());
            throw new \Exception("Seeder failed: {$seederClass} - " . $seederEx->getMessage());
        } finally {
            DB::connection('tenant')->statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // 13️⃣ Insert admin user into tenant database
        $userData = [
            'name' => $validated['user_name'],
            'email' => $validated['user_email'],
            'password' => Hash::make($validated['user_password']),
            'mobile' => $validated['user_mobile'] ?? null,
            'date_of_birth' => $validated['user_dob'] ?? null,
            'gender' => $validated['user_gender'] ?? null,
            'marital_status' => $validated['user_marital_status'] ?? null,
            'address' => $validated['user_address'] ?? null,
            'state_id' => !empty($validated['state_id']) ? $validated['state_id'] : null,
            'district_id' => !empty($validated['district_id']) ? $validated['district_id'] : null,
            'city_id' => !empty($validated['city_id']) ? $validated['city_id'] : null,
            'tehsil_id' => !empty($validated['tehsil_id']) ? $validated['tehsil_id'] : null,
            'pincode_id' => !empty($validated['pincode_id']) ? $validated['pincode_id'] : null,
            'postal_address' => $validated['postal_address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'user_type' => $validated['user_type'] ?? null,
            'user_code' => $validated['user_code'] ?? null,
            'designation_id' => !empty($validated['designation_id']) ? $validated['designation_id'] : null,
            'reporting_to' => !empty($validated['reporting_to']) ? $validated['reporting_to'] : null,
            'headquarter' => $validated['headquarter'] ?? null,
            'is_self_sale' => $validated['is_self_sale'] ?? 0,
            'is_multi_day_start_end_allowed' => $validated['is_multi_day_start_end_allowed'] ?? 0,
            'is_allow_tracking' => $validated['is_allow_tracking'] ?? 1,
            'company_id' => 1,
            'user_level' => 'company_admin',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        foreach (['state_id', 'district_id', 'city_id', 'tehsil_id', 'pincode_id', 'designation_id', 'reporting_to'] as $idField) {
            if (empty($userData[$idField]) || $userData[$idField] === '?' || $userData[$idField] === 0 || $userData[$idField] === '0') {
                $userData[$idField] = null;
            }
        }

        DB::connection('tenant')->table('users')->insert($userData);

        return redirect()->route('companies.index')
            ->with('success', "Company & Admin created successfully. Domain: {$fullDomain}");

    } catch (\Exception $e) {
        Log::error('Company/Tenant creation failed: ' . $e->getMessage());

        // Clean up
        if ($tenant) {
            try { $tenant->delete(); } catch (\Throwable $ex) {
                Log::warning('Failed to delete tenant: ' . $ex->getMessage());
            }
        }
        if ($company) {
            try { $company->delete(); } catch (\Throwable $ex) {
                Log::warning('Failed to delete company: ' . $ex->getMessage());
            }
        }
        try { DB::statement("DROP DATABASE IF EXISTS `$tenancyDbName`"); } catch (\Throwable $ex) {
            Log::warning('Failed to drop database: ' . $ex->getMessage());
        }

        // Provide detailed error message
        $errorMsg = $e->getMessage();
        if (str_contains($errorMsg, 'Integrity constraint violation')) {
            $errorMsg = "A database seeder failed due to missing parent data or foreign key mismatch. Ensure parent tables (e.g., tenants) are created and seeded. Error: " . $errorMsg;
        } elseif (str_contains($errorMsg, "Field 'subdomain' doesn't have a default value")) {
            $errorMsg = "The companies table in the tenant database requires a 'subdomain' value. Ensure the DatabaseSeeder or other seeders include the subdomain field. Error: " . $errorMsg;
        } elseif (str_contains($errorMsg, 'no active transaction')) {
            $errorMsg = "A transaction error occurred, likely due to a DDL operation. Error: " . $errorMsg;
        }

        return back()->withInput()
            ->withErrors(['error' => 'Onboarding failed: ' . $errorMsg]);
    }
}

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
