@panel('attendance')

@php

    $user = auth()->user();

    $canAttendance =
        $user->hasPermission('attendance.view')
        || $user->hasPermission('attendance.take')
        || $user->hasPermission('attendance.edit')
        || $user->hasPermission('attendance.report')
        || $user->hasPermission('attendance.export');

@endphp


@if($canAttendance)

    <li class="nav-item">

        <a class="nav-link text-white d-flex justify-content-between
            {{ request()->routeIs('dashboard.attendance.*') ? 'active' : '' }}"
           data-bs-toggle="collapse"
           data-bs-target="#attendanceMenu"
           href="#attendanceMenu">

            <span>
                <i class="fas fa-calendar-check me-2"></i>
                হাজিরা
            </span>

            <i class="fas fa-chevron-down"></i>

        </a>


        <div class="collapse {{ $open('attendance.') }}"
             id="attendanceMenu">

            <ul class="nav flex-column ms-3">


                {{-- ম্যানুয়াল হাজিরা --}}

                @sidebarPermission('attendance.take')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.attendance.*') ? 'active' : '' }}"
                           href="#">

                            <i class="fas fa-user-check me-2"></i>

                            ম্যানুয়াল হাজিরা

                        </a>

                    </li>

                @endsidebarPermission


                {{-- অনলাইন হাজিরা --}}

                @sidebarPermission('attendance.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50"
                           href="#">

                            <i class="fas fa-globe me-2"></i>

                            অনলাইন হাজিরা

                        </a>

                    </li>

                @endsidebarPermission


                {{-- বায়োমেট্রিক হাজিরা --}}

                @sidebarPermission('attendance.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50"
                           href="#">

                            <i class="fas fa-fingerprint me-2"></i>

                            বায়োমেট্রিক হাজিরা

                        </a>

                    </li>

                @endsidebarPermission


                {{-- হাজিরা ড্যাশবোর্ড --}}

                @sidebarPermission('attendance.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50"
                           href="#">

                            <i class="fas fa-chart-pie me-2"></i>

                            হাজিরা ড্যাশবোর্ড

                        </a>

                    </li>

                @endsidebarPermission


                {{-- হাজিরা সেটিং --}}

                @sidebarPermission('attendance.edit')

                    <li class="nav-item">

                        <a class="nav-link text-white-50"
                           href="#">

                            <i class="fas fa-cog me-2"></i>

                            হাজিরা সেটিং

                        </a>

                    </li>

                @endsidebarPermission


                {{-- হাজিরা রিপোর্ট --}}

                @sidebarPermission('attendance.report')

                    <li class="nav-item">

                        <a class="nav-link text-white-50"
                           href="#">

                            <i class="fas fa-file-alt me-2"></i>

                            হাজিরা রিপোর্ট

                        </a>

                    </li>

                @endsidebarPermission


                {{-- হাজিরা এক্সপোর্ট --}}

                @sidebarPermission('attendance.export')

                    <li class="nav-item">

                        <a class="nav-link text-white-50"
                           href="#">

                            <i class="fas fa-file-export me-2"></i>

                            হাজিরা এক্সপোর্ট

                        </a>

                    </li>

                @endsidebarPermission


            </ul>

        </div>

    </li>

@endif


@endpanel