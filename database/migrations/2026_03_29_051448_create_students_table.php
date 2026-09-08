<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('user_id');

            /*
            |--------------------------------------------------------------------------
            | Institution / Madrasa
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('institution_id');

            /*
            |--------------------------------------------------------------------------
            | Academic Year
            |--------------------------------------------------------------------------
            |
            | OPTIONAL
            |
            | Student Academic Year ছাড়া create করা যাবে।
            |
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('academic_year_id')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Class & Section
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('class_id')
                ->nullable();

            $table->unsignedBigInteger('section_id')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Guardian
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('guardian_user_id')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Student ID
            |--------------------------------------------------------------------------
            */
            $table->string('student_id', 50);

            /*
            |--------------------------------------------------------------------------
            | Facilities
            |--------------------------------------------------------------------------
            */
            $table->boolean('is_hostel')
                ->default(false);

            $table->boolean('is_transport')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            |
            | 1 = Active
            | 0 = Inactive
            |
            |--------------------------------------------------------------------------
            */
            $table->tinyInteger('status')
                ->default(1);

            /*
            |--------------------------------------------------------------------------
            | Created / Updated By
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('created_by')
                ->nullable();

            $table->unsignedBigInteger('updated_by')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Unique
            |--------------------------------------------------------------------------
            */
            $table->unique([
                'student_id',
                'institution_id'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */
            $table->index('user_id');
            $table->index('institution_id');
            $table->index('academic_year_id');
            $table->index('class_id');
            $table->index('section_id');
            $table->index('guardian_user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};