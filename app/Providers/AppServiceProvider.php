<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permission Gate
        |--------------------------------------------------------------------------
        |
        | @can('student.create')
        | @can('fee.collect')
        | @can('user.edit')
        |
        | এগুলো User model-এর hasPermission()
        | method ব্যবহার করবে।
        |
        */

        Gate::before(function ($user, $ability) {

            if ($user->hasPermission($ability)) {
                return true;
            }

            return null;
        });


        /*
        |--------------------------------------------------------------------------
        | Permission Blade Directive
        |--------------------------------------------------------------------------
        |
        | Usage:
        |
        | @permission('student.create')
        |     ...
        | @endpermission
        |
        */

        Blade::if('permission', function ($permission) {

            return auth()->check()
                && auth()->user()->hasPermission($permission);

        });


        /*
        |--------------------------------------------------------------------------
        | Panel Blade Directive
        |--------------------------------------------------------------------------
        |
        | Usage:
        |
        | @panel('student')
        |     ...
        | @endpanel
        |
        */

        Blade::if('panel', function ($panel) {

            return auth()->check()
                && auth()->user()->hasPanel($panel);

        });


        /*
        |--------------------------------------------------------------------------
        | Sidebar Permission Directive
        |--------------------------------------------------------------------------
        |
        | Sidebar authenticated users-এর জন্য visible থাকবে।
        |
        | Actual permission check:
        |
        | @permission(...)
        |
        | অথবা
        |
        | @can(...)
        |
        */

        Blade::if('sidebarPermission', function () {

            return auth()->check();

        });
    }
}