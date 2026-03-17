<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        // Check if group_name column exists, otherwise use a default grouping
        $permissions = Permission::all();
        
        if (\Schema::hasColumn('permissions', 'group_name')) {
            $permissions = $permissions->sortBy(['group_name', 'name'])->groupBy('group_name');
        } else {
            // Group by extracting module from permission name
            $permissions = $permissions->groupBy(function ($permission) {
                $parts = explode('.', $permission->name);
                return ucfirst($parts[0] ?? 'Other');
            });
        }
        
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name|regex:/^[a-zA-Z0-9_-]+$/|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
            'description' => $request->description,
        ]);

        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->givePermissionTo($permissions);

        // Log activity
        ActivityLog::log(
            auth()->user(),
            'create',
            'roles',
            "Created role: {$role->name}"
        );

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        $users = \App\Models\User::role($role->name)->get();
        return view('admin.roles.show', compact('role', 'users'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        // Check if group_name column exists, otherwise use a default grouping
        $permissions = Permission::all();
        
        if (\Schema::hasColumn('permissions', 'group_name')) {
            $permissions = $permissions->groupBy('group_name');
        } else {
            // Group by extracting module from permission name
            $permissions = $permissions->groupBy(function ($permission) {
                $parts = explode('.', $permission->name);
                return ucfirst($parts[0] ?? 'Other');
            });
        }
        
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Prevent editing super_admin role
        if ($role->name === 'super_admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot modify the Super Admin role.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $role->id . '|regex:/^[a-zA-Z0-9_-]+$/|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $oldName = $role->name;
        
        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Sync permissions
        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->syncPermissions($permissions);

        // Log activity
        ActivityLog::log(
            auth()->user(),
            'update',
            'roles',
            "Updated role: {$oldName} to {$role->name}"
        );

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting super_admin role
        if ($role->name === 'super_admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete the Super Admin role.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role. There are users assigned to this role.');
        }

        $roleName = $role->name;
        $role->delete();

        // Log activity
        ActivityLog::log(
            auth()->user(),
            'delete',
            'roles',
            "Deleted role: {$roleName}"
        );

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Show permissions for a role.
     */
    public function permissions(Role $role)
    {
        // Check if group_name column exists, otherwise use a default grouping
        $permissions = Permission::all();
        
        if (\Schema::hasColumn('permissions', 'group_name')) {
            $permissions = $permissions->sortBy(['group_name', 'name'])->groupBy('group_name');
        } else {
            // Group by extracting module from permission name
            $permissions = $permissions->groupBy(function ($permission) {
                $parts = explode('.', $permission->name);
                return ucfirst($parts[0] ?? 'Other');
            });
        }
        
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update permissions for a role.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        // Prevent modifying super_admin role
        if ($role->name === 'super_admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot modify permissions for Super Admin role.');
        }

        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->syncPermissions($permissions);

        // Log activity
        ActivityLog::log(
            auth()->user(),
            'update',
            'roles',
            "Updated permissions for role: {$role->name}"
        );

        return redirect()->route('roles.index')
            ->with('success', 'Permissions updated successfully for role: ' . $role->name);
    }
}
