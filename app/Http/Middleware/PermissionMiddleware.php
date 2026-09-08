<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {

        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        $user = auth()->user();

        if (!$user) {
            abort(
                401,
                'আপনাকে লগইন করতে হবে।'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Account Status
        |--------------------------------------------------------------------------
        */

        if ((int) $user->status !== 1) {
            abort(
                403,
                'আপনার অ্যাকাউন্ট বর্তমানে নিষ্ক্রিয়।'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN BYPASS
        |--------------------------------------------------------------------------
        |
        | Super Admin-এর জন্য কোনো permission check প্রয়োজন নেই।
        | Role slug দিয়েও check করা হচ্ছে, যাতে is_super_admin
        | field/accessor-এর উপর dependency না থাকে।
        |
        */

        if (
            (bool) $user->is_super_admin === true
            || optional($user->role)->slug === 'super_admin'
        ) {
            return $next($request);
        }


        /*
        |--------------------------------------------------------------------------
        | Permission Check
        |--------------------------------------------------------------------------
        */

        if (!$user->hasPermission($permission)) {
            abort(
                403,
                'এই কাজটি করার অনুমতি আপনার নেই।'
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