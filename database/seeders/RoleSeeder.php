<?php

namespace Database\Seeders;

use App\Models\Madrasa;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
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
                'role_name'  => 'সুপার অ্যাডমিন',
                'slug'       => 'super_admin',
                'level'      => 5,
                'is_system'  => true,
                'description'=> 'সম্পূর্ণ সিস্টেমের সর্বোচ্চ প্রশাসনিক ভূমিকা।',
            ],

            [
                'role_name'  => 'সাপোর্ট অ্যাডমিন',
                'slug'       => 'support_admin',
                'level'      => 4,
                'is_system'  => true,
                'description'=> 'সিস্টেম সাপোর্ট ও অনুমোদিত প্রশাসনিক কার্যক্রম পরিচালনা করে।',
            ],

            [
                'role_name'  => 'ইউজার অ্যাডমিন',
                'slug'       => 'user_admin',
                'level'      => 3,
                'is_system'  => true,
                'description'=> 'নিজ প্রতিষ্ঠানের ব্যবহারকারী, শিক্ষার্থী ও প্রতিষ্ঠানের কার্যক্রম পরিচালনা করে।',
            ],

            [
                'role_name'  => 'ইউজার',
                'slug'       => 'user',
                'level'      => 2,
                'is_system'  => true,
                'description'=> 'প্রতিষ্ঠানের সাধারণ ব্যবহারকারী, শিক্ষক ও কর্মচারীর ভূমিকা।',
            ],

            [
                'role_name'  => 'শিক্ষার্থী',
                'slug'       => 'student',
                'level'      => 1,
                'is_system'  => true,
                'description'=> 'প্রতিষ্ঠানের শিক্ষার্থীর ভূমিকা।',
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

        $superAdminRole = Role::where('slug', 'super_admin')->firstOrFail();
        $supportAdminRole = Role::where('slug', 'support_admin')->firstOrFail();
        $userAdminRole = Role::where('slug', 'user_admin')->firstOrFail();
        $userRole = Role::where('slug', 'user')->firstOrFail();
        $studentRole = Role::where('slug', 'student')->firstOrFail();

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
                'name'         => 'Al Huda Islamic Academy',
                'name_bn'      => 'আল হুদা ইসলামিক একাডেমি',
                'email'        => 'info@alhuda.edu.bd',
                'phone'        => '01700000001',
                'address'      => 'Khulna, Bangladesh',
                'status'       => 1,
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
                'name'         => 'Darul Uloom Madrasa',
                'name_bn'      => 'দারুল উলুম মাদরাসা',
                'email'        => 'info@darululum.edu.bd',
                'phone'        => '01700000002',
                'address'      => 'Khulna, Bangladesh',
                'status'       => 1,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        |
        | Super Admin-এর কোনো institution থাকবে না।
        | সে প্রয়োজন অনুযায়ী active_institution_id session ব্যবহার করবে।
        |
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            [
                'email' => 'superadmin@gmail.com',
            ],
            [
                'institution_id'     => null,
                'role_id'            => $superAdminRole->id,
                'name'               => 'Super Admin',
                'name_bn'            => 'সুপার অ্যাডমিন',
                'phone'              => '01700000000',
                'password'           => Hash::make('fahim'),
                'institution_user_id'=> null,
                'username'           => 'superadmin',
                'status'             => 1,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Support Admin
        |--------------------------------------------------------------------------
        |
        | Support Admin system-level role।
        | প্রয়োজন হলে institution context session থেকে নিতে পারবে।
        |
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
        | Teacher এবং Staff উভয়ই "user" role ব্যবহার করবে।
        |
        |--------------------------------------------------------------------------
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
                'name_bn'             => 'দারুল উলুম অ্যাডমিন',
                'phone'               => '01700000007',
                'password'            => Hash::make('fahim'),
                'institution_user_id' => '401',
                'username'            => 'darul_admin',
                'status'              => 1,
            ]
        );
    }
}