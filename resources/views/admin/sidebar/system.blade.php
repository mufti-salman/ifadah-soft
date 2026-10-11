{{-- ================================================================
     SYSTEM SIDEBAR — SUPER ADMIN / SUPPORT ADMIN
================================================================ --}}

@php
    $routeName = request()->route()?->getName() ?? '';

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    $systemDashboardActive =
        $routeName === 'dashboard.index';


    /*
    |--------------------------------------------------------------------------
    | Institution Management
    |--------------------------------------------------------------------------
    */

    $institutionActive =
        str_starts_with(
            $routeName,
            'dashboard.institutions.'
        )
        ||
        $routeName === 'dashboard.institution.info';


    /*
    |--------------------------------------------------------------------------
    | Support Admin
    |--------------------------------------------------------------------------
    */

    $supportAdminActive =
        str_starts_with(
            $routeName,
            'dashboard.support-admins.'
        );


  /*
|--------------------------------------------------------------------------
| Subscription System
|--------------------------------------------------------------------------
*/

$subscriptionActive =
    str_starts_with(
        $routeName,
        'dashboard.subscription.'
    )
    &&
    !str_starts_with(
        $routeName,
        'dashboard.subscription.transactions.'
    );


/*
|--------------------------------------------------------------------------
| Transaction History
|--------------------------------------------------------------------------
*/

$transactionActive =
    str_starts_with(
        $routeName,
        'dashboard.subscription.transactions.'
    );


    /*
    |--------------------------------------------------------------------------
    | Support Tickets
    |--------------------------------------------------------------------------
    */

    $supportTicketActive =
        str_starts_with(
            $routeName,
            'dashboard.support-tickets.'
        );


    /*
    |--------------------------------------------------------------------------
    | Help Management
    |--------------------------------------------------------------------------
    */

    $helpManagementActive =
        str_starts_with(
            $routeName,
            'dashboard.help-management'
        );


    /*
    |--------------------------------------------------------------------------
    | Database & System Status
    |--------------------------------------------------------------------------
    */

    $databaseSystemStatusActive =
        str_starts_with(
            $routeName,
            'dashboard.database-system-status.'
        );


    /*
    |--------------------------------------------------------------------------
    | System Panel
    |--------------------------------------------------------------------------
    */

    $systemPanelActive =
        str_starts_with(
            $routeName,
            'dashboard.system-panels.'
        )
        ||
        str_starts_with(
            $routeName,
            'dashboard.system-panel-manager.'
        );


    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */

    $permissionActive =
        str_starts_with(
            $routeName,
            'dashboard.permissions.'
        )
        ||
        str_starts_with(
            $routeName,
            'dashboard.role-permissions.'
        );
@endphp


{{-- ================================================================
     1. ড্যাশবোর্ড
================================================================ --}}

<li class="nav-item">

    <a
        href="{{ route('dashboard.index') }}"
        class="nav-link text-dark d-flex align-items-center gap-2 {{ $systemDashboardActive ? 'active' : '' }}"
        data-menu="system-dashboard"
    >

        <i class="fas fa-tachometer-alt fa-fw"></i>

        <span>ড্যাশবোর্ড</span>

    </a>

</li>


{{-- ================================================================
     2. প্রতিষ্ঠান ব্যবস্থাপনা
================================================================ --}}

<li class="nav-item">

    <a
        href="#systemInstitutionMenu"
        class="nav-link text-dark d-flex align-items-center gap-2 {{ $institutionActive ? 'active' : '' }}"
        data-bs-toggle="collapse"
        data-bs-target="#systemInstitutionMenu"
        aria-expanded="{{ $institutionActive ? 'true' : 'false' }}"
    >

        <i class="fas fa-school fa-fw"></i>

        <span>প্রতিষ্ঠান ব্যবস্থাপনা</span>

        <i class="fas fa-chevron-down ms-auto small"></i>

    </a>


    <div
        class="collapse {{ $institutionActive ? 'show' : '' }}"
        id="systemInstitutionMenu"
    >

        <ul class="nav flex-column ms-3 mt-1 gap-1">

            {{-- সকল প্রতিষ্ঠান --}}

            <li class="nav-item">

                <a
                    href="{{ route('dashboard.institutions.index') }}"
                    class="nav-link text-white-50 d-flex align-items-center gap-2 {{ $routeName === 'dashboard.institutions.index' ? 'active' : '' }}"
                >

                    <i class="fas fa-list fa-fw"></i>

                    <span>সকল প্রতিষ্ঠান</span>

                </a>

            </li>

        </ul>

    </div>

</li>


{{-- ================================================================
     3. সাপোর্ট এডমিন ও পারমিশন
================================================================ --}}

@if($roleSlug === 'super_admin')

    <li class="nav-item">

        <a
            href="{{ route('dashboard.support-admins.index') }}"
            class="nav-link text-dark d-flex align-items-center gap-2 {{ $supportAdminActive ? 'active' : '' }}"
            data-menu="support-admins"
        >

            <i class="fas fa-user-shield fa-fw"></i>

            <span>সাপোর্ট এডমিন ও পারমিশন</span>

        </a>

    </li>

@endif


{{-- ================================================================
     4. সাবস্ক্রিপশন সিস্টেম
================================================================ --}}

<li class="nav-item">

    <a
        href="{{ route('dashboard.subscription.index') }}"
        class="nav-link text-dark d-flex align-items-center gap-2 {{ $subscriptionActive ? 'active' : '' }}"
    >

        <i class="fas fa-credit-card fa-fw"></i>

        <span>সাবস্ক্রিপশন সিস্টেম</span>

    </a>

