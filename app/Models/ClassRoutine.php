<?php

namespace App\Models;

use App\Models\Traits\BelongsToInstitution;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoutine extends Model
{
    use HasFactory, BelongsToInstitution;

    protected $table = 'class_routines';

    public const DAYS = [
        'friday'    => 'শুক্রবার',
        'saturday'  => 'শনিবার',
        'sunday'    => 'রবিবার',
        'monday'    => 'সোমবার',
        'tuesday'   => 'মঙ্গলবার',
        'wednesday' => 'বুধবার',
        'thursday'  => 'বৃহস্পতিবার',
    ];

    protected $fillable = [
        'institution_id',
        'academic_year_id',
        'class_id',
        'section_id',
        'subject_id',
        'teacher_id',
        'time_slot_id',
        'day',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function timeSlot()
    {
        return $this->belongsTo(ClassTimeSlot::class, 'time_slot_id');
    }

    public function getDayLabelAttribute(): string
    {
        return self::DAYS[$this->day] ?? $this->day;
    }
}