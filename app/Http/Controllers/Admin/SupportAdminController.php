<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SupportAdminController extends Controller
{
    /**
     * --------------------------------------------------------------------------
     * Support Admin List
     * --------------------------------------------------------------------------
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Support Admin Role
        |--------------------------------------------------------------------------
        */

        $supportAdminRole = Role::where(
            'slug',
            'support_admin'
        )->first();

        /*
        |--------------------------------------------------------------------------
        | Role না থাকলে
        |--------------------------------------------------------------------------
        */

        if (!$supportAdminRole) {

            return back()->with(
                'error',
                'Support Admin Role পাওয়া যায়নি।'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Query
        |--------------------------------------------------------------------------
        */

        $query = User::with([
            'role',
            'creator',
            'permissions',
        ])
        ->where(
            'role_id',
            $supportAdminRole->id
        );


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'name_bn',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'phone',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'username',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'email',
                    'like',
                    "%{$search}%"
                );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('status') &&
            in_array(
                $request->status,
                ['0', '1'],
                true
            )
        ) {

            $query->where(
                'status',
                (int) $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $supportAdmins = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalSupportAdmins = User::where(
            'role_id',
            $supportAdminRole->id
        )->count();


        $activeSupportAdmins = User::where(
            'role_id',
            $supportAdminRole->id
        )
        ->where(
            'status',
            1
        )
        ->count();


        return view(
            'admin.support_admins.index',
            compact(
                'supportAdmins',
                'totalSupportAdmins',
                'activeSupportAdmins'
            )
        );
    }


    /**
     * --------------------------------------------------------------------------
     * Create Support Admin
     * --------------------------------------------------------------------------
     */
    public function create()
    {
        /*
        |--------------------------------------------------------------------------
        | Support Admin Role
        |--------------------------------------------------------------------------
        */

        $supportAdminRole = Role::where(
            'slug',
            'support_admin'
        )->first();


        if (!$supportAdminRole) {

            return back()->with(
                'error',
                'Support Admin Role পাওয়া যায়নি।'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        |
        | systemPanel relation ব্যবহার করে
        | module/page অনুযায়ী permission সাজানো হবে।
        |
        */

        $permissions = Permission::with(
            'systemPanel'
        )
        ->where(
            'is_active',
            true
        )
        ->orderBy(
            'system_panel_id'
        )
        ->orderBy(
            'serial'
        )
        ->get();


        /*
        |--------------------------------------------------------------------------
        | Group Permissions
        |--------------------------------------------------------------------------
        */

        $permissionGroups = $permissions->groupBy(
            function ($permission) {

                return optional(
                    $permission->systemPanel
                )->slug ?? 'other';

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Module Groups
        |--------------------------------------------------------------------------
        */

        $modules = $permissions->groupBy(
            'module'
        );


        return view(
            'admin.support_admins.create',
            compact(
                'supportAdminRole',
                'permissions',
                'permissionGroups',
                'modules'
            )
        );
    }


    /**
     * --------------------------------------------------------------------------
     * Store Support Admin
     * --------------------------------------------------------------------------
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'name_bn' => [
                'nullable',
                'string',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
                'unique:users,phone',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],

            'status' => [
                'required',
                'boolean',
            ],

            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Support Admin Role
        |--------------------------------------------------------------------------
        */

        $supportAdminRole = Role::where(
            'slug',
            'support_admin'
        )->first();


        if (!$supportAdminRole) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Support Admin Role পাওয়া যায়নি।'
                );
        }


        DB::beginTransaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | Generate Username
            |--------------------------------------------------------------------------
            */

            $username = $this->generateUsername(
                $request->name,
                $request->name_bn
            );


            /*
            |--------------------------------------------------------------------------
            | Generate Institution User ID
            |--------------------------------------------------------------------------
            |
            | Support Admin institution-এর অধীন নয়।
            | তাই এখানে system-level ID generate হবে।
            |
            */

            $institutionUserId =
                $this->generateSupportAdminId();


            /*
            |--------------------------------------------------------------------------
            | Create User
            |--------------------------------------------------------------------------
            */

            $user = User::create([

                'institution_id' => null,

                'role_id' =>
                    $supportAdminRole->id,

                'institution_user_id' =>
                    $institutionUserId,

                'username' =>
                    $username,

                'name' =>
                    $request->name,

                'name_bn' =>
                    $request->name_bn,

                'phone' =>
                    $request->phone,

                'email' =>
                    $request->email,

                'password' =>
                    Hash::make(
                        $request->password
                    ),

                'status' =>
                    (int) $request->status,

                'created_by' =>
                    auth()->id(),

            ]);


            /*
            |--------------------------------------------------------------------------
            | Assign Permissions
            |--------------------------------------------------------------------------
            */

            $permissionIds =
                collect(
                    $request->input(
                        'permissions',
                        []
                    )
                )
                ->map(
                    fn ($id) => (int) $id
                )
                ->unique()
                ->values();


            foreach ($permissionIds as $permissionId) {

                $user->permissions()->syncWithoutDetaching([

                    $permissionId => [
                        'allow' => true,
                    ],

                ]);
            }


            DB::commit();


            return redirect()
                ->route(
                    'dashboard.support-admins.index'
                )
                ->with(
                    'success',
                    'সাপোর্ট অ্যাডমিন সফলভাবে তৈরি হয়েছে।'
                );


        } catch (\Throwable $e) {

            DB::rollBack();


            return back()
                ->withInput()
                ->with(
                    'error',
                    'সাপোর্ট অ্যাডমিন তৈরি করতে সমস্যা হয়েছে: ' .
                    $e->getMessage()
                );
        }
    }


    /**
     * --------------------------------------------------------------------------
     * Edit Support Admin
     * --------------------------------------------------------------------------
     */
    public function edit($id)
    {
        /*
        |--------------------------------------------------------------------------
        | Support Admin
        |--------------------------------------------------------------------------
        */

        $supportAdmin = User::with([
            'role',
            'permissions',
        ])->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        */

        if (!$supportAdmin->is_support_admin) {

            abort(
                404,
                'সাপোর্ট অ্যাডমিন পাওয়া যায়নি।'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = Permission::with(
            'systemPanel'
        )
        ->where(
            'is_active',
            true
        )
        ->orderBy(
            'system_panel_id'
        )
        ->orderBy(
            'serial'
        )
        ->get();


        /*
        |--------------------------------------------------------------------------
        | Permission Groups
        |--------------------------------------------------------------------------
        */

        $permissionGroups = $permissions->groupBy(
            function ($permission) {

                return optional(
                    $permission->systemPanel
                )->slug ?? 'other';

            }
        );


        $modules = $permissions->groupBy(
            'module'
        );


        /*
        |--------------------------------------------------------------------------
        | Selected Permission IDs
        |--------------------------------------------------------------------------
        */

        $selectedPermissions =
            $supportAdmin
                ->permissions
                ->filter(
                    fn ($permission) =>
                        (bool) $permission->pivot->allow
                )
                ->pluck('id')
                ->map(
                    fn ($id) => (int) $id
                )
                ->values()
                ->toArray();


        return view(
            'admin.support_admins.edit',
            compact(
                'supportAdmin',
                'permissions',
                'permissionGroups',
                'modules',
                'selectedPermissions'
            )
        );
    }


    /**
     * --------------------------------------------------------------------------
     * Update Support Admin
     * --------------------------------------------------------------------------
     */
    public function update(
        Request $request,
        $id
    ) {

        /*
        |--------------------------------------------------------------------------
        | Find User
        |--------------------------------------------------------------------------
        */

        $supportAdmin = User::with(
            'role'
        )->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        */

        if (!$supportAdmin->is_support_admin) {

            abort(
                404,
                'সাপোর্ট অ্যাডমিন পাওয়া যায়নি।'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'name_bn' => [
                'nullable',
                'string',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',

                Rule::unique(
                    'users',
                    'phone'
                )->ignore(
                    $supportAdmin->id
                ),
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',

                Rule::unique(
                    'users',
                    'email'
                )->ignore(
                    $supportAdmin->id
                ),
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed',
            ],

            'status' => [
                'required',
                'boolean',
            ],

            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],

        ]);


        DB::beginTransaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | Update Basic Information
            |--------------------------------------------------------------------------
            */

            $supportAdmin->name =
                $request->name;

            $supportAdmin->name_bn =
                $request->name_bn;

            $supportAdmin->phone =
                $request->phone;

            $supportAdmin->email =
                $request->email;

            $supportAdmin->status =
                (int) $request->status;

            $supportAdmin->updated_by =
                auth()->id();


            /*
            |--------------------------------------------------------------------------
            | Password
            |--------------------------------------------------------------------------
            */

            if ($request->filled('password')) {

                $supportAdmin->password =
                    Hash::make(
                        $request->password
                    );
            }


            $supportAdmin->save();


            /*
            |--------------------------------------------------------------------------
            | Sync Permissions
            |--------------------------------------------------------------------------
            */

            $permissionIds =
                collect(
                    $request->input(
                        'permissions',
                        []
                    )
                )
                ->map(
                    fn ($id) => (int) $id
                )
                ->unique()
                ->values();


            $syncData = [];


            foreach ($permissionIds as $permissionId) {

                $syncData[$permissionId] = [

                    'allow' => true,

                ];
            }


            $supportAdmin
                ->permissions()
                ->sync(
                    $syncData
                );


            DB::commit();


            return redirect()
                ->route(
                    'dashboard.support-admins.index'
                )
                ->with(
                    'success',
                    'সাপোর্ট অ্যাডমিন সফলভাবে আপডেট হয়েছে।'
                );


        } catch (\Throwable $e) {

            DB::rollBack();


            return back()
                ->withInput()
                ->with(
                    'error',
                    'আপডেট করতে সমস্যা হয়েছে: ' .
                    $e->getMessage()
                );
        }
    }


    /**
     * --------------------------------------------------------------------------
     * Delete Support Admin
     * --------------------------------------------------------------------------
     */
    public function destroy($id)
    {
        try {

            $supportAdmin = User::with(
                'role'
            )->findOrFail($id);


            /*
            |--------------------------------------------------------------------------
            | Security
            |--------------------------------------------------------------------------
            */

            if (!$supportAdmin->is_support_admin) {

                return back()->with(
                    'error',
                    'এই User সাপোর্ট অ্যাডমিন নয়।'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Cannot Delete Own Account
            |--------------------------------------------------------------------------
            */

            if (
                auth()->id() ===
                $supportAdmin->id
            ) {

                return back()->with(
                    'error',
                    'নিজের অ্যাকাউন্ট ডিলিট করা যাবে না।'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Delete
            |--------------------------------------------------------------------------
            */

            $supportAdmin->permissions()->detach();

            $supportAdmin->delete();


            return redirect()
                ->route(
                    'dashboard.support-admins.index'
                )
                ->with(
                    'success',
                    'সাপোর্ট অ্যাডমিন সফলভাবে ডিলিট হয়েছে।'
                );


        } catch (\Throwable $e) {

            return back()->with(
                'error',
                'ডিলিট করতে সমস্যা হয়েছে: ' .
                $e->getMessage()
            );
        }
    }


    /**
     * --------------------------------------------------------------------------
     * Toggle Status
     * --------------------------------------------------------------------------
     */
    public function toggleStatus($id)
    {
        try {

            $supportAdmin = User::with(
                'role'
            )->findOrFail($id);


            if (!$supportAdmin->is_support_admin) {

                return back()->with(
                    'error',
                    'এই User সাপোর্ট অ্যাডমিন নয়।'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Cannot Disable Own Account
            |--------------------------------------------------------------------------
            */

            if (
                auth()->id() ===
                $supportAdmin->id
            ) {

                return back()->with(
                    'error',
                    'নিজের অ্যাকাউন্ট নিষ্ক্রিয় করা যাবে না।'
                );
            }


            $supportAdmin->status =
                $supportAdmin->status
                ? 0
                : 1;


            $supportAdmin->updated_by =
                auth()->id();


            $supportAdmin->save();


            return back()->with(
                'success',
                $supportAdmin->status
                    ? 'সাপোর্ট অ্যাডমিন সক্রিয় করা হয়েছে।'
                    : 'সাপোর্ট অ্যাডমিন নিষ্ক্রিয় করা হয়েছে।'
            );


        } catch (\Throwable $e) {

            return back()->with(
                'error',
                'স্ট্যাটাস পরিবর্তন করতে সমস্যা হয়েছে: ' .
                $e->getMessage()
            );
        }
    }


    /**
     * --------------------------------------------------------------------------
     * Generate Username
     * --------------------------------------------------------------------------
     */
    private function generateUsername(
        $nameEn = null,
        $nameBn = null
    ) {

        $base = preg_replace(
            '/[^a-zA-Z0-9]/',
            '',
            strtolower(
                $nameEn ?? ''
            )
        );


        if (empty($base)) {

            $base =
                'support' .
                substr(
                    md5(
                        ($nameBn ?? '') .
                        microtime(true)
                    ),
                    0,
                    6
                );
        }


        $username = $base;

        $counter = 1;


        while (
            User::where(
                'username',
                $username
            )->exists()
        ) {

            $username =
                $base .
                $counter;

            $counter++;
        }


        return $username;
    }


    /**
     * --------------------------------------------------------------------------
     * Generate Support Admin ID
     * --------------------------------------------------------------------------
     *
     * Support Admin institution-এর অধীন নয়।
     *
     * Format:
     *
     * 501
     * 502
     * 503
     * ...
     *
     * --------------------------------------------------------------------------
     */
    private function generateSupportAdminId()
    {
        $lastUser = User::whereHas(
            'role',
            function ($query) {

                $query->where(
                    'slug',
                    'support_admin'
                );

            }
        )
        ->whereNotNull(
            'institution_user_id'
        )
        ->orderByRaw(
            'CAST(institution_user_id AS UNSIGNED) DESC'
        )
        ->first();


        if ($lastUser) {

            return (int)
                $lastUser->institution_user_id + 1;
        }


        return 501;
    }
}