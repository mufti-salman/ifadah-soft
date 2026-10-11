<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();

            $table->string('ticket_number')->unique();

            // যে প্রতিষ্ঠানের পক্ষ থেকে টিকিট তৈরি হয়েছে
            $table->foreignId('institution_id')
                ->constrained('madrasas')
                ->restrictOnDelete();

            // যিনি টিকিট তৈরি করেছেন
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // টিকিটটি যে Support Admin-কে দেওয়া হয়েছে
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('subject');
            $table->string('category')->default('general');

            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent',
            ])->default('medium');

            $table->enum('status', [
                'open',
                'in_progress',
                'resolved',
                'closed',
            ])->default('open');

            $table->timestamps();

            $table->index(['institution_id', 'status']);
            $table->index(['assigned_to', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};