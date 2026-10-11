<?php

namespace App\Models;

use App\Models\Division;
use App\Models\District;
use App\Models\Upazila;
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
        |----------------------------------------------------------------------
        | Institution & Role
        |----------------------------------------------------------------------
        */
        'institution_id',
        'role_id',

        /*
        |----------------------------------------------------------------------
        | User Code
        |----------------------------------------------------------------------
        */
        'institution_user_id',
        'custom_id',
        'code_generation_type',

        /*
        |----------------------------------------------------------------------
        | Basic Information
        |----------------------------------------------------------------------
        */
        'username',
        'email',
        'password',
        'name',
        'name_bn',
        'photo',
        'cover_photo',

        /*
        |----------------------------------------------------------------------
        | Phone
        |----------------------------------------------------------------------
        */
        'phone',
        'phone2',
        'phone_owner',

        /*
        |----------------------------------------------------------------------
        | Personal Information
        |----------------------------------------------------------------------
        */
        'gender',
        'date_of_birth',
        'age',
        'blood_group',
        'religion',

        /*
        |----------------------------------------------------------------------
        | Identification
        |----------------------------------------------------------------------
        */
        'nid',
        'birth_certificate',

        /*
        |----------------------------------------------------------------------
        | Present Address
        |----------------------------------------------------------------------
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
        |----------------------------------------------------------------------
        | Permanent Address
        |----------------------------------------------------------------------
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
        |----------------------------------------------------------------------
        | Parents / Guardian
        |----------------------------------------------------------------------
        */
        'father_name',
        'father_phone',
        'mother_name',
        'mother_phone',
        'guardian_name',
        'guardian_phone',
        'guardian_relation',

        /*
        |----------------------------------------------------------------------
        | Status
        |----------------------------------------------------------------------
        */
        'status',

        /*
        |----------------------------------------------------------------------
        | Login Meta
        |----------------------------------------------------------------------
        */
        'email_verified_at',
        'last_login_at',
        'last_login_ip',

        /*
        |----------------------------------------------------------------------
        | Audit
        |----------------------------------------------------------------------
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
 * Present Division
 * ---------------------------------------------------------
 */
public function presentDivision(): BelongsTo
{
    return $this->belongsTo(
        Division::class,
        'present_division_id'
    );
}

/**
 * ---------------------------------------------------------
 * Present District
 * ---------------------------------------------------------
 */
public function presentDistrict(): BelongsTo
{
    return $this->belongsTo(
        District::class,
        'present_district_id'
    );
}

/**
 * ---------------------------------------------------------
 * Present Upazila
 * ---------------------------------------------------------
 */
public function presentUpazila(): BelongsTo
{
    return $this->belongsTo(
        Upazila::class,
        'present_upazila_id'
    );
}

/**
 * ---------------------------------------------------------
 * Permanent Division
 * ---------------------------------------------------------
 */
public function permanentDivision(): BelongsTo
{
    return $this->belongsTo(
        Division::class,
        'permanent_division_id'
    );
}

/**
 * ---------------------------------------------------------
 * Permanent District
 * ---------------------------------------------------------
 */
public function permanentDistrict(): BelongsTo
{
    return $this->belongsTo(
        District::class,
        'permanent_district_id'
    );
}

/**
 * ---------------------------------------------------------
 * Permanent Upazila
 * ---------------------------------------------------------
 */
