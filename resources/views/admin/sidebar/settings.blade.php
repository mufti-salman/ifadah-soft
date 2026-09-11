@panel('settings')

@php

    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Settings Panel Access
    |--------------------------------------------------------------------------
    */

    $canSettings =
        $user->hasPermission('general.view')
        || $user->hasPermission('general.edit')

        || $user->hasPermission('academic.view')
        || $user->hasPermission('academic.create')
        || $user->hasPermission('academic.edit')
        || $user->hasPermission('academic.delete')

        || $user->hasPermission('institution.view')
        || $user->hasPermission('institution.create')
        || $user->hasPermission('institution.edit')
        || $user->hasPermission('institution.delete')

        || $user->hasPermission('month.view')
        || $user->hasPermission('month.create')
        || $user->hasPermission('month.edit')
        || $user->hasPermission('month.delete')

        || $user->hasPermission('language.view')
        || $user->hasPermission('language.edit')

        || $user->hasPermission('theme.view')
        || $user->hasPermission('theme.edit')

        || $user->hasPermission('backup.view')
        || $user->hasPermission('backup.create')
        || $user->hasPermission('backup.restore')
        || $user->hasPermission('backup.delete')

        || $user->hasPermission('system-setting.view')
        || $user->hasPermission('system-setting.edit');

    /*
    |--------------------------------------------------------------------------
    | Support Admin Access
    |--------------------------------------------------------------------------
    |
    | Support Admin management is a system-level feature.
    | Only Super Admin should see this menu.
    |
    */

    $canSupportAdmin =
        $user->is_super_admin
        && (
            $user->hasPermission('support.view')
            || $user->hasPermission('support.create')
            || $user->hasPermission('support.edit')
            || $user->hasPermission('support.delete')
        );

@endphp


@if($canSettings || $canSupportAdmin)

    <li class="nav-item">

        <a class="nav-link text-white d-flex justify-content-between
            {{
                request()->routeIs(
                    'dashboard.settings.*',
                    'dashboard.academic-years.*',
                    'dashboard.institutions.*',
                    'dashboard.month.*',
                    'dashboard.language.*',
                    'dashboard.theme.*',
                    'dashboard.backup.*',
                    'dashboard.system-settings.*',
                    'dashboard.support-admins.*'
                ) ? 'active' : ''
            }}"
           data-bs-toggle="collapse"
           data-bs-target="#settingsMenu"
           href="#settingsMenu">

            <span>
                <i class="fas fa-cog me-2"></i>
                সেটিংস
            </span>

            <i class="fas fa-chevron-down"></i>

        </a>


        <div class="collapse
            {{
                request()->routeIs(
                    'dashboard.settings.*',
                    'dashboard.academic-years.*',
                    'dashboard.institutions.*',
                    'dashboard.month.*',
                    'dashboard.language.*',
                    'dashboard.theme.*',
                    'dashboard.backup.*',
                    'dashboard.system-settings.*',
                    'dashboard.support-admins.*'
                ) ? 'show' : ''
            }}"
             id="settingsMenu">

            <ul class="nav flex-column ms-3">


                {{-- =====================================================
                     সাধারণ সেটিংস
                ====================================================== --}}

                @sidebarPermission('general.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.settings.*'
                                ) ? 'active' : ''
                            }}"
                           href="{{ route('dashboard.settings.index') }}">

                            <i class="fas fa-sliders-h me-2"></i>

                            সাধারণ সেটিংস

                        </a>

                    </li>

                @endsidebarPermission


                {{-- =====================================================
                     একাডেমিক সেটিংস
                ====================================================== --}}

                @sidebarPermission('academic.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.academic-years.*'
                                ) ? 'active' : ''
                            }}"
                           href="{{ route('dashboard.academic-years.index') }}">

                            <i class="fas fa-graduation-cap me-2"></i>

                            একাডেমিক সেটিংস

                        </a>

                    </li>

                @endsidebarPermission


                {{-- =====================================================
                     প্রতিষ্ঠান সেটিংস
                ====================================================== --}}

                @sidebarPermission('institution.view')

@if(
    $user->is_super_admin
    && session('institution_mode') !== true
    && !session()->has('active_institution_id')
    && $user->hasPermission('institution.view')
)

    <li class="nav-item">

        <a
            class="nav-link text-white-50
            {{
                request()->routeIs(
                    'dashboard.institutions.*'
                ) ? 'active' : ''
            }}"
            href="{{ route('dashboard.institutions.index') }}"
        >

            <i class="fas fa-school me-2"></i>

            প্রতিষ্ঠান সেটিংস

        </a>

    </li>

    @endif

                @endsidebarPermission


                {{-- =====================================================
                     মাস সেটিংস
                ====================================================== --}}

                @sidebarPermission('month.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.month.*'
                                ) ? 'active' : ''
                            }}"
                           href="{{ route('dashboard.month.index') }}">

                            <i class="fas fa-calendar-alt me-2"></i>

                            মাস সেটিংস

                        </a>

                    </li>

                @endsidebarPermission


                {{-- =====================================================
                     ভাষা সেটিংস
                ====================================================== --}}

                @sidebarPermission('language.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.language.*'
                                ) ? 'active' : ''
                            }}"
                           href="#">

                            <i class="fas fa-language me-2"></i>

                            ভাষা সেটিংস

                        </a>

                    </li>

                @endsidebarPermission


                {{-- =====================================================
                     থিম সেটিংস
                ====================================================== --}}

                @sidebarPermission('theme.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.theme.*'
                                ) ? 'active' : ''
                            }}"
                           href="#">

                            <i class="fas fa-paint-brush me-2"></i>

                            থিম সেটিংস

                        </a>

                    </li>

                @endsidebarPermission


                {{-- =====================================================
                     ব্যাকআপ ও রিস্টোর
                ====================================================== --}}

                @sidebarPermission('backup.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.backup.*'
                                ) ? 'active' : ''
                            }}"
                           href="#">

                            <i class="fas fa-database me-2"></i>

                            ব্যাকআপ ও রিস্টোর

                        </a>

                    </li>

                @endsidebarPermission


                {{-- =====================================================
                     সিস্টেম সেটিংস
                ====================================================== --}}

                @sidebarPermission('system-setting.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.system-settings.*'
                                ) ? 'active' : ''
                            }}"
                           href="#">

                            <i class="fas fa-server me-2"></i>

                            সিস্টেম সেটিংস

                        </a>

                    </li>

                @endsidebarPermission


                {{-- =====================================================
                     SUPPORT ADMIN
                     Super Admin Only
                ====================================================== --}}

                @if($canSupportAdmin)

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.support-admins.*'
                                ) ? 'active' : ''
                            }}"
                           href="{{ route('dashboard.support-admins.index') }}">

                            <i class="fas fa-user-shield me-2"></i>

                            সাপোর্ট এডমিন ও পারমিশন

                        </a>

                    </li>

                @endif


            </ul>

        </div>

    </li>

@endif


@endpanel