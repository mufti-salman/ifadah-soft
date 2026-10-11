<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\SystemPanel;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permission Structure
        |--------------------------------------------------------------------------
        |
        | panel
        |   ↓
        | module
        |   ↓
        | actions
        |
        | Final permission slug:
        |
        | student.view
        | student.create
        | student.edit
        |
        |--------------------------------------------------------------------------
        */

        $modules = [

            /*
            |--------------------------------------------------------------------------
            | 01. Dashboard
            |--------------------------------------------------------------------------
            |
            | Sidebar order:
            |
            | Dashboard
            | New User
            | User List
            | Month Setting
            | User Photo
            | User Report
            | Institution Info
            |
            |--------------------------------------------------------------------------
            */
            'dashboard' => [

                'ড্যাশবোর্ড' => [
                    'view',
                    'analytics',
                    'export',
                ],

                'ব্যবহারকারী' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'photo',
                    'report',
                ],

                'মাস' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'প্রতিষ্ঠান' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

            ],

            /*
            |--------------------------------------------------------------------------
            | 02. Student
            |--------------------------------------------------------------------------
            */
            'student' => [

                'শিক্ষার্থী' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'photo',
                    'report',
                    'export',
                    'import',
                ],

                'অভিভাবক' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'ভর্তি' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'approve',
                    'reject',
                ],

                'শ্রেণি' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'শাখা' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'বিষয়' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'শিক্ষার্থী গ্রুপ' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'শিক্ষার্থী কার্ড' => [
                    'view',
                    'print',
                ],

                'সনদপত্র' => [
                    'view',
                    'create',
                    'print',
                ],

            ],

            /*
            |--------------------------------------------------------------------------
            | 03. Teacher
            |--------------------------------------------------------------------------
            */
            'teacher' => [

                'শিক্ষক' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'photo',
                    'report',
                    'export',
                    'import',
                ],

            ],

            /*
            |--------------------------------------------------------------------------
            | 04. Attendance
            |--------------------------------------------------------------------------
            */
            'attendance' => [

                'উপস্থিতি' => [
                    'view',
                    'take',
                    'edit',
                    'delete',
                    'report',
                    'export',
                ],

            ],

            /*
            |--------------------------------------------------------------------------
            | 05. Exam
            |--------------------------------------------------------------------------
            */
            'exam' => [

                'পরীক্ষা' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'schedule',
                    'routine',
                ],

                'পরীক্ষার ধরন' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

            ],

            /*
            |--------------------------------------------------------------------------
            | 06. Result
            |--------------------------------------------------------------------------
            */
            'result' => [

                'ফলাফল' => [
                    'view',
                    'publish',
                    'edit',
                    'delete',
                    'export',
                    'print',
                ],

                'গ্রেড' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

            ],

            /*
            |--------------------------------------------------------------------------
            | 07. Accounting
            |--------------------------------------------------------------------------
            */
            'accounting' => [

                'বেতন ও ফি' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'collect',
                    'report',
                    'export',
                ],

                'ফি-এর ধরন' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'আয়' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'ব্যয়' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'চালান' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'print',
                ],

                'পেমেন্ট' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'হিসাব প্রতিবেদন' => [
                    'view',
                    'export',
                    'print',
                ],

            ],

            /*
            |--------------------------------------------------------------------------
            | 08. Payment
            |--------------------------------------------------------------------------
            */
            'payment' => [

                'অনলাইন পেমেন্ট' => [
                    'view',
                    'create',
                    'process',
                    'verify',
                    'report',
                ],

                'পেমেন্ট গেটওয়ে' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

            ],

            /*
            |--------------------------------------------------------------------------
            | 09. Settings
            |--------------------------------------------------------------------------
            */
            'settings' => [

                'সাধারণ সেটিংস' => [
                    'view',
                    'edit',
                ],

                'একাডেমিক সেটিংস' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'ভাষা' => [
                    'view',
                    'edit',
                ],

                'থিম' => [
                    'view',
                    'edit',
                ],

                'ব্যাকআপ' => [
                    'view',
                    'create',
                    'restore',
                    'delete',
                ],

                'সিস্টেম সেটিংস' => [
                    'view',
                    'edit',
                ],

            ],

            /*
            |--------------------------------------------------------------------------
            | 10. Help
            |--------------------------------------------------------------------------
            */
            'help' => [

                'সহায়তা' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'সাধারণ জিজ্ঞাসা' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'টিকিট' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

            ],

            /*
            |--------------------------------------------------------------------------
            | 11. System
            |--------------------------------------------------------------------------
            */
            'system' => [

                'ভূমিকা' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'permission',
                ],

                'অনুমতি' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'সিস্টেম প্যানেল' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'ভূমিকা প্যানেল' => [
                    'view',
                    'edit',
                ],

                'কার্যক্রমের লগ' => [
                    'view',
                    'export',
                    'delete',
                ],

            ],

        ];

        /*
        |--------------------------------------------------------------------------
        | Action Translation
        |--------------------------------------------------------------------------
        */

        $actionNames = [

            'view' => 'দেখুন',
            'create' => 'যোগ করুন',
            'edit' => 'সম্পাদনা করুন',
            'delete' => 'মুছে ফেলুন',
            'photo' => 'ছবি পরিচালনা করুন',
            'report' => 'প্রতিবেদন দেখুন',
            'export' => 'রপ্তানি করুন',
            'import' => 'আমদানি করুন',
            'approve' => 'অনুমোদন করুন',
            'reject' => 'প্রত্যাখ্যান করুন',
            'print' => 'প্রিন্ট করুন',
            'take' => 'উপস্থিতি নিন',
            'schedule' => 'সময়সূচি পরিচালনা করুন',
            'routine' => 'রুটিন পরিচালনা করুন',
            'publish' => 'প্রকাশ করুন',
            'collect' => 'আদায় করুন',
            'analytics' => 'বিশ্লেষণ দেখুন',
            'process' => 'প্রক্রিয়াকরণ করুন',
            'verify' => 'যাচাই করুন',
            'restore' => 'পুনরুদ্ধার করুন',
            'permission' => 'অনুমতি পরিচালনা করুন',

        ];

        /*
        |--------------------------------------------------------------------------
        | Module Slug Map
        |--------------------------------------------------------------------------
        */

        $moduleSlugMap = [

            'ড্যাশবোর্ড' => 'dashboard',

            'শিক্ষার্থী' => 'student',
            'অভিভাবক' => 'guardian',
            'ভর্তি' => 'admission',
            'শ্রেণি' => 'class',
            'শাখা' => 'section',
            'বিষয়' => 'subject',
            'শিক্ষার্থী গ্রুপ' => 'student-group',
            'শিক্ষার্থী কার্ড' => 'student-card',
            'সনদপত্র' => 'certificate',

            'শিক্ষক' => 'teacher',

            'উপস্থিতি' => 'attendance',

            'পরীক্ষা' => 'exam',
            'পরীক্ষার ধরন' => 'exam-type',

            'ফলাফল' => 'result',
            'গ্রেড' => 'grade',

            'বেতন ও ফি' => 'fee',
            'ফি-এর ধরন' => 'fee-type',
            'আয়' => 'income',
            'ব্যয়' => 'expense',
            'চালান' => 'invoice',
            'পেমেন্ট' => 'payment',
            'হিসাব প্রতিবেদন' => 'accounting-report',

            'অনলাইন পেমেন্ট' => 'online-payment',
            'পেমেন্ট গেটওয়ে' => 'payment-gateway',

            'সাধারণ সেটিংস' => 'general',
            'একাডেমিক সেটিংস' => 'academic',
            'প্রতিষ্ঠান' => 'institution',
            'মাস' => 'month',
            'ভাষা' => 'language',
            'থিম' => 'theme',
            'ব্যাকআপ' => 'backup',
            'সিস্টেম সেটিংস' => 'system-setting',

            'সহায়তা' => 'support',
            'সাধারণ জিজ্ঞাসা' => 'faq',
            'টিকিট' => 'ticket',

            'ব্যবহারকারী' => 'user',
            'ভূমিকা' => 'role',
            'অনুমতি' => 'permission',
            'সিস্টেম প্যানেল' => 'system-panel',
            'ভূমিকা প্যানেল' => 'role-panel',
            'কার্যক্রমের লগ' => 'activity-log',

        ];

        /*
        |--------------------------------------------------------------------------
        | Serial
        |--------------------------------------------------------------------------
        */

        $serial = 1;

        /*
        |--------------------------------------------------------------------------
        | Create / Update Permissions
        |--------------------------------------------------------------------------
        */

        foreach ($modules as $panelSlug => $modulesData) {

            $panel = SystemPanel::query()
                ->where('slug', $panelSlug)
                ->first();

            if (!$panel) {
                continue;
            }

            foreach ($modulesData as $moduleName => $actions) {

                $moduleSlug = $moduleSlugMap[$moduleName]
                    ?? strtolower(
                        str_replace(
                            ' ',
                            '-',
                            $moduleName
                        )
                    );

                foreach ($actions as $action) {

                    $slug = $moduleSlug . '.' . $action;

                    $actionName = $actionNames[$action]
                        ?? ucfirst($action);

                    $permissionName =
                        $moduleName . ' ' . $actionName;

                    Permission::updateOrCreate(
                        [
                            'slug' => $slug,
                        ],
                        [
                            'system_panel_id' => $panel->id,
                            'module' => $moduleName,
                            'permission_name' => $permissionName,
                            'description' => null,
                            'serial' => $serial++,
                            'is_system' => true,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}