<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthSetting;
use App\Models\Classes;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MonthSettingController extends Controller
{
    /**
     * Calendar month names.
     */
    protected array $calendarMonths = [

        'bangla' => [
            1  => 'বৈশাখ',
            2  => 'জ্যৈষ্ঠ',
            3  => 'আষাঢ়',
            4  => 'শ্রাবণ',
            5  => 'ভাদ্র',
            6  => 'আশ্বিন',
            7  => 'কার্তিক',
            8  => 'অগ্রহায়ণ',
            9  => 'পৌষ',
            10 => 'মাঘ',
            11 => 'ফাল্গুন',
            12 => 'চৈত্র',
        ],

        'english' => [
            1  => 'January',
            2  => 'February',
            3  => 'March',
            4  => 'April',
            5  => 'May',
            6  => 'June',
            7  => 'July',
            8  => 'August',
            9  => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ],

        'arabic' => [
            1  => 'محرم',
            2  => 'صفر',
            3  => 'ربيع الأول',
            4  => 'ربيع الآخر',
            5  => 'جمادى الأولى',
            6  => 'جمادى الآخرة',
            7  => 'رجب',
            8  => 'شعبان',
            9  => 'رمضان',
            10 => 'شوال',
            11 => 'ذو القعدة',
            12 => 'ذو الحجة',
        ],
    ];

    /**
     * Get authorized institution ID.
     */
    private function institutionId(): int
    {
        $user = auth()->user();

        $institutionId = $user?->active_institution_id;

        if (!$institutionId && app()->bound('institution_id')) {
            $institutionId = app('institution_id');
        }

        abort_unless(
            $user && $institutionId,
            403,
            'প্রতিষ্ঠান নির্ধারণ করা যায়নি।'
        );

        return (int) $institutionId;
    }

    /**
     * Scope MonthSetting by institution.
     */
    private function scopedMonthSetting(
        MonthSetting $monthSetting
    ): MonthSetting {

        return MonthSetting::query()
            ->where('institution_id', $this->institutionId())
            ->findOrFail($monthSetting->id);
    }

    /**
     * Month Setting Index.
     */
    public function index()
    {
        $institutionId = $this->institutionId();

        $monthSettings = MonthSetting::query()
            ->where('institution_id', $institutionId)
            ->with([
                'studentClass' => function ($query) use ($institutionId) {
                    $query->where('institution_id', $institutionId);
                },
            ])
            ->orderByDesc('academic_year')
            ->paginate(15);

        $classes = Classes::query()
            ->where('institution_id', $institutionId)
            ->orderBy('id')
            ->get();

        $years = AcademicYear::query()
            ->where('institution_id', $institutionId)
            ->where('status', 'active')
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();

        $currentAcademicYear = $years->firstWhere('is_current', true);

        $currentYear = $currentAcademicYear
            ? $currentAcademicYear->id
            : $years->first()?->id;

        return view('admin.month-settings.index', compact(
            'monthSettings',
            'classes',
            'years',
            'currentYear'
        ));
    }

    /**
     * Store Month Setting.
     */
    public function store(Request $request): JsonResponse
    {
        $institutionId = $this->institutionId();

        $validator = Validator::make(
            $request->all(),
            $this->rules($institutionId)
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $data = $this->resolveAllMonths($data);

        $exists = MonthSetting::query()
            ->where('institution_id', $institutionId)
            ->where('academic_year', $data['academic_year'])
            ->where('class_id', $data['class_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'এই শিক্ষাবর্ষ ও ক্লাসের জন্য ইতোমধ্যে মাস সেটিং আছে। এডিট করুন।',
            ], 409);
        }

        $data['institution_id'] = $institutionId;

        $monthSetting = MonthSetting::create($data);

        $monthSetting->load('studentClass');

        return response()->json([
            'success' => true,
            'message' => 'মাসের তালিকা সফলভাবে সংরক্ষিত হয়েছে!',
            'data' => $this->formatData($monthSetting),
        ]);
    }

    /**
     * Edit Month Setting.
     */
    public function edit(MonthSetting $monthSetting): JsonResponse
    {
        $monthSetting = $this->scopedMonthSetting($monthSetting);

        $monthSetting->load('studentClass');

        return response()->json([
            'success' => true,
            'data' => $this->formatData($monthSetting),
        ]);
    }

    /**
     * Update Month Setting.
     */
    public function update(
        Request $request,
        MonthSetting $monthSetting
    ): JsonResponse {

        $institutionId = $this->institutionId();

        $monthSetting = $this->scopedMonthSetting($monthSetting);

        $validator = Validator::make(
            $request->all(),
            $this->rules($institutionId)
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $data = $this->resolveAllMonths($data);

        $exists = MonthSetting::query()
            ->where('institution_id', $institutionId)
            ->where('academic_year', $data['academic_year'])
            ->where('class_id', $data['class_id'])
            ->where('id', '!=', $monthSetting->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'এই শিক্ষাবর্ষ ও ক্লাসের জন্য ইতোমধ্যে মাস সেটিং আছে।',
            ], 409);
        }

        unset($data['institution_id']);

        $monthSetting->update($data);

        $monthSetting->refresh()->load('studentClass');

        return response()->json([
            'success' => true,
            'message' => 'মাসের তালিকা সফলভাবে আপডেট হয়েছে!',
            'data' => $this->formatData($monthSetting),
        ]);
    }

    /**
     * Delete Month Setting.
     */
    public function destroy(MonthSetting $monthSetting): JsonResponse
    {
        $monthSetting = $this->scopedMonthSetting($monthSetting);

        $monthSetting->delete();

        return response()->json([
            'success' => true,
            'message' => 'মাসের তালিকা সফলভাবে মুছে ফেলা হয়েছে!',
        ]);
    }

    /**
     * Return calendar month suggestions.
     */
    public function suggest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'calendar_type' => [
                'required',
                Rule::in(['bangla', 'english', 'arabic']),
            ],
            'q' => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $calendarType = $request->input('calendar_type');
        $query = trim((string) $request->input('q', ''));

        $months = $this->calendarMonths[$calendarType];

        $suggestions = [];

        foreach ($months as $number => $name) {

            if (
                $query === ''
                || mb_stripos($name, $query) !== false
                || (string) $number === $query
            ) {
                $suggestions[] = [
                    'value' => $name,
                    'label' => $number . ' → ' . $name,
                    'type' => $calendarType,
                ];
            }
        }

        return response()->json($suggestions);
    }

    /**
     * Validation rules.
     */
    private function rules(int $institutionId): array
    {
        $rules = [

            'academic_year' => [
                'required',
                'string',
                'max:255',
            ],

            'class_id' => [
                'required',
                'integer',
                Rule::exists('classes', 'id')
                    ->where('institution_id', $institutionId),
            ],

            'calendar_type' => [
                'required',
                Rule::in(['bangla', 'english', 'arabic']),
            ],
        ];

        for ($i = 1; $i <= 12; $i++) {

            $rules["month_$i"] = [
                'nullable',
                'string',
                'max:50',
            ];
        }

        return $rules;
    }

    /**
     * Resolve all month names according to calendar type.
     */
    private function resolveAllMonths(array $data): array
    {
        $calendarType = $data['calendar_type'];

        $months = $this->calendarMonths[$calendarType];

        for ($i = 1; $i <= 12; $i++) {

            $key = "month_$i";

            if (
                !isset($data[$key])
                || trim((string) $data[$key]) === ''
            ) {
                $data[$key] = $months[$i];
            }
        }

        return $data;
    }

    /**
     * Format response.
     */
    private function formatData(MonthSetting $ms): array
    {
        $data = $ms->toArray();

        $data['student_class'] = $ms->studentClass
            ? [
                'name' => $ms->studentClass->name,
                'name_bn' => $ms->studentClass->name_bn,
            ]
            : null;

        return $data;
    }
}