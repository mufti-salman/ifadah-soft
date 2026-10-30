<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'name',
        'name_bn',
        'name_ar',
        'code',
        'start_date',
        'end_date',
        'start_month_id',
        'semester_count',
        'is_current',
        'status',
        'description',
        'created_by',
        'updated_by',
        'parent_year_id',
    ];

    protected $casts = [
        'institution_id' => 'integer',
        'start_month_id' => 'integer',
        'is_current' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'semester_count' => 'integer',
    ];

    /**
     * শিক্ষাবর্ষের প্রতিষ্ঠান।
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Madrasa::class, 'institution_id');
    }

    /**
     * শিক্ষাবর্ষটি যে মাস থেকে শুরু হবে।
     */
    public function startMonth(): BelongsTo
    {
        return $this->belongsTo(
            InstitutionMonth::class,
            'start_month_id'
        );
    }

    /**
     * শিক্ষাবর্ষ তৈরি করেছেন যিনি।
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * সর্বশেষ আপডেট করেছেন যিনি।
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * মূল শিক্ষাবর্ষ।
     */
    public function parentYear(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_year_id'
        );
    }

    /**
     * এই শিক্ষাবর্ষের অধীনে তৈরি শিক্ষাবর্ষ।
     */
    public function childYears()
    {
        return $this->hasMany(
            self::class,
            'parent_year_id'
        );
    }

    /**
     * সক্রিয় শিক্ষাবর্ষ।
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * বর্তমান শিক্ষাবর্ষ।
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * নির্দিষ্ট প্রতিষ্ঠানের বর্তমান শিক্ষাবর্ষ খুঁজে বের করবে।
     */
    public static function current(?int $institutionId = null): ?self
    {
        if (!$institutionId) {
            $institutionId = auth()->user()?->active_institution_id;
        }

        if (!$institutionId && app()->bound('institution_id')) {
            $institutionId = app('institution_id');
        }

        if (!$institutionId) {
            return null;
        }

        return static::query()
            ->where('institution_id', $institutionId)
            ->where('is_current', true)
            ->where('status', 'active')
            ->first();
    }

    /**
     * শিক্ষাবর্ষের বাংলা নাম, না থাকলে ইংরেজি নাম।
     */
    public function getFullNameAttribute(): string
    {
        return $this->name_bn ?: $this->name;
    }

    /**
     * শিক্ষাবর্ষের নাম বাংলা, ইংরেজি ও আরবি থেকে পাওয়া যাবে।
     */
    public function getLocalizedNameAttribute(): array
    {
        return [
            'en' => $this->name,
            'bn' => $this->name_bn ?: $this->name,
            'ar' => $this->name_ar ?: $this->name,
        ];
    }

    /**
     * স্ট্যাটাসের বাংলা নাম।
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'সক্রিয়',
            'inactive' => 'নিষ্ক্রিয়',
            default => 'অজানা',
        };
    }
}