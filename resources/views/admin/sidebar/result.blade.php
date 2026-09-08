@panel('result')

@php
    $canResult =
        auth()->user()->hasPermission('result.view')
        || auth()->user()->hasPermission('result.publish')
        || auth()->user()->hasPermission('result.edit')
        || auth()->user()->hasPermission('result.export')
        || auth()->user()->hasPermission('result.print')
        || auth()->user()->hasPermission('grade.view')
        || auth()->user()->hasPermission('grade.create')
        || auth()->user()->hasPermission('grade.edit')
        || auth()->user()->hasPermission('grade.delete');
@endphp

@if($canResult)

    <li class="nav-item">

        <a class="nav-link text-white d-flex justify-content-between
            {{ request()->routeIs(
                'dashboard.result.*',
                'dashboard.grade.*'
            ) ? 'active' : '' }}"
           data-bs-toggle="collapse"
           data-bs-target="#resultMenu"
           href="#resultMenu">

            <span>
                <i class="fas fa-chart-line me-2"></i>
                ফলাফল
            </span>

            <i class="fas fa-chevron-down"></i>

        </a>


        <div class="collapse {{ $open('result.') }}" id="resultMenu">

            <ul class="nav flex-column ms-3">

                {{-- ফলাফল এন্ট্রি --}}
                @sidebarPermission('result.edit')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.result.create') ? 'active' : '' }}"
                           href="#">

                            <i class="fas fa-edit me-2"></i>
                            ফলাফল এন্ট্রি

                        </a>
                    </li>
                @endsidebarPermission


                {{-- ফলাফল সম্পাদনা --}}
                @sidebarPermission('result.edit')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.result.edit') ? 'active' : '' }}"
                           href="#">

                            <i class="fas fa-pencil-alt me-2"></i>
                            ফলাফল সম্পাদনা

                        </a>
                    </li>
                @endsidebarPermission


                {{-- গ্রেড / GPA সেটিং --}}
                @sidebarPermission('grade.view')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.grade.*') ? 'active' : '' }}"
                           href="#">

                            <i class="fas fa-star me-2"></i>
                            গ্রেড / GPA সেটিং

                        </a>
                    </li>
                @endsidebarPermission


                {{-- ফলাফল প্রকাশ --}}
                @sidebarPermission('result.publish')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.result.publish.*') ? 'active' : '' }}"
                           href="#">

                            <i class="fas fa-bullhorn me-2"></i>
                            ফলাফল প্রকাশ

                        </a>
                    </li>
                @endsidebarPermission


                {{-- মার্কশিট প্রিন্ট --}}
                @sidebarPermission('result.print')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.result.print.*') ? 'active' : '' }}"
                           href="#">

                            <i class="fas fa-print me-2"></i>
                            মার্কশিট প্রিন্ট

                        </a>
                    </li>
                @endsidebarPermission


                {{-- ফলাফল এক্সপোর্ট --}}
                @sidebarPermission('result.export')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.result.export.*') ? 'active' : '' }}"
                           href="#">

                            <i class="fas fa-file-export me-2"></i>
                            ফলাফল এক্সপোর্ট

                        </a>
                    </li>
                @endsidebarPermission


                {{-- ফলাফল রিপোর্ট --}}
                @sidebarPermission('result.view')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.result.report.*') ? 'active' : '' }}"
                           href="#">

                            <i class="fas fa-file-alt me-2"></i>
                            ফলাফল রিপোর্ট

                        </a>
                    </li>
                @endsidebarPermission


                {{-- ফলাফল অ্যানালিটিক্স --}}
                @sidebarPermission('result.view')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.result.analytics.*') ? 'active' : '' }}"
                           href="#">

                            <i class="fas fa-chart-pie me-2"></i>
                            ফলাফল অ্যানালিটিক্স

                        </a>
                    </li>
                @endsidebarPermission

            </ul>

        </div>

    </li>

@endif

@endpanel