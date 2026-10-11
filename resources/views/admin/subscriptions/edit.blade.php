
@extends('layouts.admin')

@section('title', 'Edit Subscription')

@section('page')
@php
    $currentStatus = old('status', $subscription->status);
    $currentPlan = old('subscription_plan_id', $subscription->subscription_plan_id);
    $currentCycle = old('billing_cycle', $subscription->billing_cycle ?? 'monthly');
@endphp

<div class="container-fluid py-3">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Edit Subscription</h3>
            <p class="text-muted mb-0">
                Update package, billing cycle, status and subscription dates.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('dashboard.subscription.show', $subscription->id) }}"
               class="btn btn-outline-primary">
                <i class="fas fa-eye me-1"></i> View Details
            </a>

            <a href="{{ route('dashboard.subscription.index') }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.subscription.update', $subscription->id) }}"
          method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- Main Form --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Subscription Settings</h5>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Plan --}}
                            <div class="col-md-6">
                                <label for="subscription_plan_id" class="form-label fw-semibold">
                                    Subscription Plan <span class="text-danger">*</span>
                                </label>

                                <select name="subscription_plan_id"
                                        id="subscription_plan_id"
                                        class="form-select @error('subscription_plan_id') is-invalid @enderror"
                                        required>
                                    <option value="">Select a plan</option>

                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}"
                                            @selected((string) $currentPlan === (string) $plan->id)>
                                            {{ $plan->name }}
                                            — ৳{{ number_format((float) $plan->price_monthly, 2) }}/month
                                            @if(!$plan->is_active)
                                                (Inactive)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                @error('subscription_plan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Billing Cycle --}}
                            <div class="col-md-6">
                                <label for="billing_cycle" class="form-label fw-semibold">
                                    Billing Cycle <span class="text-danger">*</span>
                                </label>

                                <select name="billing_cycle"
                                        id="billing_cycle"
                                        class="form-select @error('billing_cycle') is-invalid @enderror"
                                        required>
                                    <option value="monthly" @selected($currentCycle === 'monthly')>
                                        Monthly
                                    </option>
                                    <option value="yearly" @selected($currentCycle === 'yearly')>
                                        Yearly
                                    </option>
                                </select>

                                @error('billing_cycle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold">
                                    Subscription Status <span class="text-danger">*</span>
                                </label>

                                <select name="status"
                                        id="status"
                                        class="form-select @error('status') is-invalid @enderror"
                                        required>
                                    @foreach([
                                        'trialing' => 'Trialing',
                                        'active' => 'Active',
                                        'pending_payment' => 'Pending Payment',
                                        'expired' => 'Expired',
                                        'cancelled' => 'Cancelled',
                                        'rejected' => 'Rejected',
                                    ] as $value => $label)
                                        <option value="{{ $value }}"
                                            @selected($currentStatus === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <small class="text-muted">
                                    Activating a paid subscription requires an approved payment.
                                </small>
                            </div>

                            {{-- Start Date --}}
                            <div class="col-md-6">
                                <label for="starts_at" class="form-label fw-semibold">
                                    Start Date
                                </label>

                                <input type="datetime-local"
                                       name="starts_at"
                                       id="starts_at"
                                       class="form-control @error('starts_at') is-invalid @enderror"
                                       value="{{ old('starts_at', $subscription->starts_at?->format('Y-m-d\TH:i')) }}">

                                @error('starts_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Expiry Date --}}
                            <div class="col-md-6">
                                <label for="ends_at" class="form-label fw-semibold">
                                    Expiry Date
                                </label>

                                <input type="datetime-local"
                                       name="ends_at"
                                       id="ends_at"
                                       class="form-control @error('ends_at') is-invalid @enderror"
                                       value="{{ old('ends_at', $subscription->ends_at?->format('Y-m-d\TH:i')) }}">

                                @error('ends_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Notes --}}
                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">
                                    Admin Notes
                                </label>

                                <textarea name="notes"
                                          id="notes"
                                          rows="4"
                                          class="form-control @error('notes') is-invalid @enderror"
                                          placeholder="Enter internal notes about this subscription...">{{ old('notes', $subscription->notes) }}</textarea>

                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white d-flex justify-content-end gap-2 py-3">
                        <a href="{{ route('dashboard.subscription.show', $subscription->id) }}"
                           class="btn btn-light border">
                            Cancel
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">Institution</h6>
                    </div>

                    <div class="card-body">
                        <div class="text-muted small">Institution Name</div>
                        <div class="fw-semibold mb-3">
                            {{ $subscription->institution->name
                                ?? $subscription->institution->institution_name
                                ?? $subscription->institution->madrasa_name
                                ?? 'Institution #' . $subscription->institution_id }}
                        </div>

                        <div class="text-muted small">Institution ID</div>
                        <div class="mb-3">{{ $subscription->institution_id }}</div>

                        <div class="text-muted small">Subscription ID</div>
                        <div>#{{ $subscription->id }}</div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">Current Limits</h6>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Students</span>
                            <strong>{{ number_format((int) $subscription->student_limit) }}</strong>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span class="text-muted">SMS</span>
                            <strong>{{ number_format((int) $subscription->sms_limit) }}</strong>
                        </div>

                        <hr>

                        <p class="small text-muted mb-0">
                            Student and SMS limits are updated automatically from the selected plan when you save.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
