<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\SystemPanel;

class RolePanelSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | All Panels
        |--------------------------------------------------------------------------
        */

        $allPanels = [
            'dashboard',
            'student',
            'teacher',
            'attendance',
            'exam',
            'result',
            'accounting',
            'payment',
            'settings',
            'help',
        ];

        /*
        |--------------------------------------------------------------------------
        | Super Admin (System সহ)
        |--------------------------------------------------------------------------
        */

        $this->assignPanels('super-admin', [
            ...$allPanels,
            'system',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Soft Admin (System সহ)
        |--------------------------------------------------------------------------
        */

        $this->assignPanels('soft-admin', [
            ...$allPanels,
            'system',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Madrasa Admin
        |--------------------------------------------------------------------------
        */

        $this->assignPanels('madrasa-admin', $allPanels);

        /*
        |--------------------------------------------------------------------------
        | Teacher
        |--------------------------------------------------------------------------
        */

        $this->assignPanels('teacher', $allPanels);

        /*
        |--------------------------------------------------------------------------
        | Student
        |--------------------------------------------------------------------------
        */

        $this->assignPanels('student', $allPanels);

        // Guardian লাগবে না
    }

    protected function assignPanels(string $roleSlug, array $panelSlugs): void
    {
        $role = Role::where('slug', $roleSlug)->first();

        if (!$role) {
            return;
        }

        $panelIds = SystemPanel::whereIn('slug', $panelSlugs)
            ->pluck('id')
            ->toArray();

        $role->systemPanels()->sync($panelIds);
    }
}