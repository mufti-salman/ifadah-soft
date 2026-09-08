@panel('institution')

@if(auth()->user()->is_super_admin)

    @php
        $canInstitution =
            auth()->user()->hasPermission('institution.view')
            || auth()->user()->hasPermission('institution.create')
            || auth()->user()->hasPermission('institution.edit')
            || auth()->user()->hasPermission('institution.delete');
    @endphp

    @if($canInstitution)

        <li class="nav-item">

            <a class="nav-link text-white d-flex justify-content-between
                {{ request()->routeIs('dashboard.institutions.*') ? 'active' : '' }}"
               data-bs-toggle="collapse"
               data-bs-target="#institutionMenu"
               href="#institutionMenu">

                <span>
                    <i class="fas fa-school me-2"></i>
                    সকল প্রতিষ্ঠান
                </span>

                <i class="fas fa-chevron-down"></i>

            </a>


            <div class="collapse {{ $open('dashboard.institutions.') }}"
                 id="institutionMenu">

                <ul class="nav flex-column ms-3">

                    {{-- Institution List --}}
                    @sidebarPermission('institution.view')
                        <li class="nav-item">

                            <a class="nav-link text-white-50
                                {{ request()->routeIs('dashboard.institutions.index') ? 'active' : '' }}"
                               href="{{ route('dashboard.institutions.index') }}">

                                <i class="fas fa-list me-2"></i>
                                প্রতিষ্ঠান তালিকা

                            </a>

                        </li>
                    @endsidebarPermission


                    {{-- Add Institution --}}
                    @sidebarPermission('institution.create')
                        <li class="nav-item">

                            <a class="nav-link text-white-50
                                {{ request()->routeIs('dashboard.institutions.create') ? 'active' : '' }}"
                               href="#">

                                <i class="fas fa-plus-circle me-2"></i>
                                নতুন প্রতিষ্ঠান

                            </a>

                        </li>
                    @endsidebarPermission


                    {{-- Institution Report --}}
                    @sidebarPermission('institution.view')
                        <li class="nav-item">

                            <a class="nav-link text-white-50
                                {{ request()->routeIs('dashboard.institutions.report.*') ? 'active' : '' }}"
                               href="#">

                                <i class="fas fa-chart-bar me-2"></i>
                                প্রতিষ্ঠান রিপোর্ট

                            </a>

                        </li>
                    @endsidebarPermission

                </ul>

            </div>

        </li>

    @endif

@endif

@endpanel