<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super Admin always retains system access.
        if ($user->is_super_admin) {
            return $next($request);
        }

        // Support Admin may access the system dashboard
        // outside institution mode.
        if (
            $user->roleSlug === 'support_admin'
            && $request->routeIs('dashboard.index')
            && session('institution_mode') !== true
        ) {
            return $next($request);
        }

        // Non-super admins must only use their assigned institution.
        $institutionId = $user->institution_id;

        if (!$institutionId) {
            abort(403, 'No institution is assigned to this account.');
        }

        // Allow subscription-related pages and logout even if expired.
        if ($request->routeIs(
            'dashboard.subscription.plans',
            'dashboard.subscription.current',
            'dashboard.subscription.renewal.*',
            'logout'
        )) {
            return $next($request);
        }

    $hasValidSubscription = Subscription::query()
    ->where('institution_id', $institutionId)
    ->whereIn('status', ['trialing', 'active'])
    ->where(function ($query) {
        $query->whereNull('starts_at')
            ->orWhere('starts_at', '<=', now());
    })
    ->whereNotNull('ends_at')
    ->where('ends_at', '>', now())
    ->exists();

        if (!$hasValidSubscription) {
            return redirect()
                ->route('dashboard.subscription.plans')
                ->with(
                    'warning',
                    'Your subscription has expired or is inactive. Please select a package to continue.'
                );
        }

        return $next($request);
    }
}