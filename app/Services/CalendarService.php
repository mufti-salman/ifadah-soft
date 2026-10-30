<?php

namespace App\Services;

use App\Models\InstitutionCalendarSetting;
use BanglaDateTime\BanglaDateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class CalendarService
{
    /**
     * নির্দিষ্ট প্রতিষ্ঠানের ক্যালেন্ডার সেটিংস সংগ্রহ করে।
     */
    public function getSettings(int $institutionId): array
    {
        $setting = Cache::remember(
            "institution_calendar_setting:{$institutionId}",
            now()->addMinutes(10),
            fn () => InstitutionCalendarSetting::query()
                ->where('institution_id', $institutionId)
                ->first()
        );

        return [
            'institution_id' => $institutionId,
            'calendar_mode' => $setting?->calendar_mode
                ?? InstitutionCalendarSetting::MODE_ENGLISH,
            'hijri_adjustment' => $setting?->hijri_adjustment ?? 0,
        ];
    }

    /**
     * তারিখ প্রদর্শনের আগে প্রতিষ্ঠানের ক্যালেন্ডার পদ্ধতি অনুযায়ী
     * গ্রেগরিয়ান তারিখকে রূপান্তর করে।
     *
     * ডেটাবেসের মূল তারিখ পরিবর্তন করে না।
     */
    public function formatDate(
        mixed $date,
        int $institutionId
    ): string {
        if ($date === null || $date === '') {
            return '—';
        }

        $settings = $this->getSettings($institutionId);

        $date = Carbon::parse($date, 'Asia/Dhaka')
            ->startOfDay();

        switch ($settings['calendar_mode']) {
            case InstitutionCalendarSetting::MODE_BANGLA:
                return BanglaDateTime::create(
                    $date->toDateString(),
                    'Asia/Dhaka'
                )->toBangla('j F Y');

            case InstitutionCalendarSetting::MODE_HIJRI:
                return $this->formatHijriDate(
                    $date->copy()->addDays(
                        $settings['hijri_adjustment']
                    )
                );

            case InstitutionCalendarSetting::MODE_ENGLISH:
            default:
                return $date->format('d M Y');
        }
    }

    /**
     * হিজরি ক্যালেন্ডারে তারিখ প্রদর্শন করে।
     */
    private function formatHijriDate(Carbon $date): string
    {
        if (!class_exists(\IntlDateFormatter::class)) {
            throw new RuntimeException(
                'হিজরি তারিখ প্রদর্শনের জন্য PHP intl extension চালু করতে হবে।'
            );
        }

        $formatter = new \IntlDateFormatter(
            'bn_BD@calendar=islamic-umalqura',
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::NONE,
            'Asia/Dhaka',
            \IntlDateFormatter::TRADITIONAL,
            'd MMMM y'
        );

        $formattedDate = $formatter->format($date->getTimestamp());

        if ($formattedDate === false) {
            throw new RuntimeException(
                'হিজরি তারিখ রূপান্তর করা যায়নি। PHP intl configuration পরীক্ষা করুন।'
            );
        }

        return $formattedDate;
    }

    /**
     * আপডেটের পর নির্দিষ্ট প্রতিষ্ঠানের cache পরিষ্কার করে।
     */
    public function clearCache(int $institutionId): void
    {
        Cache::forget(
            "institution_calendar_setting:{$institutionId}"
        );
    }
}