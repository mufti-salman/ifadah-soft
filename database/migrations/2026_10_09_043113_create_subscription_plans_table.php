
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();

            // Plan identity
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();

            // Pricing (BDT)
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->unsignedTinyInteger('annual_discount')->default(20);

            // Usage limits
            $table->unsignedInteger('student_limit')->default(200);
            $table->unsignedInteger('sms_limit')->default(500);
            $table->unsignedSmallInteger('trial_days')->default(7);

            // Feature configuration
            $table->json('features')->nullable();

            // Plan availability and display order
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};