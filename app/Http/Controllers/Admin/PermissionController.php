<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\SystemPanel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Permission List
     */
    public function index()
    {
        $permissions = Permission::with('panel')
            ->orderBy('system_panel_id')
            ->orderBy('module')
            ->orderBy('serial')
            ->get();

        $panels = SystemPanel::orderBy('serial')
            ->orderBy('id')
            ->get();

        return view(
            'admin.permissions.index',
            compact('permissions', 'panels')
        );
    }

    /**
     * Store Permission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'system_panel_id' => [
                'required',
                'exists:system_panels,id'
            ],

            'module' => [
                'required',
                'string',
                'max:100'
            ],

            'permission_name' => [
                'required',
                'string',
                'max:150'
            ],

            'slug' => [
                'required',
                'string',
                'max:150',
                'regex:/^[a-z0-9._-]+$/',
                'unique:permissions,slug'
            ],

            'description' => [
                'nullable',
                'string'
            ],

            'serial' => [
                'nullable',
                'integer',
                'min:0'
            ],

            'is_active' => [
                'nullable',
                'boolean'
            ],
        ]);

        Permission::create([
            'system_panel_id' => $validated['system_panel_id'],
            'module' => $validated['module'],
            'permission_name' => $validated['permission_name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'serial' => $validated['serial'] ?? 0,
            'is_system' => true,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('dashboard.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Edit
     */
    public function edit(Permission $permission)
    {
        return response()->json([
            'id' => $permission->id,
            'system_panel_id' => $permission->system_panel_id,
            'module' => $permission->module,
            'permission_name' => $permission->permission_name,
            'slug' => $permission->slug,
            'description' => $permission->description,
            'serial' => $permission->serial,
            'is_active' => (bool) $permission->is_active,
        ]);
    }

    /**
     * Update
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'system_panel_id' => [
                'required',
                'exists:system_panels,id'
            ],

            'module' => [
                'required',
                'string',
                'max:100'
            ],

            'permission_name' => [
                'required',
                'string',
                'max:150'
            ],

            'slug' => [
                'required',
                'string',
                'max:150',
                'regex:/^[a-z0-9._-]+$/',
                'unique:permissions,slug,' . $permission->id
            ],

            'description' => [
                'nullable',
                'string'
            ],

            'serial' => [
                'nullable',
                'integer',
                'min:0'
            ],

            'is_active' => [
                'nullable',
                'boolean'
            ],
        ]);

        $permission->update([
            'system_panel_id' => $validated['system_panel_id'],
            'module' => $validated['module'],
            'permission_name' => $validated['permission_name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'serial' => $validated['serial'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('dashboard.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Delete
     */
    public function destroy(Permission $permission)
    {
        if ($permission->roles()->exists()) {
            return back()->with(
                'error',
                'This permission is assigned to one or more roles.'
            );
        }

        $permission->delete();

        return back()->with(
            'success',
            'Permission deleted successfully.'
        );
    }
}