<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_code_settings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('institution_id');

            // Existing roles only: student and user
            $table->string('role_slug', 50);

            // Configurable starting code
            $table->unsignedBigInteger('start_code');

            $table->timestamps();

            $table->unique(
                ['institution_id', 'role_slug'],
                'user_code_settings_institution_role_unique'
            );

            $table->foreign('institution_id')
                ->references('id')
                ->on('madrasas')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_code_settings');
    }
};