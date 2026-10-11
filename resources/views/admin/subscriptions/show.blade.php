
@extends('layouts.admin')

@section('title', 'Subscription Details')

@section('page')
@php
    $status = strtolower($subscription->status ?? 'unknown');

    $statusStyles = [
        'active' => 'success',
        'trialing' => 'info',
        'pending_payment' => 'warning',
        'expired' => 'danger',
        'cancelled' => 'secondary',
        'rejected' => 'danger',
    ];

    $badgeClass = $statusStyles[$status] ?? 'secondary';

    $institution = $subscription->institution;

    $institutionName = $institution->name
        ?? $institution->institution_name
        ?? $institution->madrasa_name
        ?? 'Institution #' . $subscription->institution_id;
@endphp

<div class="container-fluid py-3">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Subscription Details</h3>
            <p class="text-muted mb-0">
                View subscription information, payment history and status.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard.subscription.index') }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>

            <a href="{{ route('dashboard.subscription.edit', $subscription->id) }}"
               class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Edit
            </a>

            <a href="{{ route('dashboard.subscription.manage', $subscription->id) }}"
               class="btn btn-warning">
                <i class="fas fa-cog me-1"></i> Manage
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Subscription ID</div>
                    <h4 class="fw-bold mb-0">#{{ $subscription->id }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Subscription Status</div>
                    <h5 class="mb-0">
                        <span class="badge bg-{{ $badgeClass }}">
                            {{ ucwords(str_replace('_', ' ', $status)) }}
                        </span>
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Subscription Amount</div>
                    <h4 class="fw-bold mb-0">
                        {{ $subscription->currency ?? 'BDT' }}
                        {{ number_format((float) $subscription->amount, 2) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Subscription Information --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Institution Information</h5>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">Institution Name</div>
                        <div class="fw-semibold">{{ $institutionName }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">Institution ID</div>
                        <div>{{ $subscription->institution_id ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="text-muted small">Subscription ID</div>
                        <div>#{{ $subscription->id }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Plan Information</h5>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">Package</div>
                        <div class="fw-semibold">
                            {{ $subscription->plan->name ?? 'Plan unavailable' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">Billing Cycle</div>
                        <div>{{ ucfirst($subscription->billing_cycle ?? 'monthly') }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">Student Limit</div>
                        <div>
                            {{ number_format((int) ($subscription->student_limit ?? 0)) }}
                        </div>
                    </div>

                    <div>
                        <div class="text-muted small">SMS Limit</div>
                        <div>
                            {{ number_format((int) ($subscription->sms_limit ?? 0)) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Subscription Timeline</h5>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-muted small">Start Date</div>
                            <div class="fw-semibold">
                                {{ $subscription->starts_at?->format('d M Y, h:i A') ?? 'Not started' }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="text-muted small">Expiry Date</div>
                            <div class="fw-semibold">
                                {{ $subscription->ends_at?->format('d M Y, h:i A') ?? 'Not set' }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="text-muted small">Trial Ends</div>
                            <div class="fw-semibold">
                                {{ $subscription->trial_ends_at?->format('d M Y, h:i A') ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="text-muted small">Cancelled At</div>
                            <div class="fw-semibold">
                                {{ $subscription->cancelled_at?->format('d M Y, h:i A') ?? '—' }}
                            </div>
                        </div>
                    </div>

                    @if($subscription->notes)
                        <hr>
                        <div class="text-muted small mb-1">Notes</div>
                        <div class="text-break">{{ $subscription->notes }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Payment History --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Payment History</h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Payment ID</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Transaction ID</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($subscription->payments as $payment)
                                    @php
                                        $paymentStatus = strtolower($payment->status ?? 'unknown');

                                        $paymentBadge = [
                                            'approved' => 'success',
                                            'pending' => 'warning',
                                            'rejected' => 'danger',
                                        ][$paymentStatus] ?? 'secondary';
                                    @endphp

                                    <tr>
                                        <td class="ps-3">#{{ $payment->id }}</td>

                                        <td class="text-nowrap">
                                            {{ $payment->currency ?? $subscription->currency ?? 'BDT' }}
                                            {{ number_format((float) ($payment->amount ?? 0), 2) }}
                                        </td>

                                        <td>{{ $payment->payment_method ?? '—' }}</td>

                                        <td>{{ $payment->transaction_id ?? '—' }}</td>

                                        <td>
                                            <span class="badge bg-{{ $paymentBadge }}">
                                                {{ ucfirst($paymentStatus) }}
                                            </span>
                                        </td>

                                        <td class="text-nowrap">
                                            {{ $payment->created_at?->format('d M Y, h:i A') ?? '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            No payment records found for this subscription.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
