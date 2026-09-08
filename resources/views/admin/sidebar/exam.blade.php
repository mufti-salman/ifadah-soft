@panel('exam')

@php
    $canExam =
        auth()->user()->hasPermission('exam-type.view')
        || auth()->user()->hasPermission('exam.view')
        || auth()->user()->hasPermission('exam.create')
        || auth()->user()->hasPermission('exam.schedule')
        || auth()->user()->hasPermission('exam.routine')
        || auth()->user()->hasPermission('exam.edit');
@endphp

@if($canExam)

    <li class="nav-item">

        <a class="nav-link text-white d-flex justify-content-between
            {{ request()->routeIs('dashboard.exam.*') ? 'active' : '' }}"
           data-bs-toggle="collapse"
           data-bs-target="#examMenu"
           href="#examMenu">

            <span>
                <i class="fas fa-file-alt me-2"></i>
                পরীক্ষা
            </span>

            <i class="fas fa-chevron-down"></i>

        </a>

        <div class="collapse {{ $open('exam.') }}" id="examMenu">

            <ul class="nav flex-column ms-3">

                {{-- পরীক্ষার ধরন --}}
                @sidebarPermission('exam-type.view')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.exam-type.*') ? 'active' : '' }}"
                           href="#">
                            <i class="fas fa-tags me-2"></i>
                            পরীক্ষার ধরন
                        </a>
                    </li>
                @endsidebarPermission


                {{-- পরীক্ষার তালিকা --}}
                @sidebarPermission('exam.view')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.exam.index') ? 'active' : '' }}"
                           href="#">
                            <i class="fas fa-list me-2"></i>
                            পরীক্ষার তালিকা
                        </a>
                    </li>
                @endsidebarPermission


                {{-- নতুন পরীক্ষা --}}
                @sidebarPermission('exam.create')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.exam.create') ? 'active' : '' }}"
                           href="#">
                            <i class="fas fa-plus-circle me-2"></i>
                            নতুন পরীক্ষা
                        </a>
                    </li>
                @endsidebarPermission


                {{-- পরীক্ষার সময়সূচী --}}
                @sidebarPermission('exam.schedule')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.exam.schedule.*') ? 'active' : '' }}"
                           href="#">
                            <i class="fas fa-calendar-alt me-2"></i>
                            পরীক্ষার সময়সূচী
                        </a>
                    </li>
                @endsidebarPermission


                {{-- পরীক্ষার রুটিন --}}
                @sidebarPermission('exam.routine')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.exam.routine.*') ? 'active' : '' }}"
                           href="#">
                            <i class="fas fa-calendar-week me-2"></i>
                            পরীক্ষার রুটিন
                        </a>
                    </li>
                @endsidebarPermission


                {{-- পরীক্ষা সেটিং --}}
                @sidebarPermission('exam.edit')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.exam.settings.*') ? 'active' : '' }}"
                           href="#">
                            <i class="fas fa-cog me-2"></i>
                            পরীক্ষা সেটিং
                        </a>
                    </li>
                @endsidebarPermission


                {{-- পরীক্ষার রিপোর্ট --}}
                @sidebarPermission('exam.view')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.exam.report.*') ? 'active' : '' }}"
                           href="#">
                            <i class="fas fa-chart-bar me-2"></i>
                            পরীক্ষার রিপোর্ট
                        </a>
                    </li>
                @endsidebarPermission

            </ul>

        </div>

    </li>

@endif

@endpanel