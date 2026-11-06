<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_time_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institution_id')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['institution_id', 'start_time', 'end_time'], 'cts_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_time_slots');
    }
};