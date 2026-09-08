<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'role_name',
        'slug',
        'description',
        'level',
        'is_system',
    ];


    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'level'     => 'integer',
        'is_system' => 'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


    /**
     * Users assigned to this role
     */
    public function users()
    {
        return $this->hasMany(
            User::class,
            'role_id',
            'id'
        );
    }


    /**
     * System Panels assigned to this role
     */
    public function systemPanels()
    {
        return $this->belongsToMany(
            SystemPanel::class,
            'role_system_panel',
            'role_id',
            'system_panel_id'
        )
        ->withTimestamps();
    }


    /**
     * Permissions assigned to this role
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
            'permission_id'
        )
        ->withTimestamps();
    }


    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    |
    | Current Role Hierarchy:
    |
    | super_admin
    |     ↓
    | support_admin
    |     ↓
    | user_admin
    |     ↓
    | user
    |     ↓
    | student
    |
    | Teacher আলাদা Role নয়।
    | Teacher = User
    |
    |--------------------------------------------------------------------------
    */


    /**
     * Check role by slug
     *
     * Example:
     *
     * $user->role->hasSlug('user_admin')
     */
    public function hasSlug(string $slug): bool
    {
        return strtolower(
            trim((string) $this->slug)
        ) === strtolower(
            trim($slug)
        );
    }


    /**
     * Super Admin
     */
    public function getIsSuperAdminAttribute(): bool
    {
        return $this->hasSlug('super_admin');
    }


    /**
     * Support Admin
     */
    public function getIsSupportAdminAttribute(): bool
    {
        return $this->hasSlug('support_admin');
    }


    /**
     * User Admin
     */
    public function getIsUserAdminAttribute(): bool
    {
        return $this->hasSlug('user_admin');
    }


    /**
     * User
     *
     * Teacher / Staff / Other institution users
     * সবাই User role-এর অধীনে থাকবে।
     */
    public function getIsUserAttribute(): bool
    {
        return $this->hasSlug('user');
    }


    /**
     * Student
     */
    public function getIsStudentAttribute(): bool
    {
        return $this->hasSlug('student');
    }


    /*
    |--------------------------------------------------------------------------
    | Backward Compatibility
    |--------------------------------------------------------------------------
    |
    | পুরোনো code যদি $role->is_admin,
    | $role->is_madrasa,
    | $role->is_teacher,
    | $role->is_guardian ব্যবহার করে,
    | তাহলে যেন application break না করে।
    |
    |--------------------------------------------------------------------------
    */


    /**
     * Legacy Admin
     *
     * Current system-এ Admin বলতে User Admin।
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->is_user_admin;
    }


    /**
     * Legacy Madrasa Admin
     *
     * Current system-এ Madrasa Admin = User Admin।
     */
    public function getIsMadrasaAttribute(): bool
    {
        return $this->is_user_admin;
    }


    /**
     * Legacy Teacher
     *
     * Current system-এ Teacher আলাদা Role নয়।
     * Teacher = User.
     */
    public function getIsTeacherAttribute(): bool
    {
        return $this->is_user;
    }


    /**
     * Legacy Guardian
     *
     * Guardian আলাদা Role নয়।
     */
    public function getIsGuardianAttribute(): bool
    {
        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | Role Hierarchy Helpers
    |--------------------------------------------------------------------------
    */


    /**
     * Check whether this role has higher level than another role.
     */
    public function isHigherThan(?Role $role): bool
    {
        if (!$role) {
            return true;
        }

        return $this->level > $role->level;
    }


    /**
     * Check whether this role has equal or higher level.
     */
    public function isAtLeast(?Role $role): bool
    {
        if (!$role) {
            return true;
        }

        return $this->level >= $role->level;
    }


    /**
     * Check whether this role is lower than another role.
     */
    public function isLowerThan(?Role $role): bool
    {
        if (!$role) {
            return false;
        }

        return $this->level < $role->level;
    }


    /*
    |--------------------------------------------------------------------------
    | System Role Helper
    |--------------------------------------------------------------------------
    */


    /**
     * Is this a system role?
     */
    public function getIsSystemRoleAttribute(): bool
    {
        return (bool) $this->is_system;
    }


    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */


    /**
     * Only system roles
     */
    public function scopeSystemRoles($query)
    {
        return $query->where(
            'is_system',
            true
        );
    }


    /**
     * Only non-system roles
     */
    public function scopeCustomRoles($query)
    {
        return $query->where(
            'is_system',
            false
        );
    }


    /**
     * Find role by slug
     */
    public function scopeBySlug($query, string $slug)
    {
        return $query->where(
            'slug',
            $slug
        );
    }


    /**
     * Find role by level
     */
    public function scopeByLevel($query, int $level)
    {
        return $query->where(
            'level',
            $level
        );
    }


    /**
     * Order roles by hierarchy
     *
     * Highest level first.
     */
    public function scopeOrdered($query)
    {
        return $query
            ->orderByDesc('level')
            ->orderBy('role_name');
    }
}