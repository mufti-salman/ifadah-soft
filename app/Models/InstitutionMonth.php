<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstitutionMonth extends Model
{
    use HasFactory;

    protected $table = 'institution_months';

    protected $fillable = [
        'institution_id',
        'month_number',
        'name',
        'name_bn',
        'name_ar',
        'is_active',
    ];

    protected $casts = [
        'institution_id' => 'integer',
        'month_number' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * মাসটি যে প্রতিষ্ঠানের অধীনে রয়েছে।
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Madrasa::class, 'institution_id');
    }

    /**
     * এই মাসকে শিক্ষাবর্ষের শুরু মাস হিসেবে ব্যবহার করা শিক্ষাবর্ষগুলো।
     */
    public function academicYears(): HasMany
    {
        return $this->hasMany(
            AcademicYear::class,
            'start_month_id'
        );
    }

    /**
     * প্রদর্শনের জন্য উপযুক্ত মাসের নাম।
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name_bn ?: $this->name;
    }

    /**
     * একটি প্রতিষ্ঠানের জন্য ১২টি ডিফল্ট মাস তৈরি করবে।
     *
     * আগে থেকে থাকা মাসের নাম পরিবর্তন করবে না।
     */
    public static function ensureDefaultsForInstitution(int $institutionId): void
    {
        $months = [
            1  => ['January', 'জানুয়ারি', 'يناير'],
            2  => ['February', 'ফেব্রুয়ারি', 'فبراير'],
            3  => ['March', 'মার্চ', 'مارس'],
            4  => ['April', 'এপ্রিল', 'أبريل'],
            5  => ['May', 'মে', 'مايو'],
            6  => ['June', 'জুন', 'يونيو'],
            7  => ['July', 'জুলাই', 'يوليو'],
            8  => ['August', 'আগস্ট', 'أغسطس'],
            9  => ['September', 'সেপ্টেম্বর', 'سبتمبر'],
            10 => ['October', 'অক্টোবর', 'أكتوبر'],
            11 => ['November', 'নভেম্বর', 'نوفمبر'],
            12 => ['December', 'ডিসেম্বর', 'ديسمبر'],
        ];

        foreach ($months as $number => $names) {
            static::firstOrCreate(
                [
                    'institution_id' => $institutionId,
                    'month_number' => $number,
                ],
                [
                    'name' => $names[0],
                    'name_bn' => $names[1],
                    'name_ar' => $names[2],
                    'is_active' => true,
                ]
            );
        }
    }
}