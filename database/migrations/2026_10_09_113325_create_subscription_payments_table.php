<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_id')
                ->constrained('subscriptions')
                ->restrictOnDelete();

            $table->foreignId('institution_id')
                ->constrained('madrasas')
                ->restrictOnDelete();

            // bKash, Nagad, Bank, etc.
            $table->string('payment_method', 50);

            $table->string('sender_number', 30)->nullable();

            $table->string('transaction_id', 150)->nullable()->index();

            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('BDT');

            // pending, approved, rejected
            $table->string('status', 30)->default('pending');

            // Optional payment screenshot or receipt
            $table->string('proof_path')->nullable();

            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->index(['institution_id', 'status']);
            $table->index(['subscription_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};