<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Exam;
use App\Models\Result;
use App\Models\FeeGroup;
use App\Models\FeeCollection;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Active Institution
        |--------------------------------------------------------------------------
        |
        | Super Admin:
        |   session('active_institution_id')
        |
        | Institution Users:
        |   user.institution_id
        |
        */

        if ($user->is_super_admin) {
            $institutionId = session('active_institution_id');
        } else {
            $institutionId = $user->institution_id;
        }

        /*
        |--------------------------------------------------------------------------
        | Institution Required
        |--------------------------------------------------------------------------
        */

        if (!$institutionId) {
            abort(
                403,
                'কোনো প্রতিষ্ঠান নির্বাচন করা হয়নি।'
            );
        }

        $institutionId = (int) $institutionId;


        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        $studentsCount = User::where(
            'institution_id',
            $institutionId
        )
            ->whereHas('role', function ($query) {
                $query->where('slug', 'student');
            })
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Teachers
        |--------------------------------------------------------------------------
        |
        | Teacher আলাদা Role নয়।
        | Teacher model-এর institution_id ব্যবহার করা হচ্ছে।
        |
        */

        $teachersCount = Teacher::where(
            'institution_id',
            $institutionId
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Current Institution
        |--------------------------------------------------------------------------
        */

        $madrasasCount = 1;


        /*
        |--------------------------------------------------------------------------
        | Students Table
        |--------------------------------------------------------------------------
        */

        $studentsTableCount = Student::where(
            'institution_id',
            $institutionId
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Teachers Table
        |--------------------------------------------------------------------------
        */

        $teachersTableCount = Teacher::where(
            'institution_id',
            $institutionId
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Exams
        |--------------------------------------------------------------------------
        */

        $examsCount = Exam::where(
            'institution_id',
            $institutionId
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Results
        |--------------------------------------------------------------------------
        |
        | results table-এ institution_id নেই।
        |
        | Result -> student_id -> Student -> institution_id
        |
        */

        $resultsCount = Result::whereHas(
            'student',
            function ($query) use ($institutionId) {
                $query->where(
                    'institution_id',
                    $institutionId
                );
            }
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Fee Groups
        |--------------------------------------------------------------------------
        */

        $totalFees = FeeGroup::where(
            'institution_id',
            $institutionId
        )->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | Paid Fees
        |--------------------------------------------------------------------------
        */

        $totalPaid = FeeCollection::where(
            'institution_id',
            $institutionId
        )->sum('paid_amount');


        /*
        |--------------------------------------------------------------------------
        | Due Fees
        |--------------------------------------------------------------------------
        */

        $totalDue = FeeCollection::where(
            'institution_id',
            $institutionId
        )->sum('due_amount');


            /*
            |--------------------------------------------------------------------------
            | Institution Subscription
            |--------------------------------------------------------------------------
            */

            $currentSubscription = Subscription::with('plan')
                ->where('institution_id', $institutionId)
                ->orderByRaw("
                    CASE
                        WHEN status = 'trialing'
                            AND (ends_at IS NULL OR ends_at > CURRENT_TIMESTAMP)
                            THEN 0
                        WHEN status = 'active'
                            AND (ends_at IS NULL OR ends_at > CURRENT_TIMESTAMP)
                            THEN 1
                        ELSE 2
                    END
                ")
                ->latest('id')
                ->first();

            $subscriptionRemainingDays = null;

            if (
                $currentSubscription
                && $currentSubscription->ends_at
                && in_array(
                    $currentSubscription->status,
                    ['active', 'trialing'],
                    true
                )
            ) {
                $subscriptionRemainingDays = max(
                    0,
                    (int) now()->startOfDay()->diffInDays(
                        $currentSubscription->ends_at->copy()->startOfDay(),
                        false
                    )
                );
            }

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.dashboard.home',
            compact(
                'studentsCount',
                'teachersCount',
                'madrasasCount',
                'studentsTableCount',
                'teachersTableCount',
                'examsCount',
                'resultsCount',
                'totalFees',
                'totalPaid',
                'totalDue',
                'currentSubscription',
                'subscriptionRemainingDays',
            )
        );
    }
}