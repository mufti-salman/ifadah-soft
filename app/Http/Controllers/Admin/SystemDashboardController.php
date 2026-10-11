<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Madrasa;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Exam;
use App\Models\Result;

class SystemDashboardController extends Controller
{
    /**
     * System Dashboard
     *
     * This dashboard is completely separate
     * from Institution Dashboard.
     *
     * No institution context is used here.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Total Institutions
        |--------------------------------------------------------------------------
        */

        $institutionsCount = Madrasa::count();

        $activeInstitutionsCount = Madrasa::where('status', 1)->count();

        $inactiveInstitutionsCount = Madrasa::where('status', 0)->count();


        /*
        |--------------------------------------------------------------------------
        | System Users
        |--------------------------------------------------------------------------
        */

        $totalUsers = User::count();


        /*
        |--------------------------------------------------------------------------
        | System Admins
        |--------------------------------------------------------------------------
        */

        $superAdminsCount = User::whereHas('role', function ($query) {
            $query->where('slug', 'super_admin');
        })->count();

        $supportAdminsCount = User::whereHas('role', function ($query) {
            $query->where('slug', 'support_admin');
        })->count();


        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        |
        | These are system-wide numbers.
        | Institution Dashboard will later use institution-wise data.
        |
        */

        $studentsCount = Student::count();


        /*
        |--------------------------------------------------------------------------
        | Teachers
        |--------------------------------------------------------------------------
        */

        $teachersCount = Teacher::count();


        /*
        |--------------------------------------------------------------------------
        | Exams
        |--------------------------------------------------------------------------
        */

        $examsCount = class_exists(Exam::class)
            ? Exam::count()
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Results
        |--------------------------------------------------------------------------
        */

        $resultsCount = class_exists(Result::class)
            ? Result::count()
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Recent Institutions
        |--------------------------------------------------------------------------
        */

        $recentInstitutions = Madrasa::latest()
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Dashboard View
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.system.dashboard',
            compact(
                'institutionsCount',
                'activeInstitutionsCount',
                'inactiveInstitutionsCount',
                'totalUsers',
                'superAdminsCount',
                'supportAdminsCount',
                'studentsCount',
                'teachersCount',
                'examsCount',
                'resultsCount',
                'recentInstitutions'
            )
        );
    }
}