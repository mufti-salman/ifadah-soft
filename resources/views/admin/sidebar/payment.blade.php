@panel('payment')

@php

    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Payment Panel Access
    |--------------------------------------------------------------------------
    */

    $canPayment =
        $user->hasPermission('online-payment.view')
        || $user->hasPermission('online-payment.create')
        || $user->hasPermission('online-payment.process')
        || $user->hasPermission('online-payment.verify')
        || $user->hasPermission('online-payment.report')

        || $user->hasPermission('payment.view')
        || $user->hasPermission('payment.create')
        || $user->hasPermission('payment.edit')
        || $user->hasPermission('payment.delete');

@endphp


@if($canPayment)

    <li class="nav-item">

        <a class="nav-link text-white d-flex justify-content-between
            {{
                request()->routeIs(
                    'dashboard.payment.*',
                    'dashboard.online-payment.*'
                ) ? 'active' : ''
            }}"
           data-bs-toggle="collapse"
           data-bs-target="#paymentMenu"
           href="#paymentMenu">

            <span>
                <i class="fas fa-credit-card me-2"></i>
                অনলাইন পেমেন্ট
            </span>

            <i class="fas fa-chevron-down"></i>

        </a>


        <div class="collapse
            {{
                request()->routeIs(
                    'dashboard.payment.*',
                    'dashboard.online-payment.*'
                ) ? 'show' : ''
            }}"
             id="paymentMenu">

            <ul class="nav flex-column ms-3">


                {{-- ======================================================
                    সাবস্ক্রিপশন
                ======================================================= --}}

                @sidebarPermission('online-payment.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.payment.subscription.*'
                                ) ? 'active' : ''
                            }}"
                           href="#">

                            <i class="fas fa-sync-alt me-2"></i>

                            সাবস্ক্রিপশন

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ======================================================
                    স্টুডেন্ট পেমেন্ট
                ======================================================= --}}

                @sidebarPermission('payment.view')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.payment.student.*'
                                ) ? 'active' : ''
                            }}"
                           href="#">

                            <i class="fas fa-user-graduate me-2"></i>

                            স্টুডেন্ট পেমেন্ট

                        </a>

                    </li>

                @endsidebarPermission


                {{-- ======================================================
                    পেমেন্ট হিস্ট্রি
                ======================================================= --}}

                @sidebarPermission('online-payment.report')

                    <li class="nav-item">

                        <a class="nav-link text-white-50
                            {{
                                request()->routeIs(
                                    'dashboard.payment.history.*'
                                ) ? 'active' : ''
                            }}"
                           href="#">

                            <i class="fas fa-history me-2"></i>

                            পেমেন্ট হিস্ট্রি

                        </a>

                    </li>

                @endsidebarPermission


            </ul>

        </div>

    </li>

@endif


@endpanel