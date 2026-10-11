<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\BelongsToInstitution;

class Classes extends Model
{
    use HasFactory, BelongsToInstitution;

    protected $table = 'classes';

    protected $fillable = [
        'institution_id',
        'name',
        'name_bn',
        'level',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    public function sections()
    {
        return $this->hasMany(Section::class, 'class_id', 'id');
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class, 'class_id');
    }

    public function institution()
    {
        return $this->belongsTo(Madrasa::class, 'institution_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getSectionsCountAttribute()
    {
        return $this->sections()->count();
    }

    public function activeSections()
    {
        return $this->hasMany(Section::class, 'class_id', 'id')
            ->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function getLevelLabelAttribute()
    {
        return match ($this->level) {
            'preschool' => 'প্রাক-প্রাথমিক',
            'primary' => 'প্রাথমিক',
            'middle' => 'মাধ্যমিক',
            'high' => 'উচ্চ মাধ্যমিক',
            'higher_secondary' => 'উচ্চ মাধ্যমিক',
            default => 'অন্যান্য',
        };
    }

    public function getFullNameAttribute()
    {
        return $this->name_bn ?: $this->name;
    }

    public function getStatusLabelAttribute()
    {
        return $this->status == 'active'
            ? 'সক্রিয়'
            : 'নিষ্ক্রিয়';
    }
}