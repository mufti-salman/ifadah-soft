@extends('layouts.admin')

@section('title', 'পেমেন্ট পদ্ধতি')

@section('page')

@php
    $totalMethods = $paymentMethods->count();

    $cashCount = $paymentMethods->where('account_type', 'cash')->count();

    $bankCount = $paymentMethods->where('account_type', 'bank')->count();

    $mobileCount = $paymentMethods->whereIn('account_type', [
        'bkash', 'nagad', 'rocket'
    ])->count();

    $accountIcons = [
        'cash'   => 'fa-money-bill-wave',
        'bank'   => 'fa-building-columns',
        'bkash'  => 'fa-mobile-screen-button',
        'nagad'  => 'fa-mobile-screen-button',
        'rocket' => 'fa-mobile-screen-button',
        'other'  => 'fa-credit-card',
    ];

    $accountLabels = [
        'cash'   => 'নগদ টাকা',
        'bank'   => 'ব্যাংক হিসাব',
        'bkash'  => 'বিকাশ',
        'nagad'  => 'নগদ',
        'rocket' => 'রকেট',
        'other'  => 'অন্যান্য',
    ];
@endphp

<style>
    .pm-page {
        --pm-primary: #4f46e5;
        --pm-primary-dark: #4338ca;
        --pm-text: #172033;
        --pm-muted: #718096;
        --pm-border: #e8edf4;

        color: var(--pm-text);
        padding-bottom: 24px;
    }

    .pm-page * {
        box-sizing: border-box;
    }

    .pm-page .pm-heading {
        font-size: 1.55rem;
        font-weight: 750;
        letter-spacing: -.4px;
        color: var(--pm-text);
    }

    .pm-page .pm-subtitle {
        font-size: .9rem;
        color: var(--pm-muted);
        line-height: 1.7;
    }

    .pm-page .pm-primary-btn {
        border: 0;
        background: var(--pm-primary);
        color: #fff;
        border-radius: 10px;
        padding: 11px 17px;
        font-size: .875rem;
        font-weight: 600;
        box-shadow: 0 5px 12px rgba(79, 70, 229, .15);
        transition: .2s ease;
        white-space: nowrap;
    }

    .pm-page .pm-primary-btn:hover {
        background: var(--pm-primary-dark);
        color: #fff;
        transform: translateY(-1px);
    }

    .pm-page .pm-primary-btn:disabled {
        opacity: .65;
        cursor: not-allowed;
        transform: none;
    }

    .pm-page .pm-card {
        border: 1px solid var(--pm-border);
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(15, 23, 42, .025);
    }

    .pm-page .pm-stat {
        padding: 20px;
        height: 100%;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .pm-page .pm-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 9px 25px rgba(15, 23, 42, .055);
    }

    .pm-page .pm-stat-icon {
        width: 47px;
        height: 47px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .pm-page .pm-stat-label {
        color: var(--pm-muted);
        font-size: .79rem;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .pm-page .pm-stat-number {
        font-size: 1.65rem;
        line-height: 1.2;
        font-weight: 750;
        letter-spacing: -.5px;
        margin: 0;
    }

    .pm-page .pm-purple {
        background: #eeedff;
        color: #5b50dc;
    }

    .pm-page .pm-green {
        background: #e4f8ee;
        color: #169b62;
    }

    .pm-page .pm-blue {
        background: #e7f2ff;
        color: #2474d2;
    }

    .pm-page .pm-orange {
        background: #fff0df;
        color: #d97706;
    }

    .pm-page .pm-table-card {
        overflow: hidden;
    }

    .pm-page .pm-table-header {
        padding: 20px 22px;
        border-bottom: 1px solid var(--pm-border);
    }

    .pm-page .pm-section-title {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        color: var(--pm-text);
    }

    .pm-page .pm-search {
        position: relative;
        width: 100%;
        max-width: 290px;
    }

    .pm-page .pm-search i {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: .85rem;
        pointer-events: none;
    }

    .pm-page .pm-search input {
        width: 100%;
        border: 1px solid var(--pm-border);
        border-radius: 9px;
        padding: 10px 12px 10px 36px;
        outline: none;
        font-size: .84rem;
        background: #fff;
        transition: border-color .2s, box-shadow .2s;
    }

    .pm-page .pm-search input:focus {
        border-color: #a5b4fc;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, .08);
    }

    .pm-page .table {
        margin-bottom: 0;
        min-width: 780px;
    }

    .pm-page .table thead th {
        background: #f8fafc;
        color: #64748b;
        border-bottom: 1px solid var(--pm-border);
        padding: 14px 17px;
        font-size: .72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .pm-page .table tbody td {
        padding: 15px 17px;
        border-bottom: 1px solid #f0f3f8;
        font-size: .84rem;
        vertical-align: middle;
    }

    .pm-page .table tbody tr:last-child td {
        border-bottom: 0;
    }

    .pm-page .table tbody tr {
        transition: background .15s ease;
    }

    .pm-page .table tbody tr:hover {
        background: #fafbff;
    }

    .pm-page .pm-account {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 175px;
    }

    .pm-page .pm-account-icon {
        width: 43px;
        height: 43px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1rem;
    }

    .pm-page .pm-account-name {
        font-weight: 650;
        color: #1e293b;
        margin-bottom: 3px;
        overflow-wrap: anywhere;
    }

    .pm-page .pm-account-id {
        color: #94a3b8;
        font-size: .73rem;
    }

    .pm-page .pm-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 7px;
        font-size: .75rem;
        font-weight: 650;
        white-space: nowrap;
    }

    .pm-page .pm-type-cash {
        background: #e7f8ee;
        color: #168653;
    }

    .pm-page .pm-type-bank {
        background: #eaf2ff;
        color: #2864bd;
    }

    .pm-page .pm-type-mobile {
        background: #fff0e5;
        color: #bd5d15;
    }

    .pm-page .pm-type-other {
        background: #f1efff;
        color: #6656c7;
    }

    .pm-page .pm-muted {
        color: #94a3b8;
    }

    .pm-page .pm-delete-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #fee2e2;
        background: #fff;
        color: #dc2626;
        border-radius: 8px;
        padding: 7px 10px;
        font-size: .78rem;
        font-weight: 600;
        transition: .2s;
        white-space: nowrap;
    }

    .pm-page .pm-delete-btn:hover {
        background: #fef2f2;
        border-color: #fecaca;
    }

    .pm-page .pm-delete-btn:disabled {
        opacity: .6;
        cursor: wait;
    }

    .pm-page .pm-footer {
        padding: 15px 22px;
        background: #fff;
        border-top: 1px solid var(--pm-border);
        color: var(--pm-muted);
        font-size: .79rem;
    }

    .pm-page .pm-empty {
        padding: 55px 20px;
        text-align: center;
    }

    .pm-page .pm-empty-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 18px;
        border-radius: 20px;
        background: #f0efff;
        color: #6559db;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.65rem;
    }

    .pm-page .pm-alert {
        border-radius: 10px;
        font-size: .86rem;
        line-height: 1.7;
    }

    /* Modal layout */
    .pm-page .modal-content {
        border: 0;
        border-radius: 17px;
        overflow: hidden;
        box-shadow: 0 22px 70px rgba(15, 23, 42, .15);
    }

    .pm-page .pm-modal-header {
        padding: 20px 25px;
        background: #fafaff;
        border-bottom: 1px solid var(--pm-border);
        flex-shrink: 0;
    }

    .pm-page .pm-modal-icon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #eeedff;
        color: #5b50dc;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .pm-page .pm-modal-body {
        padding: 24px 25px;
        overflow-y: auto;
        overscroll-behavior: contain;
    }

    .pm-page .pm-modal-footer {
        padding: 15px 25px;
        border-top: 1px solid var(--pm-border);
        background: #fff;
        flex-shrink: 0;
        position: sticky;
        bottom: 0;
        z-index: 5;
    }

    .pm-page .form-label {
        font-size: .82rem;
        font-weight: 650;
        color: #334155;
        margin-bottom: 8px;
    }

    .pm-page .form-control,
    .pm-page .form-select {
        border: 1px solid #dfe5ee;
        border-radius: 9px;
        min-height: 43px;
        font-size: .85rem;
        box-shadow: none;
    }

    .pm-page textarea.form-control {
        min-height: auto;
    }

    .pm-page .form-control:focus,
    .pm-page .form-select:focus {
        border-color: #a5b4fc;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, .08);
    }

    .pm-page .pm-mobile-fields {
        display: none;
    }

    .pm-page .pm-section-heading {
        font-size: .88rem;
        font-weight: 700;
        color: #263248;
    }

    .pm-page .pm-help {
        font-size: .75rem;
        color: #8490a2;
        margin-top: 6px;
        line-height: 1.6;
    }

    /* Responsive modal: content scrolls, footer remains visible */
    #createPaymentModal .modal-dialog {
        width: calc(100% - 24px);
        max-width: 760px;
        margin: 12px auto;
        height: calc(100% - 24px);
        display: flex;
        align-items: center;
    }

    #createPaymentModal .modal-content {
        width: 100%;
        max-height: calc(100vh - 24px);
        max-height: calc(100dvh - 24px);
        display: flex;
        flex-direction: column;
    }

    #createPaymentModal .pm-modal-body {
        min-height: 0;
        flex: 1 1 auto;
    }

    @media (max-width: 767px) {
        .pm-page .pm-heading {
            font-size: 1.3rem;
        }

        .pm-page .pm-stat {
            padding: 15px;
        }

        .pm-page .pm-stat-number {
            font-size: 1.4rem;
        }

        .pm-page .pm-table-header {
            padding: 17px;
        }

        .pm-page .pm-search {
            max-width: 100%;
        }

        .pm-page .pm-modal-header,
        .pm-page .pm-modal-body,
        .pm-page .pm-modal-footer {
            padding: 16px;
        }

        #createPaymentModal .modal-dialog {
            width: calc(100% - 16px);
            height: calc(100% - 16px);
            margin: 8px auto;
        }

        #createPaymentModal .modal-content {
            max-height: calc(100vh - 16px);
            max-height: calc(100dvh - 16px);
            border-radius: 13px;
        }

        .pm-page .pm-modal-footer {
            gap: 8px;
        }

        .pm-page .pm-modal-footer .btn {
            flex: 1;
        }
    }
