<?php

namespace App\Models;

use App\Models\Traits\BelongsToInstitution;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassTimeSlot extends Model
{
    use HasFactory, BelongsToInstitution;

    protected $table = 'class_time_slots';

    protected $fillable = [
        'institution_id',
        'start_time',
        'end_time',
        'created_by',
        'updated_by',
    ];

    public function routines()
    {
        return $this->hasMany(ClassRoutine::class, 'time_slot_id');
    }

    public function getStartLabelAttribute(): string
    {
        return Carbon::parse($this->start_time)->format('h:i:s A');
    }

    public function getEndLabelAttribute(): string
    {
        return Carbon::parse($this->end_time)->format('h:i:s A');
    }

    public function getRangeLabelAttribute(): string
    {
        return $this->start_label . ' - ' . $this->end_label;
    }
}