<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Madrasa;

class InstitutionMiddleware
{
    /**
     * --------------------------------------------------------------------------
     * Institution Middleware
     * --------------------------------------------------------------------------
     *
     * Role Structure:
     *
     * SUPER ADMIN
     *      │
     *      ├── SUPPORT ADMIN
     *      │
     *      └── INSTITUTION CONTEXT
     *              │
     *              ├── USER ADMIN
     *              ├── USER
     *              └── STUDENT
     *
     * Institution Based Roles:
     *
     * - user_admin
     * - user
     * - student
     *
     * System Based Roles:
     *
     * - super_admin
     * - support_admin
     *
     * --------------------------------------------------------------------------
     */

    public function handle(
        Request $request,
        Closure $next
    ): Response {

        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();


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
        | Load Role
        |--------------------------------------------------------------------------
        */

        $user->loadMissing('role');

        $roleSlug = strtolower(
            trim(
                (string) optional($user->role)->slug
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Role Required
        |--------------------------------------------------------------------------
        */

        if (empty($roleSlug)) {
            abort(
                403,
                'আপনার কোনো Role নির্ধারণ করা হয়নি।'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        |
        | Super Admin:
        |
        | 1. Institution ছাড়া system mode-এ থাকতে পারবে।
        | 2. যেকোনো active institution select করতে পারবে।
        | 3. Selected institution context-এ কাজ করতে পারবে।
        |
        */

        if ($roleSlug === 'super_admin') {

            /*
            |--------------------------------------------------------------------------
            | No Institution Selected → System Mode
            |--------------------------------------------------------------------------
            */

            if (!session()->has('active_institution_id')) {

                $this->clearInstitutionContext();

                session([
                    'institution_mode' => false,
                ]);

                return $next($request);
            }


            /*
            |--------------------------------------------------------------------------
            | Selected Institution
            |--------------------------------------------------------------------------
            */

            $institution = Madrasa::find(
                session('active_institution_id')
            );


            /*
            |--------------------------------------------------------------------------
            | Institution Not Found
            |--------------------------------------------------------------------------
            */

            if (!$institution) {

                $this->clearInstitutionContext();

                return redirect()
                    ->route('dashboard.institutions.index')
                    ->with(
                        'error',
                        'নির্বাচিত প্রতিষ্ঠানটি আর পাওয়া যাচ্ছে না।'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Institution Status
            |--------------------------------------------------------------------------
            */

            if ((int) $institution->status !== 1) {

                $this->clearInstitutionContext();

                return redirect()
                    ->route('dashboard.institutions.index')
                    ->with(
                        'error',
                        'নির্বাচিত প্রতিষ্ঠানটি বর্তমানে নিষ্ক্রিয়।'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Bind Active Institution
            |--------------------------------------------------------------------------
            */

            $this->bindInstitution($institution);


            return $next($request);
        }


        /*
        |--------------------------------------------------------------------------
        | SUPPORT ADMIN
        |--------------------------------------------------------------------------
        |
        | Support Admin system-level role।
        |
        | তার নিজের কোনো institution বাধ্যতামূলক নয়।
        |
        */

        if ($roleSlug === 'support_admin') {

            /*
            |--------------------------------------------------------------------------
            | Support Admin → System Mode
            |--------------------------------------------------------------------------
            |
            | Support Admin-এর ক্ষেত্রে কোনো stale institution context
            | রাখা হবে না।
            |
            */

            $this->clearInstitutionContext();

            session([
                'institution_mode' => false,
            ]);


            return $next($request);
        }


        /*
        |--------------------------------------------------------------------------
        | INSTITUTION BASED ROLES
        |--------------------------------------------------------------------------
        */

        $institutionRoles = [
            'user_admin',
            'user',
            'student',
        ];


        /*
        |--------------------------------------------------------------------------
        | Institution Role Check
        |--------------------------------------------------------------------------
        */

        if (in_array($roleSlug, $institutionRoles, true)) {

            /*
            |--------------------------------------------------------------------------
            | Institution Required
            |--------------------------------------------------------------------------
            */

            if (empty($user->institution_id)) {

                abort(
                    403,
                    'আপনার সাথে কোনো প্রতিষ্ঠান সংযুক্ত নেই।'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | ALWAYS USE USER'S OWN INSTITUTION
            |--------------------------------------------------------------------------
            |
            | User Admin / User / Student session বা URL থেকে
            | অন্য institution select করতে পারবে না।
            |
            */

            $institution = Madrasa::find(
                $user->institution_id
            );


            /*
            |--------------------------------------------------------------------------
            | Institution Not Found
            |--------------------------------------------------------------------------
            */

            if (!$institution) {

                $this->clearInstitutionContext();

                abort(
                    403,
                    'আপনার নির্ধারিত প্রতিষ্ঠানটি পাওয়া যাচ্ছে না।'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Institution Status
            |--------------------------------------------------------------------------
            */

            if ((int) $institution->status !== 1) {

                $this->clearInstitutionContext();

                abort(
                    403,
                    'আপনার প্রতিষ্ঠানটি বর্তমানে নিষ্ক্রিয়।'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Bind User's Institution
            |--------------------------------------------------------------------------
            */

            $this->bindInstitution($institution);


            return $next($request);
        }


        /*
        |--------------------------------------------------------------------------
        | UNKNOWN ROLE
        |--------------------------------------------------------------------------
        */

        abort(
            403,
            'আপনার Role অনুযায়ী এই সিস্টেমে প্রবেশের অনুমতি নেই।'
        );
    }


    /**
     * --------------------------------------------------------------------------
     * Bind Institution Context
     * --------------------------------------------------------------------------
     */

protected function bindInstitution(
    Madrasa $institution
): void {

    /*
    |--------------------------------------------------------------------------
    | Application Container
    |--------------------------------------------------------------------------
    */

    app()->instance(
        'institution_id',
        $institution->id
    );


    /*
    |--------------------------------------------------------------------------
    | Session
    |--------------------------------------------------------------------------
    */

    session([

        /*
        |--------------------------------------------------------------------------
        | Canonical Institution ID
        |--------------------------------------------------------------------------
        */

        'active_institution_id' =>
            $institution->id,


        /*
        |--------------------------------------------------------------------------
        | Institution Information
        |--------------------------------------------------------------------------
        */

        'institution_name' =>
            $institution->name,


        'institution_name_bn' =>
            $institution->name_bn,


        'institution_logo' =>
            $institution->logo,


        /*
        |--------------------------------------------------------------------------
        | Institution Mode
        |--------------------------------------------------------------------------
        */

        'institution_mode' =>
            true,

    ]);
}

/**
 * --------------------------------------------------------------------------
 * Clear Institution Context
 * --------------------------------------------------------------------------
 */
protected function clearInstitutionContext(): void
{
    /*
    |--------------------------------------------------------------------------
    | Application Container
    |--------------------------------------------------------------------------
    */

    if (app()->bound('institution_id')) {

        app()->forgetInstance(
            'institution_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Session
    |--------------------------------------------------------------------------
    */

    session()->forget([

        'active_institution_id',

        'institution_name',

        'institution_name_bn',

        'institution_logo',

        'institution_mode',

    ]);
}
}