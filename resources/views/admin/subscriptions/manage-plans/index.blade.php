
@extends('layouts.admin')

@section('title', 'Manage Subscription Plans')

@section('page')
<div class="container-fluid py-3">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Manage Subscription Plans</h3>
            <p class="text-muted mb-0">
                Create, edit and control subscription packages.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('dashboard.subscription.index') }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>

            <a href="{{ route('dashboard.subscription.manage-plans.create') }}"
               class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Create Package
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">All Packages</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Package</th>
                            <th>Monthly</th>
                            <th>Yearly</th>
                            <th>Students</th>
                            <th>SMS</th>
                            <th>Trial</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($plans as $plan)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-semibold">{{ $plan->name }}</div>
                                    <div class="small text-muted">
                                        {{ $plan->slug }}
                                    </div>
                                </td>

                                <td class="text-nowrap">
                                    ৳{{ number_format((float) $plan->price_monthly, 2) }}
                                </td>

                                <td class="text-nowrap">
                                    ৳{{ number_format((float) $plan->price_yearly, 2) }}
                                    <div class="small text-success">
                                        {{ $plan->annual_discount }}% discount
                                    </div>
                                </td>

                                <td>{{ number_format($plan->student_limit) }}</td>
                                <td>{{ number_format($plan->sms_limit) }}</td>
                                <td>{{ $plan->trial_days }} days</td>

                                <td>
                                    @if($plan->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>

                                <td class="text-end pe-3 text-nowrap">
                                    <a href="{{ route('dashboard.subscription.manage-plans.edit', $plan) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route('dashboard.subscription.manage-plans.toggle-status', $plan) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to change this package status?')">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="btn btn-sm {{ $plan->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                            {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    No subscription packages found.
                                    <div class="mt-2">
                                        <a href="{{ route('dashboard.subscription.manage-plans.create') }}">
                                            Create your first package
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($plans->hasPages())
            <div class="card-footer bg-white">
                {{ $plans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection