<?php

namespace Database\Seeders;

use App\Models\Madrasa;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemPanel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | System Roles
        |--------------------------------------------------------------------------
        |
        | Role Hierarchy:
        |
        | 5 = Super Admin
        | 4 = Support Admin
        | 3 = User Admin
        | 2 = User
        | 1 = Student
        |
        |--------------------------------------------------------------------------
        */

        $roles = [

            [
                'role_name'   => 'সুপার অ্যাডমিন',
                'slug'        => 'super_admin',
                'level'       => 5,
                'is_system'   => true,
                'description' => 'সম্পূর্ণ সিস্টেমের সর্বোচ্চ প্রশাসনিক ভূমিকা।',
            ],

            [
                'role_name'   => 'সাপোর্ট অ্যাডমিন',
                'slug'        => 'support_admin',
                'level'       => 4,
                'is_system'   => true,
                'description' => 'সিস্টেম সাপোর্ট ও অনুমোদিত প্রশাসনিক কার্যক্রম পরিচালনা করে।',
            ],

            [
                'role_name'   => 'ইউজার অ্যাডমিন',
                'slug'        => 'user_admin',
                'level'       => 3,
                'is_system'   => true,
                'description' => 'নিজ প্রতিষ্ঠানের ব্যবহারকারী, শিক্ষার্থী ও প্রতিষ্ঠানের কার্যক্রম পরিচালনা করে।',
            ],

            [
                'role_name'   => 'ইউজার',
                'slug'        => 'user',
                'level'       => 2,
                'is_system'   => true,
                'description' => 'প্রতিষ্ঠানের সাধারণ ব্যবহারকারী, শিক্ষক ও কর্মচারীর ভূমিকা।',
            ],

            [
                'role_name'   => 'শিক্ষার্থী',
                'slug'        => 'student',
                'level'       => 1,
                'is_system'   => true,
                'description' => 'প্রতিষ্ঠানের শিক্ষার্থীর ভূমিকা।',
            ],

        ];


        /*
        |--------------------------------------------------------------------------
        | Create / Update Roles
        |--------------------------------------------------------------------------
        */

        foreach ($roles as $roleData) {

            Role::updateOrCreate(
                [
                    'slug' => $roleData['slug'],
                ],
                $roleData
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Get Roles
        |--------------------------------------------------------------------------
        */

        $superAdminRole = Role::where(
            'slug',
            'super_admin'
        )->firstOrFail();

        $supportAdminRole = Role::where(
            'slug',
            'support_admin'
        )->firstOrFail();

        $userAdminRole = Role::where(
            'slug',
            'user_admin'
        )->firstOrFail();

        $userRole = Role::where(
            'slug',
            'user'
        )->firstOrFail();

        $studentRole = Role::where(
            'slug',
            'student'
        )->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        |
        | PermissionSeeder আগে run করা উচিত।
        |
        */

        $permissions = Permission::query()
            ->where('is_active', true)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | System Panels
        |--------------------------------------------------------------------------
        */

        $panels = SystemPanel::query()
            ->where('is_active', true)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Role → Permission Assignment
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $superAdminRole,
            $supportAdminRole,
            $userAdminRole,
            $userRole,
            $studentRole,
            $permissions,
            $panels
        ) {

            /*
            |--------------------------------------------------------------------------
            | SUPER ADMIN
            |--------------------------------------------------------------------------
            |
            | Super Admin-এর Role Permission সব active permission।
            |
            | তবে Institution Mode-এ User.php
            | institution permission দিয়ে access সীমাবদ্ধ করবে।
            |
            */

            $superAdminRole->permissions()->sync(
                $permissions
                    ->pluck('id')
                    ->toArray()
            );


            /*
            |--------------------------------------------------------------------------
            | SUPER ADMIN → ALL PANELS
            |--------------------------------------------------------------------------
            */

            $superAdminRole->systemPanels()->sync(
                $panels
                    ->pluck('id')
                    ->toArray()
            );


            /*
            |--------------------------------------------------------------------------
            | SUPPORT ADMIN
            |--------------------------------------------------------------------------
            |
            | Support Admin-এর জন্য:
            |
            | - Dashboard
            | - Help
            | - System
            |
            | এবং প্রয়োজনীয় Settings access।
            |
            */

            $supportPermissionSlugs = [

                // Dashboard
                'dashboard.view',
                'dashboard.analytics',

                // Help
                'support.view',
                'support.create',
                'support.edit',
                'support.delete',

                'faq.view',
                'faq.create',
                'faq.edit',
                'faq.delete',

                'ticket.view',
                'ticket.create',
                'ticket.edit',
                'ticket.delete',

                // System
                'user.view',
                'user.create',
                'user.edit',
                'user.delete',
                'user.photo',
                'user.report',

                'role.view',
                'role.permission',

                'permission.view',

                'system-panel.view',

                'role-panel.view',
                'role-panel.edit',

                'activity-log.view',
                'activity-log.export',

                'system-setting.view',
                'system-setting.edit',

            ];


            $supportPermissions = $permissions
                ->whereIn(
                    'slug',
                    $supportPermissionSlugs
                )
                ->pluck('id')
                ->toArray();


            $supportAdminRole->permissions()->sync(
                $supportPermissions
            );


            /*
            |--------------------------------------------------------------------------
            | SUPPORT ADMIN → PANELS
            |--------------------------------------------------------------------------
            */

            $supportPanelSlugs = [

                'dashboard',

                'help',

                'system',

                'settings',

            ];


            $supportPanels = $panels
                ->whereIn(
                    'slug',
                    $supportPanelSlugs
                )
                ->pluck('id')
                ->toArray();


            $supportAdminRole->systemPanels()->sync(
                $supportPanels
            );


            /*
            |--------------------------------------------------------------------------
            | USER ADMIN
            |--------------------------------------------------------------------------
            |
            | Institution-এর administrator।
            |
            | Institution Permission Manager যদি কোনো permission
            | বন্ধ রাখে, User.php সেটি block করবে।
            |
            */

            $userAdminPermissionSlugs = [

                /*
                | Dashboard
                */

                'dashboard.view',
                'dashboard.analytics',
                'dashboard.export',

                /*
                | Student
                */

                'student.view',
                'student.create',
                'student.edit',
                'student.delete',
                'student.photo',
                'student.report',
                'student.export',
                'student.import',

                'guardian.view',
                'guardian.create',
                'guardian.edit',
                'guardian.delete',

                'admission.view',
                'admission.create',
                'admission.edit',
                'admission.delete',
                'admission.approve',
                'admission.reject',

                'class.view',
                'class.create',
                'class.edit',
                'class.delete',

                'section.view',
                'section.create',
                'section.edit',
                'section.delete',

                'subject.view',
                'subject.create',
                'subject.edit',
                'subject.delete',

                'student-group.view',
                'student-group.create',
                'student-group.edit',
                'student-group.delete',

                'student-card.view',
                'student-card.print',

                'certificate.view',
                'certificate.create',
                'certificate.print',

                /*
                | Teacher
                */

                'teacher.view',
                'teacher.create',
                'teacher.edit',
                'teacher.delete',
                'teacher.photo',
                'teacher.report',
                'teacher.export',
                'teacher.import',

                /*
                | Attendance
                */

                'attendance.view',
                'attendance.take',
                'attendance.edit',
                'attendance.delete',
                'attendance.report',
                'attendance.export',

                /*
                | Exam
                */

                'exam.view',
                'exam.create',
                'exam.edit',
                'exam.delete',
                'exam.schedule',
                'exam.routine',

                'exam-type.view',
                'exam-type.create',
                'exam-type.edit',
                'exam-type.delete',

                /*
                | Result
                */

                'result.view',
                'result.publish',
                'result.edit',
                'result.delete',
                'result.export',
                'result.print',

                'grade.view',
                'grade.create',
                'grade.edit',
                'grade.delete',

                /*
                | Accounting
                */

                'fee.view',
                'fee.create',
                'fee.edit',
                'fee.delete',
                'fee.collect',
                'fee.report',
                'fee.export',

                'fee-type.view',
                'fee-type.create',
                'fee-type.edit',
                'fee-type.delete',

                'income.view',
                'income.create',
                'income.edit',
                'income.delete',

                'expense.view',
                'expense.create',
                'expense.edit',
                'expense.delete',

                'invoice.view',
                'invoice.create',
                'invoice.edit',
                'invoice.delete',
                'invoice.print',

                'payment.view',
                'payment.create',
                'payment.edit',
                'payment.delete',

                'accounting-report.view',
                'accounting-report.export',
                'accounting-report.print',

                /*
                | Payment
                */

                'online-payment.view',
                'online-payment.create',
                'online-payment.process',
                'online-payment.verify',
                'online-payment.report',

                'payment-gateway.view',
                'payment-gateway.create',
                'payment-gateway.edit',
                'payment-gateway.delete',

                /*
                | Settings
                */

                'general.view',
                'general.edit',

                'academic.view',
                'academic.create',
                'academic.edit',
                'academic.delete',

                'institution.view',
                'institution.create',
                'institution.edit',
                'institution.delete',

                'month.view',
                'month.create',
                'month.edit',
                'month.delete',

                'language.view',
                'language.edit',

                'theme.view',
                'theme.edit',

                'backup.view',
                'backup.create',
                'backup.restore',
                'backup.delete',

                /*
                | Help
                */

                'support.view',
                'support.create',
                'support.edit',
                'support.delete',

                'faq.view',
                'faq.create',
                'faq.edit',
                'faq.delete',

                'ticket.view',
                'ticket.create',
                'ticket.edit',
                'ticket.delete',

            ];


            $userAdminPermissions = $permissions
                ->whereIn(
                    'slug',
                    $userAdminPermissionSlugs
                )
                ->pluck('id')
                ->toArray();


            $userAdminRole->permissions()->sync(
                $userAdminPermissions
            );


            /*
            |--------------------------------------------------------------------------
            | USER ADMIN → PANELS
            |--------------------------------------------------------------------------
            */

            $institutionPanelSlugs = [

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


            $institutionPanels = $panels
                ->whereIn(
                    'slug',
                    $institutionPanelSlugs
                )
                ->pluck('id')
                ->toArray();


            $userAdminRole->systemPanels()->sync(
                $institutionPanels
            );


            /*
            |--------------------------------------------------------------------------
            | USER
            |--------------------------------------------------------------------------
            |
            | User role-কে বর্তমানে সাধারণভাবে
            | institution-এর operational modules-এর
            | permission দেওয়া হচ্ছে।
            |
            */

            $userPermissionSlugs = [

                /*
                | Dashboard
                */

                'dashboard.view',

                /*
                | Student
                */

                'student.view',
                'student.create',
                'student.edit',

                'guardian.view',
                'guardian.create',
                'guardian.edit',

                'admission.view',
                'admission.create',
                'admission.edit',

                'class.view',
                'section.view',
                'subject.view',

                'student-group.view',

                'student-card.view',
                'student-card.print',

                'certificate.view',
                'certificate.print',

                /*
                | Teacher
                */

                'teacher.view',
                'teacher.create',
                'teacher.edit',
                'teacher.photo',

                /*
                | Attendance
                */

                'attendance.view',
                'attendance.take',
                'attendance.edit',
                'attendance.report',

                /*
                | Exam
                */

                'exam.view',
                'exam.create',
                'exam.edit',
                'exam.schedule',
                'exam.routine',

                'exam-type.view',

                /*
                | Result
                */

                'result.view',
                'result.edit',
                'result.export',
                'result.print',

                'grade.view',

                /*
                | Accounting
                */

                'fee.view',
                'fee.collect',
                'fee.report',

                'income.view',
                'income.create',

                'expense.view',
                'expense.create',

                'invoice.view',
                'invoice.print',

                'payment.view',

                'accounting-report.view',

                /*
                | Payment
                */

                'online-payment.view',
                'online-payment.report',

                /*
                | Settings
                */

                'general.view',

                'academic.view',

                'institution.view',

                'month.view',

                'language.view',

                'theme.view',

                /*
                | Help
                */

                'support.view',

                'faq.view',

                'ticket.view',
                'ticket.create',

            ];


            $userPermissions = $permissions
                ->whereIn(
                    'slug',
                    $userPermissionSlugs
                )
                ->pluck('id')
                ->toArray();


            $userRole->permissions()->sync(
                $userPermissions
            );


            /*
            |--------------------------------------------------------------------------
            | USER → PANELS
            |--------------------------------------------------------------------------
            */

            $userRole->systemPanels()->sync(
                $institutionPanels
            );


            /*
            |--------------------------------------------------------------------------
            | STUDENT
            |--------------------------------------------------------------------------
            |
            | Student-এর access শুধুমাত্র student-related
            | modules-এ সীমিত রাখা হচ্ছে।
            |
            */

            $studentPermissionSlugs = [

                /*
                | Dashboard
                */

                'dashboard.view',

                /*
                | Student
                */

                'student.view',

                'student-card.view',
                'student-card.print',

                'certificate.view',
                'certificate.print',

                /*
                | Attendance
                */

                'attendance.view',

                /*
                | Exam
                */

                'exam.view',

                'exam-type.view',

                /*
                | Result
                */

                'result.view',

                'grade.view',

            ];


            $studentPermissions = $permissions
                ->whereIn(
                    'slug',
                    $studentPermissionSlugs
                )
                ->pluck('id')
                ->toArray();


            $studentRole->permissions()->sync(
                $studentPermissions
            );


            /*
            |--------------------------------------------------------------------------
            | STUDENT → PANELS
            |--------------------------------------------------------------------------
            */

            $studentPanelSlugs = [

                'dashboard',
                'student',
                'attendance',
                'exam',
                'result',

            ];


            $studentPanels = $panels
                ->whereIn(
                    'slug',
                    $studentPanelSlugs
                )
                ->pluck('id')
                ->toArray();


            $studentRole->systemPanels()->sync(
                $studentPanels
            );
        });


        /*
        |--------------------------------------------------------------------------
        | Institution 1
        |--------------------------------------------------------------------------
        */

        $institutionOne = Madrasa::updateOrCreate(
            [
                'madrasa_code' => 'ALHUDA001',
            ],
            [
                'name'    => 'Al Huda Islamic Academy',
                'name_bn' => 'আল হুদা ইসলামিক একাডেমি',
                'email'   => 'info@alhuda.edu.bd',
                'phone'   => '01700000001',
                'address' => 'Khulna, Bangladesh',
                'status'  => 1,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Institution 2
        |--------------------------------------------------------------------------
        */

        $institutionTwo = Madrasa::updateOrCreate(
            [
                'madrasa_code' => 'DARUL001',
            ],
            [
                'name'    => 'Darul Uloom Madrasa',
                'name_bn' => 'দারুল উলুম মাদরাসা',
                'email'   => 'info@darululum.edu.bd',
                'phone'   => '01700000002',
                'address' => 'Khulna, Bangladesh',
                'status'  => 1,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        |
        | Super Admin-এর কোনো institution থাকবে না।
        |
        */

        User::updateOrCreate(
            [
                'email' => 'superadmin@gmail.com',
            ],
            [
                'institution_id'      => null,
                'role_id'             => $superAdminRole->id,
                'name'                => 'Super Admin',
                'name_bn'             => 'সুপার অ্যাডমিন',
                'phone'               => '01700000000',
                'password'            => Hash::make('fahim'),
                'institution_user_id' => null,
                'username'            => 'superadmin',
                'status'              => 1,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Support Admin
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            [
                'email' => 'supportadmin@gmail.com',
            ],
            [
                'institution_id'      => null,
                'role_id'             => $supportAdminRole->id,
                'name'                => 'Support Admin',
                'name_bn'             => 'সাপোর্ট অ্যাডমিন',
                'phone'               => '01700000003',
                'password'            => Hash::make('fahim'),
                'institution_user_id' => null,
                'username'            => 'supportadmin',
                'status'              => 1,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Institution 1 - User Admin
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            [
                'email' => 'admin@alhuda.edu.bd',
            ],
            [
                'institution_id'      => $institutionOne->id,
                'role_id'             => $userAdminRole->id,
                'name'                => 'Al Huda Admin',
                'name_bn'             => 'আল হুদা অ্যাডমিন',
                'phone'               => '01700000004',
                'password'            => Hash::make('fahim'),
                'institution_user_id' => '401',
                'username'            => 'alhuda_admin',
                'status'              => 1,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Institution 1 - User / Teacher / Staff
        |--------------------------------------------------------------------------
        |
        | Teacher আলাদা Role নয়।
        | Teacher / Staff → user role.
        |
        */

        User::updateOrCreate(
            [
                'email' => 'user@alhuda.edu.bd',
            ],
            [
                'institution_id'      => $institutionOne->id,
                'role_id'             => $userRole->id,
                'name'                => 'Al Huda User',
                'name_bn'             => 'আল হুদা ব্যবহারকারী',
                'phone'               => '01700000005',
                'password'            => Hash::make('fahim'),
                'institution_user_id' => '201',
                'username'            => 'alhuda_user',
                'status'              => 1,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Institution 1 - Student
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            [
                'email' => 'student@alhuda.edu.bd',
            ],
            [
                'institution_id'      => $institutionOne->id,
                'role_id'             => $studentRole->id,
                'name'                => 'Al Huda Student',
                'name_bn'             => 'আল হুদা শিক্ষার্থী',
                'phone'               => '01700000006',
                'password'            => Hash::make('fahim'),
                'institution_user_id' => '101',
                'username'            => 'alhuda_student',
                'status'              => 1,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Institution 2 - User Admin
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            [
                'email' => 'admin@darululum.edu.bd',
            ],
            [
                'institution_id'      => $institutionTwo->id,
                'role_id'             => $userAdminRole->id,
                'name'                => 'Darul Uloom Admin',
                'name_bn'              => 'দারুল উলুম অ্যাডমিন',
                'phone'               => '01700000007',
                'password'            => Hash::make('fahim'),
                'institution_user_id' => '401',
                'username'            => 'darul_admin',
                'status'              => 1,
            ]
        );
    }
}