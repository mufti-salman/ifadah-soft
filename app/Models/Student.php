<?php

namespace App\Models;

use App\Models\Traits\BelongsToInstitution;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory, BelongsToInstitution;

    protected $fillable = [
        'user_id',
        'student_id',
        'institution_id',
        'academic_year_id',
        'class_id',
        'section_id',
        'guardian_user_id',
        'is_hostel',
        'is_transport',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_hostel'    => 'boolean',
        'is_transport' => 'boolean',
        'status'       => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Institution / Madrasa
    |--------------------------------------------------------------------------
    */
    public function institution()
    {
        return $this->belongsTo(
            Madrasa::class,
            'institution_id'
        );
    }

    /*
    | Backward-compatible alias
    */
    public function madrasa()
    {
        return $this->belongsTo(
            Madrasa::class,
            'institution_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Academic Year
    |--------------------------------------------------------------------------
    */
    public function academicYear()
    {
        return $this->belongsTo(
            AcademicYear::class,
            'academic_year_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Class
    |--------------------------------------------------------------------------
    */
    public function class()
    {
        return $this->belongsTo(
            Classes::class,
            'class_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Section
    |--------------------------------------------------------------------------
    */
    public function section()
    {
        return $this->belongsTo(
            Section::class,
            'section_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Guardian
    |--------------------------------------------------------------------------
    */
    public function guardian()
    {
        return $this->belongsTo(
            User::class,
            'guardian_user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Admissions
    |--------------------------------------------------------------------------
    */
    public function admissions()
    {
        return $this->hasMany(
            Admission::class,
            'student_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Creator
    |--------------------------------------------------------------------------
    */
    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Updater
    |--------------------------------------------------------------------------
    */
    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Fee Groups
    |--------------------------------------------------------------------------
    */
    public function feeGroups()
    {
        return $this->belongsToMany(
            FeeGroup::class,
            'fee_group_student',
            'student_id',
            'fee_group_id'
        )
        ->withPivot('academic_year_id')
        ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Current Fee Group
    |--------------------------------------------------------------------------
    |
    | Academic Year না থাকলেও এখানে null return করবে।
    | 1 ধরে নেওয়া হবে না।
    |
    |--------------------------------------------------------------------------
    */
    public function currentFeeGroup()
    {
        $academicYearId = AcademicYear::where(
            'is_current',
            1
        )->value('id');

        if (!$academicYearId) {
            return null;
        }

        return $this->belongsToMany(
            FeeGroup::class,
            'fee_group_student',
            'student_id',
            'fee_group_id'
        )
        ->wherePivot(
            'academic_year_id',
            $academicYearId
        )
        ->first();
    }
}