</li>


{{-- ================================================================
     5. লেনদেন হিস্ট্রি
================================================================ --}}

<li class="nav-item">

    <a
        href="{{ route('dashboard.subscription.transactions.index') }}"
        class="nav-link text-dark d-flex align-items-center gap-2 {{ $transactionActive ? 'active' : '' }}"
    >

        <i class="fas fa-exchange-alt fa-fw"></i>

        <span>লেনদেন হিস্ট্রি</span>

    </a>

</li>


{{-- ================================================================
     6. সাপোর্ট টিকেট
================================================================ --}}

<li class="nav-item">

    <a
        href="{{ route('dashboard.support-tickets.index') }}"
        class="nav-link text-dark d-flex align-items-center gap-2 {{ $supportTicketActive ? 'active' : '' }}"
    >

        <i class="fas fa-ticket-alt fa-fw"></i>

        <span>সাপোর্ট টিকেট</span>

    </a>

</li>


{{-- ================================================================
     7. হেল্প ম্যানেজমেন্ট
================================================================ --}}

<li class="nav-item">

    <a
        href="{{ route('dashboard.help-management.index') }}"
        class="nav-link text-dark d-flex align-items-center gap-2 {{ $helpManagementActive ? 'active' : '' }}"
    >

        <i class="fas fa-question-circle fa-fw"></i>

        <span>হেল্প ম্যানেজমেন্ট</span>

    </a>

</li>


{{-- ================================================================
     8. ডাটাবেস ও সিস্টেম স্ট্যাটাস
================================================================ --}}

<li class="nav-item">

    <a
        href="{{ route('dashboard.database-system-status.index') }}"
        class="nav-link text-dark d-flex align-items-center gap-2 {{ $databaseSystemStatusActive ? 'active' : '' }}"
    >

        <i class="fas fa-server fa-fw"></i>

        <span>ডাটাবেস ও সিস্টেম স্ট্যাটাস</span>

    </a>

</li>


{{-- ================================================================
     9. সিস্টেম প্যানেল
================================================================ --}}

<li class="nav-item">

    <a
        href="#systemPanelMenu"
        class="nav-link text-dark d-flex align-items-center gap-2 {{ $systemPanelActive ? 'active' : '' }}"
        data-bs-toggle="collapse"
        data-bs-target="#systemPanelMenu"
        aria-expanded="{{ $systemPanelActive ? 'true' : 'false' }}"
    >

        <i class="fas fa-layer-group fa-fw"></i>

        <span>সিস্টেম প্যানেল</span>

        <i class="fas fa-chevron-down ms-auto small"></i>

    </a>


    <div
        class="collapse {{ $systemPanelActive ? 'show' : '' }}"
        id="systemPanelMenu"
    >

        <ul class="nav flex-column ms-3 mt-1 gap-1">

            {{-- সিস্টেম প্যানেলসমূহ --}}

            <li class="nav-item">

                <a
                    href="{{ route('dashboard.system-panels.index') }}"
                    class="nav-link text-white-50 d-flex align-items-center gap-2 {{ str_starts_with($routeName, 'dashboard.system-panels.') ? 'active' : '' }}"
                >

                    <i class="fas fa-list fa-fw"></i>

                    <span>সিস্টেম প্যানেলসমূহ</span>

                </a>

            </li>


            {{-- প্যানেল ম্যানেজার --}}

            <li class="nav-item">

                <a
                    href="{{ route('dashboard.system-panel-manager.index') }}"
                    class="nav-link text-white-50 d-flex align-items-center gap-2 {{ str_starts_with($routeName, 'dashboard.system-panel-manager.') ? 'active' : '' }}"
                >

                    <i class="fas fa-sliders-h fa-fw"></i>

                    <span>প্যানেল ম্যানেজার</span>

                </a>

            </li>

        </ul>

    </div>

</li>


{{-- ================================================================
     10. অনুমতি
================================================================ --}}

<li class="nav-item">

    <a
        href="#systemPermissionMenu"
        class="nav-link text-dark d-flex align-items-center gap-2 {{ $permissionActive ? 'active' : '' }}"
        data-bs-toggle="collapse"
        data-bs-target="#systemPermissionMenu"
        aria-expanded="{{ $permissionActive ? 'true' : 'false' }}"
    >

        <i class="fas fa-key fa-fw"></i>

        <span>অনুমতি</span>

        <i class="fas fa-chevron-down ms-auto small"></i>

    </a>


    <div
        class="collapse {{ $permissionActive ? 'show' : '' }}"
        id="systemPermissionMenu"
    >

        <ul class="nav flex-column ms-3 mt-1 gap-1">

            {{-- অনুমতিসমূহ --}}

            <li class="nav-item">

                <a
                    href="{{ route('dashboard.permissions.index') }}"
                    class="nav-link text-white-50 d-flex align-items-center gap-2 {{ str_starts_with($routeName, 'dashboard.permissions.') ? 'active' : '' }}"
                >

                    <i class="fas fa-key fa-fw"></i>

                    <span>অনুমতিসমূহ</span>

                </a>

            </li>


            {{-- রোলের অনুমতি --}}

            <li class="nav-item">

                <a
                    href="{{ route('dashboard.role-permissions.index') }}"
                    class="nav-link text-white-50 d-flex align-items-center gap-2 {{ str_starts_with($routeName, 'dashboard.role-permissions.') ? 'active' : '' }}"
                >

                    <i class="fas fa-user-lock fa-fw"></i>

                    <span>রোলের অনুমতি</span>

                </a>

            </li>

        </ul>

    </div>

</li>