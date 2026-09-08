@panel('dashboard')

@php
    $canDashboard =
        auth()->user()->hasPermission('dashboard.view')
        || auth()->user()->hasPermission('user.view')
        || auth()->user()->hasPermission('month.view')
        || auth()->user()->hasPermission('user.photo')
        || auth()->user()->hasPermission('user.report')
        || auth()->user()->hasPermission('user.edit')
        || (
            auth()->user()->is_super_admin
            && auth()->user()->hasPermission('role.view')
            && auth()->user()->hasPermission('permission.view')
        );
@endphp

@if($canDashboard)

    <li class="nav-item">

        <a class="nav-link text-white d-flex justify-content-between
            {{ request()->routeIs(
                'dashboard.index',
                'dashboard.user.*',
                'dashboard.month.*',
                'dashboard.role-permissions.*'
            ) ? 'active' : '' }}"
           data-bs-toggle="collapse"
           data-bs-target="#dashboardMenu"
           href="#dashboardMenu">

            <span>
                <i class="fas fa-home me-2"></i>
                ড্যাশবোর্ড
            </span>

            <i class="fas fa-chevron-down"></i>

        </a>


        <div class="collapse {{ $open('dashboard.') }}"
             id="dashboardMenu">

            <ul class="nav flex-column ms-3">


                {{-- Dashboard --}}

                @sidebarPermission('dashboard.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.index') ? 'active' : '' }}"
                           href="{{ route('dashboard.index') }}">

                            <i class="fas fa-chart-pie me-2"></i>

                            ড্যাশবোর্ড

                        </a>

                    </li>

                @endsidebarPermission


                {{-- নতুন ব্যবহারকারী --}}

                @sidebarPermission('user.create')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.user.create') ? 'active' : '' }}"
                           href="{{ route('dashboard.user.create') }}">

                            <i class="fas fa-user-plus me-2"></i>

                            নতুন ব্যবহারকারী

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ব্যবহারকারী তালিকা --}}

                @sidebarPermission('user.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.user.index') ? 'active' : '' }}"
                           href="{{ route('dashboard.user.index') }}">

                            <i class="fas fa-users me-2"></i>

                            ব্যবহারকারী তালিকা

                        </a>

                    </li>

                @endsidebarPermission


                {{-- মাস সেটিং --}}

                @sidebarPermission('month.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.month.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.month.index') }}">

                            <i class="fas fa-calendar-alt me-2"></i>

                            মাস সেটিং

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ব্যবহারকারী ছবি --}}

                @sidebarPermission('user.photo')

                    <li class="nav-item">

                        <a class="nav-link text-white-50"
                           href="#">

                            <i class="fas fa-image me-2"></i>

                            ব্যবহারকারী ছবি

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ব্যবহারকারী রিপোর্ট --}}

                @sidebarPermission('user.report')

                    <li class="nav-item">

                        <a class="nav-link text-white-50"
                           href="#">

                            <i class="fas fa-chart-bar me-2"></i>

                            ব্যবহারকারী রিপোর্ট

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ব্যবহারকারী পারমিশন --}}

                @sidebarPermission('user.edit')

                    <li class="nav-item">

                        <a class="nav-link text-white-50"
                           href="#">

                            <i class="fas fa-user-lock me-2"></i>

                            ব্যবহারকারী পারমিশন

                        </a>

                    </li>

                @endsidebarPermission

                {{-- প্রতিষ্ঠানের তথ্য --}}

@sidebarPermission('institution.view')

    <li class="nav-item">

        <a class="nav-link text-white-50
            {{ request()->routeIs('dashboard.institution.info') ? 'active' : '' }}"
           href="{{ route('dashboard.institution.info') }}">

            <i class="fas fa-school me-2"></i>

            প্রতিষ্ঠানের তথ্য

        </a>

    </li>

@endsidebarPermission


                {{-- Role / Institution Permission Manager --}}

                @if(
                    auth()->user()->is_super_admin
                    && auth()->user()->hasPermission('role.view')
                    && auth()->user()->hasPermission('permission.view')
                )

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.role-permissions.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.role-permissions.index') }}">

                            <i class="fas fa-user-shield me-2"></i>

                            Institution Permission Manager

                        </a>

                    </li>

                @endif


            </ul>

        </div>

    </li>

@endif

@endpanel