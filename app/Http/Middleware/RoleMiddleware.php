<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {
        /*
        |--------------------------------------------------------------------------
        | Authentication Check
        |--------------------------------------------------------------------------
        */

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | User Role Check
        |--------------------------------------------------------------------------
        */

        if (!$user->role) {
            abort(403, 'আপনার কোনো Role নির্ধারণ করা হয়নি।');
        }

        $userRole = $user->role->slug;

        /*
        |--------------------------------------------------------------------------
        | Normalize Requested Roles
        |--------------------------------------------------------------------------
        |
        | Route থেকে যেসব role পাঠানো হবে সেগুলো normalize করা হচ্ছে।
        |
        */

        $roles = collect($roles)
            ->map(fn ($role) => trim($role))
            ->filter()
            ->values()
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | No Role Restriction
        |--------------------------------------------------------------------------
        |
        | যদি middleware-এ কোনো role না দেওয়া থাকে,
        | authenticated user-কে allow করা হবে।
        |
        */

        if (empty($roles)) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Role Match
        |--------------------------------------------------------------------------
        */

        if (!in_array($userRole, $roles, true)) {
            abort(
                403,
                'আপনার Role অনুযায়ী এই পেজে প্রবেশের অনুমতি নেই।'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Continue Request
        |--------------------------------------------------------------------------
        */

        return $next($request);
    }
}