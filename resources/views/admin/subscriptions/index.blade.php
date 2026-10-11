@extends('layouts.admin')

@section('title', 'Subscription Management')

@section('page')

@php
    $statusStyles = [
        'active' => 'success',
        'trialing' => 'info',
        'pending_payment' => 'warning',
        'expired' => 'danger',
        'cancelled' => 'secondary',
        'rejected' => 'danger',
    ];

    $hasShowRoute = \Illuminate\Support\Facades\Route::has(
        'dashboard.subscription.subscriptions.show'
    );

    $hasEditRoute = \Illuminate\Support\Facades\Route::has(
        'dashboard.subscription.subscriptions.edit'
    );

    $hasManageRoute = \Illuminate\Support\Facades\Route::has(
        'dashboard.subscription.subscriptions.manage'
    );

    $hasDeleteRoute = \Illuminate\Support\Facades\Route::has(
        'dashboard.subscription.subscriptions.destroy'
    );
@endphp

<div class="container-fluid py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Subscription Management</h3>
            <p class="text-muted mb-0">
                Manage institution subscriptions, packages and payments.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">

            <a href="{{ route('dashboard.subscription.manage-plans.index') }}"
               class="btn btn-outline-primary">
                <i class="fas fa-box-open me-1"></i>
                Manage Plans
            </a>

            <a href="{{ route('dashboard.subscription.manage-plans.create') }}"
               class="btn btn-success">
                <i class="fas fa-plus me-1"></i>
                Create Package
            </a>

            <a href="{{ route('dashboard.subscription.payments.index') }}"
               class="btn btn-primary">
                <i class="fas fa-credit-card me-1"></i>
                Verify Payments

                @if($pendingPayments > 0)
                    <span class="badge bg-danger ms-1">
                        {{ $pendingPayments }}
                    </span>
                @endif
            </a>

        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please check the following errors:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Statistics --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Subscriptions</div>
                    <h3 class="fw-bold mb-0 mt-2">
                        {{ number_format($stats['total']) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Active</div>
                    <h3 class="fw-bold text-success mb-0 mt-2">
                        {{ number_format($stats['active']) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Free Trials</div>
                    <h3 class="fw-bold text-info mb-0 mt-2">
                        {{ number_format($stats['trialing']) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Awaiting Payment</div>
                    <h3 class="fw-bold text-warning mb-0 mt-2">
                        {{ number_format($stats['pending_payment']) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Expired</div>
                    <h3 class="fw-bold text-danger mb-0 mt-2">
                        {{ number_format($stats['expired']) }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Quick Actions --}}
    <div class="row g-3 mb-4">

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <div class="rounded bg-primary bg-opacity-10 p-3">
                        <i class="fas fa-credit-card fa-lg text-primary"></i>
                    </div>

                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1">Payment Verification</h6>
                        <p class="text-muted small mb-0">
                            Review institution payment submissions.
                        </p>
                    </div>

                    <a href="{{ route('dashboard.subscription.payments.index') }}"
                       class="btn btn-outline-primary btn-sm">
                        View
                    </a>

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <div class="rounded bg-success bg-opacity-10 p-3">
                        <i class="fas fa-box-open fa-lg text-success"></i>
                    </div>

                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1">Package Management</h6>
                        <p class="text-muted small mb-0">
                            {{ $plans->count() }} active package(s) available.
                        </p>
                    </div>

                    <a href="{{ route('dashboard.subscription.manage-plans.index') }}"
                       class="btn btn-outline-success btn-sm">
                        Manage
                    </a>

                </div>
            </div>
        </div>

    </div>

    {{-- Available Subscription Plans --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-1">Available Subscription Plans</h5>
                <small class="text-muted">
                    Active packages offered to institutions.
                </small>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('dashboard.subscription.manage-plans.index') }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit me-1"></i>
                    Edit / Manage Plans
                </a>

                <a href="{{ route('dashboard.subscription.manage-plans.create') }}"
                   class="btn btn-sm btn-success">
                    <i class="fas fa-plus me-1"></i>
                    Create Package
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-3">

                @forelse($plans as $plan)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="border rounded p-3 h-100">

                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <h6 class="fw-bold mb-2">
                                    {{ $plan->name }}
                                </h6>

                                <span class="badge bg-success">Active</span>
                            </div>

                            @if($plan->description)
                                <p class="small text-muted">
                                    {{ $plan->description }}
                                </p>
                            @endif

                            <div class="mb-3">
                                <span class="fs-4 fw-bold">
                                    ৳{{ number_format((float) $plan->price_monthly, 0) }}
                                </span>
                                <span class="text-muted small">/ month</span>
                            </div>

                            <div class="small text-muted mb-2">
                                <i class="fas fa-user-graduate me-1"></i>
                                Student limit:
                                {{ number_format((int) $plan->student_limit) }}
                            </div>

                            <div class="small text-muted mb-2">
                                <i class="fas fa-comment-alt me-1"></i>
                                SMS limit:
                                {{ number_format((int) $plan->sms_limit) }}
                            </div>

                            <div class="small text-muted mb-2">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Trial:
                                {{ (int) $plan->trial_days }} days
                            </div>

                            @if($plan->price_yearly !== null)
                                <hr>

                                <div class="small">
                                    Yearly:
                                    <strong>
                                        ৳{{ number_format((float) $plan->price_yearly, 0) }}
                                    </strong>

                                    @if((float) $plan->annual_discount > 0)
                                        <span class="text-success">
                                            ({{ $plan->annual_discount }}% discount)
                                        </span>
                                    @endif
                                </div>
                            @endif

                            @if(is_array($plan->features) && count($plan->features))
                                <hr>

                                <ul class="small text-muted ps-3 mb-0">
                                    @foreach($plan->features as $feature)
                                        <li class="mb-1">{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            @endif

                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4">
                        <i class="fas fa-box-open fa-2x text-muted mb-3"></i>

                        <p class="text-muted">
                            No active subscription plans found.
                        </p>

                        <a href="{{ route('dashboard.subscription.manage-plans.create') }}"
                           class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Create First Package
                        </a>
                    </div>
                @endforelse

            </div>
        </div>
    </div>

    {{-- Institution Subscriptions --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-1">Institution Subscriptions</h5>
                <small class="text-muted">
                    View and manage institution subscription records.
                </small>
            </div>

            <span class="badge bg-secondary">
                {{ $subscriptions->total() }} records
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Institution</th>
                            <th>Plan</th>
                            <th>Billing</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>Expiry Date</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($subscriptions as $subscription)

                            @php
                                $status = strtolower(
                                    $subscription->status ?? 'unknown'
                                );

                                $badgeClass = $statusStyles[$status] ?? 'secondary';

                                $institution = $subscription->institution;

                                $institutionName = $institution->name
                                    ?? $institution->institution_name
                                    ?? $institution->madrasa_name
                                    ?? null;
                            @endphp

                            <tr>

                                {{-- Institution --}}
                                <td class="ps-3">
                                    <div class="fw-semibold">
                                        {{ $institutionName ?: 'Institution #' . $subscription->institution_id }}
                                    </div>

                                    <div class="text-muted small">
                                        Institution ID:
                                        {{ $subscription->institution_id }}
                                    </div>

                                    <div class="text-muted small">
                                        Subscription ID:
                                        {{ $subscription->id }}
                                    </div>
                                </td>

                                {{-- Plan --}}
                                <td>
                                    <span class="fw-semibold">
                                        {{ $subscription->plan->name ?? 'Plan unavailable' }}
                                    </span>
                                </td>

                                {{-- Billing --}}
                                <td>
                                    {{ ucfirst($subscription->billing_cycle ?? 'monthly') }}
                                </td>

                                {{-- Amount --}}
                                <td class="text-nowrap">
                                    {{ $subscription->currency ?? 'BDT' }}
                                    {{ number_format((float) $subscription->amount, 2) }}
                                </td>

                                {{-- Status --}}
                                <td>
                                    <span class="badge bg-{{ $badgeClass }}">
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                    </span>
                                </td>

                                {{-- Start Date --}}
                                <td class="text-nowrap">
                                    {{ $subscription->starts_at
                                        ? $subscription->starts_at->format('d M Y')
                                        : '—' }}
                                </td>

                                {{-- Expiry Date --}}
                                <td class="text-nowrap">
                                    {{ $subscription->ends_at
                                        ? $subscription->ends_at->format('d M Y')
                                        : '—' }}
                                </td>


{{-- Actions --}}
<td class="text-end">
    <div class="d-flex justify-content-end gap-2">

        {{-- View --}}
        <a href="{{ route('dashboard.subscription.show', $subscription) }}"
           class="btn btn-sm btn-outline-info"
           title="View Subscription">
            <i class="fas fa-eye"></i>
        </a>

        {{-- Edit --}}
        <a href="{{ route('dashboard.subscription.edit', $subscription) }}"
           class="btn btn-sm btn-outline-primary"
           title="Edit Subscription">
            <i class="fas fa-edit"></i>
        </a>

        {{-- Manage --}}
        <a href="{{ route('dashboard.subscription.manage', $subscription) }}"
           class="btn btn-sm btn-outline-warning"
           title="Manage Subscription">
            <i class="fas fa-cog"></i>
        </a>

        {{-- Delete --}}
        <form action="{{ route('dashboard.subscription.destroy', $subscription) }}"
              method="POST"
              onsubmit="return confirm('Are you sure you want to delete this subscription?');">
            @csrf
            @method('DELETE')

            <button type="submit"
                    class="btn btn-sm btn-outline-danger"
                    title="Delete Subscription">
                <i class="fas fa-trash"></i>
            </button>
        </form>

    </div>
</td>



                            </tr>

                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-2x text-muted mb-3"></i>

                                    <p class="text-muted mb-0">
                                        No subscription records found.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($subscriptions->hasPages())
            <div class="card-footer bg-white">
                {{ $subscriptions->links() }}
            </div>
        @endif

    </div>

</div>

@endsection
