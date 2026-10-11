<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemPanel extends Model
{
    use HasFactory;

    /**
     * ----------------------------------------------------------
     * Table
     * ----------------------------------------------------------
     */
    protected $table = 'system_panels';

    /**
     * ----------------------------------------------------------
     * Mass Assignable Fields
     * ----------------------------------------------------------
     */
    protected $fillable = [
        'panel_name',
        'slug',
        'icon',
        'description',
        'serial',
        'is_system',
        'is_active',
    ];

    /**
     * ----------------------------------------------------------
     * Attribute Casts
     * ----------------------------------------------------------
     */
    protected $casts = [
        'serial' => 'integer',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * ----------------------------------------------------------
     * Roles
     * ----------------------------------------------------------
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_system_panel',
            'system_panel_id',
            'role_id'
        )->withTimestamps();
    }

    /**
     * ----------------------------------------------------------
     * Permissions
     * ----------------------------------------------------------
     */
    public function permissions()
    {
        return $this->hasMany(
            Permission::class,
            'system_panel_id',
            'id'
        );
    }

    /**
     * ----------------------------------------------------------
     * Active Permissions
     * ----------------------------------------------------------
     */
    public function activePermissions()
    {
        return $this->hasMany(
            Permission::class,
            'system_panel_id',
            'id'
        )->where(
            'is_active',
            true
        );
    }

    /**
     * ----------------------------------------------------------
     * Active Scope
     * ----------------------------------------------------------
     */
    public function scopeActive($query)
    {
        return $query->where(
            'is_active',
            true
        );
    }

    /**
     * ----------------------------------------------------------
     * Inactive Scope
     * ----------------------------------------------------------
    */
    public function scopeInactive($query)
    {
        return $query->where(
            'is_active',
            false
        );
    }

    /**
     * ----------------------------------------------------------
     * System Panels Scope
     * ----------------------------------------------------------
     */
    public function scopeSystem($query)
    {
        return $query->where(
            'is_system',
            true
        );
    }

    /**
     * ----------------------------------------------------------
     * Custom Panels Scope
     * ----------------------------------------------------------
     */
    public function scopeCustom($query)
    {
        return $query->where(
            'is_system',
            false
        );
    }

    /**
     * ----------------------------------------------------------
     * Slug Scope
     * ----------------------------------------------------------
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where(
            'slug',
            $slug
        );
    }

    /**
     * ----------------------------------------------------------
     * Ordered Scope
     * ----------------------------------------------------------
     */
    public function scopeOrdered($query)
    {
        return $query
            ->orderBy('serial')
            ->orderBy('id');
    }

    /**
     * ----------------------------------------------------------
     * Has Permission
     * ----------------------------------------------------------
     */
    public function hasPermission($permissionSlug)
    {
        return $this->permissions()
            ->where(
                'slug',
                $permissionSlug
            )
            ->where(
                'is_active',
                true
            )
            ->exists();
    }

    /**
     * ----------------------------------------------------------
     * Active Status Accessor
     * ----------------------------------------------------------
     */
    public function getIsAvailableAttribute()
    {
        return $this->is_active === true;
    }

    /**
     * ----------------------------------------------------------
     * System Panel Accessor
     * ----------------------------------------------------------
     */
    public function getIsSystemPanelAttribute()
    {
        return $this->is_system === true;
    }
}