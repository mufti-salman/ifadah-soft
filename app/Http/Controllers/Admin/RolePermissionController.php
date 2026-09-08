<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Madrasa;
use App\Models\SystemPanel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Institution Permission Manager
    |--------------------------------------------------------------------------
    |
    | এই Controller-এর কাজ:
    |
    | Super Admin
    |      ↓
    | Institution নির্বাচন
    |      ↓
    | Institution-এর জন্য Permission নির্বাচন
    |      ↓
    | institution_permissions table-এ save
    |
    |--------------------------------------------------------------------------
    */


    /**
     * ----------------------------------------------------------------------
     * Permission Manager Page
     * ----------------------------------------------------------------------
     */
    public function index(Request $request)
    {
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
        | Only Super Admin
        |--------------------------------------------------------------------------
        |
        | Institution-এর global permission শুধুমাত্র Super Admin
        | manage করতে পারবে।
        |
        */

        if (!$user->is_super_admin) {
            abort(
                403,
                'আপনার এই পেজে প্রবেশের অনুমতি নেই।'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Institutions
        |--------------------------------------------------------------------------
        */

        $institutions = Madrasa::query()
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Selected Institution
        |--------------------------------------------------------------------------
        */

        $selectedInstitution = null;

        if ($request->filled('institution')) {

            $selectedInstitution = Madrasa::with([
                'permissions' => function ($query) {
                    $query->where(
                        'permissions.is_active',
                        true
                    );
                }
            ])
            ->findOrFail(
                $request->integer('institution')
            );

        } elseif ($institutions->isNotEmpty()) {

            $selectedInstitution = Madrasa::with([
                'permissions' => function ($query) {
                    $query->where(
                        'permissions.is_active',
                        true
                    );
                }
            ])
            ->orderBy('name')
            ->first();
        }


        /*
        |--------------------------------------------------------------------------
        | System Panels + Active Permissions
        |--------------------------------------------------------------------------
        |
        | শুধু Active permission দেখানো হবে।
        |
        */

        $panels = SystemPanel::query()
            ->where('is_active', true)
            ->with([
                'permissions' => function ($query) {

                    $query
                        ->where(
                            'permissions.is_active',
                            true
                        )
                        ->orderBy('module')
                        ->orderBy('serial')
                        ->orderBy('id');
                }
            ])
            ->orderBy('serial')
            ->orderBy('id')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Selected Permission IDs
        |--------------------------------------------------------------------------
        */

        $selectedPermissionIds = $selectedInstitution
            ? $selectedInstitution
                ->permissions
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray()
            : [];


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.role-permissions.index',
            [
                'institutions' => $institutions,

                'selectedInstitution' => $selectedInstitution,

                'panels' => $panels,

                'selectedPermissionIds' => $selectedPermissionIds,
            ]
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Update Institution Permissions
     * ----------------------------------------------------------------------
     */
    public function update(Request $request)
    {
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
        | Only Super Admin
        |--------------------------------------------------------------------------
        */

        if (!$user->is_super_admin) {
            abort(
                403,
                'আপনার এই কাজটি করার অনুমতি নেই।'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'institution_id' => [
                'required',
                'integer',
                'exists:madrasas,id',
            ],

            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*' => [
                'integer',
                'distinct',
                'exists:permissions,id',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Permission IDs
        |--------------------------------------------------------------------------
        */

        $permissionIds = collect(
            $validated['permissions'] ?? []
        )
        ->map(
            fn ($id) => (int) $id
        )
        ->unique()
        ->values();


        /*
        |--------------------------------------------------------------------------
        | Only Active Permissions
        |--------------------------------------------------------------------------
        |
        | Inactive permission কখনো institution-এ assign করা যাবে না।
        |
        */

        $activePermissionIds = \App\Models\Permission::query()
            ->whereIn(
                'id',
                $permissionIds
            )
            ->where(
                'is_active',
                true
            )
            ->pluck('id');


        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $validated,
            $activePermissionIds
        ) {

            $institution = Madrasa::findOrFail(
                $validated['institution_id']
            );


            /*
            |--------------------------------------------------------------------------
            | Replace Permissions
            |--------------------------------------------------------------------------
            |
            | আগের permission remove হবে এবং নতুন selected
            | permission বসবে।
            |
            */

            $institution
                ->permissions()
                ->sync(
                    $activePermissionIds->all()
                );
        });


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'dashboard.role-permissions.index',
                [
                    'institution' =>
                        $validated['institution_id'],
                ]
            )
            ->with(
                'success',
                'প্রতিষ্ঠানের অনুমতিগুলো সফলভাবে আপডেট হয়েছে।'
            );
    }
}