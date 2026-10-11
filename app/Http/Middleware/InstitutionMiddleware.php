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
     * System Roles:
     * - super_admin
     * - support_admin
     *
     * Institution Roles:
     * - user_admin
     * - user
     * - student
     *
     * Super Admin:
     * - No active_institution_id = System Mode
     * - active_institution_id = Institution Mode
     *
     * Institution users:
     * - Always use their own user.institution_id
     * - Never trust active_institution_id from session
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
        | Super Admin দুইটি mode-এ কাজ করতে পারে:
        |
        | 1. System Mode
        | 2. Institution Mode
        |
        */

        if ($roleSlug === 'super_admin') {

            /*
            |--------------------------------------------------------------------------
            | SYSTEM MODE
            |--------------------------------------------------------------------------
            |
            | active_institution_id না থাকলে Super Admin সম্পূর্ণ System
            | Dashboard-এর context-এ থাকবে।
            |
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
            | Get Selected Institution
            |--------------------------------------------------------------------------
            */

            $activeInstitutionId = session(
                'active_institution_id'
            );


            /*
            |--------------------------------------------------------------------------
            | Validate Institution ID
            |--------------------------------------------------------------------------
            */

            if (
                !is_numeric($activeInstitutionId) ||
                (int) $activeInstitutionId <= 0
            ) {

                $this->clearInstitutionContext();

                return redirect()
                    ->route('dashboard.system.index')
                    ->with(
                        'error',
                        'নির্বাচিত প্রতিষ্ঠানের তথ্য সঠিক নয়।'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Load Institution
            |--------------------------------------------------------------------------
            */

            $institution = Madrasa::query()
                ->whereKey((int) $activeInstitutionId)
                ->first();


            /*
            |--------------------------------------------------------------------------
            | Institution Not Found
            |--------------------------------------------------------------------------
            */

            if (!$institution) {

                $this->clearInstitutionContext();

                return redirect()
                    ->route(
                        'dashboard.system.institutions.index'
                    )
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
                    ->route(
                        'dashboard.system.institutions.index'
                    )
                    ->with(
                        'error',
                        'নির্বাচিত প্রতিষ্ঠানটি বর্তমানে নিষ্ক্রিয়।'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Bind Selected Institution
            |--------------------------------------------------------------------------
            */

            $this->bindInstitution(
                $institution
            );

            return $next($request);
        }


        /*
        |--------------------------------------------------------------------------
        | SUPPORT ADMIN
        |--------------------------------------------------------------------------
        |
        | Support Admin হলো System-level role।
        |
        | তাই Support Admin কখনো কোনো Madrasa context inherit করবে না।
        |
        */

        if ($roleSlug === 'support_admin') {

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

        if (in_array(
            $roleSlug,
            $institutionRoles,
            true
        )) {

            /*
            |--------------------------------------------------------------------------
            | Institution Required
            |--------------------------------------------------------------------------
            */

            if (empty($user->institution_id)) {

                $this->clearInstitutionContext();

                abort(
                    403,
                    'আপনার সাথে কোনো প্রতিষ্ঠান সংযুক্ত নেই।'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | IMPORTANT SECURITY RULE
            |--------------------------------------------------------------------------
            |
            | Institution users-এর জন্য session-এর
            | active_institution_id কখনো trusted হবে না।
            |
            | Database-এর user.institution_id-ই canonical source।
            |
            */

            $userInstitutionId = (int) $user->institution_id;


            /*
            |--------------------------------------------------------------------------
            | Load User's Own Institution
            |--------------------------------------------------------------------------
            */

            $institution = Madrasa::query()
                ->whereKey($userInstitutionId)
                ->first();


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
            | FORCE OWN INSTITUTION
            |--------------------------------------------------------------------------
            |
            | User-এর session-এ অন্য institution-এর ID থাকলেও
            | সেটা overwrite হবে।
            |
            */

            $this->bindInstitution(
                $institution
            );

            return $next($request);
        }


        /*
        |--------------------------------------------------------------------------
        | UNKNOWN ROLE
        |--------------------------------------------------------------------------
        */

        $this->clearInstitutionContext();

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
            'active_institution_id' => $institution->id,

            'institution_name' => $institution->name,

            'institution_name_bn' => $institution->name_bn,

            'institution_logo' => $institution->logo,

            'institution_mode' => true,
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
