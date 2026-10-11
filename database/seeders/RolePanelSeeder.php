<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\SystemPanel;

class RolePanelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Available Panels
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
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        |
        | Super Admin পুরো system-এর সব panel access করবে।
        | এর সাথে System panel-ও থাকবে।
        |
        */

        $this->assignPanels(
            'super_admin',
            [
                ...$allPanels,
                'system',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | SUPPORT ADMIN
        |--------------------------------------------------------------------------
        |
        | Support Admin system support এবং administration-related
        | panel access করবে।
        |
        */

        $this->assignPanels(
            'support_admin',
            [
                'dashboard',
                'help',
                'system',
                'settings',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | USER ADMIN
        |--------------------------------------------------------------------------
        |
        | Institution-level administrator।
        |
        | User Admin-এর institution-এর operational
        | modules-এর সব panel থাকবে।
        |
        */

        $this->assignPanels(
            'user_admin',
            [
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
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        |
        | Institution-এর সাধারণ User / Teacher / Staff।
        |
        | Teacher আলাদা role নয়।
        | Teacher / Staff → user role.
        |
        */

        $this->assignPanels(
            'user',
            [
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
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | STUDENT
        |--------------------------------------------------------------------------
        |
        | Student-এর জন্য শুধুমাত্র student-related
        | এবং academic panels।
        |
        */

        $this->assignPanels(
            'student',
            [
                'dashboard',
                'student',
                'attendance',
                'exam',
                'result',
            ]
        );
    }


    /**
     * Assign panels to a role.
     */
    protected function assignPanels(
        string $roleSlug,
        array $panelSlugs
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Find Role
        |--------------------------------------------------------------------------
        */

        $role = Role::where(
            'slug',
            $roleSlug
        )->first();

        if (!$role) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Find Panels
        |--------------------------------------------------------------------------
        */

        $panelIds = SystemPanel::whereIn(
            'slug',
            $panelSlugs
        )
            ->pluck('id')
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Sync Role Panels
        |--------------------------------------------------------------------------
        |
        | sync() পুরোনো ভুল panel assignment remove করে
        | শুধুমাত্র নিচের panel-গুলো রাখবে।
        |
        */

        $role->systemPanels()->sync(
            $panelIds
        );
    }
}