<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Create User Types Table
        |--------------------------------------------------------------------------
        */

        Schema::create('user_types', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('name_bn', 100)->nullable();

            $table->string('slug', 100)->unique();

            $table->text('description')->nullable();

            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });


        /*
        |--------------------------------------------------------------------------
        | 2. Insert Default User Types
        |--------------------------------------------------------------------------
        */

        $now = now();

        $userTypes = [
            [
                'name' => 'Student',
                'name_bn' => 'শিক্ষার্থী',
                'slug' => 'student',
                'description' => 'Institution students',
                'sort_order' => 1,
            ],
            [
                'name' => 'Teacher',
                'name_bn' => 'শিক্ষক',
                'slug' => 'teacher',
                'description' => 'Institution teachers',
                'sort_order' => 2,
            ],
            [
                'name' => 'Staff',
                'name_bn' => 'স্টাফ',
                'slug' => 'staff',
                'description' => 'Institution staff',
                'sort_order' => 3,
            ],
            [
                'name' => 'Guardian',
                'name_bn' => 'অভিভাবক',
                'slug' => 'guardian',
                'description' => 'Student guardians',
                'sort_order' => 4,
            ],
            [
                'name' => 'Donor Member',
                'name_bn' => 'দাতা সদস্য',
                'slug' => 'donor_member',
                'description' => 'Institution donor members',
                'sort_order' => 5,
            ],
            [
                'name' => 'Library Member',
                'name_bn' => 'লাইব্রেরী সদস্য',
                'slug' => 'library_member',
                'description' => 'Library members',
                'sort_order' => 6,
            ],
            [
                'name' => 'Software User',
                'name_bn' => 'সফটওয়্যার ইউজার',
                'slug' => 'software_user',
                'description' => 'General software users',
                'sort_order' => 7,
            ],
            [
                'name' => 'Other',
                'name_bn' => 'অন্যান্য',
                'slug' => 'other',
                'description' => 'Other user categories',
                'sort_order' => 8,
            ],
        ];

        foreach ($userTypes as $type) {
            DB::table('user_types')->insert([
                ...$type,
                'is_system' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 3. Add User Type To Users
        |--------------------------------------------------------------------------
        */

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('user_type_id')
                ->nullable()
                ->after('role_id')
                ->constrained('user_types')
                ->nullOnDelete();
        });


        /*
        |--------------------------------------------------------------------------
        | 4. Backward-Compatible Student Mapping
        |--------------------------------------------------------------------------
        |
        | Existing student Role users will receive Student User Type.
        | Other users remain null until their actual category is known.
        |
        */

        $studentTypeId = DB::table('user_types')
            ->where('slug', 'student')
            ->value('id');

        $studentRoleId = DB::table('roles')
            ->where('slug', 'student')
            ->value('id');

        if ($studentTypeId && $studentRoleId) {
            DB::table('users')
                ->where('role_id', $studentRoleId)
                ->update([
                    'user_type_id' => $studentTypeId,
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 5. Add User Type To User Code Settings
        |--------------------------------------------------------------------------
        */

        Schema::table('user_code_settings', function (Blueprint $table) {
            $table->foreignId('user_type_id')
                ->nullable()
                ->after('institution_id')
                ->constrained('user_types')
                ->nullOnDelete();

            $table->index(
                ['institution_id', 'user_type_id'],
                'user_code_settings_institution_type_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('user_code_settings', function (Blueprint $table) {
            $table->dropIndex(
                'user_code_settings_institution_type_index'
            );

            $table->dropConstrainedForeignId('user_type_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_type_id');
        });

        Schema::dropIfExists('user_types');
    }
};