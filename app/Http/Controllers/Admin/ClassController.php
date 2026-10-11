<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Madrasa;
use Illuminate\Http\Request;

class ClassController extends Controller
{

    private function getInstitutionId()
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'অনুমতি নেই।');
        }

        $institutionId = $user->active_institution_id;

        if (!$institutionId) {
            $institutionId = $user->institution_id;
        }

        if (!$institutionId) {
            abort(403, 'কোনো প্রতিষ্ঠান নির্বাচন করা হয়নি।');
        }

        return $institutionId;
    }

    /**
     * Class List
     */
    public function index()
    {
        $institutionId = $this->getInstitutionId();

        $madrasas = Madrasa::where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        if ($madrasas->isEmpty()) {
            $madrasas = collect([
                (object) [
                    'id' => 0,
                    'name' => 'কোনো প্রতিষ্ঠান নেই'
                ]
            ]);
        }

        /*
         * Only current institution's classes will be shown.
         */
        $classes = Classes::where('institution_id', $institutionId)
            ->withCount([
                'sections' => function ($query) {
                    $query->where('is_active', true);
                }
            ])
            ->orderBy('name', 'asc')
            ->get();

        return view(
            'admin.classes.index',
            compact('classes', 'madrasas')
        );
    }

    /**
     * Create Class Page
     */
    public function create()
    {
        $institutionId = $this->getInstitutionId();

        $madrasas = Madrasa::where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        return view(
            'admin.classes.create',
            compact('madrasas', 'institutionId')
        );
    }

    /**
     * Store Class
     */
    public function store(Request $request)
    {
        $institutionId = $this->getInstitutionId();

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:classes,name,NULL,id,institution_id,' . $institutionId,
            ],
            'name_bn' => 'required|string|max:100',
            'level' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ], [
            'name.unique' => 'এই প্রতিষ্ঠানে এই নামে ক্লাস ইতোমধ্যে রয়েছে।',
        ]);

        Classes::create([
            'institution_id' => $institutionId,
            'name' => $request->name,
            'name_bn' => $request->name_bn,
            'level' => $request->level,
            'description' => $request->description,
            'status' => $request->status ?? 'active',
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('dashboard.classes.index')
            ->with('success', 'ক্লাস সফলভাবে তৈরি করা হয়েছে।');
    }

    /**
     * AJAX Store Class
     */
    public function storeAjax(Request $request)
    {
        $institutionId = $this->getInstitutionId();

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:classes,name,NULL,id,institution_id,' . $institutionId,
            ],
        ], [
            'name.unique' => 'এই প্রতিষ্ঠানে এই নামে ক্লাস ইতোমধ্যে রয়েছে।',
        ]);

        try {
            $class = Classes::create([
                'institution_id' => $institutionId,
                'name' => $request->name,
                'name_bn' => $request->name,
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'ক্লাস সফলভাবে যোগ করা হয়েছে!',
                'data' => $class
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'যোগ করা ব্যর্থ হয়েছে: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit Class
     */
    public function edit($id)
    {
        $institutionId = $this->getInstitutionId();

        /*
         * Only current institution's class can be edited.
         */
        $class = Classes::where('institution_id', $institutionId)
            ->findOrFail($id);

        $madrasas = Madrasa::where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        if (request()->ajax()) {
            return response()->json($class);
        }

        return view(
            'admin.classes.edit',
            compact('class', 'madrasas')
        );
    }

    /**
     * Update Class
     */
    public function update(Request $request, $id)
    {
        $institutionId = $this->getInstitutionId();

        /*
         * Prevent updating another institution's class.
         */
        $class = Classes::where('institution_id', $institutionId)
            ->findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:classes,name,' . $id . ',id,institution_id,' . $institutionId,
            ],
            'name_bn' => 'required|string|max:100',
            'level' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ], [
            'name.unique' => 'এই প্রতিষ্ঠানে এই নামে ক্লাস ইতোমধ্যে রয়েছে।',
        ]);

        $class->update([
            'name' => $request->name,
            'name_bn' => $request->name_bn,
            'level' => $request->level,
            'description' => $request->description,
            'status' => $request->status ?? 'active',
            'updated_by' => auth()->id(),
        ]);

        return back()
            ->with('success', 'ক্লাস আপডেট করা হয়েছে।');
    }

    /**
     * Delete Class
     */
    public function destroy($id)
    {
        try {
            $institutionId = $this->getInstitutionId();

            /*
             * Only current institution's class can be deleted.
             */
            $class = Classes::where('institution_id', $institutionId)
                ->findOrFail($id);

            if ($class->sections()->count() > 0) {
                return back()->with(
                    'error',
                    'এই ক্লাসে সেকশন থাকায় ডিলিট করা সম্ভব নয়। আগে সেকশন ডিলিট করুন।'
                );
            }

            $class->delete();

            return back()
                ->with('success', 'ক্লাস ডিলিট করা হয়েছে।');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    /**
     * Toggle Status
     */
    public function toggleStatus($id)
    {
        $institutionId = $this->getInstitutionId();

        /*
         * Only current institution's class can be modified.
         */
        $class = Classes::where('institution_id', $institutionId)
            ->findOrFail($id);

        $class->status = $class->status === 'active'
            ? 'inactive'
            : 'active';

        $class->updated_by = auth()->id();

        $class->save();

        return back()
            ->with('success', 'স্ট্যাটাস পরিবর্তন করা হয়েছে।');
    }
}