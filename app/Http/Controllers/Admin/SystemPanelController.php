<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemPanel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SystemPanelController extends Controller
{
    /**
     * Display all system panels.
     */
    public function index()
    {
        $panels = SystemPanel::query()
            ->orderBy('serial')
            ->get();

        return view(
            'admin.system-panels.index',
            compact('panels')
        );
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.system-panels.create');
    }

    /**
     * Store new system panel.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'panel_name' => [
                'required',
                'string',
                'max:100',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:100',
                'unique:system_panels,slug',
            ],

            'icon' => [
                'nullable',
                'string',
                'max:100',
            ],

            'serial' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        SystemPanel::create([
            'panel_name' => $validated['panel_name'],

            'slug' => !empty($validated['slug'])
                ? Str::slug($validated['slug'])
                : Str::slug($validated['panel_name']),

            'icon' => $validated['icon'] ?? null,

            'serial' => $validated['serial'] ?? 0,

            'is_active' => $request->boolean('status'),
        ]);

        return redirect()
            ->route('dashboard.system-panels.index')
            ->with(
                'success',
                'System Panel created successfully.'
            );
    }

    /**
     * Show edit form.
     */
    public function edit(SystemPanel $system_panel)
    {
        return view(
            'admin.system-panels.edit',
            [
                'panel' => $system_panel,
            ]
        );
    }

    /**
     * Update system panel.
     */
    public function update(
        Request $request,
        SystemPanel $system_panel
    ) {
        $validated = $request->validate([
            'panel_name' => [
                'required',
                'string',
                'max:100',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:100',
                'unique:system_panels,slug,' . $system_panel->id,
            ],

            'icon' => [
                'nullable',
                'string',
                'max:100',
            ],

            'serial' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        $system_panel->update([
            'panel_name' => $validated['panel_name'],

            'slug' => !empty($validated['slug'])
                ? Str::slug($validated['slug'])
                : Str::slug($validated['panel_name']),

            'icon' => $validated['icon'] ?? null,

            'serial' => $validated['serial'] ?? 0,

            'is_active' => $request->boolean('status'),
        ]);

        return redirect()
            ->route('dashboard.system-panels.index')
            ->with(
                'success',
                'System Panel updated successfully.'
            );
    }

    /**
     * Delete system panel.
     */
    public function destroy(SystemPanel $system_panel)
    {
        /*
         * Do not delete a panel if it is already
         * assigned to one or more roles.
         */
        if ($system_panel->roles()->exists()) {
            return back()->with(
                'error',
                'This panel is assigned to one or more roles.'
            );
        }

        $system_panel->delete();

        return redirect()
            ->route('dashboard.system-panels.index')
            ->with(
                'success',
                'System Panel deleted successfully.'
            );
    }
}