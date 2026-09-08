@panel('settings')

@php
    $canSettings =
        auth()->user()->hasPermission('general.view')
        || auth()->user()->hasPermission('academic.view')
        || auth()->user()->hasPermission('institution.view')
        || auth()->user()->hasPermission('month.view')
        || auth()->user()->hasPermission('language.view')
        || auth()->user()->hasPermission('theme.view')
        || auth()->user()->hasPermission('backup.view')
        || auth()->user()->hasPermission('system-setting.view');
@endphp

@if($canSettings)

    <li class="nav-item">

        <a class="nav-link text-white d-flex justify-content-between
            {{ request()->routeIs(
                'dashboard.settings.*',
                'dashboard.academic-years.*',
                'dashboard.institutions.*',
                'dashboard.month.*',
                'dashboard.language.*',
                'dashboard.theme.*',
                'dashboard.backup.*'
            ) ? 'active' : '' }}"
           data-bs-toggle="collapse"
           data-bs-target="#settingsMenu"
           href="#settingsMenu">

            <span>
                <i class="fas fa-cog me-2"></i>
                সেটিংস
            </span>

            <i class="fas fa-chevron-down"></i>

        </a>


        <div class="collapse {{ $open('settings.') }}" id="settingsMenu">

            <ul class="nav flex-column ms-3">


                {{-- General Settings --}}
                @sidebarPermission('general.view')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.settings.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.settings.index') }}">

                            <i class="fas fa-sliders-h me-2"></i>
                            সাধারণ সেটিংস

                        </a>

                    </li>
                @endsidebarPermission


                {{-- Academic Settings --}}
                @sidebarPermission('academic.view')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.academic-years.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.academic-years.index') }}">

                            <i class="fas fa-graduation-cap me-2"></i>
                            একাডেমিক সেটিংস

                        </a>

                    </li>
                @endsidebarPermission


                {{-- Institution Settings --}}
                @sidebarPermission('institution.view')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.institutions.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.institutions.index') }}">

                            <i class="fas fa-school me-2"></i>
                            প্রতিষ্ঠান সেটিংস

                        </a>

                    </li>
                @endsidebarPermission


                {{-- Month Settings --}}
                @sidebarPermission('month.view')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.month.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.month.index') }}">

                            <i class="fas fa-calendar-alt me-2"></i>
                            মাস সেটিংস

                        </a>

                    </li>
                @endsidebarPermission


                {{-- Language Settings --}}
                @sidebarPermission('language.view')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.language.*') ? 'active' : '' }}"
                           href="#">

                            <i class="fas fa-language me-2"></i>
                            ভাষা সেটিংস

                        </a>

                    </li>
                @endsidebarPermission


                {{-- Theme Settings --}}
                @sidebarPermission('theme.view')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.theme.*') ? 'active' : '' }}"
                           href="#">

                            <i class="fas fa-paint-brush me-2"></i>
                            থিম সেটিংস

                        </a>

                    </li>
                @endsidebarPermission


                {{-- Backup & Restore --}}
                @sidebarPermission('backup.view')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.backup.*') ? 'active' : '' }}"
                           href="#">

                            <i class="fas fa-database me-2"></i>
                            ব্যাকআপ ও রিস্টোর

                        </a>

                    </li>
                @endsidebarPermission


                {{-- =====================================================
                     SUPPORT ADMIN
                ====================================================== --}}

{{-- =====================================================
     SUPPORT ADMIN
====================================================== --}}

@if(auth()->user()->is_super_admin)

    <li class="nav-item">

        <a class="nav-link text-white-50
            {{ request()->routeIs('dashboard.support-admins.*') ? 'active' : '' }}"
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