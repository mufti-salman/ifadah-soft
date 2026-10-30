<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstitutionCalendarSetting;
use App\Services\CalendarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CalendarSettingController extends Controller
{
    public function __construct(
        protected CalendarService $calendarService
    ) {
    }

    /**
     * Display the calendar settings for the active institution.
     */
    public function index(): View
    {
        $institutionId = $this->getInstitutionId();

        $calendarSettings = $this->calendarService->getSettings(
            $institutionId
        );

        return view('admin.settings.calendar', compact(
            'calendarSettings'
        ));
    }

    /**
     * Save calendar settings for the active institution.
     */
    public function update(Request $request): RedirectResponse
    {
        $institutionId = $this->getInstitutionId();

        $validated = $request->validate([
            'calendar_mode' => [
                'required',
                'string',
                'in:english,bangla,hijri',
            ],

            'hijri_adjustment' => [
                'nullable',
                'integer',
                'min:-30',
                'max:30',
            ],
        ]);

        InstitutionCalendarSetting::updateOrCreate(
            [
                'institution_id' => $institutionId,
            ],
            [
                'calendar_mode' => $validated['calendar_mode'],
                'hijri_adjustment' => $validated['hijri_adjustment'] ?? 0,
            ]
        );

        // Remove cached settings so the new values are loaded.
        $this->calendarService->clearCache($institutionId);

        return redirect()
            ->route('dashboard.settings.calendar')
            ->with('success', 'Calendar settings updated successfully.');
    }

    /**
     * Resolve the institution whose settings are being managed.
     */
    private function getInstitutionId(): int
    {
        $user = Auth::user();

        abort_unless($user, 403, 'Unauthorized access.');

        /*
         * When Super Admin enters an institution's panel,
         * use that active institution.
         */
        if (session('institution_mode') === true) {
            $institutionId = session('active_institution_id');

            abort_unless(
                is_numeric($institutionId) && (int) $institutionId > 0,
                403,
                'No institution is currently selected.'
            );

            return (int) $institutionId;
        }

        /*
         * For normal institution users, use their own institution.
         */
        $institutionId = $user->institution_id ?? null;

        abort_unless(
            is_numeric($institutionId) && (int) $institutionId > 0,
            403,
            'Your account is not associated with an institution.'
        );

        return (int) $institutionId;
    }
}