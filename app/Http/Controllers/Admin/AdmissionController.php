<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Admission::with([
            'student.user',
            'academicYear',
            'class'
        ]);

        // Filter by academic year
        if (
            $request->has('academic_year_id')
            && $request->academic_year_id
        ) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        // Filter by class
        if (
            $request->has('class_id')
            && $request->class_id
        ) {
            $query->where(
                'class_id',
                $request->class_id
            );
        }

        // Search by student name or ID
        if (
            $request->has('search')
            && $request->search
        ) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'admission_no',
                    'like',
                    "%{$search}%"
                )
                ->orWhereHas(
                    'student',
                    function ($sq) use ($search) {

                        $sq->where(
                            'student_id',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhereHas(
                            'user',
                            function ($uq) use ($search) {

                                $uq->where(
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
                                );
                            }
                        );
                    }
                );
            });
        }

        $admissions = $query
            ->orderBy(
                'created_at',
                'desc'
            )
            ->paginate(15);

        /*
        |--------------------------------------------------------------------------
        | Selected Student
        |--------------------------------------------------------------------------
        |
        | User List থেকে:
        |
        | /dashboard/admissions?student_id=5
        |
        | এভাবে আসলে students.id = 5 এর student
        | automatically load হবে।
        |
        |--------------------------------------------------------------------------
        */

        $selectedStudent = null;

        if ($request->filled('student_id')) {

            $selectedStudent = Student::with([
                'user',
                'class'
            ])->find(
                $request->student_id
            );
        }

        // Active academic years
        $academicYears = AcademicYear::where(
            'status',
            'active'
        )->get();

        // Active classes
        $classes = Classes::where(
            'status',
            'active'
        )->get();

        // Stats
        $currentYear = AcademicYear::where(
            'is_current',
            true
        )->first();

        $currentYearStudents = Admission::where(
            'academic_year_id',
            $currentYear
                ? $currentYear->id
                : null
        )
        ->where(
            'status',
            'active'
        )
        ->count();

        $totalStudents = Student::where(
            'status',
            'active'
        )->count();

        return view(
            'admin.admissions.index',
            compact(
                'admissions',
                'academicYears',
                'classes',
                'currentYearStudents',
                'totalStudents',
                'selectedStudent'
            )
        );
    }

    public function create()
    {
        $students = Student::with('user')
            ->whereDoesntHave(
                'admissions',
                function ($q) {
                    $q->where(
                        'status',
                        'active'
                    );
                }
            )
            ->get();

        $academicYears = AcademicYear::where(
            'status',
            'active'
        )->get();

        $classes = Classes::where(
            'status',
            'active'
        )->get();

        return view(
            'admin.admissions.create',
            compact(
                'students',
                'academicYears',
                'classes'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([

            'student_id' =>
                'required|exists:students,id',

            'academic_year_id' =>
                'required|exists:academic_years,id',

            'class_id' =>
                'required|exists:classes,id',

            'entry_date' =>
                'required|date',

            'financial_status' =>
                'nullable|in:solvent,insolvent,orphan,helpless',

            'residence_status' =>
                'nullable|in:resident,non-resident,daycare,nightcare',

            'admission_type' =>
                'nullable|in:new,old',
        ]);

        try {

            DB::beginTransaction();

            $student = Student::findOrFail(
                $request->student_id
            );

            $lastAdmission =
                Admission::latest('id')->first();

            $nextId =
                $lastAdmission
                    ? $lastAdmission->id + 1
                    : 1;

            $admissionNo =
                'ADM-'
                . date('Y')
                . '-'
                . str_pad(
                    $nextId,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

            // CREATE ADMISSION
            $admission = Admission::create([

                'admission_no' =>
                    $admissionNo,

                'student_id' =>
                    $request->student_id,

                'academic_year_id' =>
                    $request->academic_year_id,

                'class_id' =>
                    $request->class_id,

                'admission_date' =>
                    $request->entry_date,

                'financial_status' =>
                    $request->financial_status
                    ?? 'solvent',

                'residence_status' =>
                    $request->residence_status
                    ?? 'non-resident',

                'admission_type' =>
                    $request->admission_type
                    ?? 'new',

                'status' =>
                    'active',

                'created_by' =>
                    auth()->id(),
            ]);

            // SYNC STUDENT TABLE
            $student->update([

                'academic_year_id' =>
                    $request->academic_year_id,

                'class_id' =>
                    $request->class_id,
            ]);

            DB::commit();

            return redirect()
                ->route(
                    'dashboard.admissions.index'
                )
                ->with(
                    'success',
                    'শিক্ষার্থী ভর্তি সম্পন্ন হয়েছে। ভর্তি নম্বর: '
                    . $admissionNo
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with(
                    'error',
                    'ভর্তি করতে সমস্যা হয়েছে: '
                    . $e->getMessage()
                )
                ->withInput();
        }
    }

    public function show($id)
    {
        $admission = Admission::with([
            'student.user',
            'academicYear',
            'class'
        ])->findOrFail($id);

        return view(
            'admin.admissions.show',
            compact('admission')
        );
    }

    public function edit($id)
    {
        $admission = Admission::findOrFail($id);

        $academicYears = AcademicYear::all();

        $classes = Classes::all();

        return view(
            'admin.admissions.edit',
            compact(
                'admission',
                'academicYears',
                'classes'
            )
        );
    }

    public function update(
        Request $request,
        $id
    ) {
        $admission =
            Admission::findOrFail($id);

        $request->validate([

            'class_id' =>
                'required|exists:classes,id',

            'financial_status' =>
                'nullable|in:solvent,insolvent,orphan,helpless',

            'residence_status' =>
                'nullable|in:resident,non-resident,daycare,nightcare',

            'status' =>
                'required|in:active,inactive,transferred,graduated,dropped',
        ]);

        try {

            DB::beginTransaction();

            $admission->update([

                'class_id' =>
                    $request->class_id,

                'financial_status' =>
                    $request->financial_status,

                'residence_status' =>
                    $request->residence_status,

                'status' =>
                    $request->status,

                'leaving_date' =>
                    $request->leaving_date,

                'leaving_reason' =>
                    $request->leaving_reason,

                'remarks' =>
                    $request->remarks,

                'updated_by' =>
                    auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route(
                    'dashboard.admissions.index'
                )
                ->with(
                    'success',
                    'ভর্তি তথ্য আপডেট করা হয়েছে।'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with(
                    'error',
                    'আপডেট করতে সমস্যা হয়েছে।'
                )
                ->withInput();
        }
    }

    /**
     * Delete admission
     */
    public function destroy($id)
    {
        $admission = Admission::findOrFail($id);

        // সরাসরি Admission delete হবে
        $admission->delete();

        return redirect()
            ->route(
                'dashboard.admissions.index'
            )
            ->with(
                'success',
                'ভর্তি তথ্য ডিলিট করা হয়েছে।'
            );
    }

    public function toggleStatus($id)
    {
        $admission =
            Admission::findOrFail($id);

        $admission->status =
            $admission->status === 'active'
                ? 'inactive'
                : 'active';

        $admission->save();

        return back()->with(
            'success',
            'স্ট্যাটাস পরিবর্তন করা হয়েছে।'
        );
    }

    /**
     * Search students for admission
     * Works with single digit ID as well
     */
    public function search(Request $request)
    {
        $q = $request->q;

        if (!$q) {
            return response()->json([]);
        }

        $students = Student::with([
            'user',
            'class'
        ])
        ->where(function ($query) use ($q) {

            $query->where(
                'student_id',
                'like',
                "%{$q}%"
            )
            ->orWhereHas(
                'user',
                function ($userQuery) use ($q) {

                    $userQuery->where(
                        'name',
                        'like',
                        "%{$q}%"
                    )
                    ->orWhere(
                        'name_bn',
                        'like',
                        "%{$q}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$q}%"
                    )
                    ->orWhere(
                        'institution_user_id',
                        'like',
                        "%{$q}%"
                    );
                }
            );

        })
        ->limit(20)
        ->get();

        return response()->json(

            $students->map(
                function ($student) {

                    return [

                        'id' =>
                            $student->id,

                        'institution_user_id' =>
                            $student->user
                                ->institution_user_id
                                ?? '',

                        'student_id' =>
                            $student->student_id
                            ?? '',

                        'name' =>
                            $student->user->name_bn
                            ?? $student->user->name
                            ?? '',

                        'father_name' =>
                            $student->user->father_name
                            ?? '',

                        'mobile' =>
                            $student->user->phone
                            ?? '',

                        'class_name' =>
                            optional(
                                $student->class
                            )->name_bn
                            ?? '',

                        'photo' =>
                            $student->user->photo
                                ? asset(
                                    'storage/'
                                    . $student->user->photo
                                )
                                : asset(
                                    'images/default-user.png'
                                ),
                    ];
                }
            )
        );
    }
}
