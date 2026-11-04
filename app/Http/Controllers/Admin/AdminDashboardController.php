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
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Admission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Institution
        |--------------------------------------------------------------------------
        */

        $institutionId = $user->is_super_admin
            ? session('active_institution_id')
            : $user->institution_id;

        if (!$institutionId) {
            abort(403, 'প্রতিষ্ঠানের তথ্য পাওয়া যায়নি।');
        }

        /*
        |--------------------------------------------------------------------------
        | Academic Year
        |--------------------------------------------------------------------------
        */

        $currentAcademicYear = AcademicYear::where('institution_id', $institutionId)
            ->where('is_current', true)
            ->where('status', 'active')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Academic Statistics
        |--------------------------------------------------------------------------
        */

        $studentsCount = Student::where('institution_id', $institutionId)
            ->count();

        $teachersCount = Teacher::where('institution_id', $institutionId)
            ->count();

        $classesCount = Classes::where('institution_id', $institutionId)
            ->count();

        $sectionsCount = Section::where('institution_id', $institutionId)
            ->count();

        $examsCount = Exam::where('institution_id', $institutionId)
            ->count();

        $resultsCount = Result::whereHas('exam', function ($query) use ($institutionId) {
            $query->where('institution_id', $institutionId);
        })->count();

        /*
        |--------------------------------------------------------------------------
        | Admission Statistics
        |--------------------------------------------------------------------------
        */

        $admissionsCount = Admission::whereHas('student', function ($query) use ($institutionId) {
            $query->where('institution_id', $institutionId);
        })->count();

        $activeAdmissionsCount = Admission::where('status', 'active')
            ->whereHas('student', function ($query) use ($institutionId) {
                $query->where('institution_id', $institutionId);
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Financial Statistics
        |--------------------------------------------------------------------------
        */

        $totalFees = FeeGroup::where('institution_id', $institutionId)
            ->where('is_active', true)
            ->sum('amount');

        $totalPaid = FeeCollection::where('institution_id', $institutionId)
            ->sum('paid_amount');

        $totalDue = FeeCollection::where('institution_id', $institutionId)
            ->sum('due_amount');

        $totalDiscount = FeeCollection::where('institution_id', $institutionId)
            ->sum('discount');

        /*
        |--------------------------------------------------------------------------
        | Current Month Collection
        |--------------------------------------------------------------------------
        */

        $currentMonthPaid = FeeCollection::where('institution_id', $institutionId)
            ->whereMonth('collection_date', now()->month)
            ->whereYear('collection_date', now()->year)
            ->sum('paid_amount');

        /*
        |--------------------------------------------------------------------------
        | Monthly Fee Collection Chart - Last 6 Months
        |--------------------------------------------------------------------------
        */

        $monthlyCollectionLabels = [];
        $monthlyCollectionData = [];

        for ($i = 5; $i >= 0; $i--) {

            $date = now()->subMonths($i);

            $monthlyCollectionLabels[] = $date->translatedFormat('M Y');

            $monthlyCollectionData[] = (float) FeeCollection::where(
                'institution_id',
                $institutionId
            )
                ->whereMonth('collection_date', $date->month)
                ->whereYear('collection_date', $date->year)
                ->sum('paid_amount');
        }

        /*
        |--------------------------------------------------------------------------
        | Fee Collection Statistics
        |--------------------------------------------------------------------------
        */

        $paidCollectionsCount = FeeCollection::where('institution_id', $institutionId)
            ->where('status', 'paid')
            ->count();

        $partialCollectionsCount = FeeCollection::where('institution_id', $institutionId)
            ->where('status', 'partial')
            ->count();

        $dueCollectionsCount = FeeCollection::where('institution_id', $institutionId)
            ->where('status', 'due')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Recent Admissions
        |--------------------------------------------------------------------------
        */

        $recentAdmissions = Admission::with([
            'student.user',
            'class',
            'academicYear'
        ])
            ->whereHas('student', function ($query) use ($institutionId) {
                $query->where('institution_id', $institutionId);
            })
            ->latest('admission_date')
            ->take(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Fee Collections
        |--------------------------------------------------------------------------
        */

        $recentFeeCollections = FeeCollection::with([
            'student.user',
            'collector'
        ])
            ->where('institution_id', $institutionId)
            ->latest('collection_date')
            ->take(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Upcoming Exams
        |--------------------------------------------------------------------------
        */

        $upcomingExams = Exam::where('institution_id', $institutionId)
            ->whereDate('start_date', '>=', today())
            ->orderBy('start_date')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Exam Status Statistics
        |--------------------------------------------------------------------------
        */

        $ongoingExamsCount = Exam::where('institution_id', $institutionId)
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->count();

        $upcomingExamsCount = Exam::where('institution_id', $institutionId)
            ->whereDate('start_date', '>', today())
            ->count();

        $completedExamsCount = Exam::where('institution_id', $institutionId)
            ->whereDate('end_date', '<', today())
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Subscription
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
            $currentSubscription &&
            $currentSubscription->ends_at &&
            in_array($currentSubscription->status, ['active', 'trialing'], true)
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
        | Return Dashboard
        |--------------------------------------------------------------------------
        */

        return view('admin.dashboard.home', compact(

            'institutionId',

            'currentAcademicYear',

            'studentsCount',
            'teachersCount',
            'classesCount',
            'sectionsCount',
            'examsCount',
            'resultsCount',

            'admissionsCount',
            'activeAdmissionsCount',

            'totalFees',
            'totalPaid',
            'totalDue',
            'totalDiscount',
            'currentMonthPaid',

            'monthlyCollectionLabels',
            'monthlyCollectionData',

            'paidCollectionsCount',
            'partialCollectionsCount',
            'dueCollectionsCount',

            'recentAdmissions',
            'recentFeeCollections',

            'upcomingExams',

            'ongoingExamsCount',
            'upcomingExamsCount',
            'completedExamsCount',

            'currentSubscription',
            'subscriptionRemainingDays'

        ));
    }
}