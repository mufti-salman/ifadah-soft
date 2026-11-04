<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('month_settings', function (Blueprint $table) {
            $table->string('academic_year', 100)->change();
        });
    }

    public function down(): void
    {
        Schema::table('month_settings', function (Blueprint $table) {
            $table->string('academic_year', 4)->change();
        });
    }
};