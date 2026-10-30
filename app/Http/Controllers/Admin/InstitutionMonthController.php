<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstitutionMonth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InstitutionMonthController extends Controller
{
    /**
     * Resolve the current institution.
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
     * Update month names belonging to the current institution.
     */
    public function update(Request $request)
    {
        $institutionId = $this->institutionId();

        $validated = $request->validate([
            'months' => [
                'required',
                'array',
                'min:1',
            ],

            'months.*.id' => [
                'required',
                'integer',
                Rule::exists('institution_months', 'id')
                    ->where('institution_id', $institutionId),
            ],

            'months.*.name' => [
                'required',
                'string',
                'max:100',
            ],

            'months.*.name_bn' => [
                'nullable',
                'string',
                'max:100',
            ],

            'months.*.name_ar' => [
                'nullable',
                'string',
                'max:100',
            ],

            'months.*.is_active' => [
                'sometimes',
                'boolean',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $institutionId
        ) {
            foreach ($validated['months'] as $monthData) {
                $month = InstitutionMonth::query()
                    ->where('institution_id', $institutionId)
                    ->findOrFail((int) $monthData['id']);

                $month->update([
                    'name' => $monthData['name'],
                    'name_bn' => $monthData['name_bn'] ?? null,
                    'name_ar' => $monthData['name_ar'] ?? null,
                    'is_active' => (bool) (
                        $monthData['is_active'] ?? false
                    ),
                ]);
            }
        });

        return redirect()
            ->route('dashboard.academic-years.index')
            ->with('success', 'মাসের নামগুলো আপডেট হয়েছে।');
    }
}