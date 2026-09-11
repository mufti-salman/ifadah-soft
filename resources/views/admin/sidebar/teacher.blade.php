@panel('teacher')

@php

    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Teacher Menu Visibility
    |--------------------------------------------------------------------------
    |
    | Teacher panel-এর নিচে থাকা যেকোনো একটি permission থাকলেই
    | "শিক্ষক" parent menu show হবে।
    |
    */

    $canTeacher =
        $user->hasPermission('student.view')
        || $user->hasPermission('admission.view')
        || $user->hasPermission('teacher.view')
        || $user->hasPermission('teacher.edit')
        || $user->hasPermission('teacher.report');

@endphp


@if($canTeacher)

    <li class="nav-item">

        {{-- Parent Menu --}}

        <a
            class="nav-link text-white d-flex justify-content-between
            {{ request()->routeIs(
                'dashboard.teacher.*',
                'dashboard.teacher.students.*',
                'dashboard.teacher.admissions.*'
            ) ? 'active' : '' }}"

            data-bs-toggle="collapse"
            data-bs-target="#teacherMenu"
            href="#teacherMenu"
        >

            <span>

                <i class="fas fa-chalkboard-teacher me-2"></i>

                শিক্ষক

            </span>

            <i class="fas fa-chevron-down"></i>

        </a>


        {{-- Sub Menu --}}

        <div
            class="collapse {{ $open('teacher.') }}"
            id="teacherMenu"
        >

            <ul class="nav flex-column ms-3">


                {{-- =====================================================
                | শিক্ষার্থীর তালিকা
                ====================================================== --}}

                @sidebarPermission('student.view')

                    <li class="nav-item">

                        <a
                            class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.teacher.students.*') ? 'active' : '' }}"

                            href="{{ route('dashboard.teacher.students.index') }}"
                        >

                            <i class="fas fa-users me-2"></i>

                            শিক্ষার্থীর তালিকা

                        </a>

                    </li>

                @endsidebarPermission



                {{-- =====================================================
                | ভর্তি তথ্য
                ====================================================== --}}

                @sidebarPermission('admission.view')

                    <li class="nav-item">

                        <a
                            class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.teacher.admissions.*') ? 'active' : '' }}"

                            href="{{ route('dashboard.teacher.admissions.index') }}"
                        >

                            <i class="fas fa-user-plus me-2"></i>

                            ভর্তি তথ্য

                        </a>

                    </li>

                @endsidebarPermission



                {{-- =====================================================
                | শিক্ষক তথ্য
                ====================================================== --}}

                @sidebarPermission('teacher.view')

                    <li class="nav-item">

                        <a
                            class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.teacher.info.*') ? 'active' : '' }}"

                            href="#"
                        >

                            <i class="fas fa-user-tie me-2"></i>

                            শিক্ষক তথ্য

                        </a>

                    </li>

                @endsidebarPermission



                {{-- =====================================================
                | ক্লাস এসাইন
                ====================================================== --}}

                @sidebarPermission('teacher.edit')

                    <li class="nav-item">

                        <a
                            class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.teacher.assign.*') ? 'active' : '' }}"

                            href="#"
                        >

                            <i class="fas fa-chalkboard me-2"></i>

                            ক্লাস এসাইন

                        </a>

                    </li>

                @endsidebarPermission



                {{-- =====================================================
                | শিক্ষক রিপোর্ট
                ====================================================== --}}

                @sidebarPermission('teacher.report')

                    <li class="nav-item">

                        <a
                            class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.teacher.report.*') ? 'active' : '' }}"

                            href="#"
                        >

                            <i class="fas fa-chart-bar me-2"></i>

                            শিক্ষক রিপোর্ট

                        </a>

                    </li>

                @endsidebarPermission


            </ul>

        </div>

    </li>

@endif


@endpanel