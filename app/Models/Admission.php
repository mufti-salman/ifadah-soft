<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'admission_no',
        'student_id',
        'academic_year_id',
        'class_id',
        'admission_date',
        'financial_status',
        'residence_status',
        'admission_type',
        'status',
        'leaving_date',
        'leaving_reason',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'leaving_date'   => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function institution()
{
    return $this->belongsTo(Madrasa::class, 'institution_id');
}

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            /*
            |--------------------------------------------------------------------------
            | Auto Admission Number
            |--------------------------------------------------------------------------
            */

            if (empty($model->admission_no)) {

                $year = date('Y');

                $lastId = static::whereYear('created_at', $year)
                    ->max('id') ?? 0;

                $model->admission_no =
                    'ADM-' .
                    $year .
                    '-' .
                    str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            }

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByYear($query, $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {

            'active'      => 'সক্রিয়',
            'inactive'    => 'নিষ্ক্রিয়',
            'transferred' => 'স্থানান্তরিত',
            'graduated'   => 'পাস',
            'dropped'     => 'ছাড়পত্র',

            default => 'অজানা',
        };
    }

    public function getFinancialLabelAttribute(): string
    {
        return match ($this->financial_status) {

            'solvent'   => 'সচ্ছল',
            'insolvent' => 'অসচ্ছল',
            'orphan'    => 'এতিম',
            'helpless'  => 'অসহায়',

            default => '',
        };
    }

    public function getResidenceLabelAttribute(): string
    {
        return match ($this->residence_status) {

            'resident'     => 'আবাসিক',
            'non-resident' => 'অনাবাসিক',
            'daycare'      => 'ডে-কেয়ার',
            'nightcare'    => 'নাইট কেয়ার',

            default => '',
        };
    }

    public function getAdmissionTypeLabelAttribute(): string
    {
        return match ($this->admission_type) {

            'new' => 'নতুন',
            'old' => 'পুরাতন',

            default => '',
        };
    }

    public function getStudentNameAttribute()
    {
        return
            $this->student->user->name_bn
            ?? $this->student->user->name
            ?? 'N/A';
    }

    public function getStudentCodeAttribute()
    {
        return $this->student->student_id ?? 'N/A';
    }

    public function getClassNameAttribute()
    {
        return $this->class->name_bn
            ?? $this->class->name
            ?? 'N/A';
    }

    public function getAcademicYearNameAttribute()
    {
        return $this->academicYear->name_bn
            ?? $this->academicYear->name
            ?? 'N/A';
    }
}