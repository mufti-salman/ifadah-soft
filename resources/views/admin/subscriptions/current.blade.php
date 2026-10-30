@extends('layouts.admin')

@section('title', 'আমার সাবস্ক্রিপশন')

@section('page')
<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">আমার সাবস্ক্রিপশন</h2>
            <p class="text-muted mb-0">
                আপনার প্রতিষ্ঠানের সাবস্ক্রিপশন, প্যাকেজ ও পেমেন্টের তথ্য দেখুন।
            </p>
        </div>

        <a href="{{ route('dashboard.subscription.plans') }}"
           class="btn btn-primary mt-3 mt-md-0">
            <i class="fas fa-box-open me-1"></i>
            প্যাকেজ দেখুন
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>সমস্যা হয়েছে:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $statusLabels = [
            'active' => 'সক্রিয়',
            'trialing' => 'ট্রায়াল চলছে',
            'pending_payment' => 'পেমেন্ট অপেক্ষমাণ',
            'expired' => 'মেয়াদ শেষ',
            'cancelled' => 'বাতিল',
            'rejected' => 'প্রত্যাখ্যাত',
        ];

        $statusClasses = [
            'active' => 'success',
            'trialing' => 'info',
            'pending_payment' => 'warning',
            'expired' => 'danger',
            'cancelled' => 'secondary',
            'rejected' => 'danger',
        ];

        $status = $subscription?->status ?? 'none';
        $statusClass = $statusClasses[$status] ?? 'secondary';
        $statusLabel = $statusLabels[$status] ?? 'সাবস্ক্রিপশন নেই';

        $billingCycleLabels = [
            'monthly' => 'মাসিক',
            'yearly' => 'বার্ষিক',
        ];
    @endphp

    @if($subscription)

        {{-- Current Subscription Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="mb-0">বর্তমান প্যাকেজের বিবরণ</h5>

                    <span class="badge bg-{{ $statusClass }} px-3 py-2">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="text-muted small mb-1">প্যাকেজের নাম</div>
                        <h5 class="mb-0">
                            {{ $subscription->plan?->name ?? 'প্যাকেজের তথ্য নেই' }}
                        </h5>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="text-muted small mb-1">বিলিং সাইকেল</div>
                        <h5 class="mb-0">
                            {{ $billingCycleLabels[$subscription->billing_cycle] ?? $subscription->billing_cycle ?? '—' }}
                        </h5>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="text-muted small mb-1">সাবস্ক্রিপশনের মূল্য</div>
                        <h5 class="mb-0">
                            ৳{{ number_format((float) $subscription->amount, 2) }}
                            <small class="text-muted fs-6">
                                {{ $subscription->currency ?? 'BDT' }}
                            </small>
                        </h5>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="text-muted small mb-1">শুরুর তারিখ</div>
                        <div>
                            {{ $subscription->starts_at
                                ? $subscription->starts_at->format('d M Y')
                                : 'এখনো সক্রিয় হয়নি' }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="text-muted small mb-1">মেয়াদ শেষ হওয়ার তারিখ</div>
                        <div>
                            {{ $subscription->ends_at
                                ? $subscription->ends_at->format('d M Y')
                                : 'নির্ধারিত নয়' }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="text-muted small mb-1">স্বয়ংক্রিয় নবায়ন</div>
                        <div>
                            @if($subscription->auto_renew)
                                <span class="text-success">চালু</span>
                            @else
                                <span class="text-muted">বন্ধ</span>
                            @endif
                        </div>
                    </div>

                </div>

                @if($status === 'pending_payment')
                    <div class="alert alert-warning mt-4 mb-0">
                        <i class="fas fa-clock me-1"></i>
                        আপনার প্যাকেজের পেমেন্ট এখনো যাচাই করা হচ্ছে অথবা পেমেন্ট জমা দেওয়া বাকি।
                        পেমেন্টের অবস্থা নিশ্চিত না হওয়া পর্যন্ত সাবস্ক্রিপশন সক্রিয় হয়েছে ধরে নেবেন না।
                    </div>
                @elseif($status === 'expired')
                    <div class="alert alert-danger mt-4 mb-0">
                        আপনার সাবস্ক্রিপশনের মেয়াদ শেষ হয়েছে।
                        নতুন প্যাকেজ নির্বাচন করে নবায়নের প্রক্রিয়া শুরু করুন।
                    </div>
                @elseif($status === 'cancelled')
                    <div class="alert alert-secondary mt-4 mb-0">
                        এই সাবস্ক্রিপশনটি বাতিল করা হয়েছে।
                        প্রয়োজনে নতুন প্যাকেজ নির্বাচন করুন।
                    </div>
                @elseif($status === 'rejected')
                    <div class="alert alert-danger mt-4 mb-0">
                        এই সাবস্ক্রিপশনের আবেদন প্রত্যাখ্যাত হয়েছে।
                        নতুন প্যাকেজ নির্বাচন করার আগে পেমেন্টের অবস্থা যাচাই করুন।
                    </div>
                @endif
            </div>
        </div>

        {{-- Plan Limits --}}
        <div class="row g-3 mb-4">

            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded bg-primary-subtle text-primary p-3 me-3">
                                <i class="fas fa-user-graduate fa-lg"></i>
                            </div>

                            <div>
                                <div class="text-muted small">শিক্ষার্থীর সীমা</div>
                                <h4 class="mb-0">
                                    @if($subscription->student_limit !== null)
                                        {{ $subscription->student_limit == 0
                                            ? 'সীমাহীন'
                                            : number_format($subscription->student_limit) }}
                                    @else
                                        নির্ধারিত নয়
                                    @endif
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded bg-success-subtle text-success p-3 me-3">
                                <i class="fas fa-sms fa-lg"></i>
                            </div>

                            <div>
                                <div class="text-muted small">SMS সীমা</div>
                                <h4 class="mb-0">
                                    @if($subscription->sms_limit !== null)
                                        {{ $subscription->sms_limit == 0
                                            ? 'সীমাহীন'
                                            : number_format($subscription->sms_limit) }}
                                    @else
                                        নির্ধারিত নয়
                                    @endif
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Plan Features --}}
        @if($subscription->plan && $subscription->plan->features)
            @php
                $features = $subscription->plan->features;

                if (is_string($features)) {
                    $features = json_decode($features, true) ?? [];
                }
            @endphp

            @if(is_array($features) && count($features))
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">প্যাকেজের সুবিধাসমূহ</h5>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($features as $feature)
                                <div class="col-12 col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                        <span>{{ is_string($feature) ? $feature : '' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif

    @else

        {{-- No Subscription --}}
        <div class="card border-0 shadow-sm text-center mb-4">
            <div class="card-body py-5 px-3">
                <div class="mb-3 text-primary">
                    <i class="fas fa-box-open fa-3x"></i>
                </div>

                <h4>আপনার কোনো সাবস্ক্রিপশন নেই</h4>

                <p class="text-muted mb-4">
                    আপনার প্রতিষ্ঠানের জন্য উপযুক্ত প্যাকেজ নির্বাচন করে সাবস্ক্রিপশন শুরু করুন।
                </p>

                <a href="{{ route('dashboard.subscription.plans') }}"
                   class="btn btn-primary">
                    <i class="fas fa-th-large me-1"></i>
                    প্যাকেজ দেখুন
                </a>
            </div>
        </div>

    @endif

    {{-- Actions --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('dashboard.subscription.plans') }}"
           class="btn btn-outline-primary">
            <i class="fas fa-box me-1"></i>
            প্যাকেজ পরিবর্তন / নবায়ন
        </a>

        @if(\Illuminate\Support\Facades\Route::has('dashboard.subscription.payments.index'))
            <a href="{{ route('dashboard.subscription.payments.index') }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-receipt me-1"></i>
                পেমেন্টের তথ্য
            </a>
        @endif

        @if(\Illuminate\Support\Facades\Route::has('dashboard.subscription.transactions.index'))
            <a href="{{ route('dashboard.subscription.transactions.index') }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-history me-1"></i>
                লেনদেনের ইতিহাস
            </a>
        @endif
    </div>

</div>
@endsection