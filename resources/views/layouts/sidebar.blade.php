{{-- ================================================================
     ADMIN SIDEBAR
================================================================ --}}

@php
    use App\Models\Madrasa;

    /*
    |--------------------------------------------------------------------------
    | Authenticated User
    |--------------------------------------------------------------------------
    */

    $authUser = auth()->user();


    /*
    |--------------------------------------------------------------------------
    | Role
    |--------------------------------------------------------------------------
    */

    $roleSlug = strtolower(
        trim(
            (string) optional($authUser?->role)->slug
        )
    );


    /*
    |--------------------------------------------------------------------------
    | Active Institution
    |--------------------------------------------------------------------------
    |
    | Super Admin যখন কোনো institution-এ Enter করে,
    | তখন active_institution_id session-এ থাকবে।
    |
    */

    $activeInstitutionId = session(
        'active_institution_id'
    );


    /*
    |--------------------------------------------------------------------------
    | System Roles
    |--------------------------------------------------------------------------
    */

    $isSystemRole = in_array(
        $roleSlug,
        [
            'super_admin',
            'support_admin',
        ],
        true
    );


    /*
    |--------------------------------------------------------------------------
    | Institution Mode
    |--------------------------------------------------------------------------
    |
    | শুধু active institution থাকলেই institution mode হবে।
    |
    */

    $isInstitutionMode =
        $isSystemRole
        && session('institution_mode') === true
        && !empty($activeInstitutionId);


    /*
    |--------------------------------------------------------------------------
    | System Mode
    |--------------------------------------------------------------------------
    |
    | System role + কোনো institution active নেই
    | = System Mode
    |
    */

    $isSystemMode =
        $isSystemRole
        && !$isInstitutionMode;


    /*
    |--------------------------------------------------------------------------
    | Resolve Current Institution
    |--------------------------------------------------------------------------
    */

    $currentInstitution = null;


    /*
    |--------------------------------------------------------------------------
    | Super Admin → Selected Institution
    |--------------------------------------------------------------------------
    */

    if (
        $isInstitutionMode
        && !empty($activeInstitutionId)
    ) {

        $currentInstitution = Madrasa::find(
            (int) $activeInstitutionId
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Normal Institution User
    |--------------------------------------------------------------------------
    |
    | user_admin / user / student নিজের institution ব্যবহার করবে।
    |
    */

    elseif (
        !$isSystemRole
        && $authUser?->institution_id
    ) {

        $currentInstitution = $authUser->institution;

    }


    /*
    |--------------------------------------------------------------------------
    | Institution Information
    |--------------------------------------------------------------------------
    */

    $institutionName = $currentInstitution
        ? (
            $currentInstitution->name_bn
            ?: $currentInstitution->name
        )
        : null;


    $institutionLogo = $currentInstitution?->logo;


    /*
    |--------------------------------------------------------------------------
    | Display Name
    |--------------------------------------------------------------------------
    */

    if ($isSystemMode) {

        $displayInstitutionName =
            'System Administration';

    } elseif ($institutionName) {

        $displayInstitutionName =
            $institutionName;

    } else {

        $displayInstitutionName =
            'সিস্টেম';
    }


    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    */

    if (
        $isInstitutionMode
        && $institutionLogo
    ) {

        $displayLogo = asset(
            'storage/' . $institutionLogo
        );

    } else {

        $displayLogo = asset(
            'contents/admin/images/logo.png'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Institution / System Name Split
    |--------------------------------------------------------------------------
    */

    $words = preg_split(
        '/\s+/',
        trim($displayInstitutionName)
    );


    $firstPart = $words[0]
        ?? $displayInstitutionName;


    $secondPart = count($words) > 1
        ? implode(
            ' ',
            array_slice(
                $words,
                1
            )
        )
        : '';


    /*
    |--------------------------------------------------------------------------
    | Sidebar Home Route
    |--------------------------------------------------------------------------
    |
    | এখন System এবং Institution — দুটোই একই /dashboard ব্যবহার করে।
    |
    */

    $sidebarHomeRoute =
        'dashboard.index';

@endphp


{{-- ================================================================
     SIDEBAR
================================================================ --}}

<aside
    class="admin-sidebar bg-dark text-white"
    id="adminSidebar"
>

    {{-- ============================================================
         SIDEBAR BRAND
    ============================================================= --}}

    <div class="sidebar-brand p-2 border-secondary">

        <div class="sidebar_flex d-flex align-items-center">

            {{-- Logo --}}

            <div class="logo">

                <a
                    href="{{ route($sidebarHomeRoute) }}"
                    class="text-white text-decoration-none d-flex align-items-center gap-2"
                >

                    <img
                        src="{{ $displayLogo }}"
                        alt="{{ $displayInstitutionName }}"
                        class="sidebar-logo"
                    >

                </a>

            </div>


            {{-- Institution / System Name --}}

            <div class="content text-secondary small mt-1">

                <h3 class="institution-title">
                    {{ $firstPart }}
                </h3>


                @if($secondPart)

                    <p class="institution-subtitle">
                        {{ $secondPart }}
                    </p>

                @endif

            </div>

        </div>

    </div>


    {{-- ============================================================
         SIDEBAR BODY
    ============================================================= --}}

    <div class="sidebar-body">

        <ul
            class="nav flex-column gap-1"
            id="sidebarNav"
        >

            {{-- ====================================================
                 SYSTEM MODE
            ===================================================== --}}

            @if($isSystemMode)

                @include(
                    'admin.sidebar.system'
                )


            {{-- ====================================================
                 MADRASA / INSTITUTION MODE
            ===================================================== --}}

            @else

                {{-- Dashboard --}}

                @include(
                    'admin.sidebar.dashboard'
                )


                {{-- Institution --}}

                @include(
                    'admin.sidebar.institution'
                )


                {{-- Student --}}

                @include(
                    'admin.sidebar.student'
                )


                {{-- Teacher --}}

                @include(
                    'admin.sidebar.teacher'
                )


                {{-- Attendance --}}

                @include(
                    'admin.sidebar.attendance'
                )


                {{-- Exam --}}

                @include(
                    'admin.sidebar.exam'
                )


                {{-- Result --}}

                @include(
                    'admin.sidebar.result'
                )


                {{-- Accounting --}}

                @include(
                    'admin.sidebar.accounting'
                )


                {{-- Payment --}}

                @include(
                    'admin.sidebar.payment'
                )


                {{-- Settings --}}

                @include(
                    'admin.sidebar.settings'
                )


                {{-- Help --}}

                @include(
                    'admin.sidebar.help'
                )

            @endif

        </ul>

    </div>

</aside>


{{-- ================================================================
     SIDEBAR STYLE
================================================================ --}}

<style>

    /*
    |--------------------------------------------------------------------------
    | Sidebar Logo
    |--------------------------------------------------------------------------
    */

    .sidebar-logo {

        width: 52px;

        height: 52px;

        object-fit: contain;

        display: block;

        border-radius: 8px;
    }


    /*
    |--------------------------------------------------------------------------
    | Institution / System Title
    |--------------------------------------------------------------------------
    */

    .institution-title {

        margin: 0;

        padding: 0;

        font-size: 17px;

        line-height: 1.2;

        font-weight: 700;

        color: #1f2937;

        white-space: nowrap;

        overflow: hidden;

        text-overflow: ellipsis;

        max-width: 165px;
    }


    /*
    |--------------------------------------------------------------------------
    | Institution Subtitle
    |--------------------------------------------------------------------------
    */

    .institution-subtitle {

        margin: 2px 0 0;

        font-size: 11px;

        line-height: 1.3;

        color: #6b7280;

        white-space: nowrap;

        overflow: hidden;

        text-overflow: ellipsis;

        max-width: 165px;
    }


    /*
    |--------------------------------------------------------------------------
    | Brand Layout
    |--------------------------------------------------------------------------
    */

    .sidebar-brand {

        overflow: hidden;
    }


    .sidebar_flex {

        min-width: 0;

        gap: 8px;
    }


    .sidebar_flex .logo {

        flex: 0 0 auto;
    }


    .sidebar_flex .content {

        min-width: 0;

        flex: 1;
    }


    /*
    |--------------------------------------------------------------------------
    | Dark Sidebar Compatibility
    |--------------------------------------------------------------------------
    */

    .admin-sidebar .institution-title {

        color: #1f2937;
    }


    .admin-sidebar .institution-subtitle {

        color: #6b7280;
    }


    /*
    |--------------------------------------------------------------------------
    | Mobile
    |--------------------------------------------------------------------------
    */

    @media (max-width: 991.98px) {

        .institution-title {

            max-width: 180px;
        }


        .institution-subtitle {

            max-width: 180px;
        }

    }

</style>