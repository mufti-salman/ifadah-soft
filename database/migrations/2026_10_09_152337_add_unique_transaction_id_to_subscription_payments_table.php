
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->dropIndex(
                'subscription_payments_transaction_id_index'
            );

            $table->unique(
                'transaction_id',
                'subscription_payments_transaction_id_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->dropUnique(
                'subscription_payments_transaction_id_unique'
            );

            $table->index('transaction_id');
        });
    }
};