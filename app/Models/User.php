<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * ----------------------------------------------------------
     * Mass Assignable Fields
     * ----------------------------------------------------------
     */
    protected $fillable = [

        // Institution
        'institution_id',

        // Role
        'role_id',

        // Basic Information
        'name',
        'name_bn',
        'email',
        'phone',
        'password',

        // User ID
        'institution_user_id',

        // Login
        'username',
        'status',

        // Personal Information
        'gender',
        'date_of_birth',
        'blood_group',
        'nid_no',

        // Address
        'address',
        'village',
        'post_office',
        'upazila',
        'district',
        'division',

        // Family
        'father_name',
        'mother_name',
        'guardian_name',

        // Account
        'email_verified_at',
        'last_login_at',
        'last_login_ip',

        // Audit
        'created_by',
        'updated_by',
    ];

    /**
     * ----------------------------------------------------------
     * Hidden Attributes
     * ----------------------------------------------------------
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * ----------------------------------------------------------
     * Casts
     * ----------------------------------------------------------
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'date_of_birth'     => 'date',
        'password'          => 'hashed',
        'status'            => 'integer',
    ];

    /**
     * ----------------------------------------------------------
     * Institution
     * ----------------------------------------------------------
     */
    public function institution()
    {
        return $this->belongsTo(
            Madrasa::class,
            'institution_id',
            'id'
        );
    }

    /**
     * ----------------------------------------------------------
     * Madrasa
     * ----------------------------------------------------------
     *
     * Backward-compatible alias of institution().
     */
    public function madrasa()
    {
        return $this->belongsTo(
            Madrasa::class,
            'institution_id',
            'id'
        );
    }

    /**
     * ----------------------------------------------------------
     * Role
     * ----------------------------------------------------------
     */
    public function role()
    {
        return $this->belongsTo(
            Role::class,
            'role_id',
            'id'
        );
    }

    /**
     * ----------------------------------------------------------
     * Student Profile
     * ----------------------------------------------------------
     */
    public function student()
    {
        return $this->hasOne(
            Student::class,
            'user_id',
            'id'
        );
    }

    /**
     * ----------------------------------------------------------
     * Teacher Profile
     * ----------------------------------------------------------
     *
     * Teacher is an entity/profile.
     * Teacher is NOT a separate Role.
     */
    public function teacher()
    {
        return $this->hasOne(
            Teacher::class,
            'user_id',
            'id'
        );
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
     * Direct Permissions
     * ----------------------------------------------------------
     *
     * Pivot:
     * user_permissions
     *
     * allow:
     * 1 = allowed
     * 0 = denied
     */
    public function permissions()
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
     * ----------------------------------------------------------
     * Allowed Direct Permissions
     * ----------------------------------------------------------
     */
    public function allowedPermissions()
    {
        return $this->permissions()
            ->wherePivot('allow', true);
    }

    /**
     * ----------------------------------------------------------
     * Role Check
     * ----------------------------------------------------------
     */
    public function hasRole($role)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->slug === $role;
    }

    /**
     * ----------------------------------------------------------
     * Multiple Role Check
     * ----------------------------------------------------------
     */
    public function hasAnyRole(array $roles)
    {
        if (!$this->role) {
            return false;
        }

        return in_array(
            $this->role->slug,
            $roles,
            true
        );
    }

    /**
     * ----------------------------------------------------------
     * Super Admin
     * ----------------------------------------------------------
     */
    public function getIsSuperAdminAttribute()
    {
        return $this->hasRole('super_admin');
    }

    /**
     * ----------------------------------------------------------
     * Support Admin
     * ----------------------------------------------------------
     */
    public function getIsSupportAdminAttribute()
    {
        return $this->hasRole('support_admin');
    }

    /**
     * ----------------------------------------------------------
     * User Admin
     * ----------------------------------------------------------
     */
    public function getIsUserAdminAttribute()
    {
        return $this->hasRole('user_admin');
    }

    /**
     * ----------------------------------------------------------
     * User
     * ----------------------------------------------------------
     */
    public function getIsUserAttribute()
    {
        return $this->hasRole('user');
    }

    /**
     * ----------------------------------------------------------
     * Student
     * ----------------------------------------------------------
     */
    public function getIsStudentAttribute()
    {
        return $this->hasRole('student');
    }

    /**
     * ----------------------------------------------------------
     * Backward Compatibility: Admin
     * ----------------------------------------------------------
     *
     * Old admin role এখন user_admin-এর equivalent।
     */
    public function getIsAdminAttribute()
    {
        return $this->is_user_admin;
    }

    /**
     * ----------------------------------------------------------
     * Backward Compatibility: Madrasa
     * ----------------------------------------------------------
     */
    public function getIsMadrasaAttribute()
    {
        return $this->is_user_admin;
    }

    /**
     * ----------------------------------------------------------
     * Backward Compatibility: Teacher
     * ----------------------------------------------------------
     *
     * Teacher এখন user role।
     */
    public function getIsTeacherAttribute()
    {
        return $this->is_user;
    }

    /**
     * ----------------------------------------------------------
     * Guardian
     * ----------------------------------------------------------
     *
     * Guardian আলাদা role নয়।
     */
    public function getIsGuardianAttribute()
    {
        return false;
    }

    /**
     * ----------------------------------------------------------
     * Generate Institution User ID
     * ----------------------------------------------------------
     *
     * Prefix:
     *
     * Student       = 101
     * User          = 201
     * User Admin    = 401
     * Support Admin = 501
     * Super Admin   = 601
     */
    public static function generateInstitutionUserId(
        $institutionId,
        $roleSlug
    ) {
        $prefixes = [
            'student'       => 101,
            'user'          => 201,
            'user_admin'    => 401,
            'support_admin' => 501,
            'super_admin'   => 601,
        ];

        $prefix = $prefixes[$roleSlug] ?? 201;

        $lastUser = static::where(
            'institution_id',
            $institutionId
        )
        ->whereHas('role', function ($query) use ($roleSlug) {
            $query->where('slug', $roleSlug);
        })
        ->orderByDesc('id')
        ->first();

        if (!$lastUser || !$lastUser->institution_user_id) {
            return (string) $prefix;
        }

        $lastId = (int) $lastUser->institution_user_id;

        return (string) ($lastId + 1);
    }

    /**
     * ----------------------------------------------------------
     * Preview Institution User ID
     * ----------------------------------------------------------
     */
    public static function previewInstitutionUserId(
        $institutionId,
        $roleSlug
    ) {
        return static::generateInstitutionUserId(
            $institutionId,
            $roleSlug
        );
    }


