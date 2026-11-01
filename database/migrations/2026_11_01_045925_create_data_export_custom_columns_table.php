<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_export_custom_columns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institution_id')->index();

            $table->string('name', 100);
            $table->string('slug', 120);
            $table->string('type', 30)->default('text');
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['institution_id', 'slug'],
                'export_custom_columns_institution_slug_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_export_custom_columns');
    }
};