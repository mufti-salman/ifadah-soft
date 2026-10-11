<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            // Institution and selected plan
            $table->foreignId('institution_id')
                ->constrained('madrasas')
                ->restrictOnDelete();

            $table->foreignId('subscription_plan_id')
                ->constrained('subscription_plans')
                ->restrictOnDelete();

            // Billing configuration
            $table->string('billing_cycle', 20)->default('monthly');
            $table->string('status', 30)->default('trialing');

            // Subscription period
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // Trial period
            $table->timestamp('trial_starts_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();

            // Cancellation
            $table->timestamp('cancelled_at')->nullable();

            // Price and limits snapshot
            $table->decimal('amount', 10, 2)->default(0);
            $table->char('currency', 3)->default('BDT');
            $table->unsignedInteger('student_limit')->default(200);
            $table->unsignedInteger('sms_limit')->default(500);

            // Renewal and notes
            $table->boolean('auto_renew')->default(false);
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['institution_id', 'status']);
            $table->index(['status', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};