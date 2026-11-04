<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::table('madrasas', function (Blueprint $table) {
        $table->unsignedBigInteger('updated_by')
            ->nullable()
            ->after('created_by');
    });
}

public function down(): void
{
    Schema::table('madrasas', function (Blueprint $table) {
        $table->dropColumn('updated_by');
    });
}
};
