<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institution_id')->index();
            $table->unsignedBigInteger('student_id')->index();
            $table->unsignedBigInteger('academic_year_id')->nullable()->index();
            $table->unsignedBigInteger('custom_column_id')->index();

            $table->longText('value')->nullable();

            $table->timestamps();

            $table->unique(
                [
                    'institution_id',
                    'student_id',
                    'academic_year_id',
                    'custom_column_id',
                ],
                'student_custom_values_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_custom_field_values');
    }
};