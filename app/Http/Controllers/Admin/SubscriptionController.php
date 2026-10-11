<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Subscription Dashboard
    |--------------------------------------------------------------------------
    */

    /**
     * Display all subscriptions and dashboard statistics.
     */
    public function index()
    {
        $subscriptions = Subscription::with([
            'institution',
            'plan',
        ])
            ->latest('id')
            ->paginate(15);

        $stats = [
            'total' => Subscription::count(),

            'active' => Subscription::where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('starts_at')
                        ->orWhere('starts_at', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('ends_at')
                        ->orWhere('ends_at', '>', now());
                })
                ->count(),

            'trialing' => Subscription::where('status', 'trialing')
                ->where(function ($query) {
                    $query->whereNull('ends_at')
                        ->orWhere('ends_at', '>', now());
                })
                ->count(),

            'pending_payment' => Subscription::where(
                'status',
                'pending_payment'
            )->count(),

            'expired' => Subscription::where('status', 'expired')
                ->orWhere(function ($query) {
                    $query->whereIn('status', [
                        'active',
                        'trialing',
                    ])
                        ->whereNotNull('ends_at')
                        ->where('ends_at', '<=', now());
                })
                ->count(),
        ];

        $pendingPayments = SubscriptionPayment::where(
            'status',
            'pending'
        )->count();

        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('admin.subscriptions.index', compact(
            'subscriptions',
            'stats',
            'pendingPayments',
            'plans'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Institution Subscription Plans
    |--------------------------------------------------------------------------
    */

    /**
     * Display available active plans.
     */
    public function plans()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('admin.subscriptions.plans', compact('plans'));
    }

    /**
     * Display the authenticated institution's current subscription.
     */
    public function current(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Always use the authenticated user's institution.
        // Never trust a browser-submitted or session-selected institution ID.
        $institutionId = $user->institution_id;

        if (!$institutionId) {
            abort(403, 'No institution is assigned to this account.');
        }

        $subscription = Subscription::with('plan')
            ->where('institution_id', $institutionId)
            ->orderByRaw("
                CASE
                    WHEN status = 'trialing'
                        AND (ends_at IS NULL OR ends_at > CURRENT_TIMESTAMP)
                        THEN 0
                    WHEN status = 'active'
                        AND (ends_at IS NULL OR ends_at > CURRENT_TIMESTAMP)
                        THEN 1
                    ELSE 2
                END
            ")
            ->latest('id')
            ->first();

        return view(
            'admin.subscriptions.current',
            compact('subscription')
        );
    }

    /**
     * Select a subscription plan.
     */
    public function choosePlan(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => [
                'required',
                'integer',
                Rule::exists('subscription_plans', 'id')
                    ->where('is_active', true),
            ],

            'billing_cycle' => [
                'required',
                Rule::in(['monthly', 'yearly']),
            ],
        ]);

        $user = $request->user();

        if (!$user || !$user->institution_id) {
            abort(403, 'No institution is assigned to this account.');
        }

        $institutionId = $user->institution_id;

        $plan = SubscriptionPlan::where('is_active', true)
            ->findOrFail($validated['plan_id']);

        $billingCycle = $validated['billing_cycle'];

        $amount = $billingCycle === 'yearly'
            ? $plan->price_yearly
            : $plan->price_monthly;

        DB::transaction(function () use (
            $institutionId,
            $plan,
            $billingCycle,
            $amount
        ) {
            $pending = Subscription::where(
                'institution_id',
                $institutionId
            )
                ->where('status', 'pending_payment')
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if ($pending) {
                $hasSubmittedPayment = $pending->payments()
                    ->where('status', 'pending')
                    ->exists();

                if ($hasSubmittedPayment) {
                    abort(
                        422,
                        'A payment is awaiting verification. Please wait for the admin decision before changing your plan.'
                    );
                }
            }

            $data = [
                'subscription_plan_id' => $plan->id,
                'billing_cycle' => $billingCycle,
                'status' => 'pending_payment',
                'starts_at' => null,
                'ends_at' => null,
                'trial_starts_at' => null,
                'trial_ends_at' => null,
                'cancelled_at' => null,
                'amount' => $amount,
                'currency' => 'BDT',
                'student_limit' => $plan->student_limit,
                'sms_limit' => $plan->sms_limit,
                'auto_renew' => false,
            ];

            if ($pending) {
                $pending->update($data);
            } else {
                Subscription::create([
                    'institution_id' => $institutionId,
                    ...$data,
                ]);
            }
        });

        return redirect()
            ->route('dashboard.subscription.current')
            ->with(
                'success',
                'Package selected. Complete payment to activate your subscription.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Super Admin: Subscription Plan Management
    |--------------------------------------------------------------------------
    */

    /**
     * List all plans, including inactive plans.
     */
    public function managePlans()
    {
        $plans = SubscriptionPlan::orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15);

        return view(
            'admin.subscriptions.manage-plans.index',
            compact('plans')
        );
    }

    /**
     * Show the create-plan form.
     */
    public function createPlan()
    {
        $plan = new SubscriptionPlan();
        $isEdit = false;

        return view(
            'admin.subscriptions.manage-plans.form',
            compact('plan', 'isEdit')
        );
    }

    /**
     * Store a new plan.
     */
    public function storePlan(Request $request)
    {
        $validated = $this->validatePlan($request);

        $validated['slug'] = $this->uniqueSlug(
            $validated['name']
        );

        $validated['features'] = $this->parseFeatures(
            $validated['features'] ?? null
        );

        $validated['is_active'] = $request->boolean('is_active');

        SubscriptionPlan::create($validated);

        return redirect()
            ->route('dashboard.subscription.manage-plans.index')
            ->with('success', 'Subscription package created successfully.');
    }

    /**
     * Show the edit-plan form.
     */
    public function editPlan(SubscriptionPlan $plan)
    {
        $isEdit = true;

        return view(
            'admin.subscriptions.manage-plans.form',
            compact('plan', 'isEdit')
        );
    }

    /**
     * Update an existing plan.
     */
    public function updatePlan(
        Request $request,
        SubscriptionPlan $plan
    ) {
        $validated = $this->validatePlan($request);

        // Keep the current slug so existing references remain stable.
        unset($validated['slug']);

        $validated['features'] = $this->parseFeatures(
            $validated['features'] ?? null
        );

        $validated['is_active'] = $request->boolean('is_active');

        $plan->update($validated);

        return redirect()
            ->route('dashboard.subscription.manage-plans.index')
            ->with('success', 'Subscription package updated successfully.');
    }

    /**
     * Activate or deactivate a plan.
     */
    public function togglePlanStatus(SubscriptionPlan $plan)
    {
        $plan->is_active = !$plan->is_active;
        $plan->save();

        return redirect()
            ->route('dashboard.subscription.manage-plans.index')
            ->with('success', 'Package status updated successfully.');
    }

    /**
     * Validate subscription plan data.
     */
    private function validatePlan(Request $request): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'price_monthly' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'price_yearly' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'annual_discount' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],

            'student_limit' => [
                'required',
                'integer',
                'min:0',
            ],

            'sms_limit' => [
                'required',
                'integer',
                'min:0',
            ],

            'trial_days' => [
                'required',
                'integer',
                'min:0',
                'max:365',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:0',
            ],

            'features' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'is_active' => [
                'required',
                Rule::in(['0', '1', 0, 1]),
            ],
        ]);
    }

    /**
     * Generate a unique slug for a new plan.
     */
    private function uniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'subscription-package';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (SubscriptionPlan::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Convert newline-separated features into an array.
     */
    private function parseFeatures(?string $features): ?array
    {
        if (!$features) {
            return null;
        }

        $items = preg_split('/\r\n|\r|\n/', $features);

        $items = array_values(array_filter(
            array_map('trim', $items),
            fn ($item) => $item !== ''
        ));

        return count($items) > 0 ? $items : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Super Admin: Individual Subscription Management
    |--------------------------------------------------------------------------
    */

    /**
     * Show subscription details.
     */
    public function showSubscription(Subscription $subscription)
    {
        $subscription->load([
            'institution',
            'plan',
            'payments',
        ]);

        return view(
            'admin.subscriptions.show',
            compact('subscription')
        );
    }

    /**
     * Show the subscription edit form.
     */
    public function editSubscription(Subscription $subscription)
    {
        $subscription->load([
            'institution',
            'plan',
        ]);

        $plans = SubscriptionPlan::orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view(
            'admin.subscriptions.edit',
            compact('subscription', 'plans')
        );
    }

    /**
     * Update a subscription.
     */
    public function updateSubscription(
        Request $request,
        Subscription $subscription
    ) {
        $validated = $request->validate([
            'subscription_plan_id' => [
                'required',
                'integer',
                Rule::exists('subscription_plans', 'id'),
            ],

            'billing_cycle' => [
                'required',
                Rule::in(['monthly', 'yearly']),
            ],

            'status' => [
                'required',
                Rule::in([
                    'trialing',
                    'active',
                    'pending_payment',
                    'expired',
                    'cancelled',
                    'rejected',
                ]),
            ],

            'starts_at' => [
                'nullable',
                'date',
            ],

            'ends_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $plan = SubscriptionPlan::findOrFail(
            $validated['subscription_plan_id']
        );

        /*
         * Do not allow a paid subscription to be activated through
         * the edit form without a verified payment.
         */
        if (
            $validated['status'] === 'active'
            && (float) (
                $validated['billing_cycle'] === 'yearly'
                    ? $plan->price_yearly
                    : $plan->price_monthly
            ) > 0
        ) {
            $hasVerifiedPayment = $subscription->payments()
                ->where('status', 'approved')
                ->exists();

            if (!$hasVerifiedPayment) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'status' => 'An approved payment is required before activating this paid subscription.',
                    ]);
            }
        }

        $validated['amount'] = $validated['billing_cycle'] === 'yearly'
            ? $plan->price_yearly
            : $plan->price_monthly;

        $validated['student_limit'] = $plan->student_limit;
        $validated['sms_limit'] = $plan->sms_limit;
        $validated['currency'] = $subscription->currency ?: 'BDT';

        DB::transaction(function () use (
            $subscription,
            $validated
        ) {
            if ($validated['status'] === 'cancelled') {
                $validated['cancelled_at'] =
                    $subscription->cancelled_at ?? now();
            } else {
                $validated['cancelled_at'] = null;
            }

            $subscription->update($validated);
        });

        return redirect()
            ->route('dashboard.subscription.show', $subscription)
            ->with('success', 'Subscription updated successfully.');
    }

    /**
     * Show the subscription management page.
     */
    public function manageSubscription(Subscription $subscription)
    {
        $subscription->load([
            'institution',
            'plan',
            'payments',
        ]);

        return view(
            'admin.subscriptions.manage',
            compact('subscription')
        );
    }

    /**
     * Handle activation, extension and cancellation actions.
     */
    public function handleSubscriptionAction(
        Request $request,
        Subscription $subscription
    ) {
        $validated = $request->validate([
            'action' => [
                'required',
                Rule::in([
                    'activate',
                    'extend',
                    'cancel',
                ]),
            ],

            'extension_days' => [
                'nullable',
                'required_if:action,extend',
                'integer',
                'min:1',
                'max:3650',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $subscription
        ) {
            if ($validated['action'] === 'activate') {
                if ((float) $subscription->amount > 0) {
                    $hasVerifiedPayment = $subscription->payments()
                        ->where('status', 'approved')
                        ->exists();

                    if (!$hasVerifiedPayment) {
                        abort(
                            422,
                            'An approved payment is required before activating this paid subscription.'
                        );
                    }
                }

                $subscription->status = 'active';
                $subscription->starts_at =
                    $subscription->starts_at ?? now();

                $subscription->ends_at =
                    $subscription->ends_at ?? now()->addMonth();

                $subscription->cancelled_at = null;
            }

            if ($validated['action'] === 'extend') {
                if (!in_array(
                    $subscription->status,
                    ['active', 'trialing'],
                    true
                )) {
                    abort(
                        422,
                        'Only active or trial subscriptions can be extended.'
                    );
                }

                $baseDate = $subscription->ends_at
                    && $subscription->ends_at->isFuture()
                        ? $subscription->ends_at
                        : now();

                $subscription->ends_at = $baseDate->copy()->addDays(
                    (int) $validated['extension_days']
                );
            }

            if ($validated['action'] === 'cancel') {
                $subscription->status = 'cancelled';
                $subscription->cancelled_at = now();
            }

            if (!empty($validated['notes'])) {
                $existingNotes = $subscription->notes;

                $subscription->notes = trim(
                    ($existingNotes
                        ? $existingNotes . "\n"
                        : '') . $validated['notes']
                );
            }

            $subscription->save();
        });

        return redirect()
            ->route('dashboard.subscription.manage', $subscription)
            ->with(
                'success',
                'Subscription action completed successfully.'
            );
    }

    /**
     * Soft-delete a subscription record.
     */
    public function destroySubscription(Subscription $subscription)
    {
        // SoftDeletes must be configured on the Subscription model.
        if (!method_exists($subscription, 'trashed')) {
            abort(
                500,
                'Soft deletes are not configured on the Subscription model.'
            );
        }

        $subscription->delete();

        return redirect()
            ->route('dashboard.subscription.index')
            ->with(
                'success',
                'Subscription archived successfully.'
            );
    }
}
