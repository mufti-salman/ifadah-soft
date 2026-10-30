<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            // Existing records will remain NULL until ownership is verified.
            $table->unsignedBigInteger('institution_id')
                ->nullable()
                ->after('id');

            $table->foreign('institution_id')
                ->references('id')
                ->on('madrasas')
                ->onDelete('cascade');

            $table->index('institution_id');

            // The old global unique constraint must be removed so
            // different institutions can use the same academic-year code.
            $table->dropUnique('academic_years_code_unique');

            // Non-unique indexes for institution-level lookups.
            $table->index(
                ['institution_id', 'code'],
                'academic_years_institution_code_index'
            );

            $table->index(
                ['institution_id', 'name'],
                'academic_years_institution_name_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropIndex('academic_years_institution_code_index');
            $table->dropIndex('academic_years_institution_name_index');
            $table->dropIndex(['institution_id']);
            $table->dropForeign(['institution_id']);
            $table->dropColumn('institution_id');

            // Restore the original global unique constraint.
            $table->unique('code', 'academic_years_code_unique');
        });
    }
};