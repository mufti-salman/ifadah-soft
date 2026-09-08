<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\SystemPanel;

class RoleSystemPanelSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        |
        | Super Admin সব system panel access করতে পারবে।
        |
        */

        $this->assignAllPanels('super_admin');


        /*
        |--------------------------------------------------------------------------
        | SUPPORT ADMIN
        |--------------------------------------------------------------------------
        |
        | Support Admin শুধুমাত্র support/system-level
        | প্রয়োজনীয় panel access করবে।
        |
        */

        $this->assignPanels('support_admin', [
            'dashboard',
            'help',
            'system',
        ]);


        /*
        |--------------------------------------------------------------------------
        | USER ADMIN
        |--------------------------------------------------------------------------
        |
        | User Admin institution-level সব operational panel
        | access করতে পারবে।
        |
        | System panel এখানে থাকবে না।
        |
        */

        $this->assignPanels('user_admin', [
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
        ]);


        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        |
        | Teacher-সহ সাধারণ User এই panelগুলো ব্যবহার করবে।
        |
        */

        $this->assignPanels('user', [
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
        ]);


        /*
        |--------------------------------------------------------------------------
        | STUDENT
        |--------------------------------------------------------------------------
        |
        | Student-এর জন্য প্রয়োজনীয় panel।
        |
        */

        $this->assignPanels('student', [
            'dashboard',
            'student',
            'attendance',
            'exam',
            'result',
            'payment',
            'help',
        ]);
    }


    /**
     * --------------------------------------------------------------------------
     * Assign all panels to a role.
     * --------------------------------------------------------------------------
     */
    protected function assignAllPanels(string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->first();

        if (!$role) {
            return;
        }

        $panelIds = SystemPanel::where('is_active', true)
            ->pluck('id')
            ->toArray();

        $role->systemPanels()->sync($panelIds);
    }


    /**
     * --------------------------------------------------------------------------
     * Assign selected panels to a role.
     * --------------------------------------------------------------------------
     */
    protected function assignPanels(
        string $roleSlug,
        array $panelSlugs
    ): void {
        $role = Role::where('slug', $roleSlug)->first();

        if (!$role) {
            return;
        }

        if (empty($panelSlugs)) {
            $role->systemPanels()->sync([]);

            return;
        }

        $panelIds = SystemPanel::whereIn('slug', $panelSlugs)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        $role->systemPanels()->sync($panelIds);
    }
}