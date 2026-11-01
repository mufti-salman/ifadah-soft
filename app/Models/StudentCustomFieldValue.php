<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCustomFieldValue extends Model
{
    protected $fillable = [
        'institution_id',
        'student_id',
        'academic_year_id',
        'custom_column_id',
        'value',
    ];

    public function customColumn()
    {
        return $this->belongsTo(
            DataExportCustomColumn::class,
            'custom_column_id'
        );
    }
}