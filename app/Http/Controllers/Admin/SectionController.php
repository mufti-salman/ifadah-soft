<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    /**
     * Resolve the currently authorized institution.
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
     * Find a class belonging to the current institution.
     */
    private function scopedClass(int $classId): Classes
    {
        return Classes::query()
            ->where('institution_id', $this->institutionId())
            ->findOrFail($classId);
    }

    /**
     * Find a section belonging to the current institution.
     */
    private function scopedSection(int $id): Section
    {
        return Section::query()
            ->where('institution_id', $this->institutionId())
            ->findOrFail($id);
    }

    /**
     * List classes and their sections for the current institution.
     */
    public function index()
    {
        $institutionId = $this->institutionId();

        $classes = Classes::query()
            ->where('institution_id', $institutionId)
            ->where('status', 'active')
            ->with([
                'sections' => function ($query) use ($institutionId) {
                    $query->where('institution_id', $institutionId)
                        ->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('admin.sections.index', compact('classes'));
    }

    /**
     * Create a section.
     */
    public function store(Request $request)
    {
        $institutionId = $this->institutionId();

        $validated = $request->validate([
            'class_id' => [
                'required',
                'integer',
                Rule::exists('classes', 'id')
                    ->where('institution_id', $institutionId),
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'name_bn' => [
                'nullable',
                'string',
                'max:100',
            ],

            'is_active' => [
                'nullable',
                'in:0,1',
            ],
        ]);

        try {
            $section = DB::transaction(function () use (
                $validated,
                $request,
                $institutionId
            ) {
                // Confirm the class belongs to this institution.
                $this->scopedClass((int) $validated['class_id']);

                $section = Section::query()
                    ->where('institution_id', $institutionId)
                    ->where('class_id', $validated['class_id'])
                    ->where('name', $validated['name'])
                    ->first();

                $isNew = !$section;

                if (!$section) {
                    $section = new Section();
                    $section->institution_id = $institutionId;
                    $section->class_id = $validated['class_id'];
                    $section->name = $validated['name'];
                    $section->created_by = auth()->id();
                }

                $section->name_bn = $validated['name_bn'] ?? null;
                $section->is_active = $request->input('is_active', '1') === '1';
                $section->updated_by = auth()->id();
                $section->save();

                $section->wasRecentlyCreated = $isNew;

                return $section;
            });

            $message = $section->wasRecentlyCreated
                ? 'সেকশন সফলভাবে তৈরি করা হয়েছে।'
                : 'এই সেকশনটি আগে থেকেই ছিল; তথ্য আপডেট করা হয়েছে।';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $section,
                ]);
            }

            return redirect()
                ->route('dashboard.sections.index')
                ->with('success', $message);

        } catch (\Illuminate\Database\QueryException $e) {
            report($e);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'সেকশন সংরক্ষণ করা যায়নি। নামটি আগে থেকে আছে কি না যাচাই করুন।',
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', 'সেকশন সংরক্ষণ করা যায়নি। নামটি আগে থেকে আছে কি না যাচাই করুন।');
        }
    }

    /**
     * Fetch a section for editing.
     */
    public function edit($id)
    {
        $section = $this->scopedSection((int) $id);

        // Load only the related class from the same institution.
        $section->setRelation(
            'class',
            $this->scopedClass((int) $section->class_id)
        );

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($section);
        }

        return view('admin.sections.edit', compact('section'));
    }

    /**
     * Update a section.
     */
    public function update(Request $request, $id)
    {
        $institutionId = $this->institutionId();
        $section = $this->scopedSection((int) $id);

        $validated = $request->validate([
            'class_id' => [
                'required',
                'integer',
                Rule::exists('classes', 'id')
                    ->where('institution_id', $institutionId),
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'name_bn' => [
                'nullable',
                'string',
                'max:100',
            ],

            'is_active' => [
                'nullable',
                'in:0,1',
            ],
        ]);

        try {
            DB::transaction(function () use (
                $section,
                $validated,
                $request,
                $institutionId
            ) {
                $this->scopedClass((int) $validated['class_id']);

                // Prevent a duplicate section within the same class.
                $duplicate = Section::query()
                    ->where('institution_id', $institutionId)
                    ->where('class_id', $validated['class_id'])
                    ->where('name', $validated['name'])
                    ->where('id', '!=', $section->id)
                    ->exists();

                if ($duplicate) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'name' => 'এই ক্লাসে একই নামে সেকশন আগে থেকেই আছে।',
                    ]);
                }

                $section->class_id = $validated['class_id'];
                $section->name = $validated['name'];
                $section->name_bn = $validated['name_bn'] ?? null;
                $section->is_active = $request->input('is_active', '1') === '1';
                $section->updated_by = auth()->id();
                $section->save();
            });

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'সেকশন আপডেট করা হয়েছে।',
                    'data' => $section->fresh(),
                ]);
            }

            return redirect()
                ->route('dashboard.sections.index')
                ->with('success', 'সেকশন আপডেট করা হয়েছে।');

        } catch (\Illuminate\Database\QueryException $e) {
            report($e);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'সেকশন আপডেট করা যায়নি।',
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', 'সেকশন আপডেট করা যায়নি।');

        }
    }

    /**
     * Delete a section if it has no associated students.
     */
    public function destroy($id)
    {
        $section = $this->scopedSection((int) $id);

        try {
            if (
                $section->admissions()->exists()
                || $section->studentAssignments()->exists()
            ) {
                $message = 'এই সেকশনের সঙ্গে শিক্ষার্থী যুক্ত থাকায় ডিলিট করা সম্ভব নয়।';

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 422);
                }

                return back()->with('error', $message);
            }

            $section->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'সেকশন ডিলিট করা হয়েছে।',
                ]);
            }

            return back()->with('success', 'সেকশন ডিলিট করা হয়েছে।');

        } catch (\Exception $e) {
            report($e);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'সেকশন ডিলিট করা যায়নি।',
                ], 500);
            }

            return back()->with(
                'error',
                'সেকশন ডিলিট করা যায়নি।'
            );
        }
    }
}