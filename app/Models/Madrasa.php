<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Madrasa extends Model
{
    use HasFactory;

    /**
     * ----------------------------------------------------------
     * Table
     * ----------------------------------------------------------
     */
    protected $table = 'madrasas';

    /**
     * ----------------------------------------------------------
     * Mass Assignable Fields
     * ----------------------------------------------------------
     */
    protected $fillable = [
        'madrasa_code',
        'name',
        'name_bn',
        'email',
        'phone',
        'address',
        'logo',
        'banner',
        'eiin_no',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * ----------------------------------------------------------
     * Attribute Casts
     * ----------------------------------------------------------
     */
    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * ----------------------------------------------------------
     * Users
     * ----------------------------------------------------------
     *
     * All users belonging to this institution.
     */
    public function users()
    {
        return $this->hasMany(
            User::class,
            'institution_id',
            'id'
        );
    }

    /**
     * ----------------------------------------------------------
     * Students
     * ----------------------------------------------------------
     */
    public function students()
    {
        return $this->hasMany(
            Student::class,
            'institution_id',
            'id'
        );
    }

    /**
     * ----------------------------------------------------------
     * Teachers
     * ----------------------------------------------------------
     *
     * Teacher remains an entity/profile.
     * It is NOT a separate role anymore.
     */
    public function teachers()
    {
        return $this->hasMany(
            Teacher::class,
            'institution_id',
            'id'
        );
    }

    /**
     * ----------------------------------------------------------
     * Institution Permissions
     * ----------------------------------------------------------
     *
     * Permissions assigned to this institution by Super Admin.
     *
     * Pivot:
     * institution_permissions
     *
     * Columns:
     * institution_id
     * permission_id
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'institution_permissions',
            'institution_id',
            'permission_id'
        )->withTimestamps();
    }

    /**
     * ----------------------------------------------------------
     * Creator
     * ----------------------------------------------------------
     */
    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by',
            'id'
        );
    }

    /**
     * ----------------------------------------------------------
     * Updater
     * ----------------------------------------------------------
     */
    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by',
            'id'
        );
    }

    /**
     * ----------------------------------------------------------
     * Active Scope
     * ----------------------------------------------------------
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * ----------------------------------------------------------
     * Inactive Scope
     * ----------------------------------------------------------
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    /**
     * ----------------------------------------------------------
     * Search Scope
     * ----------------------------------------------------------
     *
     * Search by:
     * - Madrasa code
     * - English name
     * - Bangla name
     * - Email
     * - Phone
     * - EIIN
     */
    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('madrasa_code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('name_bn', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('eiin_no', 'like', "%{$search}%");
        });
    }

    /**
     * ----------------------------------------------------------
     * Code Scope
     * ----------------------------------------------------------
     */
    public function scopeByCode($query, $code)
    {
        return $query->where(
            'madrasa_code',
            $code
        );
    }

    /**
     * ----------------------------------------------------------
     * Ordered Scope
     * ----------------------------------------------------------
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    /**
     * ----------------------------------------------------------
     * Display Name Accessor
     * ----------------------------------------------------------
     *
     * Bangla name থাকলে সেটা দেখাবে,
     * না থাকলে English name দেখাবে।
     */
    public function getDisplayNameAttribute()
    {
        return $this->name_bn ?: $this->name;
    }

    /**
     * ----------------------------------------------------------
     * Is Active Accessor
     * ----------------------------------------------------------
     */
    public function getIsActiveAttribute()
    {
        return (int) $this->status === 1;
    }

    /**
     * ----------------------------------------------------------
     * Is Inactive Accessor
     * ----------------------------------------------------------
     */
    public function getIsInactiveAttribute()
    {
        return (int) $this->status === 0;
    }
}