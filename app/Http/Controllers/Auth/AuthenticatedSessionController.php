<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show login page.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function store(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Login
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'phone' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Find User
        |--------------------------------------------------------------------------
        */

        $user = User::with([
            'institution',
            'role',
        ])
            ->where(
                'phone',
                $request->phone
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | User Not Found
        |--------------------------------------------------------------------------
        */

        if (!$user) {

            return back()
                ->withErrors([
                    'phone' =>
                        'মোবাইল নম্বর অথবা পাসওয়ার্ড সঠিক নয়।',
                ])
                ->onlyInput('phone');
        }


        /*
        |--------------------------------------------------------------------------
        | User Status
        |--------------------------------------------------------------------------
        */

        if ((int) $user->status !== 1) {

            return back()
                ->withErrors([
                    'phone' =>
                        'আপনার অ্যাকাউন্ট নিষ্ক্রিয় করা হয়েছে।',
                ])
                ->onlyInput('phone');
        }


        /*
        |--------------------------------------------------------------------------
        | Resolve Role
        |--------------------------------------------------------------------------
        */

        $roleSlug = strtolower(
            trim(
                (string) optional($user->role)->slug
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Validate Role
        |--------------------------------------------------------------------------
        */

        if (empty($roleSlug)) {

            return back()
                ->withErrors([
                    'phone' =>
                        'আপনার অ্যাকাউন্টের সাথে কোনো Role যুক্ত নেই।',
                ])
                ->onlyInput('phone');
        }


        /*
        |--------------------------------------------------------------------------
        | System User
        |--------------------------------------------------------------------------
        |
        | System-level users:
        |
        |   super_admin
        |   support_admin
        |
        | These users do not require an institution.
        |
        */

        $isSystemUser = in_array(
            $roleSlug,
            [
                'super_admin',
                'support_admin',
            ],
            true
        );


        /*
        |--------------------------------------------------------------------------
        | Institution User
        |--------------------------------------------------------------------------
        |
        | Institution-level users:
        |
        |   user_admin
        |   user
        |   student
        |
        | They must have an active institution.
        |
        */

        if (!$isSystemUser) {

            if (!$user->institution) {

                return back()
                    ->withErrors([
                        'phone' =>
                            'আপনার অ্যাকাউন্টের সাথে কোনো প্রতিষ্ঠান যুক্ত নেই।',
                    ])
                    ->onlyInput('phone');
            }


            if ((int) $user->institution->status !== 1) {

                return back()
                    ->withErrors([
                        'phone' =>
                            'প্রতিষ্ঠানটি বর্তমানে নিষ্ক্রিয়।',
                    ])
                    ->onlyInput('phone');
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Password Check
        |--------------------------------------------------------------------------
        */

        if (!Hash::check(
            $request->password,
            $user->password
        )) {

            return back()
                ->withErrors([
                    'phone' =>
                        'মোবাইল নম্বর অথবা পাসওয়ার্ড সঠিক নয়।',
                ])
                ->onlyInput('phone');
        }


        /*
        |--------------------------------------------------------------------------
        | Clear Old Institution Context
        |--------------------------------------------------------------------------
        |
        | Important:
        |
        | If a Super Admin previously entered an institution,
        | that old institution must not remain active after login.
        |
        */

        $request->session()->forget([
            'active_institution_id',
            'institution_name',
            'institution_name_bn',
            'institution_logo',
            'institution_mode',
            'institution_id',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Update Login Information
        |--------------------------------------------------------------------------
        */

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);


        /*
        |--------------------------------------------------------------------------
        | Login
        |--------------------------------------------------------------------------
        */

        Auth::login(
            $user,
            $request->boolean('remember')
        );


        /*
        |--------------------------------------------------------------------------
        | Regenerate Session
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Unified Dashboard
        |--------------------------------------------------------------------------
        |
        | System users and institution users both go to:
        |
        |   /dashboard
        |
        | The dashboard route itself decides:
        |
        |   super_admin/support_admin
        |       -> System Dashboard
        |
        |   user_admin/user/student
        |       -> Institution Dashboard
        |
        */

        return redirect()->route(
            'dashboard.index'
        );
    }


    /**
     * Logout.
     */
    public function destroy(
        Request $request
    ): RedirectResponse {

        Auth::guard('web')->logout();


        /*
        |--------------------------------------------------------------------------
        | Clear Session
        |--------------------------------------------------------------------------
        */

        $request->session()->forget([
            'active_institution_id',
            'institution_name',
            'institution_name_bn',
            'institution_logo',
            'institution_mode',
            'institution_id',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Invalidate Session
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return redirect('/login');
    }
}