<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_export_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institution_id')->index();
            $table->unsignedBigInteger('user_id')->index();

            $table->string('report_name', 150);
            $table->json('columns');

            $table->timestamps();

            $table->index(['institution_id', 'report_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_export_reports');
    }
};