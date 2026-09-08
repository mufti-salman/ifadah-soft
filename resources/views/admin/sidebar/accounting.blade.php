@panel('accounting')

@php
    $canAccounting =
        auth()->user()->hasPermission('income.view')
        || auth()->user()->hasPermission('fee.view')
        || auth()->user()->hasPermission('fee.collect')
        || auth()->user()->hasPermission('fee.report')
        || auth()->user()->hasPermission('payment.view')
        || auth()->user()->hasPermission('accounting-report.view');
@endphp

@if($canAccounting)

    <li class="nav-item">

        <a class="nav-link text-white d-flex justify-content-between
            {{ request()->routeIs(
                'dashboard.transactions.*',
                'dashboard.fees.*',
                'dashboard.fee-settings.*',
                'dashboard.fee-collection.*',
                'dashboard.fee-report.*',
                'dashboard.ledger.*',
                'dashboard.sub-ledger.*',
                'dashboard.payment-method.*',
                'dashboard.cashier.*',
                'dashboard.reports.*'
            ) ? 'active' : '' }}"
           data-bs-toggle="collapse"
           data-bs-target="#accountMenu"
           href="#accountMenu">

            <span>
                <i class="fas fa-wallet me-2"></i>
                অ্যাকাউন্টিং
            </span>

            <i class="fas fa-chevron-down"></i>

        </a>


        <div class="collapse {{ $open('accounting.') }}"
             id="accountMenu">

            <ul class="nav flex-column ms-3">


                {{-- আয় / ব্যয় --}}

                @sidebarPermission('income.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.transactions.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.transactions.index') }}">

                            <i class="fas fa-exchange-alt me-2"></i>

                            আয় / ব্যয়

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ফি সেটিং --}}

                @sidebarPermission('fee.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.fee-settings.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.fee-settings.index') }}">

                            <i class="fas fa-cog me-2"></i>

                            ফি সেটিং

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ফি গ্রুপ --}}

                @sidebarPermission('fee.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.fees.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.fees.index') }}">

                            <i class="fas fa-layer-group me-2"></i>

                            ফি গ্রুপ

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ফি গ্রহণ --}}

                @sidebarPermission('fee.collect')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.fee-collection.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.fee-collection.index') }}">

                            <i class="fas fa-hand-holding-usd me-2"></i>

                            ফি গ্রহণ

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ফি রিপোর্ট --}}

                @sidebarPermission('fee.report')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.fee-report.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.fee-report.index') }}">

                            <i class="fas fa-file-invoice-dollar me-2"></i>

                            ফি রিপোর্ট

                        </a>

                    </li>

                @endsidebarPermission


                {{-- লেজার --}}

                @sidebarPermission('income.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.ledger.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.ledger.index') }}">

                            <i class="fas fa-book me-2"></i>

                            লেজার

                        </a>

                    </li>

                @endsidebarPermission


                {{-- সাব লেজার --}}

                @sidebarPermission('income.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.sub-ledger.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.sub-ledger.index') }}">

                            <i class="fas fa-book-open me-2"></i>

                            সাব লেজার

                        </a>

                    </li>

                @endsidebarPermission


                {{-- পেমেন্ট মেথড --}}

                @sidebarPermission('payment.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.payment-method.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.payment-method.index') }}">

                            <i class="fas fa-credit-card me-2"></i>

                            পেমেন্ট মেথড

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ক্যাশিয়ার --}}

                @sidebarPermission('payment.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.cashier.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.cashier.index') }}">

                            <i class="fas fa-cash-register me-2"></i>

                            ক্যাশিয়ার

                        </a>

                    </li>

                @endsidebarPermission


                {{-- আয়-ব্যয় রিপোর্ট --}}

                @sidebarPermission('accounting-report.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{ request()->routeIs('dashboard.reports.*') ? 'active' : '' }}"
                           href="{{ route('dashboard.reports.income-expense') }}">

                            <i class="fas fa-chart-line me-2"></i>

                            আয়-ব্যয় রিপোর্ট

                        </a>

                    </li>

                @endsidebarPermission


            </ul>

        </div>

    </li>

@endif

@endpanel