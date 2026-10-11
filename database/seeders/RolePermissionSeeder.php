<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        |
        | Super Admin সব active permission পাবে।
        |
        */

        $this->assignAllPermissions('super_admin');


        /*
        |--------------------------------------------------------------------------
        | SUPPORT ADMIN
        |--------------------------------------------------------------------------
        */

        $this->assignPermissions('support_admin', [

            // Dashboard
            'dashboard.view',
            'dashboard.analytics',

            // Support
            'support.view',
            'support.create',
            'support.edit',
            'support.delete',

            // Ticket
            'ticket.view',
            'ticket.create',
            'ticket.edit',
            'ticket.delete',

            // User
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'user.photo',
            'user.report',

            // Role
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'role.permission',

            // Permission
            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete',

            // System Panel
            'system-panel.view',
            'system-panel.create',
            'system-panel.edit',
            'system-panel.delete',

            // Role Panel
            'role-panel.view',
            'role-panel.edit',

            // System Settings
            'system-setting.view',
            'system-setting.edit',

        ]);


        /*
        |--------------------------------------------------------------------------
        | USER ADMIN
        |--------------------------------------------------------------------------
        |
        | User Admin-এর permissions RoleSeeder থেকে manage হবে।
        |
        | এখানে sync([]) করা যাবে না।
        |
        */


        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        |
        | Teacher-ও এখন User role-এর অধীনে থাকবে।
        |
        */

        $this->assignPermissions('user', [

            // Dashboard
            'dashboard.view',

            // Student
            'student.view',
            'student.photo',
            'student.report',

            // Guardian
            'guardian.view',

            // Class
            'class.view',

            // Section
            'section.view',

            // Subject
            'subject.view',

            // Student Group
            'student-group.view',

            // Student Card
            'student-card.view',
            'student-card.print',

            // Certificate
            'certificate.view',
            'certificate.print',

            // Teacher
            'teacher.view',

            // Attendance
            'attendance.view',
            'attendance.take',
            'attendance.edit',
            'attendance.report',

            // Exam
            'exam.view',

            // Exam Type
            'exam-type.view',

            // Result
            'result.view',
            'result.edit',
            'result.print',

            // Grade
            'grade.view',

            // Fee
            'fee.view',
            'fee.collect',

            // Fee Type
            'fee-type.view',

            // Payment
            'payment.view',

            // Accounting Report
            'accounting-report.view',

            // General Settings
            'general.view',

            // Help
            'support.view',

            // Ticket
            'ticket.view',
            'ticket.create',

        ]);


        /*
        |--------------------------------------------------------------------------
        | STUDENT
        |--------------------------------------------------------------------------
        |
        | Student শুধুমাত্র নিজের প্রয়োজনীয় basic permission পাবে।
        |
        */

        $this->assignPermissions('student', [

            // Dashboard
            'dashboard.view',

            // Student
            'student.view',

            // Student Card
            'student-card.view',

            // Attendance
            'attendance.view',

            // Exam
            'exam.view',

            // Result
            'result.view',
            'result.print',

            // Payment
            'payment.view',

            // Support
            'support.view',

        ]);
    }


    /**
     * --------------------------------------------------------------------------
     * Assign all active permissions to a role.
     * --------------------------------------------------------------------------
     */
    protected function assignAllPermissions(string $roleSlug): void
    {
        $role = Role::where(
            'slug',
            $roleSlug
        )->first();

        if (!$role) {
            return;
        }

        $permissionIds = Permission::where(
            'is_active',
            true
        )
            ->pluck('id')
            ->toArray();

        $role->permissions()->sync(
            $permissionIds
        );
    }


    /**
     * --------------------------------------------------------------------------
     * Assign specific permissions to a role.
     * --------------------------------------------------------------------------
     */
    protected function assignPermissions(
        string $roleSlug,
        array $permissions
    ): void {
        $role = Role::where(
            'slug',
            $roleSlug
        )->first();

        if (!$role) {
            return;
        }

        if (empty($permissions)) {
            $role->permissions()->sync([]);

            return;
        }

        $permissionIds = Permission::whereIn(
            'slug',
            $permissions
        )
            ->where(
                'is_active',
                true
            )
            ->pluck('id')
            ->toArray();

        $role->permissions()->sync(
            $permissionIds
        );
    }
}