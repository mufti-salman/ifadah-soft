<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    /**
     * ----------------------------------------------------------
     * Table
     * ----------------------------------------------------------
     */
    protected $table = 'permissions';

    /**
     * ----------------------------------------------------------
     * Mass Assignable Fields
     * ----------------------------------------------------------
     */
    protected $fillable = [
        'system_panel_id',
        'module',
        'permission_name',
        'slug',
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
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'serial' => 'integer',
    ];

    /**
     * ----------------------------------------------------------
     * System Panel
     * ----------------------------------------------------------
     */
    public function systemPanel()
    {
        return $this->belongsTo(
            SystemPanel::class,
            'system_panel_id',
            'id'
        );
    }

    /**
     * ----------------------------------------------------------
     * Roles
     * ----------------------------------------------------------
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_permissions',
            'permission_id',
            'role_id'
        )->withTimestamps();
    }

    /**
     * ----------------------------------------------------------
     * Users
     * ----------------------------------------------------------
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_permissions',
            'permission_id',
            'user_id'
        )
        ->withPivot('allow')
        ->withTimestamps();
    }

    /**
     * ----------------------------------------------------------
     * Institutions
     * ----------------------------------------------------------
     */
    public function institutions()
    {
        return $this->belongsToMany(
            Madrasa::class,
            'institution_permissions',
            'permission_id',
            'institution_id'
        )->withTimestamps();
    }

    /**
     * ----------------------------------------------------------
     * Active Scope
     * ----------------------------------------------------------
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * ----------------------------------------------------------
     * Inactive Scope
     * ----------------------------------------------------------
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * ----------------------------------------------------------
     * System Permissions Scope
     * ----------------------------------------------------------
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * ----------------------------------------------------------
     * Custom Permissions Scope
     * ----------------------------------------------------------
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * ----------------------------------------------------------
     * Module Scope
     * ----------------------------------------------------------
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * ----------------------------------------------------------
     * Slug Scope
     * ----------------------------------------------------------
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * ----------------------------------------------------------
     * Panel Scope
     * ----------------------------------------------------------
     */
    public function scopeByPanel($query, $panelId)
    {
        return $query->where(
            'system_panel_id',
            $panelId
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
     * Check Active
     * ----------------------------------------------------------
     */
    public function getIsAvailableAttribute()
    {
        return $this->is_active === true;
    }

    /**
     * ----------------------------------------------------------
     * Check System Permission
     * ----------------------------------------------------------
     */
    public function getIsSystemPermissionAttribute()
    {
        return $this->is_system === true;
    }
}