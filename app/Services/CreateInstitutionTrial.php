<?php

namespace App\Services;

use App\Models\Madrasa;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateInstitutionTrial
{
    public function execute(Madrasa $institution): Subscription
    {
        return DB::transaction(function () use ($institution) {

            // Prevent duplicate initial subscriptions.
            $existingSubscription = Subscription::query()
                ->where('institution_id', $institution->id)
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if ($existingSubscription) {
                return $existingSubscription;
            }

            $plan = SubscriptionPlan::query()
                ->where('slug', 'basic-starter')
                ->where('is_active', true)
                ->first();

            if (!$plan) {
                throw new RuntimeException(
                    'Active Basic Starter subscription plan was not found.'
                );
            }

            $trialStartsAt = now();
            $trialEndsAt = $trialStartsAt->copy()
                ->addDays($plan->trial_days);

            return $institution->subscriptions()->create([
                'subscription_plan_id' => $plan->id,
                'billing_cycle' => 'monthly',
                'status' => 'trialing',

                'starts_at' => $trialStartsAt,
                'ends_at' => $trialEndsAt,

                'trial_starts_at' => $trialStartsAt,
                'trial_ends_at' => $trialEndsAt,

                // Trial does not charge the institution.
                'amount' => 0,
                'currency' => 'BDT',

                'student_limit' => $plan->student_limit,
                'sms_limit' => $plan->sms_limit,

                'auto_renew' => false,
            ]);
        });
    }
}