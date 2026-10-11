<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\SystemPanel;
use Illuminate\Http\Request;

class SystemPanelManagerController extends Controller
{
    /**
     * Display panel manager.
     */
    public function index(Request $request)
    {
        $roles = Role::query()
            ->orderBy('level')
            ->get();

        /*
         * If a role is selected from the query string,
         * load that role.
         *
         * Otherwise select the first available role.
         */
        $selectedRole = $request->filled('role')
            ? Role::findOrFail($request->role)
            : $roles->first();

        $panels = SystemPanel::query()
            ->where('is_active', true)
            ->orderBy('serial')
            ->get();

        return view(
            'admin.system-panel-manager.index',
            compact(
                'roles',
                'selectedRole',
                'panels'
            )
        );
    }

    /**
     * Update system panel access for a role.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],

            'panels' => [
                'nullable',
                'array',
            ],

            'panels.*' => [
                'integer',
                'exists:system_panels,id',
            ],
        ]);

        $role = Role::findOrFail(
            $validated['role_id']
        );

        $role->systemPanels()->sync(
            $validated['panels'] ?? []
        );

        return redirect()
            ->route(
                'dashboard.system-panel-manager.index',
                [
                    'role' => $role->id,
                ]
            )
            ->with(
                'success',
                'Panel access updated successfully.'
            );
    }
}