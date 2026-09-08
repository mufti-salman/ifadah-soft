@panel('student')

@php
    $canStudent =
        auth()->user()->hasPermission('academic.view')
        || auth()->user()->hasPermission('class.view')
        || auth()->user()->hasPermission('section.view')
        || auth()->user()->hasPermission('admission.view')
        || auth()->user()->hasPermission('student.view')
        || auth()->user()->hasPermission('student.create')
        || auth()->user()->hasPermission('student.export')
        || auth()->user()->hasPermission('student.report');
@endphp

@if($canStudent)

    <li class="nav-item">

        <a class="nav-link text-white d-flex justify-content-between
            {{ request()->routeIs(
                'dashboard.students.*',
                'dashboard.admissions.*',
                'dashboard.classes.*',
                'dashboard.sections.*',
                'dashboard.academic-years.*'
            ) ? 'active' : '' }}"
           data-bs-toggle="collapse"
           data-bs-target="#studentMenu"
           href="#studentMenu">

            <span>
                <i class="fas fa-user-graduate me-2"></i>
                শিক্ষার্থী
            </span>

            <i class="fas fa-chevron-down"></i>

        </a>


        <div class="collapse {{ $open('students.') }}" id="studentMenu">

            <ul class="nav flex-column ms-3">


                {{-- শিক্ষাবর্ষ --}}
                @sidebarPermission('academic.view')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.academic-years.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.academic-years.index') }}">

                            <i class="fas fa-calendar-alt me-2"></i>
                            শিক্ষাবর্ষ

                        </a>

                    </li>
                @endsidebarPermission


                {{-- ক্লাস --}}
                @sidebarPermission('class.view')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.classes.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.classes.index') }}">

                            <i class="fas fa-school me-2"></i>
                            ক্লাস

                        </a>

                    </li>
                @endsidebarPermission


                {{-- ক্লাস গ্রুপ --}}
                @sidebarPermission('section.view')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.sections.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.sections.index') }}">

                            <i class="fas fa-layer-group me-2"></i>
                            ক্লাস গ্রুপ

                        </a>

                    </li>
                @endsidebarPermission


                {{-- শিক্ষার্থী ভর্তি --}}
                @sidebarPermission('admission.view')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.admissions.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.admissions.index') }}">

                            <i class="fas fa-user-plus me-2"></i>
                            শিক্ষার্থী ভর্তি

                        </a>

                    </li>
                @endsidebarPermission


                {{-- শিক্ষার্থীর তালিকা --}}
                @sidebarPermission('student.view')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.students.index') ? 'active' : '' }}"
                           href="{{ route('dashboard.students.index') }}">

                            <i class="fas fa-users me-2"></i>
                            শিক্ষার্থীর তালিকা

                        </a>

                    </li>
                @endsidebarPermission


                {{-- নতুন শিক্ষার্থী --}}
                @sidebarPermission('student.create')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.students.create') ? 'active' : '' }}"
                           href="{{ route('dashboard.students.create') }}">

                            <i class="fas fa-user-plus me-2"></i>
                            নতুন শিক্ষার্থী

                        </a>

                    </li>
                @endsidebarPermission


                {{-- ডাটা এক্সপোর্ট --}}
                @sidebarPermission('student.export')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.students.export.*') ? 'active' : '' }}"
                           href="#">

                            <i class="fas fa-file-export me-2"></i>
                            ডাটা এক্সপোর্ট

                        </a>

                    </li>
                @endsidebarPermission


                {{-- শিক্ষার্থী রিপোর্ট --}}
                @sidebarPermission('student.report')
                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.students.report.*') ? 'active' : '' }}"
                           href="#">

                            <i class="fas fa-chart-bar me-2"></i>
                            শিক্ষার্থী রিপোর্ট

                        </a>

                    </li>
                @endsidebarPermission


            </ul>

        </div>

    </li>

@endif

@endpanel