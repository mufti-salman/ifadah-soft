<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoutine;
use App\Models\ClassTimeSlot;
use Illuminate\Http\Request;

class ClassTimeSlotController extends Controller
{
    private function institutionId(): int
    {
        $user = auth()->user();

        $institutionId = $user?->active_institution_id;

        if (!$institutionId && app()->bound('institution_id')) {
            $institutionId = app('institution_id');
        }

        abort_unless($user && $institutionId, 403, 'প্রতিষ্ঠান নির্ধারণ করা যায়নি।');

        return (int) $institutionId;
    }

    private function scopedSlot($id): ClassTimeSlot
    {
        return ClassTimeSlot::where('institution_id', $this->institutionId())
            ->findOrFail($id);
    }

    private function backToSlots()
    {
        return redirect()->route('dashboard.class-routines.index', ['tab' => 'slots']);
    }

    /**
     * Time slots now live in the Class Routine page (second tab).
     */
    public function index()
    {
        return $this->backToSlots();
    }

    private function validateSlot(Request $request): array
    {
        return $request->validate([
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
        ], [
            'start_time.required' => 'শুরুর সময় দিন।',
            'end_time.required'   => 'শেষ সময় দিন।',
            'end_time.after'      => 'শেষ সময় শুরুর সময়ের পরে হতে হবে।',
            '*.date_format'       => 'সময়ের ফরম্যাট সঠিক নয়।',
        ]);
    }

    private function overlaps(int $institutionId, array $data, ?int $ignoreId = null): bool
    {
        $query = ClassTimeSlot::where('institution_id', $institutionId)
            ->where('start_time', '<', $data['end_time'] . ':00')
            ->where('end_time', '>', $data['start_time'] . ':00');

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    public function store(Request $request)
    {
        $institutionId = $this->institutionId();

        $data = $this->validateSlot($request);

        if ($this->overlaps($institutionId, $data)) {
            return back()->withInput()->withErrors([
                'conflict' => ['এই সময়টি অন্য একটি সময়-স্লটের সাথে মিলে যাচ্ছে।'],
            ]);
        }

        ClassTimeSlot::create([
            'institution_id' => $institutionId,
            'start_time'     => $data['start_time'] . ':00',
            'end_time'       => $data['end_time'] . ':00',
            'created_by'     => auth()->id(),
        ]);

        return $this->backToSlots()->with('success', 'সময়-স্লট সংরক্ষণ করা হয়েছে।');
    }

    public function update(Request $request, $id)
    {
        $institutionId = $this->institutionId();

        $slot = $this->scopedSlot($id);

        $data = $this->validateSlot($request);

        if ($this->overlaps($institutionId, $data, $slot->id)) {
            return back()->withInput()->withErrors([
                'conflict' => ['এই সময়টি অন্য একটি সময়-স্লটের সাথে মিলে যাচ্ছে।'],
            ]);
        }

        $slot->update([
            'start_time' => $data['start_time'] . ':00',
            'end_time'   => $data['end_time'] . ':00',
            'updated_by' => auth()->id(),
        ]);

        return $this->backToSlots()->with('success', 'সময়-স্লট আপডেট করা হয়েছে।');
    }

    public function destroy($id)
    {
        $slot = $this->scopedSlot($id);

        $used = ClassRoutine::where('institution_id', $this->institutionId())
            ->where('time_slot_id', $slot->id)
            ->exists();

        if ($used) {
            return back()->with('error', 'এই সময়-স্লট ক্লাস রুটিনে ব্যবহৃত হচ্ছে, তাই মুছে ফেলা যাবে না।');
        }

        $slot->delete();

        return $this->backToSlots()->with('success', 'সময়-স্লট মুছে ফেলা হয়েছে।');
    }
}