</style>

<div class="pm-page container-fluid py-3">

    {{-- পেজ হেডার --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="pm-muted small">হিসাব ব্যবস্থাপনা</span>
                <i class="fas fa-chevron-right pm-muted" style="font-size:9px;"></i>
                <span class="small fw-semibold" style="color:#4f46e5;">পেমেন্ট পদ্ধতি</span>
            </div>

            <h3 class="pm-heading mb-1">পেমেন্ট পদ্ধতি</h3>

            <p class="pm-subtitle mb-0">
                নগদ টাকা, ব্যাংক ও মোবাইল ব্যাংকিং হিসাব সহজেই পরিচালনা করুন।
            </p>
        </div>

        @permission('payment.create')
        <button type="button" class="btn pm-primary-btn" id="openCreateModal">
            <i class="fas fa-plus me-2"></i>
            নতুন পেমেন্ট হিসাব
        </button>
        @endpermission
    </div>

    {{-- বার্তা --}}
    <div id="alertBox" class="alert pm-alert d-none" role="alert"></div>

    {{-- সারসংক্ষেপ --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-xl-3">
            <div class="pm-card pm-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="pm-stat-icon pm-purple">
                        <i class="fas fa-wallet"></i>
                    </div>

                    <div>
                        <div class="pm-stat-label">মোট হিসাব</div>
                        <h4 class="pm-stat-number">{{ $totalMethods }}</h4>
                    </div>
                </div>

                <div class="text-muted small mt-3">সকল পেমেন্ট হিসাব</div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="pm-card pm-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="pm-stat-icon pm-green">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>

                    <div>
                        <div class="pm-stat-label">নগদ হিসাব</div>
                        <h4 class="pm-stat-number">{{ $cashCount }}</h4>
                    </div>
                </div>

                <div class="text-muted small mt-3">হাতে থাকা নগদ টাকা</div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="pm-card pm-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="pm-stat-icon pm-blue">
                        <i class="fas fa-building-columns"></i>
                    </div>

                    <div>
                        <div class="pm-stat-label">ব্যাংক হিসাব</div>
                        <h4 class="pm-stat-number">{{ $bankCount }}</h4>
                    </div>
                </div>

                <div class="text-muted small mt-3">সকল ব্যাংক হিসাব</div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="pm-card pm-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="pm-stat-icon pm-orange">
                        <i class="fas fa-mobile-screen-button"></i>
                    </div>

                    <div>
                        <div class="pm-stat-label">মোবাইল ব্যাংকিং</div>
                        <h4 class="pm-stat-number">{{ $mobileCount }}</h4>
                    </div>
                </div>

                <div class="text-muted small mt-3">বিকাশ, নগদ ও রকেট</div>
            </div>
        </div>

    </div>

    {{-- পেমেন্ট হিসাবের তালিকা --}}
    <div class="pm-card pm-table-card">

        <div class="pm-table-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="pm-section-title mb-1">পেমেন্ট হিসাবের তালিকা</h5>
                <div class="text-muted small">আপনার সকল হিসাব দেখুন ও পরিচালনা করুন।</div>
            </div>

            <div class="pm-search">
                <i class="fas fa-search"></i>

                <input type="search"
                       id="tableSearch"
                       placeholder="হিসাব খুঁজুন..."
                       autocomplete="off"
                       aria-label="পেমেন্ট হিসাব খুঁজুন">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" id="paymentMethodsTable">

                <thead>
                    <tr>
                        <th style="width:60px;">ক্রমিক</th>
                        <th>হিসাবের নাম</th>
                        <th>হিসাবের ধরন</th>
                        <th>হিসাব নম্বর</th>
                        <th>মোবাইল নম্বর</th>
                        <th>তৈরির তারিখ</th>

                        @permission('payment.delete')
                        <th class="text-end">কার্যক্রম</th>
                        @endpermission
                    </tr>
                </thead>

                <tbody>
                    @forelse($paymentMethods as $method)
                        @php
                            $type = strtolower($method->account_type ?? 'other');

                            $icon = $accountIcons[$type] ?? 'fa-credit-card';
                            $label = $accountLabels[$type] ?? 'অন্যান্য';

                            if ($type === 'cash') {
                                $typeClass = 'pm-type-cash';
                                $iconClass = 'pm-green';
                            } elseif ($type === 'bank') {
                                $typeClass = 'pm-type-bank';
                                $iconClass = 'pm-blue';
                            } elseif (in_array($type, ['bkash', 'nagad', 'rocket'])) {
                                $typeClass = 'pm-type-mobile';
                                $iconClass = 'pm-orange';
                            } else {
                                $typeClass = 'pm-type-other';
                                $iconClass = 'pm-purple';
                            }
                        @endphp

                        <tr class="pm-data-row">
                            <td class="text-muted">{{ $loop->iteration }}</td>

                            <td>
                                <div class="pm-account">
                                    <div class="pm-account-icon {{ $iconClass }}">
                                        <i class="fas {{ $icon }}"></i>
                                    </div>

                                    <div>
                                        <div class="pm-account-name">
                                            {{ $method->name }}
                                        </div>

                                        <div class="pm-account-id">
                                            হিসাব নম্বর: #{{ $method->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="pm-type-badge {{ $typeClass }}">
                                    <i class="fas {{ $icon }}"></i>
                                    {{ $label }}
                                </span>
                            </td>

                            <td>
                                @if($method->account_number)
                                    <span class="fw-semibold text-dark">
                                        {{ $method->account_number }}
                                    </span>
                                @else
                                    <span class="pm-muted">উল্লেখ নেই</span>
                                @endif
                            </td>

                            <td>
                                @if($method->mobile1)
                                    <div class="fw-semibold">{{ $method->mobile1 }}</div>
                                @endif

                                @if($method->mobile2)
                                    <div class="text-muted small mt-1">{{ $method->mobile2 }}</div>
                                @endif

                                @if(!$method->mobile1 && !$method->mobile2)
                                    <span class="pm-muted">উল্লেখ নেই</span>
                                @endif
                            </td>

                            <td>
                                <div class="fw-semibold text-dark">
                                    {{ $method->created_at?->format('d M Y') ?? '—' }}
                                </div>

                                <div class="text-muted small mt-1">
                                    {{ $method->created_at?->format('h:i A') ?? '' }}
                                </div>
                            </td>

                            @permission('payment.delete')
                            <td class="text-end">
                                <button type="button"
                                        class="pm-delete-btn delete-payment-method"
                                        data-id="{{ $method->id }}"
                                        data-name="{{ $method->name }}">
                                    <i class="fas fa-trash-alt"></i>
                                    মুছুন
                                </button>
                            </td>
                            @endpermission
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="pm-empty">
                                    <div class="pm-empty-icon">
                                        <i class="fas fa-wallet"></i>
                                    </div>

                                    <h5 class="fw-bold mb-2">এখনো কোনো পেমেন্ট হিসাব নেই</h5>

                                    <p class="text-muted mb-3">
                                        শুরু করতে একটি নগদ, ব্যাংক অথবা মোবাইল ব্যাংকিং হিসাব যোগ করুন।
                                    </p>

                                    @permission('payment.create')
                                    <button type="button"
                                            class="btn pm-primary-btn"
                                            id="emptyCreateBtn">
                                        <i class="fas fa-plus me-2"></i>
                                        প্রথম হিসাব যোগ করুন
                                    </button>
                                    @endpermission
                                </div>
                            </td>
                        </tr>
                    @endforelse

                    <tr id="noSearchResults" style="display:none;">
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-search fa-2x text-muted mb-3"></i>

                            <h6 class="fw-bold">কোনো মিল পাওয়া যায়নি</h6>

                            <p class="text-muted small mb-0">
                                অন্য নাম অথবা হিসাব নম্বর দিয়ে খুঁজে দেখুন।
                            </p>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="pm-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>
                <i class="fas fa-database me-1"></i>
                মোট {{ $totalMethods }}টি পেমেন্ট হিসাব
            </span>

            <span>
                <i class="fas fa-shield-alt me-1"></i>
                পেমেন্ট হিসাব ব্যবস্থাপনা
            </span>
        </div>

    </div>

</div>

{{-- নতুন পেমেন্ট হিসাব যোগ করার Modal --}}
<div class="modal fade pm-page"
     id="createPaymentModal"
     tabindex="-1"
     aria-labelledby="createPaymentModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- Modal Header --}}
            <div class="pm-modal-header d-flex align-items-center justify-content-between gap-3">

                <div class="d-flex align-items-center gap-3">
                    <div class="pm-modal-icon">
                        <i class="fas fa-wallet"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold mb-1" id="createPaymentModalLabel">
                            নতুন পেমেন্ট হিসাব
                        </h5>

                        <p class="text-muted small mb-0">
                            প্রয়োজনীয় তথ্য দিয়ে হিসাব তৈরি করুন।
                        </p>
                    </div>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="বন্ধ করুন"></button>
            </div>

            <form id="paymentMethodForm" class="d-flex flex-column flex-grow-1" style="min-height:0;">
                @csrf

                {{-- Scrollable Body --}}
                <div class="pm-modal-body">

                    <div id="formErrors"
                         class="alert alert-danger pm-alert d-none"
                         role="alert"></div>

                    <div class="mb-4">
                        <div class="pm-section-heading mb-3">
                            <i class="fas fa-info-circle me-2 text-primary"></i>
                            সাধারণ তথ্য
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    হিসাবের নাম <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       class="form-control"
                                       id="name"
                                       name="name"
                                       placeholder="যেমন: প্রধান নগদ হিসাব"
                                       maxlength="255"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label for="account_type" class="form-label">
                                    হিসাবের ধরন <span class="text-danger">*</span>
                                </label>

                                <select class="form-select"
                                        id="account_type"
                                        name="account_type"
                                        required>
                                    <option value="">হিসাবের ধরন নির্বাচন করুন</option>
                                    <option value="cash">নগদ টাকা</option>
                                    <option value="bank">ব্যাংক হিসাব</option>
                                    <option value="bkash">বিকাশ</option>
                                    <option value="nagad">নগদ</option>
                                    <option value="rocket">রকেট</option>
                                    <option value="other">অন্যান্য</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="pm-section-heading mb-3">
                            <i class="fas fa-address-card me-2 text-primary"></i>
                            হিসাবের বিস্তারিত
                        </div>

                        <div class="row g-3">

                            <div class="col-12" id="accountNumberField">
                                <label for="account_number" class="form-label">
                                    হিসাব নম্বর
                                </label>

                                <input type="text"
                                       class="form-control"
                                       id="account_number"
                                       name="account_number"
                                       maxlength="255"
                                       placeholder="ব্যাংক বা মোবাইল হিসাব নম্বর লিখুন">

                                <div class="pm-help">
                                    এই তথ্য দেওয়া বাধ্যতামূলক নয়।
                                </div>
                            </div>

                            <div class="col-md-6 pm-mobile-fields">
                                <label for="mobile1" class="form-label">
                                    প্রধান মোবাইল নম্বর
                                </label>

                                <input type="text"
                                       class="form-control"
                                       id="mobile1"
                                       name="mobile1"
                                       maxlength="20"
                                       placeholder="০১XXXXXXXXX">
                            </div>

                            <div class="col-md-6 pm-mobile-fields">
                                <label for="mobile2" class="form-label">
                                    বিকল্প মোবাইল নম্বর
                                </label>

                                <input type="text"
                                       class="form-control"
                                       id="mobile2"
                                       name="mobile2"
                                       maxlength="20"
                                       placeholder="প্রয়োজনে লিখুন">
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">
                                    ঠিকানা / বিবরণ
                                </label>

                                <textarea class="form-control"
                                          id="address"
                                          name="address"
                                          rows="3"
                                          maxlength="1000"
                                          placeholder="ব্যাংকের শাখার ঠিকানা অথবা অতিরিক্ত তথ্য লিখুন"></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="alert alert-light border pm-alert mb-0">
                        <i class="fas fa-lightbulb text-warning me-2"></i>

                        <span class="small text-muted">
                            সংরক্ষণ করার আগে হিসাবের তথ্যগুলো সঠিকভাবে যাচাই করে নিন।
                        </span>
                    </div>

                </div>

                {{-- Fixed Footer: Save button always accessible --}}
                <div class="pm-modal-footer d-flex justify-content-end gap-2">

                    <button type="button"
                            class="btn btn-light border"
                            data-bs-dismiss="modal">
                        বাতিল
                    </button>

                    <button type="submit"
                            class="btn pm-primary-btn"
                            id="savePaymentMethodBtn">
                        <i class="fas fa-check me-2"></i>
                        সংরক্ষণ করুন
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl = @json(route('dashboard.payment-method.store'));
    const indexUrl = @json(route('dashboard.payment-method.index'));

    const destroyUrl = @json(
        route('dashboard.payment-method.destroy', ['id' => '__ID__'])
    );

    const csrfToken =
        document.querySelector('meta[name="csrf-token"]')?.content
        || @json(csrf_token());

    const modalElement = document.getElementById('createPaymentModal');

    const paymentModal = (window.bootstrap && modalElement)
        ? new bootstrap.Modal(modalElement)
        : null;

    const form = document.getElementById('paymentMethodForm');
    const saveButton = document.getElementById('savePaymentMethodBtn');
    const formErrors = document.getElementById('formErrors');
    const alertBox = document.getElementById('alertBox');

    const accountType = document.getElementById('account_type');
    const accountNumber = document.getElementById('account_number');
    const mobileFields = document.querySelectorAll('.pm-mobile-fields');

    function showAlert(message, type = 'success') {
        alertBox.className = 'alert alert-' + type + ' pm-alert';
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

        setTimeout(() => {
            alertBox.classList.add('d-none');
        }, 4500);
    }

    function updateAccountFields() {
        const type = accountType.value;
        const mobileTypes = ['bkash', 'nagad', 'rocket'];

        mobileFields.forEach(field => {
            const shouldShow = mobileTypes.includes(type);

            field.style.display = shouldShow ? 'block' : 'none';

            const input = field.querySelector('input');

            if (input) {
                input.disabled = !shouldShow;
            }
        });

        if (type === 'cash') {
            accountNumber.placeholder = 'নগদ হিসাবের রেফারেন্স (ঐচ্ছিক)';
        } else if (type === 'bank') {
            accountNumber.placeholder = 'ব্যাংক হিসাব নম্বর লিখুন';
        } else if (mobileTypes.includes(type)) {
            accountNumber.placeholder = 'মোবাইল ওয়ালেট নম্বর লিখুন';
        } else {
            accountNumber.placeholder = 'প্রযোজ্য হলে হিসাব নম্বর লিখুন';
        }
    }

    accountType.addEventListener('change', updateAccountFields);

    function openCreateModal() {
        if (!paymentModal) {
            showAlert(
                'ফর্ম খোলা যাচ্ছে না। অনুগ্রহ করে অ্যাডমিন লেআউটে Bootstrap JS পরীক্ষা করুন।',
                'danger'
            );
            return;
        }

        form.reset();

        formErrors.classList.add('d-none');
        formErrors.textContent = '';

        saveButton.disabled = false;
        saveButton.innerHTML =
            '<i class="fas fa-check me-2"></i> সংরক্ষণ করুন';

        updateAccountFields();

        paymentModal.show();
    }

    document.getElementById('openCreateModal')
        ?.addEventListener('click', openCreateModal);

    document.getElementById('emptyCreateBtn')
        ?.addEventListener('click', openCreateModal);

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        formErrors.classList.add('d-none');
        formErrors.textContent = '';

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        saveButton.disabled = true;
        saveButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span> সংরক্ষণ হচ্ছে...';

        try {
            const response = await fetch(storeUrl, {
                method: 'POST',

                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },

                body: new FormData(form)
            });

            const contentType = response.headers.get('content-type') || '';

            let result = {};

            if (contentType.includes('application/json')) {
                result = await response.json();
            }

            if (!response.ok || !result.success) {
                let message = result.message
                    || 'পেমেন্ট হিসাব সংরক্ষণ করা যায়নি।';

                if (result.errors) {
                    message = Object.values(result.errors)
                        .flat()
                        .join('\n');
                }

                formErrors.textContent = message;
                formErrors.style.whiteSpace = 'pre-line';
                formErrors.classList.remove('d-none');

                // Validation error দেখানোর জন্য Modal-এর ভেতরে স্ক্রল
                const modalBody = modalElement.querySelector('.pm-modal-body');

                if (modalBody) {
                    modalBody.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }

                return;
            }

            paymentModal?.hide();

            showAlert(
                result.message || 'পেমেন্ট হিসাব সফলভাবে সংরক্ষণ করা হয়েছে।'
            );

            setTimeout(() => {
                window.location.href = indexUrl;
            }, 600);

        } catch (error) {
            console.error(error);

            formErrors.textContent =
                'সার্ভারের সঙ্গে যোগাযোগ করা যাচ্ছে না। আবার চেষ্টা করুন।';

            formErrors.classList.remove('d-none');

        } finally {
            saveButton.disabled = false;

            saveButton.innerHTML =
                '<i class="fas fa-check me-2"></i> সংরক্ষণ করুন';
        }
    });

    // পেমেন্ট হিসাব মুছে ফেলা
    document.querySelectorAll('.delete-payment-method')
        .forEach(function (button) {

            button.addEventListener('click', async function () {

                const id = this.dataset.id;
                const name = this.dataset.name;

                if (!confirm(
                    'আপনি কি "' + name + '" হিসাবটি মুছে ফেলতে চান?'
                )) {
                    return;
                }

                this.disabled = true;

                try {
                    const deleteUrl = destroyUrl.replace(
                        '__ID__',
                        encodeURIComponent(id)
                    );

                    const response = await fetch(deleteUrl, {
                        method: 'DELETE',

                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    const contentType = response.headers.get('content-type') || '';
                    let result = {};

                    if (contentType.includes('application/json')) {
                        result = await response.json();
                    }

                    if (!response.ok || !result.success) {
                        showAlert(
                            result.message || 'হিসাবটি মুছে ফেলা যায়নি।',
                            'danger'
                        );

                        this.disabled = false;
                        return;
                    }

                    showAlert(
                        result.message || 'পেমেন্ট হিসাব সফলভাবে মুছে ফেলা হয়েছে।'
                    );

                    setTimeout(() => {
                        window.location.reload();
                    }, 400);

                } catch (error) {
                    console.error(error);

                    showAlert(
                        'হিসাব মুছতে সমস্যা হয়েছে। আবার চেষ্টা করুন।',
                        'danger'
                    );

                    this.disabled = false;
                }
            });
        });

    // তালিকা থেকে হিসাব খোঁজা
    const searchInput = document.getElementById('tableSearch');
    const tableRows = document.querySelectorAll('.pm-data-row');
    const noResults = document.getElementById('noSearchResults');

    searchInput?.addEventListener('input', function () {

        const query = this.value.toLowerCase().trim();

        let visibleCount = 0;

        tableRows.forEach(function (row) {
            const matches = row.textContent
                .toLowerCase()
                .includes(query);

            row.style.display = matches ? '' : 'none';

            if (matches) {
                visibleCount++;
            }
        });

        if (noResults) {
            noResults.style.display =
                query !== '' && visibleCount === 0 ? '' : 'none';
        }
    });

});
</script>
@endpush
