<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SubscriptionPaymentController extends Controller
{
    /**
     * Submit a manual payment for the institution's pending subscription.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subscription_id' => [
                'required',
                'integer',
                'exists:subscriptions,id',
            ],
            'payment_method' => [
                'required',
                Rule::in(['bKash', 'Nagad', 'Rocket', 'Bank Transfer']),
            ],
            'sender_number' => [
                'nullable',
                'string',
                'max:30',
            ],
            'transaction_id' => [
                'required',
                'string',
                'max:150',
                Rule::unique('subscription_payments', 'transaction_id'),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $user = $request->user();

        // Never trust an institution ID supplied by the browser.
        $institutionId = $user->institution_id;

        if (!$institutionId) {
            abort(403, 'No institution is assigned to this account.');
        }

        DB::transaction(function () use (
            $validated,
            $institutionId
        ) {
            $subscription = Subscription::query()
                ->whereKey($validated['subscription_id'])
                ->where('institution_id', $institutionId)
                ->where('status', 'pending_payment')
                ->lockForUpdate()
                ->firstOrFail();

            if ((float) $subscription->amount <= 0) {
                abort(422, 'This subscription does not require a paid transaction.');
            }

            $alreadyPending = SubscriptionPayment::query()
                ->where('subscription_id', $subscription->id)
                ->where('status', 'pending')
                ->exists();

            if ($alreadyPending) {
                abort(
                    422,
                    'A payment is already awaiting verification for this subscription.'
                );
            }

            SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'institution_id' => $institutionId,
                'payment_method' => $validated['payment_method'],
                'sender_number' => $validated['sender_number'] ?? null,
                'transaction_id' => $validated['transaction_id'],
                'amount' => $subscription->amount,
                'currency' => $subscription->currency,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'submitted_at' => now(),
            ]);
        });

        return redirect()
            ->route('dashboard.subscription.current')
            ->with(
                'success',
                'Payment submitted successfully. Please wait for admin verification.'
            );
    }
}