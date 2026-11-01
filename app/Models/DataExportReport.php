<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataExportReport extends Model
{
    protected $fillable = [
        'institution_id',
        'user_id',
        'report_name',
        'columns',
    ];

    protected $casts = [
        'columns' => 'array',
    ];

    public function customColumns()
    {
        return $this->hasMany(
            DataExportCustomColumn::class,
            'institution_id',
            'institution_id'
        );
    }
}