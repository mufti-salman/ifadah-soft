@extends('layouts.admin')

@section('title', 'Transaction Details')

@section('page')

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between
                align-items-center gap-3 mb-4">

        <div>

            <h3 class="fw-bold mb-1">
                <i class="fas fa-receipt me-2 text-primary"></i>
                Transaction Details
            </h3>

            <p class="text-muted mb-0">
                Complete payment information and verification history.
            </p>

        </div>

        <a href="{{ route('dashboard.subscription.transactions.index') }}"
           class="btn btn-outline-secondary">

            <i class="fas fa-arrow-left me-1"></i>
            Back to Transactions

        </a>

    </div>


    @php

        $statusClasses = [
            'approved' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
        ];

        $statusClass = $statusClasses[$transaction->status]
            ?? 'secondary';

    @endphp


    {{-- Main Details --}}
    <div class="row g-4">

        {{-- Payment Information --}}
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white py-3">

                    <h5 class="fw-bold mb-0">
                        Payment Information
                    </h5>

                </div>

                <div class="card-body">

                    <div class="row g-4">

                        <div class="col-md-6">

                            <label class="text-muted small">
                                Transaction ID
                            </label>

                            <div class="fw-bold text-break">

                                {{ $transaction->transaction_id ?: 'Not provided' }}

                            </div>

                        </div>


                        <div class="col-md-6">

                            <label class="text-muted small">
                                Payment Status
                            </label>

                            <div>

                                <span class="badge bg-{{ $statusClass }}">

                                    {{ ucfirst($transaction->status) }}

                                </span>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <label class="text-muted small">
                                Amount
                            </label>

                            <h4 class="fw-bold text-success mb-0">

                                {{ $transaction->currency }}
                                {{ number_format((float) $transaction->amount, 2) }}

                            </h4>

                        </div>


                        <div class="col-md-6">

                            <label class="text-muted small">
                                Payment Method
                            </label>

                            <div class="fw-semibold">

                                {{ strtoupper($transaction->payment_method) }}

                            </div>

                        </div>


                        <div class="col-md-6">

                            <label class="text-muted small">
                                Sender Number
                            </label>

                            <div class="fw-semibold">

                                {{ $transaction->sender_number ?: 'Not provided' }}

                            </div>

                        </div>


                        <div class="col-md-6">

                            <label class="text-muted small">
                                Payment Submitted At
                            </label>

                            <div class="fw-semibold">

                                {{ $transaction->submitted_at?->format('d M Y, h:i A') ?? 'Not recorded' }}

                            </div>

                        </div>


                        <div class="col-md-6">

                            <label class="text-muted small">
                                Record Created At
                            </label>

                            <div class="fw-semibold">

                                {{ $transaction->created_at?->format('d M Y, h:i A') ?? 'N/A' }}

                            </div>

                        </div>


                        <div class="col-md-6">

                            <label class="text-muted small">
                                Last Updated
                            </label>

                            <div class="fw-semibold">

                                {{ $transaction->updated_at?->format('d M Y, h:i A') ?? 'N/A' }}

                            </div>

                        </div>

                    </div>


                    <hr class="my-4">


                    {{-- Payment Notes --}}
                    <h6 class="fw-bold mb-2">
                        Payment Notes
                    </h6>

                    <div class="bg-light rounded p-3">

                        {{ $transaction->notes ?: 'No payment notes provided.' }}

                    </div>


                    {{-- Rejection Reason --}}
                    @if($transaction->status === 'rejected')

                        <div class="mt-4">

                            <h6 class="fw-bold text-danger mb-2">
                                Rejection Reason
                            </h6>

                            <div class="alert alert-danger mb-0">

                                {{ $transaction->rejection_reason ?: 'No rejection reason recorded.' }}

                            </div>

                        </div>

                    @endif

                </div>

            </div>


            {{-- Payment Proof --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white py-3">

                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-file-image me-2 text-primary"></i>
                        Payment Proof
                    </h5>

                </div>

                <div class="card-body">

                    @if($transaction->proof_path)

                        <a href="{{ asset('storage/' . $transaction->proof_path) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-outline-primary">

                            <i class="fas fa-external-link-alt me-1"></i>
                            View Payment Proof

                        </a>

                    @else

                        <p class="text-muted mb-0">
                            No payment proof was uploaded.
                        </p>

                    @endif

                </div>

            </div>

        </div>


        {{-- Institution and Subscription --}}
        <div class="col-lg-4">

            {{-- Institution --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white py-3">

                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-school me-2 text-primary"></i>
                        Institution
                    </h5>

                </div>

                <div class="card-body">

                    <h5 class="fw-bold">

                        {{ $transaction->institution?->name ?? 'Unknown Institution' }}

                    </h5>

                    <p class="text-muted mb-1">

                        Institution ID:
                        {{ $transaction->institution_id }}

                    </p>

                    <hr>

                    <p class="small text-muted mb-0">

                        All institution information is displayed
                        from the existing payment record.

                    </p>

                </div>

            </div>


            {{-- Subscription --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white py-3">

                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-box me-2 text-primary"></i>
                        Subscription
                    </h5>

                </div>

                <div class="card-body">

                    @if($transaction->subscription)

                        <h5 class="fw-bold">

                            {{ $transaction->subscription->plan?->name ?? 'Plan unavailable' }}

                        </h5>

                        <p class="text-muted mb-3">

                            Subscription ID:
                            {{ $transaction->subscription_id }}

                        </p>


                        <div class="mb-3">

                            <label class="text-muted small">
                                Billing Cycle
                            </label>

                            <div class="fw-semibold">

                                {{ ucfirst($transaction->subscription->billing_cycle ?? 'N/A') }}

                            </div>

                        </div>


                        <div class="mb-3">

                            <label class="text-muted small">
                                Subscription Status
                            </label>

                            <div>

                                <span class="badge bg-light text-dark border">

                                    {{ ucfirst($transaction->subscription->status) }}

                                </span>

                            </div>

                        </div>


                        <div class="mb-3">

                            <label class="text-muted small">
                                Subscription Amount
                            </label>

                            <div class="fw-bold">

                                {{ $transaction->subscription->currency }}
                                {{ number_format((float) $transaction->subscription->amount, 2) }}

                            </div>

                        </div>

                    @else

                        <p class="text-muted mb-0">
                            Subscription information is unavailable.
                        </p>

                    @endif

                </div>

            </div>


            {{-- Verification Information --}}
            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">

                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-user-check me-2 text-primary"></i>
                        Verification Information
                    </h5>

                </div>

                <div class="card-body">

                    <div class="mb-3">

                        <label class="text-muted small">
                            Verified By
                        </label>

                        <div class="fw-semibold">

                            {{ $transaction->verifier?->name ?? 'Not verified yet' }}

                        </div>

                    </div>


                    <div>

                        <label class="text-muted small">
                            Verified At
                        </label>

                        <div class="fw-semibold">

                            {{ $transaction->verified_at?->format('d M Y, h:i A') ?? 'Not verified yet' }}

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection