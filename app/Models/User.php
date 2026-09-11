<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * ---------------------------------------------------------
     * Table
     * ---------------------------------------------------------
     */
    protected $table = 'users';

    /**
     * ---------------------------------------------------------
     * Fillable
     * ---------------------------------------------------------
     */
    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | Institution & Role
        |--------------------------------------------------------------------------
        */
        'institution_id',
        'role_id',

        /*
        |--------------------------------------------------------------------------
        | User Code
        |--------------------------------------------------------------------------
        */
        'institution_user_id',
        'custom_id',
        'code_generation_type',

        /*
        |--------------------------------------------------------------------------
        | Basic Information
        |--------------------------------------------------------------------------
        */
        'username',
        'email',
        'password',
        'name',
        'name_bn',
        'photo',
        'cover_photo',

        /*
        |--------------------------------------------------------------------------
        | Phone
        |--------------------------------------------------------------------------
        */
        'phone',
        'phone2',
        'phone_owner',

        /*
        |--------------------------------------------------------------------------
        | Personal Information
        |--------------------------------------------------------------------------
        */
        'gender',
        'date_of_birth',
        'age',
        'blood_group',
        'religion',

        /*
        |--------------------------------------------------------------------------
        | Identification
        |--------------------------------------------------------------------------
        */
        'nid',
        'birth_certificate',

        /*
        |--------------------------------------------------------------------------
        | Present Address
        |--------------------------------------------------------------------------
        */
        'present_division_id',
        'present_district_id',
        'present_upazila_id',
        'present_union',
        'present_post_office',
        'present_village_road',
        'present_postal_code',
        'present_address_full',

        /*
        |--------------------------------------------------------------------------
        | Permanent Address
        |--------------------------------------------------------------------------
        */
        'permanent_division_id',
        'permanent_district_id',
        'permanent_upazila_id',
        'permanent_union',
        'permanent_post_office',
        'permanent_village_road',
        'permanent_postal_code',
        'permanent_address_full',

        /*
        |--------------------------------------------------------------------------
        | Parents / Guardian
        |--------------------------------------------------------------------------
        */
        'father_name',
        'father_phone',
        'mother_name',
        'mother_phone',
        'guardian_name',
        'guardian_phone',
        'guardian_relation',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */
        'status',

        /*
        |--------------------------------------------------------------------------
        | Login Meta
        |--------------------------------------------------------------------------
        */
        'email_verified_at',
        'last_login_at',
        'last_login_ip',

        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */
        'created_by',
        'updated_by',
    ];

    /**
     * ---------------------------------------------------------
     * Hidden
     * ---------------------------------------------------------
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * ---------------------------------------------------------
     * Casts
     * ---------------------------------------------------------
     */
    protected function casts(): array
    {
        return [

            'email_verified_at' =>
                'datetime',

            'last_login_at' =>
                'datetime',

            'date_of_birth' =>
                'date',

            'password' =>
                'hashed',

            'status' =>
                'integer',

            'code_generation_type' =>
                'string',
        ];
    }

    /**
     * ---------------------------------------------------------
     * Institution
     * ---------------------------------------------------------
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(
            Madrasa::class,
            'institution_id'
        );
    }

    /**
     * ---------------------------------------------------------
     * Madrasa Alias
     * ---------------------------------------------------------
     */
    public function madrasa(): BelongsTo
    {
        return $this->belongsTo(
            Madrasa::class,
            'institution_id'
        );
    }

    /**
     * ---------------------------------------------------------
     * Role
     * ---------------------------------------------------------
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(
            Role::class,
            'role_id'
        );
    }

    /**
     * ---------------------------------------------------------
     * Student
     * ---------------------------------------------------------
     */
    public function student(): HasOne
    {
        return $this->hasOne(
            Student::class,
            'user_id'
        );
    }

    /**
     * ---------------------------------------------------------
     * Teacher
     * ---------------------------------------------------------
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(
            Teacher::class,
            'user_id'
        );
    }

    /**
     * ---------------------------------------------------------
     * Creator
     * ---------------------------------------------------------
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'created_by'
        );
    }

    /**
     * ---------------------------------------------------------
     * Updater
     * ---------------------------------------------------------
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'updated_by'
        );
    }

    /**
     * ---------------------------------------------------------
     * Direct Permissions
     * ---------------------------------------------------------
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'user_permissions',
            'user_id',
            'permission_id'
        )->withPivot('allow')
         ->withTimestamps();
    }

    /**
     * ---------------------------------------------------------
     * Allowed Permissions
     * ---------------------------------------------------------
     */
    public function allowedPermissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'user_permissions',
            'user_id',
            'permission_id'
        )
        ->wherePivot('allow', true)
        ->withTimestamps();
    }

    /**
     * ---------------------------------------------------------
     * Active Institution
     * ---------------------------------------------------------
     */
    public function getActiveInstitutionAttribute()
    {
        $institutionId =
            session('active_institution_id');

        /*
        |--------------------------------------------------------------------------
        | Super Admin selected institution
        |--------------------------------------------------------------------------
        */
        if (
            $this->is_super_admin
            && $institutionId
        ) {

            return Madrasa::query()
                ->find($institutionId);
        }

        /*
        |--------------------------------------------------------------------------
        | Normal institution users
        |--------------------------------------------------------------------------
        */
        if ($this->institution_id) {

            return $this->institution;
        }

        return null;
    }

    /**
     * ---------------------------------------------------------
     * Role Slug
     * ---------------------------------------------------------
     */
    public function getRoleSlugAttribute(): ?string
    {
        return strtolower(
            trim(
                (string) optional($this->role)->slug
            )
        ) ?: null;
    }

    /**
     * ---------------------------------------------------------
     * Role Checks
     * ---------------------------------------------------------
     */
    public function getIsSuperAdminAttribute(): bool
    {
        return $this->roleSlug === 'super_admin';
    }

    public function getIsSupportAdminAttribute(): bool
    {
        return $this->roleSlug === 'support_admin';
    }

    public function getIsUserAdminAttribute(): bool
    {
        return $this->roleSlug === 'user_admin';
    }

    public function getIsUserAttribute(): bool
    {
        return $this->roleSlug === 'user';
    }

    public function getIsStudentAttribute(): bool
    {
        return $this->roleSlug === 'student';
    }

    /**
     * ---------------------------------------------------------
     * Institution User ID Generator
     * ---------------------------------------------------------
     *
     * IMPORTANT:
     *
     * Only AUTO generated codes are considered.
     *
     * Manual codes such as:
     *
     * 1001
     * 5000
     * 9000
     *
     * will NOT affect the next automatic code.
     *
     * Example:
     *
     * 101 -> auto
     * 102 -> auto
     * 1001 -> manual
     * 103 -> auto
     *
     * Next auto = 104
     *
     * ---------------------------------------------------------
     */
    public static function generateInstitutionUserId(
        $institutionId,
        $roleSlug
    ): string {

        $roleSlug = strtolower(
            trim(
                (string) $roleSlug
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Starting Code
        |--------------------------------------------------------------------------
        */
        $prefixes = [

            'student' =>
                101,

            'user' =>
                201,

            'user_admin' =>
                401,

            'support_admin' =>
                501,

            'super_admin' =>
                601,
        ];

        $prefix =
            $prefixes[$roleSlug]
            ?? 201;

        /*
        |--------------------------------------------------------------------------
        | Find Role
        |--------------------------------------------------------------------------
        */
        $role = Role::query()
            ->where(
                'slug',
                $roleSlug
            )
            ->first();

        if (!$role) {
            return (string) $prefix;
        }

        /*
        |--------------------------------------------------------------------------
        | Last AUTO Code
        |--------------------------------------------------------------------------
        |
        | Manual records are intentionally excluded.
        |--------------------------------------------------------------------------
        */
        $lastUser = static::query()
            ->where(
                'institution_id',
                $institutionId
            )
            ->where(
                'role_id',
                $role->id
            )
            ->whereNotNull(
                'institution_user_id'
            )
            ->where(function ($query) {

                /*
                |--------------------------------------------------------------------------
                | New records
                |--------------------------------------------------------------------------
                */
                $query->where(
                    'code_generation_type',
                    'auto'
                )

                /*
                |--------------------------------------------------------------------------
                | Backward compatibility
                |--------------------------------------------------------------------------
                |
                | Existing users created before this field was added
                | have NULL. Treat them as AUTO.
                |--------------------------------------------------------------------------
                */
                ->orWhereNull(
                    'code_generation_type'
                );
            })
            ->orderByRaw(
                'CAST(institution_user_id AS UNSIGNED) DESC'
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | No Previous Auto Code
        |--------------------------------------------------------------------------
        */
        if (
            !$lastUser
            || !$lastUser->institution_user_id
        ) {
            return (string) $prefix;
        }

        /*
        |--------------------------------------------------------------------------
        | Next Auto Code
        |--------------------------------------------------------------------------
        */
        $lastId =
            (int) $lastUser->institution_user_id;

        return (string) (
            $lastId + 1
        );
    }

    /**
     * ---------------------------------------------------------
     * Preview Institution User ID
     * ---------------------------------------------------------
     */
    public static function previewInstitutionUserId(
        $institutionId,
        $roleSlug
    ): ?string {

        if (!$institutionId) {
            return null;
        }

        $roleSlug = strtolower(
            trim(
                (string) $roleSlug
            )
        );

        /*
        |--------------------------------------------------------------------------
        | System-level roles
        |--------------------------------------------------------------------------
        */
        if (
            in_array(
                $roleSlug,
                [
                    'support_admin',
                    'user_admin',
                ],
                true
            )
        ) {
            return null;
        }

        return static::generateInstitutionUserId(
            $institutionId,
            $roleSlug
        );
    }

    /**
     * ---------------------------------------------------------
     * Has Permission
     * ---------------------------------------------------------
     */
    public function hasPermission(
        string $permissionSlug
    ): bool {

        $permission = Permission::query()
            ->where(
                'slug',
                $permissionSlug
            )
            ->where(
                'is_active',
                true
            )
            ->first();

        if (!$permission) {
            return false;
        }

        $roleSlug = strtolower(
            trim(
                (string) optional($this->role)->slug
            )
        );

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */
        if ($roleSlug === 'super_admin') {

            $activeInstitutionId =
                session(
                    'active_institution_id'
                );

            /*
            |--------------------------------------------------------------------------
            | System Mode
            |--------------------------------------------------------------------------
            */
            if (!$activeInstitutionId) {
                return true;
            }

            /*
            |--------------------------------------------------------------------------
            | Institution Mode
            |--------------------------------------------------------------------------
            */
            $institution =
                Madrasa::query()
                    ->find(
                        $activeInstitutionId
                    );

            if (!$institution) {
                return false;
            }

            return $institution
                ->permissions()
                ->where(
                    'permissions.id',
                    $permission->id
                )
                ->where(
                    'permissions.is_active',
                    true
                )
                ->exists();
        }

        /*
        |--------------------------------------------------------------------------
        | SUPPORT ADMIN
        |--------------------------------------------------------------------------
        */
        if (
            $roleSlug === 'support_admin'
        ) {

            $userPermission =
                $this->permissions()
                    ->where(
                        'permissions.id',
                        $permission->id
                    )
                    ->first();

            if ($userPermission) {

                return (bool)
                    $userPermission
                        ->pivot
                        ->allow;
            }

            if (!$this->role) {
                return false;
            }

            return $this->role
                ->permissions()
                ->where(
                    'permissions.id',
                    $permission->id
                )
                ->where(
                    'permissions.is_active',
                    true
                )
                ->exists();
        }

        /*
        |--------------------------------------------------------------------------
        | INSTITUTION ROLES
        |--------------------------------------------------------------------------
        */
        $institutionRoles = [
            'user_admin',
            'user',
            'student',
        ];

        if (
            in_array(
                $roleSlug,
                $institutionRoles,
                true
            )
        ) {

            $institution =
                $this->activeInstitution;

            if (!$institution) {
                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | Institution Permission Boundary
            |--------------------------------------------------------------------------
            */
            $institutionHasPermission =
                $institution
                    ->permissions()
                    ->where(
                        'permissions.id',
                        $permission->id
                    )
                    ->where(
                        'permissions.is_active',
                        true
                    )
                    ->exists();

            if (!$institutionHasPermission) {
                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | Direct User Permission
            |--------------------------------------------------------------------------
            */
            $userPermission =
                $this->permissions()
                    ->where(
                        'permissions.id',
                        $permission->id
                    )
                    ->first();

            if ($userPermission) {

                return (bool)
                    $userPermission
                        ->pivot
                        ->allow;
            }

            /*
            |--------------------------------------------------------------------------
            | Role Permission
            |--------------------------------------------------------------------------
            */
            if (!$this->role) {
                return false;
            }

            return $this->role
                ->permissions()
                ->where(
                    'permissions.id',
                    $permission->id
                )
                ->where(
                    'permissions.is_active',
                    true
                )
                ->exists();
        }

        return false;
    }

    /**
     * ---------------------------------------------------------
     * Has Panel
     * ---------------------------------------------------------
     */
    public function hasPanel(
        string $panelSlug
    ): bool {

        $panel = SystemPanel::query()
            ->where(
                'slug',
                $panelSlug
            )
            ->where(
                'is_active',
                true
            )
            ->first();

        if (!$panel) {
            return false;
        }

        $roleSlug = strtolower(
            trim(
                (string) optional($this->role)->slug
            )
        );

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */
        if ($roleSlug === 'super_admin') {

            $activeInstitutionId =
                session(
                    'active_institution_id'
                );

            /*
            |--------------------------------------------------------------------------
            | System Mode
            |--------------------------------------------------------------------------
            */
            if (!$activeInstitutionId) {
                return true;
            }

            /*
            |--------------------------------------------------------------------------
            | Institution Mode
            |--------------------------------------------------------------------------
            */
            $institution =
                Madrasa::query()
                    ->find(
                        $activeInstitutionId
                    );

            if (!$institution) {
                return false;
            }

            return $institution
                ->permissions()
                ->where(
                    'permissions.system_panel_id',
                    $panel->id
                )
                ->where(
                    'permissions.is_active',
                    true
                )
                ->exists();
        }

        if (!$this->role) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | SUPPORT ADMIN
        |--------------------------------------------------------------------------
        */
        if (
            $roleSlug === 'support_admin'
        ) {

            return $this->role
                ->systemPanels()
                ->where(
                    'system_panels.id',
                    $panel->id
                )
                ->where(
                    'system_panels.is_active',
                    true
                )
                ->exists();
        }

        /*
        |--------------------------------------------------------------------------
        | INSTITUTION ROLES
        |--------------------------------------------------------------------------
        */
        $institutionRoles = [
            'user_admin',
            'user',
            'student',
        ];

        if (
            in_array(
                $roleSlug,
                $institutionRoles,
                true
            )
        ) {

            $institution =
                $this->activeInstitution;

            if (!$institution) {
                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | Institution Permission Boundary
            |--------------------------------------------------------------------------
            */
            $institutionHasPanelPermission =
                $institution
                    ->permissions()
                    ->where(
                        'permissions.system_panel_id',
                        $panel->id
                    )
                    ->where(
                        'permissions.is_active',
                        true
                    )
                    ->exists();

            if (!$institutionHasPanelPermission) {
                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | Role Panel Permission
            |--------------------------------------------------------------------------
            */
            return $this->role
                ->systemPanels()
                ->where(
                    'system_panels.id',
                    $panel->id
                )
                ->where(
                    'system_panels.is_active',
                    true
                )
                ->exists();
        }

        return false;
    }

    /**
     * ---------------------------------------------------------
     * Active Scope
     * ---------------------------------------------------------
     */
    public function scopeActive(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            1
        );
    }

    /**
     * ---------------------------------------------------------
     * Inactive Scope
     * ---------------------------------------------------------
     */
    public function scopeInactive(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            0
        );
    }
}