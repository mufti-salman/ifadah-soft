<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionPaymentVerificationController extends Controller
{


/**
 * List payments awaiting Super Admin verification.
 */
public function index(Request $request)
{
    $user = $request->user();

    if (!$user || $user->roleSlug !== 'super_admin') {
        abort(403, 'Only Super Admin can view subscription payments.');
    }

    $payments = SubscriptionPayment::query()
        ->with([
            'subscription.plan',
            'institution',
        ])
        ->where('status', 'pending')
        ->latest('submitted_at')
        ->paginate(20);

    return view(
        'admin.subscriptions.payments.index',
        compact('payments')
    );
}
    /**
     * Approve a verified manual payment.
     */
    public function approve(Request $request, int $paymentId)
    {
        $user = $request->user();

        if (!$user || $user->roleSlug !== 'super_admin') {
            abort(403, 'Only Super Admin can verify subscription payments.');
        }

        DB::transaction(function () use ($paymentId, $user) {
            $payment = SubscriptionPayment::query()
                ->whereKey($paymentId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($payment->status !== 'pending') {
                abort(422, 'This payment has already been processed.');
            }

            $subscription = Subscription::query()
                ->whereKey($payment->subscription_id)
                ->where('institution_id', $payment->institution_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($subscription->status !== 'pending_payment') {
                abort(422, 'This subscription is not awaiting payment.');
            }

            if (
                (float) $payment->amount <= 0 ||
                (float) $payment->amount !== (float) $subscription->amount ||
                $payment->currency !== $subscription->currency
            ) {
                abort(422, 'Payment amount or currency does not match the subscription.');
            }

$now = now();

// Preserve the remaining trial period, if one exists.
$currentTrialEnd = Subscription::query()
    ->where('institution_id', $subscription->institution_id)
    ->where('id', '!=', $subscription->id)
    ->where('status', 'trialing')
    ->whereNotNull('ends_at')
    ->where('ends_at', '>', $now)
    ->max('ends_at');

$startsAt = $currentTrialEnd
    ? \Illuminate\Support\Carbon::parse($currentTrialEnd)
    : $now->copy();

$endsAt = $subscription->billing_cycle === 'yearly'
    ? $startsAt->copy()->addYear()
    : $startsAt->copy()->addMonth();

$subscription->update([
    'status' => 'active',
    'starts_at' => $startsAt,
    'ends_at' => $endsAt,
    'cancelled_at' => null,
]);

            $payment->update([
                'status' => 'approved',
                'verified_by' => $user->id,
                'verified_at' => $now,
                'rejection_reason' => null,
            ]);
        });

        return back()->with(
            'success',
            'Payment approved and subscription activated successfully.'
        );
    }

    /**
     * Reject a payment with a reason.
     */
    public function reject(Request $request, int $paymentId)
    {
        $user = $request->user();

        if (!$user || $user->roleSlug !== 'super_admin') {
            abort(403, 'Only Super Admin can verify subscription payments.');
        }

        $validated = $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'min:5',
                'max:2000',
            ],
        ]);

        DB::transaction(function () use (
            $paymentId,
            $user,
            $validated
        ) {
            $payment = SubscriptionPayment::query()
                ->whereKey($paymentId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($payment->status !== 'pending') {
                abort(422, 'This payment has already been processed.');
            }

            $payment->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'verified_by' => $user->id,
                'verified_at' => now(),
            ]);
        });

        return back()->with(
            'success',
            'Payment rejected. The subscription has not been activated.'
        );
    }
}