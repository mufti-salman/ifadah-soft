<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ----------------------------------------------------------
     * Run the migrations
     * ----------------------------------------------------------
     */
    public function up(): void
    {
        Schema::create('institution_permissions', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Institution
            |--------------------------------------------------------------------------
            |
            | Institution = Madrasa
            |
            | madrasas.id
            |
            */
            $table->foreignId('institution_id')
                ->constrained('madrasas')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Permission
            |--------------------------------------------------------------------------
            |
            | permissions.id
            |
            */
            $table->foreignId('permission_id')
                ->constrained('permissions')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Permission
            |--------------------------------------------------------------------------
            |
            */
            $table->unique(
                ['institution_id', 'permission_id'],
                'institution_permission_unique'
            );

            $table->timestamps();
        });
    }

    /**
     * ----------------------------------------------------------
     * Reverse the migrations
     * ----------------------------------------------------------
     */
    public function down(): void
    {
        Schema::dropIfExists('institution_permissions');
    }
};