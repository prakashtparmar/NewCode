<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
        ]);

        // Create a new role with the validated data
        $role = Role::create(['name' => $request->name]);

        // Sync the permissions with the role
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(string $id)
    {
        // Fetch the role by ID
        $role = Role::findOrFail($id);

        // Fetch all permissions from the database
        $permissions = Permission::all();

        // Return the view with the role and permissions data
        return view('admin.roles.edit', compact('role', 'permissions'));  
    }

    public function update(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
        ]);

        // Fetch the role by ID
        $role = Role::findOrFail($id);

        // Update the role name
        $role->update(['name' => $request->name]);

        // Sync the permissions with the role — this will remove all if none selected
        $role->syncPermissions($request->input('permissions', []));

        // Redirect to the roles index page with a success message
        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        return view('admin.roles.show', compact('role'));
    }
}
