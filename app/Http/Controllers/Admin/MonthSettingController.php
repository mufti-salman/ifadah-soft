<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthSetting;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MonthSettingController extends Controller
{
    protected array $monthMap = [
        '1'  => 'জানুয়ারি',
        '2'  => 'ফেব্রুয়ারি',
        '3'  => 'মার্চ',
        '4'  => 'এপ্রিল',
        '5'  => 'মে',
        '6'  => 'জুন',
        '7'  => 'জুলাই',
        '8'  => 'আগস্ট',
        '9'  => 'সেপ্টেম্বর',
        '10' => 'অক্টোবর',
        '11' => 'নভেম্বর',
        '12' => 'ডিসেম্বর',
    ];

    /**
     * Get the institution authorized for the current request.
     */
    private function institutionId(): int
    {
        $user = auth()->user();

        // The User model should resolve this according to
        // the existing institution-switching rules.
        $institutionId = $user?->active_institution_id;

        // Middleware fallback, if available.
        if (!$institutionId && app()->bound('institution_id')) {
            $institutionId = app('institution_id');
        }

        abort_unless($user && $institutionId, 403, 'প্রতিষ্ঠান নির্ধারণ করা যায়নি।');

        return (int) $institutionId;
    }

    /**
     * Retrieve a MonthSetting only from the authorized institution.
     */
    private function scopedMonthSetting(MonthSetting $monthSetting): MonthSetting
    {
        return MonthSetting::query()
            ->where('institution_id', $this->institutionId())
            ->findOrFail($monthSetting->id);
    }

    /**
     * List Month Settings for the current institution.
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
            ->orderBy('name')
            ->get();

        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 5);

        return view('admin.month-settings.index', compact(
            'monthSettings',
            'classes',
            'years',
            'currentYear'
        ));
    }

    /**
     * Store a MonthSetting for the current institution.
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

        // Never trust institution_id sent by the browser.
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
     * Edit only a MonthSetting belonging to the current institution.
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
     * Update only a MonthSetting belonging to the current institution.
     */
    public function update(
        Request $request,
        MonthSetting $monthSetting
    ): JsonResponse {
        $institutionId = $this->institutionId();

        // Re-fetch within the authorized institution.
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

        // Prevent institution reassignment through request parameters.
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
     * Delete only a MonthSetting belonging to the current institution.
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
     * Suggest month names.
     * This endpoint returns static month names, not institution data.
     */
    public function suggest(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('q', ''));

        $banglaDigits = [
            '০' => '0',
            '১' => '1',
            '২' => '2',
            '৩' => '3',
            '৪' => '4',
            '৫' => '5',
            '৬' => '6',
            '৭' => '7',
            '৮' => '8',
            '৯' => '9',
        ];

        $normalizedQuery = strtr($query, $banglaDigits);
        $suggestions = [];

        $englishMonthMap = [
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];

        if (ctype_digit($normalizedQuery)) {
            if (isset($this->monthMap[$normalizedQuery])) {
                $suggestions[] = [
                    'value' => $this->monthMap[$normalizedQuery],
                    'label' => "{$query} → {$this->monthMap[$normalizedQuery]}",
                    'type' => 'bangla',
                ];
            }

            if (isset($englishMonthMap[$normalizedQuery])) {
                $suggestions[] = [
                    'value' => $englishMonthMap[$normalizedQuery],
                    'label' => "{$query} → {$englishMonthMap[$normalizedQuery]}",
                    'type' => 'english',
                ];
            }
        } else {
            foreach ($this->monthMap as $name) {
                if (mb_stripos($name, $query) !== false) {
                    $suggestions[] = [
                        'value' => $name,
                        'label' => $name,
                        'type' => 'bangla',
                    ];
                }
            }

            foreach ($englishMonthMap as $name) {
                if (stripos($name, $query) !== false) {
                    $suggestions[] = [
                        'value' => $name,
                        'label' => $name,
                        'type' => 'english',
                    ];
                }
            }
        }

        return response()->json($suggestions);
    }

    /**
     * Validate the submitted class against the current institution.
     */
    private function rules(int $institutionId): array
    {
        $rules = [
            'academic_year' => [
                'required',
                'digits:4',
            ],

            'class_id' => [
                'required',
                'integer',
                Rule::exists('classes', 'id')
                    ->where('institution_id', $institutionId),
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

    private function resolveAllMonths(array $data): array
    {
        for ($i = 1; $i <= 12; $i++) {
            $key = "month_$i";

            if (!empty($data[$key])) {
                $data[$key] = $this->resolveMonthName($data[$key]);
            }
        }

        return $data;
    }

    private function formatData(MonthSetting $ms): array
    {
        $data = $ms->toArray();

        $data['student_class'] = $ms->studentClass
            ? ['name' => $ms->studentClass->name]
            : null;

        return $data;
    }

    protected function resolveMonthName(string $value): string
    {
        $value = trim($value);

        $banglaDigits = [
            '০' => '0',
            '১' => '1',
            '২' => '2',
            '৩' => '3',
            '৪' => '4',
            '৫' => '5',
            '৬' => '6',
            '৭' => '7',
            '৮' => '8',
            '৯' => '9',
        ];

        $normalizedValue = strtr($value, $banglaDigits);

        if (
            ctype_digit($normalizedValue)
            && isset($this->monthMap[$normalizedValue])
        ) {
            return $this->monthMap[$normalizedValue];
        }

        return $value;
    }
}