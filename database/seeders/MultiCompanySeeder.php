<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class MultiCompanySeeder extends Seeder
{
    public function run(): void
    {
        // Step 1: Disable foreign key checks to prevent errors while truncating tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Step 2: Truncate relevant tables to reset the database state
        // Forget cached permissions (if any)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Truncate necessary tables to start fresh
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('users')->truncate();
        DB::table('companies')->truncate();

        // Step 3: Enable foreign key checks after truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Step 4: Define permissions for users, roles, products, and companies
        $permissions = [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'toggle_users',
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'view_permissions',
            'create_permissions',
            'edit_permissions',
            'delete_permissions',
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'view_companies',
            'create_companies',
            'edit_companies',
            'delete_companies',
        ];

        // Create the permissions in the database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Step 5: Create companies, roles, and users

        // Company data
        $companies = [
            ['name' => 'TATA', 'code' => 'TATA'],
            ['name' => 'AIRTEL', 'code' => 'AIRTEL'],
            ['name' => 'RELIENCE', 'code' => 'RELIENCE'],
        ];

        // Loop through each company and set up roles and users
        foreach ($companies as $companyData) {
            // Create the company
            $company = Company::create($companyData);

            // Create roles for each company
            $adminRole = Role::create(['name' => "admin_{$company->id}", 'guard_name' => 'web']);
            $executiveRole = Role::create(['name' => "executive_{$company->id}", 'guard_name' => 'web']);

            // Assign scoped permissions to roles
            $adminRole->syncPermissions([
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
                'toggle_users',
                'view_roles',
                'create_roles',
                'edit_roles',
                'delete_roles',
                'view_permissions',
                'create_permissions',
                'edit_permissions',
                'delete_permissions',
                'view_products',
                'create_products',
                'edit_products',
                'delete_products',
                'view_companies',
                'create_companies',
                'edit_companies',
                'delete_companies',
            ]);
            $executiveRole->syncPermissions(['view_users']);

            // Create admin user for the company
            $admin = User::create([
                'name' => "Admin {$company->name}",
                'email' => "admin{$company->id}@example.com",
                'password' => bcrypt('password'),
                'company_id' => $company->id,
                'user_level' => 'admin',
                'is_active' => true,
            ]);
            // Assign admin role to the admin user
            $admin->assignRole($adminRole);

            // Create 2 executive users for the company
            for ($i = 1; $i <= 2; $i++) {
                $executive = User::create([
                    'name' => "Executive{$i} {$company->name}",
                    'email' => "executive{$i}_{$company->id}@example.com",
                    'password' => bcrypt('password'),
                    'company_id' => $company->id,
                    'user_level' => 'executive',
                    'is_active' => true,
                ]);
                // Assign executive role to each executive user
                $executive->assignRole($executiveRole);
            }
        }

        // Step 6: Create Master Admin user with all permissions and roles
        $masterAdmin = User::create([
            'name' => 'Master Admin',
            'email' => 'masteradmin@example.com',
            'password' => bcrypt('password'),
            'user_level' => 'master_admin',
            'is_active' => true,
        ]);

        // Assign all roles and permissions to the Master Admin
        $masterAdmin->syncRoles(Role::all());
        $masterAdmin->syncPermissions(Permission::all());
    }
}
