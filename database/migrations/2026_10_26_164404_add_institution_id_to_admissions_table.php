<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->unsignedBigInteger('institution_id')
                ->nullable()
                ->after('id');

            $table->foreign('institution_id')
                ->references('id')
                ->on('madrasas')
                ->cascadeOnDelete();

            $table->index(
                ['institution_id', 'academic_year_id'],
                'admissions_institution_year_index'
            );

            $table->index(
                ['institution_id', 'class_id'],
                'admissions_institution_class_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);

            $table->dropIndex('admissions_institution_year_index');
            $table->dropIndex('admissions_institution_class_index');

            $table->dropColumn('institution_id');
        });
    }
};