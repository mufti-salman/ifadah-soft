<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubscriptionPlanManagementController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15);

        return view('admin.subscriptions.manage-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscriptions.manage-plans.form', [
            'plan' => new SubscriptionPlan(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePlan($request);

        $plan = DB::transaction(function () use ($validated) {
            $validated['slug'] = $this->uniqueSlug($validated['name']);
            $validated['features'] = $this->parseFeatures(
                $validated['features'] ?? null
            );
            $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

            return SubscriptionPlan::create($validated);
        });

        return redirect()
            ->route('dashboard.subscription.manage-plans.index')
            ->with('success', "{$plan->name} package created successfully.");
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.subscriptions.manage-plans.form', [
            'plan' => $plan,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $this->validatePlan($request);

        DB::transaction(function () use ($validated, $plan) {
            $validated['features'] = $this->parseFeatures(
                $validated['features'] ?? null
            );
            $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

            // Keep the existing slug stable for old references.
            $plan->update($validated);
        });

        return redirect()
            ->route('dashboard.subscription.manage-plans.index')
            ->with('success', "{$plan->name} package updated successfully.");
    }

    public function toggleStatus(SubscriptionPlan $plan)
    {
        $plan->update([
            'is_active' => ! $plan->is_active,
        ]);

        return redirect()
            ->route('dashboard.subscription.manage-plans.index')
            ->with(
                'success',
                $plan->name . ' package is now ' .
                ($plan->is_active ? 'active.' : 'inactive.')
            );
    }

    private function validatePlan(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price_monthly' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'price_yearly' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'annual_discount' => ['required', 'integer', 'min:0', 'max:100'],
            'student_limit' => ['required', 'integer', 'min:0'],
            'sms_limit' => ['required', 'integer', 'min:0'],
            'trial_days' => ['required', 'integer', 'min:0', 'max:365'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'features' => ['nullable', 'string', 'max:10000'],
            'is_active' => ['nullable', Rule::in(['1', '0', 1, 0, true, false, 'on'])],
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);

        if ($base === '') {
            $base = 'plan';
        }

        $slug = $base;
        $counter = 2;

        while (SubscriptionPlan::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function parseFeatures(?string $features): ?array
    {
        if (! $features || trim($features) === '') {
            return null;
        }

        return collect(preg_split('/\r\n|\r|\n/', $features))
            ->map(fn ($feature) => trim($feature))
            ->filter()
            ->values()
            ->all();
    }
}