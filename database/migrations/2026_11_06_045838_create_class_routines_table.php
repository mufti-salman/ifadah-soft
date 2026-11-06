<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_routines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institution_id')->index();
            $table->unsignedBigInteger('academic_year_id')->index();
            $table->unsignedBigInteger('class_id')->nullable()->index();
            $table->unsignedBigInteger('section_id')->index();
            $table->unsignedBigInteger('subject_id')->index();
            $table->unsignedBigInteger('teacher_id')->index();
            $table->unsignedBigInteger('time_slot_id')->index();
            $table->string('day', 20);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Same section, same slot, same day => only one routine
            $table->unique(
                ['institution_id', 'academic_year_id', 'section_id', 'time_slot_id', 'day'],
                'class_routine_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_routines');
    }
};