<?php

namespace App\Models;

use App\Models\Traits\BelongsToInstitution;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    use HasFactory, BelongsToInstitution;

    protected $table = 'teachers';

    protected $fillable = [
        'user_id',
        'institution_id',

        // Teacher Identity
        'teacher_id',

        // Professional Information
        'designation',
        'joining_date',

        // Educational Information
        'pass_department',
        'pass_year',
        'pass_date',
        'qualification',
        'completion_institution',

        // Experience & Expertise
        'experience',
        'expertise_subjects',

        // Salary
        'salary_scale',
        'basic_salary',

        // Class Teacher
        'is_class_teacher',
        'class_teacher_for',

        // Additional
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'pass_date' => 'date',
            'pass_year' => 'integer',
            'basic_salary' => 'decimal:2',
            'is_class_teacher' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Institution
    |--------------------------------------------------------------------------
    */

    public function institution(): BelongsTo
    {
        return $this->belongsTo(
            Madrasa::class,
            'institution_id'
        );
    }

    /**
     * Backward compatibility.
     */
    public function madrasa(): BelongsTo
    {
        return $this->belongsTo(
            Madrasa::class,
            'institution_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Class Teacher
    |--------------------------------------------------------------------------
    */

    public function classTeacherFor(): BelongsTo
    {
        return $this->belongsTo(
            Classes::class,
            'class_teacher_for'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return (string) (
            $this->user?->name_bn
            ?: $this->user?->name
            ?: ''
        );
    }

    public function getPhoneAttribute(): string
    {
        return (string) (
            $this->user?->phone ?? ''
        );
    }

    public function getUserCodeAttribute(): string
    {
        return (string) (
            $this->user?->institution_user_id ?? ''
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Teacher Code
    |--------------------------------------------------------------------------
    |
    | Teacher Code:
    |
    | 1
    | 2
    | 3
    | 4
    | 5
    |
    | Institution-wise separate sequence.
    |
    */

    public static function generateTeacherCode(
        int $institutionId
    ): string {

        $lastTeacher = static::query()
            ->where(
                'institution_id',
                $institutionId
            )
            ->whereNotNull('teacher_id')
            ->orderByRaw(
                'CAST(teacher_id AS UNSIGNED) DESC'
            )
            ->first();

        if (!$lastTeacher) {
            return '1';
        }

        $lastCode = (int) $lastTeacher->teacher_id;

        return (string) ($lastCode + 1);
    }
}