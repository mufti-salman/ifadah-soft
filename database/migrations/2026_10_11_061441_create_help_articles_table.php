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
    Schema::create('help_articles', function (Blueprint $table) {
        $table->id();

        $table->string('title');
        $table->string('slug')->unique();

        $table->string('category')->default('general');
        $table->string('type')->default('article');

        $table->text('summary')->nullable();
        $table->longText('content');

        $table->boolean('is_published')->default(false);
        $table->unsignedInteger('sort_order')->default(0);

        $table->timestamps();

        $table->index(['type', 'is_published']);
        $table->index(['category', 'is_published']);
    });
}

public function down(): void
{
    Schema::dropIfExists('help_articles');
}
};
