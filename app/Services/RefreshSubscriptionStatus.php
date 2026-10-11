<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class RefreshSubscriptionStatus
{
    /**
     * Mark expired trial and paid subscriptions as expired.
     */
    public function execute(): int
    {
        return DB::transaction(function () {
            $now = now();

            return Subscription::query()
                ->whereIn('status', ['trialing', 'active'])
                ->whereNotNull('ends_at')
                ->where('ends_at', '<=', $now)
                ->update([
                    'status' => 'expired',
                    'updated_at' => $now,
                ]);
        });
    }
}