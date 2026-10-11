@panel('accounting')

@php

    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Accounting Panel Access
    |--------------------------------------------------------------------------
    */

    $canAccounting =
        $user->hasPermission('income.view')
        || $user->hasPermission('income.create')
        || $user->hasPermission('income.edit')
        || $user->hasPermission('income.delete')

        || $user->hasPermission('expense.view')
        || $user->hasPermission('expense.create')
        || $user->hasPermission('expense.edit')
        || $user->hasPermission('expense.delete')

        || $user->hasPermission('fee.view')
        || $user->hasPermission('fee.create')
        || $user->hasPermission('fee.edit')
        || $user->hasPermission('fee.delete')
        || $user->hasPermission('fee.collect')
        || $user->hasPermission('fee.report')
        || $user->hasPermission('fee.export')

        || $user->hasPermission('payment.view')
        || $user->hasPermission('payment.create')
        || $user->hasPermission('payment.edit')
        || $user->hasPermission('payment.delete')

        || $user->hasPermission('accounting-report.view')
        || $user->hasPermission('accounting-report.export')
        || $user->hasPermission('accounting-report.print');

@endphp


@if($canAccounting)

    <li class="nav-item">

        <a class="nav-link text-white d-flex justify-content-between
            {{
                request()->routeIs(
                    'dashboard.transactions.*',
                    'dashboard.income.*',
                    'dashboard.expense.*',
                    'dashboard.fees.*',
                    'dashboard.fee-settings.*',
                    'dashboard.fee-collection.*',
                    'dashboard.fee-report.*',
                    'dashboard.ledger.*',
                    'dashboard.sub-ledger.*',
                    'dashboard.payment-method.*',
                    'dashboard.cashier.*',
                    'dashboard.reports.*'
                ) ? 'active' : ''
            }}"
           data-bs-toggle="collapse"
           data-bs-target="#accountMenu"
           href="#accountMenu">

            <span>
                <i class="fas fa-wallet me-2"></i>
                অ্যাকাউন্টিং
            </span>

            <i class="fas fa-chevron-down"></i>

        </a>


        <div class="collapse
            {{
                request()->routeIs(
                    'dashboard.transactions.*',
                    'dashboard.income.*',
                    'dashboard.expense.*',
                    'dashboard.fees.*',
                    'dashboard.fee-settings.*',
                    'dashboard.fee-collection.*',
                    'dashboard.fee-report.*',
                    'dashboard.ledger.*',
                    'dashboard.sub-ledger.*',
                    'dashboard.payment-method.*',
                    'dashboard.cashier.*',
                    'dashboard.reports.*'
                ) ? 'show' : ''
            }}"
             id="accountMenu">

            <ul class="nav flex-column ms-3">


                {{-- ======================================================
                    আয় / ব্যয়
                ======================================================= --}}

                @if(
                    $user->hasPermission('income.view')
                    || $user->hasPermission('expense.view')
                    || $user->hasPermission('income.create')
                    || $user->hasPermission('expense.create')
                )

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.transactions.*'
                                ) ? 'active' : ''
                            }}"
                           href="{{ route('dashboard.transactions.index') }}">

                            <i class="fas fa-exchange-alt me-2"></i>

                            আয় / ব্যয়

                        </a>

                    </li>

                @endif


                {{-- ======================================================
                    ফি সেটিং
                ======================================================= --}}

                @sidebarPermission('fee.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.fee-settings.*'
                                ) ? 'active' : ''
                            }}"
                           href="{{ route('dashboard.fee-settings.index') }}">

                            <i class="fas fa-cog me-2"></i>

                            ফি সেটিং

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ======================================================
                    ফি গ্রুপ
                ======================================================= --}}

                @sidebarPermission('fee.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.fees.*'
                                ) ? 'active' : ''
                            }}"
                           href="{{ route('dashboard.fees.index') }}">

                            <i class="fas fa-layer-group me-2"></i>

                            ফি গ্রুপ

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ======================================================
                    ফি গ্রহণ
                ======================================================= --}}

                @sidebarPermission('fee.collect')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.fee-collection.*'
                                ) ? 'active' : ''
                            }}"
                           href="{{ route('dashboard.fee-collection.index') }}">

                            <i class="fas fa-hand-holding-usd me-2"></i>

                            ফি গ্রহণ

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ======================================================
                    ফি রিপোর্ট
                ======================================================= --}}

                @sidebarPermission('fee.report')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.fee-report.*'
                                ) ? 'active' : ''
                            }}"
                           href="{{ route('dashboard.fee-report.index') }}">

                            <i class="fas fa-file-invoice-dollar me-2"></i>

                            ফি রিপোর্ট

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ======================================================
                    পেমেন্ট মেথড
                ======================================================= --}}

                @sidebarPermission('payment.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.payment-method.*'
                                ) ? 'active' : ''
                            }}"
                           href="{{ route('dashboard.payment-method.index') }}">

                            <i class="fas fa-credit-card me-2"></i>

                            পেমেন্ট মেথড

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ======================================================
                    আয়-ব্যয় রিপোর্ট
                ======================================================= --}}

                @sidebarPermission('accounting-report.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.reports.*'
                                ) ? 'active' : ''
                            }}"
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