@extends('layouts.admin')

@section('title', 'Transaction History')

@section('page')

<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="fas fa-exchange-alt me-2 text-primary"></i>
                Transaction History
            </h3>

            <p class="text-muted mb-0">
                Manage and monitor all institution subscription payments.
            </p>
        </div>

        <a href="{{ route('dashboard.subscription.payments.index') }}"
           class="btn btn-outline-primary">

            <i class="fas fa-check-circle me-1"></i>
            Verify Payments

            @if($pendingPayments > 0)
                <span class="badge bg-danger ms-1">
                    {{ $pendingPayments }}
                </span>
            @endif

        </a>

    </div>


    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        {{-- Total Transactions --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <p class="text-muted mb-1">
                                Total Transactions
                            </p>

                            <h3 class="fw-bold mb-0">
                                {{ number_format($totalTransactions) }}
                            </h3>
                        </div>

                        <div class="rounded-circle bg-primary bg-opacity-10
                                    text-primary p-3">
                            <i class="fas fa-exchange-alt fa-lg"></i>
                        </div>

                    </div>

                </div>
            </div>
        </div>


        {{-- Total Revenue --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <p class="text-muted mb-1">
                                Approved Revenue
                            </p>

                            <h3 class="fw-bold text-success mb-0">
                                ৳{{ number_format((float) $totalRevenue, 2) }}
                            </h3>
                        </div>

                        <div class="rounded-circle bg-success bg-opacity-10
                                    text-success p-3">
                            <i class="fas fa-wallet fa-lg"></i>
                        </div>

                    </div>

                    <small class="text-muted">
                        Approved payments only
                    </small>

                </div>
            </div>
        </div>


        {{-- Pending Payments --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <p class="text-muted mb-1">
                                Pending Payments
                            </p>

                            <h3 class="fw-bold text-warning mb-0">
                                {{ number_format($pendingPayments) }}
                            </h3>
                        </div>

                        <div class="rounded-circle bg-warning bg-opacity-10
                                    text-warning p-3">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>

                    </div>

                    <small class="text-muted">
                        Awaiting verification
                    </small>

                </div>
            </div>
        </div>


        {{-- Rejected Payments --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <p class="text-muted mb-1">
                                Rejected Payments
                            </p>

                            <h3 class="fw-bold text-danger mb-0">
                                {{ number_format($rejectedPayments) }}
                            </h3>
                        </div>

                        <div class="rounded-circle bg-danger bg-opacity-10
                                    text-danger p-3">
                            <i class="fas fa-times-circle fa-lg"></i>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>


    {{-- Transactions Table --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex flex-wrap justify-content-between
                        align-items-center gap-2">

                <h5 class="fw-bold mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>
                    All Transactions
                </h5>

                <span class="badge bg-light text-dark border">
                    {{ $transactions->total() }} records
                </span>

            </div>

        </div>


        <div class="card-body">

            {{-- Filters --}}
            <form method="GET"
                  action="{{ route('dashboard.subscription.transactions.index') }}"
                  class="mb-4">

                <div class="row g-3">

                    {{-- Search --}}
                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Search
                        </label>

                        <input type="text"
                               name="search"
                               class="form-control"
                               value="{{ request('search') }}"
                               placeholder="Transaction ID, institution, plan...">

                    </div>


                    {{-- Status --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="form-label">
                            Payment Status
                        </label>

                        <select name="status" class="form-select">

                            <option value="">All Statuses</option>

                            <option value="approved"
                                @selected(request('status') === 'approved')>
                                Approved
                            </option>

                            <option value="pending"
                                @selected(request('status') === 'pending')>
                                Pending
                            </option>

                            <option value="rejected"
                                @selected(request('status') === 'rejected')>
                                Rejected
                            </option>

                        </select>

                    </div>


                    {{-- Payment Method --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="form-label">
                            Payment Method
                        </label>

                        <select name="payment_method" class="form-select">

                            <option value="">All Methods</option>

                            @foreach($paymentMethods as $method)

                                <option value="{{ $method }}"
                                    @selected(request('payment_method') === $method)>

                                    {{ $method }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- From Date --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="form-label">
                            From Date
                        </label>

                        <input type="date"
                               name="date_from"
                               class="form-control"
                               value="{{ request('date_from') }}">

                    </div>


                    {{-- To Date --}}
                    <div class="col-lg-2 col-md-6">

                        <label class="form-label">
                            To Date
                        </label>

                        <input type="date"
                               name="date_to"
                               class="form-control"
                               value="{{ request('date_to') }}">

                    </div>


                    {{-- Buttons --}}
                    <div class="col-12 d-flex flex-wrap gap-2">

                        <button type="submit" class="btn btn-primary">

                            <i class="fas fa-search me-1"></i>
                            Apply Filters

                        </button>

                        <a href="{{ route('dashboard.subscription.transactions.index') }}"
                           class="btn btn-outline-secondary">

                            <i class="fas fa-undo me-1"></i>
                            Reset

                        </a>

                    </div>

                </div>

            </form>


            {{-- Responsive Table --}}
            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>
                            <th>#</th>
                            <th>Transaction</th>
                            <th>Institution</th>
                            <th>Subscription Plan</th>
                            <th>Payment Method</th>
                            <th>Amount</th>
                            <th>Submitted At</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>

                    </thead>


                    <tbody>

                        @forelse($transactions as $transaction)

                            <tr>

                                {{-- Serial --}}
                                <td>
                                    {{ $transactions->firstItem() + $loop->index }}
                                </td>


                                {{-- Transaction --}}
                                <td>

                                    <div class="fw-semibold">

                                        {{ $transaction->transaction_id ?: 'Not provided' }}

                                    </div>

                                    @if($transaction->sender_number)

                                        <small class="text-muted">
                                            {{ $transaction->sender_number }}
                                        </small>

                                    @endif

                                </td>


                                {{-- Institution --}}
                                <td>

                                    <div class="fw-semibold">

                                        {{ $transaction->institution?->name ?? 'Unknown Institution' }}

                                    </div>

                                    <small class="text-muted">

                                        Institution ID:
                                        {{ $transaction->institution_id }}

                                    </small>

                                </td>


                                {{-- Plan --}}
                                <td>

                                    <span class="fw-semibold">

                                        {{ $transaction->subscription?->plan?->name ?? 'Plan unavailable' }}

                                    </span>

                                    @if($transaction->subscription)

                                        <div class="small text-muted">

                                            {{ ucfirst($transaction->subscription->billing_cycle ?? 'N/A') }}

                                        </div>

                                    @endif

                                </td>


                                {{-- Payment Method --}}
                                <td>

                                    <span class="badge bg-light text-dark border">

                                        {{ strtoupper($transaction->payment_method) }}

                                    </span>

                                </td>


                                {{-- Amount --}}
                                <td>

                                    <span class="fw-bold">

                                        {{ $transaction->currency }}
                                        {{ number_format((float) $transaction->amount, 2) }}

                                    </span>

                                </td>


                                {{-- Submitted Date --}}
                                <td>

                                    @if($transaction->submitted_at)

                                        <div>
                                            {{ $transaction->submitted_at->format('d M Y') }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $transaction->submitted_at->format('h:i A') }}
                                        </small>

                                    @else

                                        <div>
                                            {{ $transaction->created_at?->format('d M Y') ?? 'N/A' }}
                                        </div>

                                        <small class="text-muted">
                                            Created date
                                        </small>

                                    @endif

                                </td>


                                {{-- Status --}}
                                <td>

                                    @php
                                        $statusClasses = [
                                            'approved' => 'success',
                                            'pending' => 'warning',
                                            'rejected' => 'danger',
                                        ];

                                        $statusClass = $statusClasses[$transaction->status]
                                            ?? 'secondary';
                                    @endphp

                                    <span class="badge bg-{{ $statusClass }}">

                                        {{ ucfirst($transaction->status) }}

                                    </span>

                                </td>


                                {{-- Action --}}
                                <td class="text-center">

                                    <a href="{{ route('dashboard.subscription.transactions.show', $transaction->id) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="View Transaction Details">

                                        <i class="fas fa-eye"></i>

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9" class="text-center py-5">

                                    <div class="mb-3">

                                        <i class="fas fa-receipt fa-3x text-muted"></i>

                                    </div>

                                    <h6 class="fw-bold">
                                        No Transactions Found
                                    </h6>

                                    <p class="text-muted mb-0">
                                        No payment records match your search criteria.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if($transactions->hasPages())

                <div class="d-flex flex-wrap justify-content-between
                            align-items-center gap-3 mt-4">

                    <div class="text-muted small">

                        Showing
                        {{ $transactions->firstItem() }}
                        to
                        {{ $transactions->lastItem() }}
                        of
                        {{ $transactions->total() }}
                        transactions

                    </div>

                    <div>

                        {{ $transactions->links() }}

                    </div>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection