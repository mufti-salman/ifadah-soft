
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
    |
    | Current role slugs:
    | super_admin
    | admin
    | madrasa
    | teacher
    | student
    | guardian
    |
    */
    $roleSlug = optional($authUser?->role)->slug;


    /*
    |--------------------------------------------------------------------------
    | Active Institution Context
    |--------------------------------------------------------------------------
    |
    | Super Admin / Admin যখন কোনো institution-এ switch করবে,
    | তখন session('active_institution_id') থাকবে।
    |
    | সেটাই আগে priority পাবে।
    |
    */
    $activeInstitutionId = session('active_institution_id');


    /*
    |--------------------------------------------------------------------------
    | Resolve Institution
    |--------------------------------------------------------------------------
    |
    | 1. Session institution থাকলে সেটি
    | 2. না থাকলে logged-in user's institution
    |
    */
    if ($activeInstitutionId) {

        $currentInstitution = Madrasa::find($activeInstitutionId);

    } elseif ($authUser?->institution_id) {

        $currentInstitution = $authUser->institution;

    } else {

        $currentInstitution = null;
    }


    /*
    |--------------------------------------------------------------------------
    | Institution Information
    |--------------------------------------------------------------------------
    */

    $institutionName = $currentInstitution
        ? ($currentInstitution->name_bn ?: $currentInstitution->name)
        : null;

    $institutionLogo = $currentInstitution?->logo;


    /*
    |--------------------------------------------------------------------------
    | System Mode
    |--------------------------------------------------------------------------
    |
    | Super Admin কোনো institution-এ ঢোকেনি = System Mode
    |
    */
    $isInstitutionMode = session('institution_mode') === true
        && !empty($activeInstitutionId);


    /*
    |--------------------------------------------------------------------------
    | Display Name
    |--------------------------------------------------------------------------
    */

    if ($isInstitutionMode && $institutionName) {

        $displayInstitutionName = $institutionName;

    } elseif ($currentInstitution && $institutionName) {

        $displayInstitutionName = $institutionName;

    } else {

        $displayInstitutionName = 'সিস্টেম';
    }


    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Madrasa logo database-এ institutions/xxx/file.jpg
    | হিসেবে থাকলে Storage URL ব্যবহার হবে।
    |
    */
    if ($institutionLogo) {

        $displayLogo = asset('storage/' . $institutionLogo);

    } else {

        $displayLogo = asset('contents/admin/images/logo.png');
    }


    /*
    |--------------------------------------------------------------------------
    | Institution Name Split
    |--------------------------------------------------------------------------
    |
    | প্রথম অংশ বড় করে দেখাবে।
    |
    */
    $words = preg_split('/\s+/', trim($displayInstitutionName));

    $firstPart = $words[0] ?? $displayInstitutionName;

    $secondPart = count($words) > 1
        ? implode(' ', array_slice($words, 1))
        : '';


    /*
    |--------------------------------------------------------------------------
    | System Admin Check
    |--------------------------------------------------------------------------
    */

    $isSuperAdmin = $roleSlug === 'super_admin';
    $isAdmin      = $roleSlug === 'admin';
    $isMadrasa    = $roleSlug === 'madrasa';
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
                    href="{{ route('dashboard.index') }}"
                    class="text-white text-decoration-none d-flex align-items-center gap-2"
                >

                    <img
                        src="{{ $displayLogo }}"
                        alt="{{ $displayInstitutionName }}"
                        class="sidebar-logo"
                    >

                </a>

            </div>


            {{-- Institution Name --}}
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

            {{-- Dashboard --}}
            @include('admin.sidebar.dashboard')


            {{-- Institution --}}
            @include('admin.sidebar.institution')


            {{-- Student --}}
            @include('admin.sidebar.student')


            {{-- Teacher --}}
            @include('admin.sidebar.teacher')


            {{-- Attendance --}}
            @include('admin.sidebar.attendance')


            {{-- Exam --}}
            @include('admin.sidebar.exam')


            {{-- Result --}}
            @include('admin.sidebar.result')


            {{-- Accounting --}}
            @include('admin.sidebar.accounting')


            {{-- Payment --}}
            @include('admin.sidebar.payment')


            {{-- Settings --}}
            @include('admin.sidebar.settings')


            {{-- Help --}}
            @include('admin.sidebar.help')


            {{-- System --}}
            @include('admin.sidebar.system')

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
    | Institution Title
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
