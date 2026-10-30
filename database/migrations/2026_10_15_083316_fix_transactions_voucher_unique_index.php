<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check for duplicate voucher numbers
        // within the same institution and transaction type.
        $duplicates = DB::table('transactions')
            ->select(
                'institution_id',
                'type',
                'voucher_no',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(
                'institution_id',
                'type',
                'voucher_no'
            )
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isNotEmpty()) {
            throw new RuntimeException(
                'Duplicate voucher numbers exist within an institution. '
                . 'Resolve these records before running this migration.'
            );
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(
                'transactions_type_voucher_no_unique'
            );

            $table->unique(
                [
                    'institution_id',
                    'type',
                    'voucher_no',
                ],
                'transactions_institution_type_voucher_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(
                'transactions_institution_type_voucher_unique'
            );

            $table->unique(
                ['type', 'voucher_no'],
                'transactions_type_voucher_no_unique'
            );
        });
    }
};