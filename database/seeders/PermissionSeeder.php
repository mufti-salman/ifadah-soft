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
        | Permission Modules
        |--------------------------------------------------------------------------
        | module        = Admin Panel-এ বাংলা নামে দেখাবে
        | permission    = Admin Panel-এ বাংলা নামে দেখাবে
        | slug          = System-এর জন্য English থাকবে
        |--------------------------------------------------------------------------
        */

        $modules = [

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */
            'dashboard' => [

                'ড্যাশবোর্ড' => [
                    'view',
                    'analytics',
                    'export',
                ],

            ],

            /*
            |--------------------------------------------------------------------------
            | Student
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
            | Teacher
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
            | Attendance
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
            | Exam
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
            | Result
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
            | Accounting
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
            | Payment
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
            | Settings
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

                'প্রতিষ্ঠান' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],

                'মাস' => [
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

            ],

            /*
            |--------------------------------------------------------------------------
            | Help
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
            | System
            |--------------------------------------------------------------------------
            */
            'system' => [

                'ব্যবহারকারী' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'photo',
                    'report',
                ],

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

                'সিস্টেম সেটিংস' => [
                    'view',
                    'edit',
                ],

            ],

        ];

        $serial = 1;

        /*
        |--------------------------------------------------------------------------
        | Action Translation
        |--------------------------------------------------------------------------
        */
        $actionNames = [

            'view'      => 'দেখুন',
            'create'    => 'যোগ করুন',
            'edit'      => 'সম্পাদনা করুন',
            'delete'    => 'মুছে ফেলুন',

            'photo'     => 'ছবি পরিচালনা করুন',
            'report'    => 'প্রতিবেদন দেখুন',
            'export'    => 'রপ্তানি করুন',
            'import'    => 'আমদানি করুন',

            'approve'   => 'অনুমোদন করুন',
            'reject'    => 'প্রত্যাখ্যান করুন',

            'print'     => 'প্রিন্ট করুন',

            'take'      => 'উপস্থিতি নিন',

            'schedule'  => 'সময়সূচি পরিচালনা করুন',
            'routine'   => 'রুটিন পরিচালনা করুন',

            'publish'   => 'প্রকাশ করুন',

            'collect'   => 'আদায় করুন',

            'analytics' => 'বিশ্লেষণ দেখুন',

            'process'   => 'প্রক্রিয়াকরণ করুন',
            'verify'    => 'যাচাই করুন',

            'restore'   => 'পুনরুদ্ধার করুন',

            'permission' => 'অনুমতি পরিচালনা করুন',

        ];

        foreach ($modules as $panelSlug => $modulesData) {

            /*
            |--------------------------------------------------------------------------
            | Panel খুঁজে বের করা
            |--------------------------------------------------------------------------
            */
            $panel = SystemPanel::where('slug', $panelSlug)->first();

            if (!$panel) {
                continue;
            }

            foreach ($modulesData as $moduleName => $actions) {

                /*
                |--------------------------------------------------------------------------
                | Module-এর English System Slug
                |--------------------------------------------------------------------------
                |
                | বাংলা module name database-এ থাকবে,
                | কিন্তু permission slug English থাকবে।
                |
                |--------------------------------------------------------------------------
                */

                $moduleSlugMap = [

                    // Student
                    'শিক্ষার্থী'        => 'student',
                    'অভিভাবক'          => 'guardian',
                    'ভর্তি'            => 'admission',
                    'শ্রেণি'            => 'class',
                    'শাখা'             => 'section',
                    'বিষয়'             => 'subject',
                    'শিক্ষার্থী গ্রুপ'   => 'student-group',
                    'শিক্ষার্থী কার্ড'  => 'student-card',
                    'সনদপত্র'           => 'certificate',

                    // Teacher
                    'শিক্ষক'            => 'teacher',

                    // Attendance
                    'উপস্থিতি'          => 'attendance',

                    // Exam
                    'পরীক্ষা'           => 'exam',
                    'পরীক্ষার ধরন'      => 'exam-type',

                    // Result
                    'ফলাফল'             => 'result',
                    'গ্রেড'             => 'grade',

                    // Accounting
                    'বেতন ও ফি'         => 'fee',
                    'ফি-এর ধরন'         => 'fee-type',
                    'আয়'               => 'income',
                    'ব্যয়'              => 'expense',
                    'চালান'             => 'invoice',
                    'পেমেন্ট'           => 'payment',
                    'হিসাব প্রতিবেদন'   => 'accounting-report',

                    // Payment
                    'অনলাইন পেমেন্ট'    => 'online-payment',
                    'পেমেন্ট গেটওয়ে'   => 'payment-gateway',

                    // Settings
                    'সাধারণ সেটিংস'     => 'general',
                    'একাডেমিক সেটিংস'   => 'academic',
                    'প্রতিষ্ঠান'         => 'institution',
                    'মাস'               => 'month',
                    'ভাষা'              => 'language',
                    'থিম'               => 'theme',
                    'ব্যাকআপ'           => 'backup',

                    // Help
                    'সহায়তা'           => 'support',
                    'সাধারণ জিজ্ঞাসা'   => 'faq',
                    'টিকিট'             => 'ticket',

                    // System
                    'ব্যবহারকারী'       => 'user',
                    'ভূমিকা'            => 'role',
                    'অনুমতি'            => 'permission',
                    'সিস্টেম প্যানেল'   => 'system-panel',
                    'ভূমিকা প্যানেল'    => 'role-panel',
                    'কার্যক্রমের লগ'    => 'activity-log',
                    'সিস্টেম সেটিংস'    => 'system-setting',

                    // Dashboard
                    'ড্যাশবোর্ড'        => 'dashboard',
                ];

                $moduleSlug = $moduleSlugMap[$moduleName] ?? strtolower(
                    str_replace(' ', '-', $moduleName)
                );

                foreach ($actions as $action) {

                    /*
                    |--------------------------------------------------------------------------
                    | Stable Permission Slug
                    |--------------------------------------------------------------------------
                    |
                    | Example:
                    |
                    | student.view
                    | student.create
                    | student.edit
                    | student.delete
                    |
                    |--------------------------------------------------------------------------
                    */

                    $slug = $moduleSlug . '.' . $action;

                    /*
                    |--------------------------------------------------------------------------
                    | বাংলা Permission Name
                    |--------------------------------------------------------------------------
                    */

                    $actionName = $actionNames[$action] ?? ucfirst($action);

                    $permissionName = $moduleName . ' ' . $actionName;

                    /*
                    |--------------------------------------------------------------------------
                    | Permission Save
                    |--------------------------------------------------------------------------
                    */

                    Permission::updateOrCreate(
                        [
                            'slug' => $slug,
                        ],
                        [
                            'system_panel_id' => $panel->id,

                            'module' => $moduleName,

                            'permission_name' => $permissionName,

                            'description' => null,

                            'is_system' => true,

                            'is_active' => true,

                            'serial' => $serial++,
                        ]
                    );
                }
            }
        }
    }
}