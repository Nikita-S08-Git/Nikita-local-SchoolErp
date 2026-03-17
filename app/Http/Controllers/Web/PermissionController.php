<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index()
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
        
        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name|regex:/^[a-zA-Z0-9_.-]+$/|max:255',
            'group_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $permission = Permission::create([
            'name' => $request->name,
            'group_name' => $request->group_name,
            'guard_name' => 'web',
        ]);

        // Log activity
        ActivityLog::log(
            auth()->user(),
            'create',
            'permissions',
            "Created permission: {$permission->name}"
        );

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        $roles = Role::permission($permission->name)->get();
        return view('admin.permissions.show', compact('permission', 'roles'));
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name,' . $permission->id . '|regex:/^[a-zA-Z0-9_.-]+$/|max:255',
            'group_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $oldName = $permission->name;
        
        $permission->update([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);

        // Log activity
        ActivityLog::log(
            auth()->user(),
            'update',
            'permissions',
            "Updated permission: {$oldName} to {$permission->name}"
        );

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return redirect()->route('permissions.index')
                ->with('error', 'Cannot delete permission. It is assigned to roles.');
        }

        // Check if permission is assigned to any users
        if ($permission->users()->count() > 0) {
            return redirect()->route('permissions.index')
                ->with('error', 'Cannot delete permission. It is assigned to users.');
        }

        $permissionName = $permission->name;
        $permission->delete();

        // Log activity
        ActivityLog::log(
            auth()->user(),
            'delete',
            'permissions',
            "Deleted permission: {$permissionName}"
        );

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