public function permanentUpazila(): BelongsTo
{
    return $this->belongsTo(
        Upazila::class,
        'permanent_upazila_id'
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
        )
        ->withPivot('allow')
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
        /*
        |----------------------------------------------------------------------
        | Active Institution ID
        |----------------------------------------------------------------------
        */

        $activeInstitutionId = $this->active_institution_id;

        /*
        |----------------------------------------------------------------------
        | No Active Institution
        |----------------------------------------------------------------------
        */

        if (!$activeInstitutionId) {
            return null;
        }

        /*
        |----------------------------------------------------------------------
        | Get Active Institution
        |----------------------------------------------------------------------
        */

        return Madrasa::query()
            ->whereKey($activeInstitutionId)
            ->first();
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

    /**
     * ---------------------------------------------------------
     * Active Institution ID
     * ---------------------------------------------------------
     */
    public function getActiveInstitutionIdAttribute()
    {
        /*
        |----------------------------------------------------------------------
        | Super Admin
        |----------------------------------------------------------------------
        */

        if ($this->is_super_admin) {

            return session('active_institution_id')
                ?: $this->institution_id;
        }

        /*
        |----------------------------------------------------------------------
        | Normal Institution User
        |----------------------------------------------------------------------
        */

        return $this->institution_id;
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
     * Generate Institution User ID
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
        |----------------------------------------------------------------------
        | Starting Code
        |----------------------------------------------------------------------
        */

        $prefixes = [

            'student' =>
                101,

            'user' =>
                201,

            'user_admin' =>
                301,

            'support_admin' =>
                401,

            'super_admin' =>
                601,

        ];

        $startingCode =
            $prefixes[$roleSlug]
            ?? 201;

        /*
        |----------------------------------------------------------------------
        | Find Role
        |----------------------------------------------------------------------
        */

        $role = Role::query()
            ->where(
                'slug',
                $roleSlug
            )
            ->first();

        if (!$role) {

            return (string) $startingCode;
        }

        /*
        |----------------------------------------------------------------------
        | Base Query
        |----------------------------------------------------------------------
        */

        $query = static::query()
            ->where(
                'role_id',
                $role->id
            )
            ->whereNotNull(
                'institution_user_id'
            )
            ->where(function ($query) {

                $query
                    ->where(
                        'code_generation_type',
                        'auto'
                    )
                    ->orWhereNull(
                        'code_generation_type'
                    );

            });

        /*
        |----------------------------------------------------------------------
        | Institution Based Roles
        |----------------------------------------------------------------------
        */

        $institutionRoles = [

            'student',
            'user',
            'user_admin',

        ];

        if (
            in_array(
                $roleSlug,
                $institutionRoles,
                true
            )
        ) {

            if (!$institutionId) {

                return (string) $startingCode;
            }

            $query->where(
                'institution_id',
                $institutionId
            );
        }

        /*
        |----------------------------------------------------------------------
        | System Roles
        |----------------------------------------------------------------------
        */

        $lastUser =
            $query
                ->orderByRaw(
                    'CAST(institution_user_id AS UNSIGNED) DESC'
                )
                ->first();

        /*
        |----------------------------------------------------------------------
        | First Auto Code
        |----------------------------------------------------------------------
        */

        if (
            !$lastUser
            ||
            !$lastUser->institution_user_id
        ) {

            return (string) $startingCode;
        }

        /*
        |----------------------------------------------------------------------
        | Next Auto Code
        |----------------------------------------------------------------------
        */

        return (string) (
            ((int) $lastUser->institution_user_id)
            + 1
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

        $roleSlug = strtolower(
            trim(
                (string) $roleSlug
            )
        );

        $institutionRoles = [

            'student',
            'user',
            'user_admin',

        ];

        /*
        |----------------------------------------------------------------------
        | Institution Role
        |----------------------------------------------------------------------
        */

        if (
            in_array(
                $roleSlug,
                $institutionRoles,
                true
            )
            &&
            !$institutionId
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
     * Permission Check
     * ---------------------------------------------------------
     */
    public function hasPermission(string $permissionSlug): bool
    {
        /*
        |----------------------------------------------------------------------
        | Find Permission
        |----------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        |
        | System Mode:
        |     সব active permission।
        |
        | Institution Mode:
        |     Institution Base Permission
        |     +
        |     user_admin Role Permission
        |
        |     অর্থাৎ Super Admin যখন কোনো Institution-এ ঢুকবে,
        |     তখন সেই Institution-এর User Admin-এর মতো permission
        |     structure অনুসরণ করবে।
        |
        |--------------------------------------------------------------------------
        */

        if ($this->is_super_admin) {

            $activeInstitutionId =
                $this->active_institution_id;

            /*
            |----------------------------------------------------------------------
            | System Mode
            |----------------------------------------------------------------------
            */

            if (!$activeInstitutionId) {

                return true;
            }

            /*
            |----------------------------------------------------------------------
            | Institution Mode
            |----------------------------------------------------------------------
            */

            $institution = Madrasa::query()
                ->whereKey($activeInstitutionId)
                ->first();

            if (!$institution) {

                return false;
            }

            /*
            |----------------------------------------------------------------------
            | Institution Base Permission
            |----------------------------------------------------------------------
            |
            | Institution Permission Manager থেকে
            | permission enable করা থাকতে হবে।
            |
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
            |----------------------------------------------------------------------
            | User Admin Role
            |----------------------------------------------------------------------
            |
            | Institution-এর User Admin role যেসব permission পায়,
            | Super Admin Institution Mode-এ একই permission পাবে।
            |
            */

            $userAdminRole = Role::query()
                ->where(
                    'slug',
                    'user_admin'
                )
                ->first();

            if (!$userAdminRole) {

                return false;
            }

            return $userAdminRole
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
        | Support Admin
        |--------------------------------------------------------------------------
        */

        if ($this->roleSlug === 'support_admin') {

            /*
            |----------------------------------------------------------------------
            | Direct User Permission
            |----------------------------------------------------------------------
            */

            $directPermission =
                $this->permissions()
                    ->where(
                        'permissions.id',
                        $permission->id
                    )
                    ->where(
                        'permissions.is_active',
                        true
                    )
                    ->exists();

            if ($directPermission) {

                return true;
            }

            /*
            |----------------------------------------------------------------------
            | Role Permission
            |----------------------------------------------------------------------
            */

            if ($this->role) {

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

        /*
        |--------------------------------------------------------------------------
        | Institution Based Users
        |--------------------------------------------------------------------------
        |
        | user_admin
        | user
        | student
        |
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $this->roleSlug,
                [
                    'user_admin',
                    'user',
                    'student',
                ],
                true
            )
        ) {

            $institutionId =
                $this->active_institution_id;

            if (!$institutionId) {

                return false;
            }

            /*
            |----------------------------------------------------------------------
            | Current Institution
            |----------------------------------------------------------------------
            */

            $institution = Madrasa::query()
                ->whereKey($institutionId)
                ->first();

            if (!$institution) {

                return false;
            }

            /*
            |----------------------------------------------------------------------
            | Institution Base Permission
            |----------------------------------------------------------------------
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
            |----------------------------------------------------------------------
            | Direct User Permission
            |----------------------------------------------------------------------
            */

            $directPermission =
                $this->permissions()
                    ->where(
                        'permissions.id',
                        $permission->id
                    )
                    ->where(
                        'permissions.is_active',
                        true
                    )
                    ->exists();

            if ($directPermission) {

                return true;
            }

            /*
            |----------------------------------------------------------------------
            | Role Permission
            |----------------------------------------------------------------------
            */

            if ($this->role) {

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

        /*
        |--------------------------------------------------------------------------
        | Other Roles
        |--------------------------------------------------------------------------
        */

        return false;
    }

    /**
     * ---------------------------------------------------------
     * Panel Check
     * ---------------------------------------------------------
     */
    public function hasPanel(string $panelSlug): bool
    {
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

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        |
        | System Mode:
        |     সব Panel access থাকবে।
        |
        | Institution Mode:
        |     selected Institution-এর Panel permission
        |     +
        |     user_admin role-এর Panel permission।
        |
        |--------------------------------------------------------------------------
        */

        if ($this->is_super_admin) {

            $activeInstitutionId =
                $this->active_institution_id;

            /*
            |----------------------------------------------------------------------
            | System Mode
            |----------------------------------------------------------------------
            */

            if (!$activeInstitutionId) {

                return true;
            }

            /*
            |----------------------------------------------------------------------
            | Institution Mode
            |----------------------------------------------------------------------
            */

            $institution = Madrasa::query()
                ->whereKey($activeInstitutionId)
                ->first();

            if (!$institution) {

                return false;
            }

            /*
            |----------------------------------------------------------------------
            | Institution Base Panel Permission
            |----------------------------------------------------------------------
            */

            $institutionHasPanel =
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

            if (!$institutionHasPanel) {

                return false;
            }

            /*
            |----------------------------------------------------------------------
            | User Admin Role Panel Permission
            |----------------------------------------------------------------------
            */

            $userAdminRole = Role::query()
                ->where(
                    'slug',
                    'user_admin'
                )
                ->first();

            if (!$userAdminRole) {

                return false;
            }

            return $userAdminRole
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

        /*
        |--------------------------------------------------------------------------
        | Support Admin
        |--------------------------------------------------------------------------
        */

        if ($this->roleSlug === 'support_admin') {

            /*
            |----------------------------------------------------------------------
            | Direct User Panel Permission
            |----------------------------------------------------------------------
            */

            $directPanelPermission =
                $this->permissions()
                    ->where(
                        'permissions.system_panel_id',
                        $panel->id
                    )
                    ->where(
                        'permissions.is_active',
                        true
                    )
                    ->exists();

            if ($directPanelPermission) {

                return true;
            }

            /*
            |----------------------------------------------------------------------
            | Role Panel Permission
            |----------------------------------------------------------------------
            */

            if ($this->role) {

                return $this->role
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

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Institution Users
        |--------------------------------------------------------------------------
        |
        | user_admin
        | user
        | student
        |
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $this->roleSlug,
                [
                    'user_admin',
                    'user',
                    'student'
                ],
                true
            )
        ) {

            $institutionId =
                $this->active_institution_id;

            if (!$institutionId) {

                return false;
            }

            $institution = Madrasa::query()
                ->whereKey($institutionId)
                ->first();

            if (!$institution) {

                return false;
            }

            /*
            |----------------------------------------------------------------------
            | Institution Base Panel Permission
            |----------------------------------------------------------------------
            */

            $institutionHasPanel =
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

            if (!$institutionHasPanel) {

                return false;
            }

            /*
            |----------------------------------------------------------------------
            | Direct User Panel Permission
            |----------------------------------------------------------------------
            */

            $directPanelPermission =
                $this->permissions()
                    ->where(
                        'permissions.system_panel_id',
                        $panel->id
                    )
                    ->where(
                        'permissions.is_active',
                        true
                    )
                    ->exists();

            if ($directPanelPermission) {

                return true;
            }

            /*
            |----------------------------------------------------------------------
            | Role Panel Permission
            |----------------------------------------------------------------------
            */

            if ($this->role) {

                return $this->role
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

            return false;
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
