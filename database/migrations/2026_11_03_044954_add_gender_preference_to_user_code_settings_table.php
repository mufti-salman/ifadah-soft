<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_code_settings', function (Blueprint $table) {
            $table->string('gender_preference', 20)
                ->default('male')
                ->after('start_code');
        });
    }

    public function down(): void
    {
        Schema::table('user_code_settings', function (Blueprint $table) {
            $table->dropColumn('gender_preference');
        });
    }
};