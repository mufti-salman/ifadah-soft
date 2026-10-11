
@extends('layouts.admin')

@section('title', 'Manage Subscription')

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

    $institutionName = $subscription->institution->name
        ?? $subscription->institution->institution_name
        ?? $subscription->institution->madrasa_name
        ?? 'Institution #' . $subscription->institution_id;
@endphp

<div class="container-fluid py-3">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Manage Subscription</h3>
            <p class="text-muted mb-0">
                Activate, extend or cancel an institution subscription.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard.subscription.show', $subscription->id) }}"
               class="btn btn-outline-primary">
                <i class="fas fa-eye me-1"></i> View Details
            </a>

            <a href="{{ route('dashboard.subscription.edit', $subscription->id) }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-edit me-1"></i> Edit
            </a>

            <a href="{{ route('dashboard.subscription.index') }}"
               class="btn btn-light border">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i>
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
            <strong>Please check the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Subscription Summary --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-4 align-items-center">

                <div class="col-md-4">
                    <div class="text-muted small mb-1">Institution</div>
                    <h5 class="fw-bold mb-1">{{ $institutionName }}</h5>
                    <div class="small text-muted">
                        Institution ID: {{ $subscription->institution_id }}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="text-muted small mb-1">Subscription Plan</div>
                    <h6 class="fw-semibold mb-1">
                        {{ $subscription->plan->name ?? 'Plan unavailable' }}
                    </h6>
                    <div class="small text-muted">
                        {{ ucfirst($subscription->billing_cycle ?? 'monthly') }} billing
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="text-muted small mb-1">Current Status</div>
                    <span class="badge bg-{{ $badgeClass }}">
                        {{ ucwords(str_replace('_', ' ', $status)) }}
                    </span>
                </div>

                <div class="col-md-3">
                    <div class="text-muted small mb-1">Expiry Date</div>
                    <div class="fw-semibold">
                        {{ $subscription->ends_at?->format('d M Y, h:i A') ?? 'Not set' }}
                    </div>
                    <div class="small text-muted">
                        Amount:
                        {{ $subscription->currency ?? 'BDT' }}
                        {{ number_format((float) $subscription->amount, 2) }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Activate --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Activate Subscription
                    </h5>
                </div>

                <div class="card-body">
                    <p class="text-muted small">
                        Activate this subscription. A paid subscription requires at least one approved payment.
                    </p>

                    <form action="{{ route('dashboard.subscription.manage.action', $subscription->id) }}"
                          method="POST"
                          onsubmit="return confirm('Are you sure you want to activate this subscription?');">
                        @csrf

                        <input type="hidden" name="action" value="activate">

                        <div class="mb-3">
                            <label for="activate_notes" class="form-label fw-semibold">
                                Admin Note (optional)
                            </label>

                            <textarea name="notes"
                                      id="activate_notes"
                                      rows="3"
                                      class="form-control"
                                      maxlength="2000"
                                      placeholder="Reason for activation..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-1"></i> Activate
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Extend --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-calendar-plus text-primary me-2"></i>
                        Extend Subscription
                    </h5>
                </div>

                <div class="card-body">
                    <p class="text-muted small">
                        Extend the expiry date of an active or trial subscription.
                    </p>

                    <form action="{{ route('dashboard.subscription.manage.action', $subscription->id) }}"
                          method="POST"
                          onsubmit="return confirm('Apply this subscription extension?');">
                        @csrf

                        <input type="hidden" name="action" value="extend">

                        <div class="mb-3">
                            <label for="extension_days" class="form-label fw-semibold">
                                Extension Days <span class="text-danger">*</span>
                            </label>

                            <input type="number"
                                   name="extension_days"
                                   id="extension_days"
                                   class="form-control @error('extension_days') is-invalid @enderror"
                                   value="{{ old('extension_days', 30) }}"
                                   min="1"
                                   max="3650"
                                   required>

                            @error('extension_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Enter a value between 1 and 3650 days.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="extend_notes" class="form-label fw-semibold">
                                Admin Note (optional)
                            </label>

                            <textarea name="notes"
                                      id="extend_notes"
                                      rows="3"
                                      class="form-control"
                                      maxlength="2000"
                                      placeholder="Reason for extension...">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-calendar-plus me-1"></i> Extend Subscription
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Cancel --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-ban text-danger me-2"></i>
                        Cancel Subscription
                    </h5>
                </div>

                <div class="card-body d-flex flex-column">
                    <p class="text-muted small">
                        Mark this subscription as cancelled. This action changes its status and records the cancellation time.
                    </p>

                    <div class="alert alert-warning small">
                        Cancelling a subscription changes its status. Confirm that this is the intended action before proceeding.
                    </div>

                    <form action="{{ route('dashboard.subscription.manage.action', $subscription->id) }}"
                          method="POST"
                          class="mt-auto"
                          onsubmit="return confirm('Are you sure you want to cancel this subscription?');">
                        @csrf

                        <input type="hidden" name="action" value="cancel">

                        <div class="mb-3">
                            <label for="cancel_notes" class="form-label fw-semibold">
                                Admin Note (optional)
                            </label>

                            <textarea name="notes"
                                      id="cancel_notes"
                                      rows="3"
                                      class="form-control"
                                      maxlength="2000"
                                      placeholder="Reason for cancellation..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-ban me-1"></i> Cancel Subscription
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- Dates and Limits --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Subscription Information</h5>
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
                    <div class="text-muted small">Student Limit</div>
                    <div class="fw-semibold">
                        {{ number_format((int) $subscription->student_limit) }}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="text-muted small">SMS Limit</div>
                    <div class="fw-semibold">
                        {{ number_format((int) $subscription->sms_limit) }}
                    </div>
                </div>

            </div>

            @if($subscription->notes)
                <hr>
                <div class="text-muted small mb-1">Existing Admin Notes</div>
                <div class="text-break">{{ $subscription->notes }}</div>
            @endif
        </div>
    </div>

</div>
@endsection
