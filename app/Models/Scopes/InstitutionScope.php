<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class InstitutionScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Determine Active Institution
        |--------------------------------------------------------------------------
        */

        $institutionId = session('active_institution_id')
            ?: session('institution_id')
            ?: $user->active_institution_id
            ?: $user->institution_id;

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        |
        | Super Admin may need system-wide access.
        | Do not restrict their queries here.
        |
        */

        if ($user->is_super_admin) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Institution Isolation
        |--------------------------------------------------------------------------
        */

        if (!$institutionId) {
            $builder->whereRaw('1 = 0');

            return;
        }

        $builder->where(
            $model->qualifyColumn('institution_id'),
            $institutionId
        );
    }
}