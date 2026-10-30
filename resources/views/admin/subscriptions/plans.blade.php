@extends('layouts.admin')

@section('title', 'সাবস্ক্রিপশন প্যাকেজ')

@section('page')


<style>
    /* ==========================================
       PREMIUM SUBSCRIPTION PLANS UI
       Institution / Madrasa ERP
    ========================================== */

    :root {
        --plan-primary: #4f46e5;
        --plan-primary-dark: #3730a3;
        --plan-primary-light: #eef2ff;
        --plan-success: #059669;
        --plan-text: #172033;
        --plan-muted: #64748b;
        --plan-border: #e8edf5;
        --plan-surface: #ffffff;
        --plan-background: #f5f7fc;
    }

    /* Main page */

    .container-fluid:has(.plan-card) {
        color: var(--plan-text);
    }

    /* Page heading */

    .container-fluid > .d-flex h3 {
        color: var(--plan-text);
        font-size: 1.65rem;
        font-weight: 800;
        letter-spacing: -.6px;
    }

    .container-fluid > .d-flex p {
        color: var(--plan-muted);
        font-size: .94rem;
    }

    .container-fluid > .d-flex > a {
        padding: 10px 17px;
        border-radius: 10px;
        font-weight: 600;
        transition: all .2s ease;
    }

    .container-fluid > .d-flex > a:hover {
        transform: translateY(-2px);
    }

    /* Billing selector */

    .billing-selector {
        position: relative;
        max-width: 530px;
        margin-left: auto;
        margin-right: auto;
        padding: 22px 20px !important;
        background: linear-gradient(135deg, #ffffff, #f4f6ff) !important;
        border: 1px solid #e0e7ff !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 30px rgba(79, 70, 229, .055);
    }

    .billing-label {
        font-size: .95rem;
        font-weight: 700;
        color: #94a3b8;
        cursor: pointer;
        transition: color .2s ease;
    }

    .billing-label.active {
        color: var(--plan-primary);
    }

    .billing-switch {
        width: 3rem !important;
        height: 1.55rem !important;
        margin-top: 0 !important;
        border: 0 !important;
        background-color: #cbd5e1 !important;
        cursor: pointer;
        box-shadow: none !important;
    }

    .billing-switch:checked {
        background-color: var(--plan-primary) !important;
    }

    .billing-selector .small {
        font-weight: 600;
        letter-spacing: .1px;
    }

    /* Plan card */

    .plan-card {
        position: relative;
        background: var(--plan-surface);
        border: 1px solid var(--plan-border);
        border-radius: 22px;
        box-shadow: 0 5px 24px rgba(15, 23, 42, .045);
        transition:
            transform .25s ease,
            box-shadow .25s ease,
            border-color .25s ease;
    }

    .plan-card:hover {
        transform: translateY(-7px);
        border-color: #c7d2fe;
        box-shadow: 0 20px 45px rgba(79, 70, 229, .12);
    }

    /* Card header */

    .plan-card-header {
        position: relative;
        padding: 30px 25px 24px;
        text-align: center;
        background:
            radial-gradient(
                circle at top right,
                rgba(129, 140, 248, .12),
                transparent 55%
            ),
            linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
        border-radius: 21px 21px 0 0;
    }

    .plan-icon {
        width: 68px;
        height: 68px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        color: var(--plan-primary);
        font-size: 27px;
        background: linear-gradient(135deg, #eef2ff, #e0e7ff);
        border: 1px solid #dbe4ff;
        box-shadow: 0 7px 18px rgba(79, 70, 229, .09);
        transition: transform .25s ease;
    }

    .plan-card:hover .plan-icon {
        transform: translateY(-3px) rotate(-3deg);
    }

    .plan-card-header h4 {
        margin-top: 18px !important;
        margin-bottom: 10px !important;
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--plan-text);
        letter-spacing: -.3px;
    }

    .plan-card-header p {
        max-width: 290px;
        margin-left: auto;
        margin-right: auto;
        font-size: .9rem;
        line-height: 1.75;
        color: var(--plan-muted);
    }

    /* Price section */

    .plan-price-area {
        position: relative;
        padding: 25px 18px;
        text-align: center;
        background: linear-gradient(135deg, #f8faff, #f3f5ff);
        border-top: 1px solid #edf0ff;
        border-bottom: 1px solid #e8ecfb;
    }

    .price-display {
        display: flex;
        align-items: baseline;
        justify-content: center;
        flex-wrap: wrap;
        gap: 5px;
    }

    .currency {
        color: #64748b;
        font-size: .82rem;
        font-weight: 800;
        letter-spacing: .4px;
    }

    .plan-price {
        color: var(--plan-primary-dark);
        font-size: clamp(1.8rem, 3vw, 2.35rem);
        line-height: 1.2;
        font-weight: 850;
        letter-spacing: -1.3px;
        overflow-wrap: anywhere;
    }

    .price-period {
        color: #64748b;
        font-size: .88rem;
        font-weight: 500;
    }

    .yearly-details {
        margin-top: 12px;
    }

    .yearly-details .badge {
        padding: 7px 11px;
        border-radius: 8px;
        font-size: .76rem;
        font-weight: 700;
    }

    .yearly-details .bg-success-subtle {
        background: #dcfce7 !important;
        color: #047857 !important;
    }

    /* Limits */

    .plan-limits {
        padding: 20px 25px 10px;
    }

    .limit-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 13px 0;
        border-bottom: 1px solid #f0f2f7;
        color: #475569;
        font-size: .9rem;
    }

    .limit-item:last-child {
        border-bottom: 0;
    }

    .limit-item > span {
        display: inline-flex;
        align-items: center;
    }

    .limit-item i {
        width: 18px;
        text-align: center;
        color: var(--plan-primary) !important;
    }

    .limit-item strong {
        color: var(--plan-text);
        font-size: .88rem;
        font-weight: 750;
        text-align: right;
    }

    /* Features */

    .plan-features {
        flex-grow: 1;
        padding: 20px 25px 25px;
    }

    .plan-features h6 {
        margin-bottom: 19px !important;
        color: var(--plan-text);
        font-size: .96rem;
        font-weight: 800;
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        margin-bottom: 14px;
        color: #475569;
        font-size: .89rem;
        line-height: 1.65;
        overflow-wrap: anywhere;
    }

    .feature-item:last-child {
        margin-bottom: 0;
    }

    .feature-item i {
        flex-shrink: 0;
        margin-top: 4px;
        color: var(--plan-success) !important;
        font-size: .95rem;
    }

    /* Card footer */

    .plan-card-footer {
        padding: 22px 25px 25px;
        background: #fbfcff;
        border-top: 1px solid #edf0f6;
        border-radius: 0 0 21px 21px;
    }

    .choose-plan-btn {
        position: relative;
        padding: 13px 16px;
        border: 1px solid var(--plan-primary);
        border-radius: 11px;
        background: linear-gradient(
            135deg,
            var(--plan-primary),
            #6366f1
        );
        box-shadow: 0 6px 16px rgba(79, 70, 229, .18);
        color: #fff;
        font-size: .92rem;
        font-weight: 750;
        transition: all .22s ease;
    }

    .choose-plan-btn:hover,
    .choose-plan-btn:focus {
        transform: translateY(-2px);
        background: linear-gradient(
            135deg,
            var(--plan-primary-dark),
            var(--plan-primary)
        );
        border-color: var(--plan-primary-dark);
        color: #fff;
        box-shadow: 0 10px 23px rgba(79, 70, 229, .26);
    }

    .choose-plan-btn:active {
        transform: translateY(0);
    }

    .plan-card-footer > p {
        color: #94a3b8 !important;
        font-size: .76rem;
        line-height: 1.6;
    }

    /* Empty state */

    .empty-plans {
        padding: 65px 25px !important;
        background: linear-gradient(135deg, #fff, #f8faff);
        border: 1px dashed #cbd5e1;
        border-radius: 20px;
    }

    .empty-plans-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 85px;
        height: 85px;
        border-radius: 25px;
        background: #eef2ff;
        color: var(--plan-primary);
        font-size: 36px;
    }

    .empty-plans h5 {
        color: var(--plan-text);
        font-weight: 800;
    }

    .empty-plans p {
        max-width: 470px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.8;
    }

    /* Subscription note */

    .subscription-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 17px 20px;
        border: 1px solid #dbeafe;
        border-radius: 13px;
        background: linear-gradient(135deg, #eff6ff, #f5f7ff);
        color: #1e40af;
        font-size: .86rem;
        line-height: 1.8;
    }

    .subscription-note i {
        margin-top: 4px;
        flex-shrink: 0;
        font-size: 1rem;
    }

    /* Alerts */

    .container-fluid > .alert {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(15, 23, 42, .035);
    }

    /* Responsive */

    @media (max-width: 767.98px) {
        .container-fluid > .d-flex h3 {
            font-size: 1.4rem;
        }

        .billing-selector {
            padding: 18px 12px !important;
        }

        .billing-label {
            font-size: .84rem;
        }

        .plan-card-header {
            padding: 25px 20px 21px;
        }

        .plan-limits,
        .plan-features {
            padding-left: 21px;
            padding-right: 21px;
        }

        .plan-card-footer {
            padding: 19px 21px 22px;
        }

        .plan-price {
            font-size: 2rem;
        }
    }

    @media (max-width: 400px) {
        .billing-label {
            font-size: .78rem;
        }

        .billing-switch {
            width: 2.5rem !important;
            height: 1.3rem !important;
        }

        .plan-price {
            font-size: 1.8rem;
        }

        .limit-item {
            font-size: .84rem;
        }
    }

    /* Reduced motion accessibility */

    @media (prefers-reduced-motion: reduce) {
        .plan-card,
        .plan-icon,
        .choose-plan-btn,
        .container-fluid > .d-flex > a {
            transition: none !important;
        }

        .plan-card:hover,
        .plan-card:hover .plan-icon,
        .choose-plan-btn:hover {
            transform: none !important;
        }
    }
</style>


<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="fas fa-cubes text-primary me-2"></i>
                সাবস্ক্রিপশন প্যাকেজ
            </h3>

            <p class="text-muted mb-0">
                আপনার প্রতিষ্ঠানের প্রয়োজন অনুযায়ী একটি প্যাকেজ নির্বাচন করুন।
            </p>
        </div>

        <a href="{{ route('dashboard.subscription.current') }}"
           class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            আমার সাবস্ক্রিপশন
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('warning') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times-circle me-2"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>অনুগ্রহ করে নিচের সমস্যাগুলো ঠিক করুন:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Billing Cycle Selector --}}
    <div class="billing-selector text-center mb-5">
        <span class="billing-label active" id="monthlyLabel">
            মাসিক বিলিং
        </span>

        <div class="form-check form-switch d-inline-flex align-items-center mx-3">
            <input class="form-check-input billing-switch"
                   type="checkbox"
                   role="switch"
                   id="billingToggle"
                   aria-label="মাসিক অথবা বার্ষিক বিলিং নির্বাচন করুন">
        </div>

        <span class="billing-label" id="yearlyLabel">
            বার্ষিক বিলিং
        </span>

        <div class="small text-success mt-2">
            <i class="fas fa-tags me-1"></i>
            বার্ষিক প্যাকেজে সাশ্রয় হতে পারে
        </div>
    </div>

    {{-- Subscription Plans --}}
    @if($plans->isNotEmpty())

        <div class="row g-4 justify-content-center">

            @foreach($plans as $plan)

                @php
                    $monthlyPrice = (float) ($plan->price_monthly ?? 0);
                    $yearlyPrice = (float) ($plan->price_yearly ?? 0);

                    $features = $plan->features ?? [];

                    if (is_string($features)) {
                        $decodedFeatures = json_decode($features, true);
                        $features = is_array($decodedFeatures) ? $decodedFeatures : [];
                    }

                    if (!is_array($features)) {
                        $features = [];
                    }

                    $studentLimit = $plan->student_limit ?? 0;
                    $smsLimit = $plan->sms_limit ?? 0;

                    $monthlyEquivalent = $yearlyPrice > 0
                        ? $yearlyPrice / 12
                        : 0;

                    $yearlySaving = ($monthlyPrice * 12) - $yearlyPrice;

                    $discountPercentage = ($monthlyPrice > 0 && $yearlySaving > 0)
                        ? round(($yearlySaving / ($monthlyPrice * 12)) * 100)
                        : 0;
                @endphp

                <div class="col-12 col-md-6 col-xl-4">

                    <div class="plan-card h-100">

                        {{-- Plan Header --}}
                        <div class="plan-card-header">

                            <div class="plan-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>

                            <h4 class="fw-bold mt-3 mb-2">
                                {{ $plan->name }}
                            </h4>

                            @if(!empty($plan->description))
                                <p class="text-muted mb-0">
                                    {{ $plan->description }}
                                </p>
                            @else
                                <p class="text-muted mb-0">
                                    আপনার প্রতিষ্ঠানের জন্য সাবস্ক্রিপশন প্যাকেজ।
                                </p>
                            @endif

                        </div>

                        {{-- Price --}}
                        <div class="plan-price-area">

                            <div class="price-display">
                                <span class="currency">
                                    {{ $plan->currency ?? 'BDT' }}
                                </span>

                                <span class="plan-price"
                                      data-monthly="{{ $monthlyPrice }}"
                                      data-yearly="{{ $yearlyPrice }}"
                                      data-monthly-equivalent="{{ $monthlyEquivalent }}">
                                    {{ number_format($monthlyPrice, 2) }}
                                </span>

                                <span class="price-period">
                                    / <span class="period-text">মাস</span>
                                </span>
                            </div>

                            <div class="yearly-details d-none">
                                @if($yearlyPrice > 0)
                                    <div class="text-muted small">
                                        বছরে {{ number_format($yearlyPrice, 2) }}
                                        {{ $plan->currency ?? 'BDT' }}
                                    </div>
                                @endif

                                @if($discountPercentage > 0)
                                    <span class="badge bg-success-subtle text-success mt-2">
                                        {{ $discountPercentage }}% পর্যন্ত সাশ্রয়
                                    </span>
                                @endif
                            </div>

                        </div>

                        {{-- Plan Limits --}}
                        <div class="plan-limits">

                            <div class="limit-item">
                                <span>
                                    <i class="fas fa-user-graduate me-2 text-primary"></i>
                                    শিক্ষার্থী
                                </span>

                                <strong>
                                    {{ (int) $studentLimit > 0
                                        ? number_format($studentLimit)
                                        : 'সীমাহীন' }}
                                </strong>
                            </div>

                            <div class="limit-item">
                                <span>
                                    <i class="fas fa-sms me-2 text-primary"></i>
                                    SMS
                                </span>

                                <strong>
                                    {{ (int) $smsLimit > 0
                                        ? number_format($smsLimit)
                                        : 'সীমাহীন' }}
                                </strong>
                            </div>

                            @if(isset($plan->trial_days) && (int) $plan->trial_days > 0)
                                <div class="limit-item">
                                    <span>
                                        <i class="fas fa-calendar-check me-2 text-primary"></i>
                                        ট্রায়াল
                                    </span>

                                    <strong>
                                        {{ (int) $plan->trial_days }} দিন
                                    </strong>
                                </div>
                            @endif

                        </div>

                        {{-- Features --}}
                        <div class="plan-features">

                            <h6 class="fw-bold mb-3">
                                প্যাকেজের সুবিধাসমূহ
                            </h6>

                            @if(count($features) > 0)

                                @foreach($features as $feature)
                                    <div class="feature-item">
                                        <i class="fas fa-check-circle text-success me-2"></i>

                                        <span>
                                            @if(is_array($feature))
                                                {{ $feature['name'] ?? $feature['title'] ?? $feature['label'] ?? 'অন্তর্ভুক্ত সুবিধা' }}
                                            @else
                                                {{ $feature }}
                                            @endif
                                        </span>
                                    </div>
                                @endforeach

                            @else

                                <div class="text-muted small">
                                    এই প্যাকেজের অতিরিক্ত সুবিধার বিবরণ দেওয়া হয়নি।
                                </div>

                            @endif

                        </div>

                        {{-- Choose Plan Form --}}
                        <div class="plan-card-footer">

                            <form action="{{ route('dashboard.subscription.choose') }}"
                                  method="POST"
                                  class="choose-plan-form">

                                @csrf

                                <input type="hidden"
                                       name="plan_id"
                                       value="{{ $plan->id }}">

                                <input type="hidden"
                                       name="billing_cycle"
                                       class="billing-cycle-input"
                                       value="monthly">

                                <button type="submit"
                                        class="btn btn-primary w-100 choose-plan-btn">

                                    <i class="fas fa-check-circle me-2"></i>
                                    এই প্যাকেজ নির্বাচন করুন

                                </button>

                            </form>

                            <p class="text-muted text-center small mt-3 mb-0">
                                নির্বাচনের পর পরবর্তী নির্দেশনা অনুসরণ করুন।
                            </p>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        {{-- No Plans Available --}}
        <div class="empty-plans text-center py-5 px-3">

            <div class="empty-plans-icon mb-3">
                <i class="fas fa-box-open"></i>
            </div>

            <h5 class="fw-bold">
                বর্তমানে কোনো প্যাকেজ পাওয়া যাচ্ছে না
            </h5>

            <p class="text-muted mb-4">
                নতুন প্যাকেজ চালু হলে এখানে দেখা যাবে।
                প্রয়োজনে আপনার প্রতিষ্ঠানের অ্যাডমিনের সঙ্গে যোগাযোগ করুন।
            </p>

            <a href="{{ route('dashboard.subscription.current') }}"
               class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i>
                আমার সাবস্ক্রিপশনে ফিরে যান
            </a>

        </div>

    @endif

    {{-- Footer Note --}}
    <div class="subscription-note mt-5">
        <i class="fas fa-info-circle me-2"></i>
        প্যাকেজ নির্বাচন করলেই পেমেন্ট সম্পন্ন হয়েছে বলে গণ্য হবে না।
        পরবর্তী পেমেন্ট বা অনুমোদনের নির্দেশনা অনুসরণ করুন।
    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const billingToggle = document.getElementById('billingToggle');
    const monthlyLabel = document.getElementById('monthlyLabel');
    const yearlyLabel = document.getElementById('yearlyLabel');

    function updateBillingCycle() {
        const isYearly = billingToggle.checked;

        monthlyLabel.classList.toggle('active', !isYearly);
        yearlyLabel.classList.toggle('active', isYearly);

        document.querySelectorAll('.plan-price').forEach(function (priceElement) {
            const monthlyPrice = Number(priceElement.dataset.monthly || 0);
            const yearlyPrice = Number(priceElement.dataset.yearly || 0);
            const monthlyEquivalent = Number(
                priceElement.dataset.monthlyEquivalent || 0
            );

            if (isYearly) {
                priceElement.textContent = yearlyPrice.toLocaleString(
                    'en-US',
                    { minimumFractionDigits: 2, maximumFractionDigits: 2 }
                );

                const yearlyDetails = priceElement
                    .closest('.plan-price-area')
                    .querySelector('.yearly-details');

                if (yearlyDetails) {
                    yearlyDetails.classList.remove('d-none');
                }

                const periodText = priceElement
                    .closest('.plan-price-area')
                    .querySelector('.period-text');

                if (periodText) {
                    periodText.textContent = 'বছর';
                }
            } else {
                priceElement.textContent = monthlyPrice.toLocaleString(
                    'en-US',
                    { minimumFractionDigits: 2, maximumFractionDigits: 2 }
                );

                const yearlyDetails = priceElement
                    .closest('.plan-price-area')
                    .querySelector('.yearly-details');

                if (yearlyDetails) {
                    yearlyDetails.classList.add('d-none');
                }

                const periodText = priceElement
                    .closest('.plan-price-area')
                    .querySelector('.period-text');

                if (periodText) {
                    periodText.textContent = 'মাস';
                }
            }
        });

        document.querySelectorAll('.billing-cycle-input').forEach(function (input) {
            input.value = isYearly ? 'yearly' : 'monthly';
        });
    }

    if (billingToggle) {
        billingToggle.addEventListener('change', updateBillingCycle);
        updateBillingCycle();
    }
});
</script>

@endsection