/**
 * ----------------------------------------------------------
 * Has Permission
 * ----------------------------------------------------------
 *
 * Permission hierarchy:
 *
 * Super Admin + System Mode
 *     → All permissions
 *
 * Super Admin + Institution Mode
 *     → Selected institution permissions
 *
 * Support Admin
 *     → Direct permission / Role permission
 *
 * User Admin / User / Student
 *     → Institution permission
 *     → Direct permission / Role permission
 *
 * Institution permission is the maximum boundary.
 * ----------------------------------------------------------
 */
public function hasPermission(string $permissionSlug): bool
{
    /*
    |--------------------------------------------------------------------------
    | Find Permission
    |--------------------------------------------------------------------------
    */

    $permission = Permission::query()
        ->where('slug', $permissionSlug)
        ->where('is_active', true)
        ->first();

    if (!$permission) {
        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | Current Role
    |--------------------------------------------------------------------------
    */

    $roleSlug = strtolower(
        trim(
            (string) optional($this->role)->slug
        )
    );


    /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN
    |--------------------------------------------------------------------------
    |
    | যদি কোনো institution select করা না থাকে,
    | তাহলে Super Admin system mode-এ থাকবে এবং
    | সব permission পাবে।
    |
    | যদি institution select করা থাকে,
    | তাহলে শুধুমাত্র সেই institution-এর permission
    | অনুযায়ী access পাবে।
    |
    */

    if ($roleSlug === 'super_admin') {

        $activeInstitutionId = session(
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

        $institution = Madrasa::find(
            $activeInstitutionId
        );

        if (!$institution) {
            return false;
        }

        return $institution->permissions()
            ->where('permissions.id', $permission->id)
            ->where('permissions.is_active', true)
            ->exists();
    }


    /*
    |--------------------------------------------------------------------------
    | SUPPORT ADMIN
    |--------------------------------------------------------------------------
    |
    | Support Admin system-level role.
    | তাই institution permission বাধ্যতামূলক নয়।
    |
    */

    if ($roleSlug === 'support_admin') {

        /*
        |--------------------------------------------------------------------------
        | Direct User Permission
        |--------------------------------------------------------------------------
        */

        $userPermission = $this->permissions()
            ->where('permissions.id', $permission->id)
            ->first();

        if ($userPermission) {
            return (bool) $userPermission->pivot->allow;
        }

        /*
        |--------------------------------------------------------------------------
        | Role Permission
        |--------------------------------------------------------------------------
        */

        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()
            ->where('permissions.id', $permission->id)
            ->where('permissions.is_active', true)
            ->exists();
    }


    /*
    |--------------------------------------------------------------------------
    | INSTITUTION BASED ROLES
    |--------------------------------------------------------------------------
    |
    | user_admin
    | user
    | student
    |
    */

    $institutionRoles = [
        'user_admin',
        'user',
        'student',
    ];

    if (in_array($roleSlug, $institutionRoles, true)) {

        /*
        |--------------------------------------------------------------------------
        | Get Active Institution
        |--------------------------------------------------------------------------
        */

        $institution = $this->activeInstitution;

        if (!$institution) {
            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | Institution Permission
        |--------------------------------------------------------------------------
        |
        | প্রথমে নিশ্চিত করতে হবে যে এই institution
        | permission-টি ব্যবহার করার অনুমতি দিয়েছে।
        |
        */

        $institutionHasPermission = $institution
            ->permissions()
            ->where('permissions.id', $permission->id)
            ->where('permissions.is_active', true)
            ->exists();

        if (!$institutionHasPermission) {
            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | Direct User Permission
        |--------------------------------------------------------------------------
        |
        | Institution permission থাকলে:
        |
        | allow = 1 → allowed
        | allow = 0 → denied
        |
        */

        $userPermission = $this->permissions()
            ->where('permissions.id', $permission->id)
            ->first();

        if ($userPermission) {
            return (bool) $userPermission->pivot->allow;
        }


        /*
        |--------------------------------------------------------------------------
        | Role Permission
        |--------------------------------------------------------------------------
        */

        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()
            ->where('permissions.id', $permission->id)
            ->where('permissions.is_active', true)
            ->exists();
    }


    /*
    |--------------------------------------------------------------------------
    | Unknown Role
    |--------------------------------------------------------------------------
    */

    return false;
}




    /**
     * ----------------------------------------------------------
     * Permission Alias
     * ----------------------------------------------------------
     */
    public function canPermission($permission)
    {
        return $this->hasPermission($permission);
    }

    /**
     * ----------------------------------------------------------
     * Active Institution
     * ----------------------------------------------------------
     *
     * IMPORTANT:
     *
     * Super Admin institution switch করলে login user
     * পরিবর্তন হবে না।
     *
     * শুধু session-এর active institution পরিবর্তন হবে।
     */
    public function getActiveInstitutionAttribute()
    {
        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */
        if ($this->is_super_admin) {

            $institutionId = session(
                'active_institution_id'
            );

            if ($institutionId) {
                return Madrasa::find($institutionId);
            }

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Support Admin
        |--------------------------------------------------------------------------
        |
        | Assigned institution থাকলে সেটা ব্যবহার করবে।
        | না থাকলে active session institution ব্যবহার করতে পারবে।
        |
        */
        if ($this->is_support_admin) {

            if ($this->institution_id) {
                return $this->institution;
            }

            $institutionId = session(
                'active_institution_id'
            );

            if ($institutionId) {
                return Madrasa::find($institutionId);
            }

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Normal Institution User
        |--------------------------------------------------------------------------
        */
        return $this->institution;
    }

    /**
     * ----------------------------------------------------------
     * Active Institution ID
     * ----------------------------------------------------------
     */
    public function getActiveInstitutionIdAttribute()
    {
        return $this->activeInstitution?->id;
    }

    /**
     * ----------------------------------------------------------
     * Active Institution Name
     * ----------------------------------------------------------
     */
    public function getActiveInstitutionNameAttribute()
    {
        return $this->activeInstitution?->display_name;
    }

/**
 * --------------------------------------------------------------------------
 * Check System Panel Access
 * --------------------------------------------------------------------------
 *
 * Panel access is controlled by role_system_panel.
 *
 * Super Admin
 *     → All active panels
 *
 * Support Admin
 *     → Assigned system panels
 *
 * User Admin
 *     → Assigned institution-level panels
 *
 * User
 *     → Assigned institution-level panels
 *
 * Student
 *     → Assigned institution-level panels
 *
 * --------------------------------------------------------------------------
 */
public function hasPanel(string $panelSlug): bool
{
    /*
    |--------------------------------------------------------------------------
    | Find Panel
    |--------------------------------------------------------------------------
    */

    $panel = SystemPanel::where('slug', $panelSlug)
        ->where('is_active', true)
        ->first();

    if (!$panel) {
        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | Super Admin
    |--------------------------------------------------------------------------
    |
    | Super Admin সব active panel access করতে পারবে।
    |
    */

    if ($this->is_super_admin) {
        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | No Role
    |--------------------------------------------------------------------------
    */

    if (!$this->role) {
        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | Role Panel Access
    |--------------------------------------------------------------------------
    |
    | support_admin, user_admin, user এবং student
    | role_system_panel table থেকে panel access পাবে।
    |
    */

    return $this->role->systemPanels()
        ->where('system_panels.id', $panel->id)
        ->where('system_panels.is_active', true)
        ->exists();
}

    /**
     * ----------------------------------------------------------
     * Same Institution Check
     * ----------------------------------------------------------
     */
    public function belongsToInstitution($institutionId)
    {
        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        |
        | Super Admin active institution অনুযায়ী কাজ করবে।
        |
        */
        if ($this->is_super_admin) {
            return (int) $this->active_institution_id
                === (int) $institutionId;
        }

        return (int) $this->institution_id
            === (int) $institutionId;
    }

    /**
     * ----------------------------------------------------------
     * Is Active
     * ----------------------------------------------------------
     */
    public function getIsActiveAttribute()
    {
        return (int) $this->status === 1;
    }

    /**
     * ----------------------------------------------------------
     * Is Inactive
     * ----------------------------------------------------------
     */
    public function getIsInactiveAttribute()
    {
        return (int) $this->status === 0;
    }

    /**
     * ----------------------------------------------------------
     * Role Slug
     * ----------------------------------------------------------
     */
    public function getRoleSlugAttribute()
    {
        return $this->role?->slug;
    }

    /**
     * ----------------------------------------------------------
     * Role Name
     * ----------------------------------------------------------
     */
    public function getRoleNameAttribute()
    {
        return $this->role?->role_name;
    }
}