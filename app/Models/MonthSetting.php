<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthSetting extends Model
{
    use HasFactory;

protected $fillable = [
    'institution_id',
    'academic_year',
    'class_id',
    'calendar_type',
    'month_1',
    'month_2',
    'month_3',
    'month_4',
    'month_5',
    'month_6',
    'month_7',
    'month_8',
    'month_9',
    'month_10',
    'month_11',
    'month_12',
];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Madrasa::class, 'institution_id');
    }

    public function studentClass(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function getMonthsArray(): array
    {
        return [
            1  => $this->month_1,
            2  => $this->month_2,
            3  => $this->month_3,
            4  => $this->month_4,
            5  => $this->month_5,
            6  => $this->month_6,
            7  => $this->month_7,
            8  => $this->month_8,
            9  => $this->month_9,
            10 => $this->month_10,
            11 => $this->month_11,
            12 => $this->month_12,
        ];
    }
}