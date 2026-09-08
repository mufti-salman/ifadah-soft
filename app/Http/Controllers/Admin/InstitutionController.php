<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Madrasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InstitutionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DISPLAY INSTITUTIONS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        |
        | সব প্রতিষ্ঠান দেখতে পারবে।
        |
        */

        if ($user->is_super_admin) {

            $institutions = Madrasa::with('users')
                ->latest()
                ->get();

        } elseif ($user->is_user_admin) {

            /*
            |--------------------------------------------------------------------------
            | User Admin
            |--------------------------------------------------------------------------
            |
            | শুধু নিজের প্রতিষ্ঠান দেখতে পারবে।
            |
            */

            $institutions = Madrasa::with('users')
                ->where('id', $user->institution_id)
                ->latest()
                ->get();

        } else {

            /*
            |--------------------------------------------------------------------------
            | Other Users
            |--------------------------------------------------------------------------
            |
            | সাধারণ User / Student institution management page
            | দেখতে পারবে না।
            |
            */

            abort(
                403,
                'আপনার প্রতিষ্ঠান ব্যবস্থাপনার অনুমতি নেই।'
            );
        }

        return view(
            'admin.institutions.index',
            compact('institutions')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE NEW INSTITUTION
    |--------------------------------------------------------------------------
    |
    | শুধুমাত্র Super Admin
    |
    */

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

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

            'madrasa_code' => [
                'nullable',
                'string',
                'max:100',
                'unique:madrasas,madrasa_code',
            ],

            'eiin_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'boolean',
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Upload Logo
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('logo')) {

            $validated['logo'] = $request
                ->file('logo')
                ->store(
                    'institutions',
                    'public'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Audit Fields
        |--------------------------------------------------------------------------
        */

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();


        /*
        |--------------------------------------------------------------------------
        | Create Institution
        |--------------------------------------------------------------------------
        */

        Madrasa::create($validated);


        return redirect()
            ->route('dashboard.institutions.index')
            ->with(
                'success',
                'প্রতিষ্ঠান সফলভাবে তৈরি হয়েছে।'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT INSTITUTION
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $user = Auth::user();

        $institution = Madrasa::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Permission
        |--------------------------------------------------------------------------
        */

        if ($user->is_super_admin) {

            // Super Admin সব institution edit করতে পারবে।

        } elseif ($user->is_user_admin) {

            if (
                (int) $user->institution_id !==
                (int) $institution->id
            ) {
                abort(
                    403,
                    'আপনার এই প্রতিষ্ঠানে প্রবেশের অনুমতি নেই।'
                );
            }

        } else {

            abort(
                403,
                'আপনার প্রতিষ্ঠান সম্পাদনার অনুমতি নেই।'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | JSON Response
        |--------------------------------------------------------------------------
        */

        if (request()->expectsJson()) {

            return response()->json([
                'success' => true,
                'data' => $institution,
            ]);
        }


        return view(
            'admin.institutions.edit',
            compact('institution')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE INSTITUTION
    |--------------------------------------------------------------------------
    |
    | Institution master data শুধুমাত্র Super Admin পরিবর্তন করবে।
    |
    */

    public function update(
        Request $request,
        $id
    ) {

        $this->authorizeSuperAdmin();

        $institution = Madrasa::findOrFail($id);


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

            'madrasa_code' => [
                'nullable',
                'string',
                'max:100',

                Rule::unique(
                    'madrasas',
                    'madrasa_code'
                )->ignore($institution->id),
            ],

            'eiin_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'boolean',
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Replace Logo
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('logo')) {

            if (
                $institution->logo &&
                Storage::disk('public')->exists(
                    $institution->logo
                )
            ) {
                Storage::disk('public')->delete(
                    $institution->logo
                );
            }

            $validated['logo'] = $request
                ->file('logo')
                ->store(
                    'institutions',
                    'public'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */

        $validated['updated_by'] = Auth::id();


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $institution->update($validated);


        /*
        |--------------------------------------------------------------------------
        | Update Active Institution Session
        |--------------------------------------------------------------------------
        |
        | Canonical key:
        | active_institution_id
        |
        */

        if (
            (int) session('active_institution_id') ===
            (int) $institution->id
        ) {

            session([
                'institution_name' =>
                    $institution->name,

                'institution_logo' =>
                    $institution->logo,
            ]);
        }


        return redirect()
            ->route(
                'dashboard.institutions.index'
            )
            ->with(
                'success',
                'প্রতিষ্ঠানের তথ্য সফলভাবে আপডেট হয়েছে।'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE INSTITUTION
    |--------------------------------------------------------------------------
    |
    | শুধুমাত্র Super Admin
    |
    */

    public function destroy($id)
    {
        $this->authorizeSuperAdmin();

        $institution = Madrasa::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Prevent Current Institution Delete
        |--------------------------------------------------------------------------
        */

        if (
            (int) session('active_institution_id') ===
            (int) $institution->id
        ) {

            return redirect()
                ->route(
                    'dashboard.institutions.index'
                )
                ->with(
                    'error',
                    'বর্তমানে নির্বাচিত প্রতিষ্ঠান ডিলিট করা যাবে না। আগে প্রতিষ্ঠান থেকে বের হয়ে আসুন।'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Logo
        |--------------------------------------------------------------------------
        */

        if (
            $institution->logo &&
            Storage::disk('public')->exists(
                $institution->logo
            )
        ) {

            Storage::disk('public')->delete(
                $institution->logo
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Institution
        |--------------------------------------------------------------------------
        */

        $institution->delete();


        return redirect()
            ->route(
                'dashboard.institutions.index'
            )
            ->with(
                'success',
                'প্রতিষ্ঠান সফলভাবে ডিলিট হয়েছে।'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | SWITCH INSTITUTION
    |--------------------------------------------------------------------------
    |
    | শুধুমাত্র Super Admin
    |
    */

    public function switchInstitution(
        Request $request
    ) {

        $this->authorizeSuperAdmin();


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

        ]);


        /*
        |--------------------------------------------------------------------------
        | Find Institution
        |--------------------------------------------------------------------------
        */

        $institution = Madrasa::findOrFail(
            $validated['institution_id']
        );


        /*
        |--------------------------------------------------------------------------
        | Status Check
        |--------------------------------------------------------------------------
        */

        if ((int) $institution->status !== 1) {

            return redirect()
                ->route(
                    'dashboard.institutions.index'
                )
                ->with(
                    'error',
                    'এই প্রতিষ্ঠানটি বর্তমানে নিষ্ক্রিয়।'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Set Active Institution
        |--------------------------------------------------------------------------
        */

        $this->setInstitutionContext(
            $institution
        );


        return redirect()
            ->route('dashboard.index')
            ->with(
                'success',
                $institution->name .
                ' প্রতিষ্ঠানে সফলভাবে প্রবেশ করা হয়েছে।'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ENTER INSTITUTION
    |--------------------------------------------------------------------------
    |
    | শুধুমাত্র Super Admin
    |
    */

    public function enter($id)
    {
        $this->authorizeSuperAdmin();


        /*
        |--------------------------------------------------------------------------
        | Find Institution
        |--------------------------------------------------------------------------
        */

        $institution = Madrasa::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if ((int) $institution->status !== 1) {

            return redirect()
                ->route(
                    'dashboard.institutions.index'
                )
                ->with(
                    'error',
                    'এই প্রতিষ্ঠানটি বর্তমানে নিষ্ক্রিয়।'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Set Institution Context
        |--------------------------------------------------------------------------
        */

        $this->setInstitutionContext(
            $institution
        );


        return redirect()
            ->route('dashboard.index')
            ->with(
                'success',
                $institution->name .
                ' প্রতিষ্ঠানে সফলভাবে প্রবেশ করা হয়েছে।'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EXIT INSTITUTION
    |--------------------------------------------------------------------------
    |
    | Super Admin institution mode থেকে বের হবে।
    |
    */

    /**
 * ----------------------------------------------------------
 * Institution Information
 * ----------------------------------------------------------
 */
public function info()
{
    $user = auth()->user();

    $institution = $user->activeInstitution;

    if (!$institution) {
        return redirect()
            ->route('dashboard.index')
            ->with(
                'error',
                'কোনো প্রতিষ্ঠান নির্বাচন করা হয়নি।'
            );
    }

    return view(
        'admin.institutions.info',
        compact('institution')
    );
}

public function exit()
{
    if (!auth()->user()->is_super_admin) {
        abort(403);
    }

    session()->forget([
        'active_institution_id',
        'institution_name',
        'institution_mode',
    ]);

    if (app()->bound('institution_id')) {
        app()->forgetInstance('institution_id');
    }

    return redirect()
        ->route('dashboard.index')
        ->with(
            'success',
            'প্রতিষ্ঠান মোড থেকে সফলভাবে বের হয়েছেন।'
        );
}


    /*
    |--------------------------------------------------------------------------
    | SET INSTITUTION CONTEXT
    |--------------------------------------------------------------------------
    */

    private function setInstitutionContext(
        Madrasa $institution
    ): void {

        app()->instance(
            'institution_id',
            $institution->id
        );

        session([
            'active_institution_id' =>
                $institution->id,

            'institution_name' =>
                $institution->name,

            'institution_logo' =>
                $institution->logo,

            'institution_mode' =>
                true,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CLEAR INSTITUTION CONTEXT
    |--------------------------------------------------------------------------
    */

    private function clearInstitutionContext(): void
    {
        if (
            app()->bound(
                'institution_id'
            )
        ) {
            app()->forgetInstance(
                'institution_id'
            );
        }

        session()->forget([
            'active_institution_id',
            'institution_name',
            'institution_logo',
            'institution_mode',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN AUTHORIZATION
    |--------------------------------------------------------------------------
    */

    private function authorizeSuperAdmin(): void
    {
        $user = Auth::user();

        if (
            !$user ||
            !$user->is_super_admin
        ) {

            abort(
                403,
                'শুধুমাত্র Super Admin এই কাজটি করতে পারবেন।'
            );
        }
        
    }


    
    
}