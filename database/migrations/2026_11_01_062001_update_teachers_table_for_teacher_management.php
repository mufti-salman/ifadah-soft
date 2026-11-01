<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Add Missing Teacher Information Columns
        |--------------------------------------------------------------------------
        */

        Schema::table('teachers', function (Blueprint $table) {

            // Educational Information
            if (!Schema::hasColumn('teachers', 'pass_department')) {
                $table->string('pass_department', 150)
                    ->nullable();
            }

            if (!Schema::hasColumn('teachers', 'pass_year')) {
                $table->unsignedSmallInteger('pass_year')
                    ->nullable();
            }

            if (!Schema::hasColumn('teachers', 'pass_date')) {
                $table->date('pass_date')
                    ->nullable();
            }

            if (!Schema::hasColumn('teachers', 'completion_institution')) {
                $table->string('completion_institution', 255)
                    ->nullable();
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Foreign Keys
        |--------------------------------------------------------------------------
        |
        | The old migration defined upForeignKeys(), but Laravel does not
        | automatically execute that method. Add the constraints here.
        |
        */

        Schema::table('teachers', function (Blueprint $table) {

            $table->foreign('user_id', 'teachers_user_id_foreign')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign(
                'institution_id',
                'teachers_institution_id_foreign'
            )
                ->references('id')
                ->on('madrasas')
                ->cascadeOnDelete();

            $table->foreign(
                'class_teacher_for',
                'teachers_class_teacher_for_foreign'
            )
                ->references('id')
                ->on('classes')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Drop Foreign Keys
        |--------------------------------------------------------------------------
        */

        Schema::table('teachers', function (Blueprint $table) {

            $table->dropForeign('teachers_user_id_foreign');

            $table->dropForeign(
                'teachers_institution_id_foreign'
            );

            $table->dropForeign(
                'teachers_class_teacher_for_foreign'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Drop Newly Added Columns
        |--------------------------------------------------------------------------
        */

        Schema::table('teachers', function (Blueprint $table) {

            $table->dropColumn([
                'pass_department',
                'pass_year',
                'pass_date',
                'completion_institution',
            ]);
        });
    }
};