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
        Schema::create('user_permissions', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Permission
            |--------------------------------------------------------------------------
            */
            $table->foreignId('permission_id')
                ->constrained('permissions')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Allow / Deny
            |--------------------------------------------------------------------------
            |
            | true  = Permission allowed
            | false = Permission denied
            |
            */
            $table->boolean('allow')
                ->default(true);

            $table->unique(
                ['user_id', 'permission_id'],
                'user_permission_unique'
            );

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */
            $table->index('allow');
        });
    }

    /**
     * ----------------------------------------------------------
     * Reverse the migrations
     * ----------------------------------------------------------
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};