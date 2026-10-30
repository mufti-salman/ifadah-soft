<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_calendar_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('institution_id')
                ->constrained('madrasas')
                ->cascadeOnDelete();

            $table->string('calendar_mode', 20)
                ->default('english');

            // Future Hijri date adjustment: -2 to +2 days.
            $table->smallInteger('hijri_adjustment')
                ->default(0);

            $table->timestamps();

            // One calendar configuration per institution.
            $table->unique('institution_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_calendar_settings');
    }
};