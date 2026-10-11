@extends('layouts.admin')

@section('title', 'Subscription Payment Verification')

@section('page')

<div class="container-fluid py-3">


{{-- Page Header --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Subscription Payments</h4>
        <p class="text-muted mb-0">
            Review and verify institution subscription payments.
        </p>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('dashboard.index') }}"
           class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Dashboard
        </a>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Please check the following:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-warning-subtle text-warning p-3">
                    <i class="fas fa-clock fa-lg"></i>
                </div>
                <div>
                    <div class="text-muted small">Pending on this page</div>
                    <h4 class="fw-bold mb-0">{{ $payments->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary-subtle text-primary p-3">
                    <i class="fas fa-receipt fa-lg"></i>
                </div>
                <div>
                    <div class="text-muted small">Total pending records</div>
                    <h4 class="fw-bold mb-0">{{ $payments->total() }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Payments Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="fw-bold mb-0">Payment Verification Queue</h5>
            <span class="badge bg-warning-subtle text-warning-emphasis">
                {{ $payments->total() }} pending
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Institution</th>
                        <th>Plan</th>
                        <th>Payment Details</th>
                        <th>Amount</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold">
                                {{ $payment->institution->name
                                    ?? $payment->institution->institution_name
                                    ?? 'Institution #'.$payment->institution_id }}
                            </div>
                            <small class="text-muted">
                                ID: {{ $payment->institution_id }}
                            </small>
                        </td>

                        <td>
                            <div class="fw-semibold">
                                {{ $payment->subscription->plan->name ?? 'N/A' }}
                            </div>
                            <small class="text-muted">
                                {{ ucfirst($payment->subscription->billing_cycle ?? 'N/A') }}
                            </small>
                        </td>

                        <td>
                            <div>
                                <span class="text-muted">Method:</span>
                                {{ $payment->payment_method }}
                            </div>
                            <div>
                                <span class="text-muted">Sender:</span>
                                {{ $payment->sender_number ?: '—' }}
                            </div>
                            <div class="small">
                                <span class="text-muted">Transaction:</span>
                                <span class="fw-semibold">
                                    {{ $payment->transaction_id }}
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="fw-bold">
                                {{ $payment->currency }}
                                {{ number_format((float) $payment->amount, 2) }}
                            </span>
                        </td>

                        <td>
                            {{ $payment->submitted_at
                                ? $payment->submitted_at->format('d M Y, h:i A')
                                : '—' }}
                        </td>

                        <td>
                            <span class="badge bg-warning-subtle text-warning-emphasis">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>

                        <td class="text-end pe-3">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button"
                                        class="btn btn-sm btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#approvePayment{{ $payment->id }}">
                                    <i class="fas fa-check me-1"></i> Approve
                                </button>

                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejectPayment{{ $payment->id }}">
                                    <i class="fas fa-times me-1"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Approve Confirmation Modal --}}
                    <div class="modal fade"
                         id="approvePayment{{ $payment->id }}"
                         tabindex="-1"
                         aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Approve Payment</h5>
                                    <button type="button" class="btn-close"
                                            data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <p>
                                        Confirm that you have verified this payment
                                        and the transaction details are correct.
                                    </p>
                                    <div class="rounded-3 bg-light p-3">
                                        <div>
                                            <strong>Transaction:</strong>
                                            {{ $payment->transaction_id }}
                                        </div>
                                        <div>
                                            <strong>Amount:</strong>
                                            {{ $payment->currency }}
                                            {{ number_format((float) $payment->amount, 2) }}
                                        </div>
                                    </div>
                                    <p class="text-muted small mt-3 mb-0">
                                        Approval will activate the subscription according
                                        to the existing verification logic.
                                    </p>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light"
                                            data-bs-dismiss="modal">Cancel</button>

                                    <form method="POST"
                                          action="{{ route('dashboard.payments.approve', $payment->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            Confirm Approval
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reject Modal --}}
                    <div class="modal fade"
                         id="rejectPayment{{ $payment->id }}"
                         tabindex="-1"
                         aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <form method="POST"
                                      action="{{ route('dashboard.payments.reject', $payment->id) }}">
                                    @csrf

                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold text-danger">
                                            Reject Payment
                                        </h5>
                                        <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <p class="text-muted">
                                            Explain why this payment cannot be approved.
                                        </p>

                                        <label class="form-label fw-semibold">
                                            Rejection reason
                                        </label>
                                        <textarea name="rejection_reason"
                                                  class="form-control"
                                                  rows="4"
                                                  minlength="5"
                                                  maxlength="2000"
                                                  required
                                                  placeholder="Enter the reason for rejection..."></textarea>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">
                                            Confirm Rejection
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="mb-3 text-muted">
                                <i class="fas fa-inbox fa-3x"></i>
                            </div>
                            <h6 class="fw-bold">No pending payments</h6>
                            <p class="text-muted mb-0">
                                New institution payment submissions will appear here.
                            </p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($payments->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $payments->links() }}
        </div>
    @endif
</div>


</div>
@endsection
