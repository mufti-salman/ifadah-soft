<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('month_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('institution_id')
                ->nullable()
                ->after('id')
                ->index();

            $table->foreign('institution_id')
                ->references('id')
                ->on('madrasas')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('month_settings', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
            $table->dropIndex(['institution_id']);
            $table->dropColumn('institution_id');
        });
    }
};