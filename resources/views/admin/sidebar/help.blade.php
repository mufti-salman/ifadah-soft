@panel('help')

@php
    $canHelp =
        auth()->user()->hasPermission('support.view')
        || auth()->user()->hasPermission('support.create')
        || auth()->user()->hasPermission('ticket.view')
        || auth()->user()->hasPermission('ticket.create');
@endphp

@if($canHelp)

    <li class="nav-item">

        <a class="nav-link text-white d-flex justify-content-between
            {{ request()->routeIs(
                'dashboard.help.*',
                'dashboard.support.*',
                'dashboard.tickets.*'
            ) ? 'active' : '' }}"
           data-bs-toggle="collapse"
           data-bs-target="#helpsMenu"
           href="#helpsMenu">

            <span>
                <i class="fas fa-life-ring me-2"></i>
                হেল্প
            </span>

            <i class="fas fa-chevron-down"></i>

        </a>

        <div class="collapse {{ $open('help.') }}" id="helpsMenu">

            <ul class="nav flex-column ms-3">

                {{-- সফটওয়্যার ভিডিও --}}
                @sidebarPermission('support.view')
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.help.videos.*') ? 'active' : '' }}"
                           href="#">
                            <i class="fas fa-video me-2"></i>
                            সফটওয়্যার ভিডিও
                        </a>
                    </li>
                @endsidebarPermission


                {{-- সাপোর্ট / টিকেট --}}
                @if(
                    auth()->user()->hasPermission('support.view')
                    || auth()->user()->hasPermission('ticket.view')
                    || auth()->user()->hasPermission('ticket.create')
                )
                    <li class="nav-item">
                        <a class="nav-link text-white-50
                            {{ request()->routeIs(
                                'dashboard.support.*',
                                'dashboard.tickets.*'
                            ) ? 'active' : '' }}"
                           href="#">
                            <i class="fas fa-headset me-2"></i>
                            সাপোর্ট / টিকেট
                        </a>
                    </li>
                @endif

            </ul>

        </div>

    </li>

@endif

@endpanel