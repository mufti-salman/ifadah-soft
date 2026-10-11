@extends('layouts.admin')

@section('title', 'সাপোর্ট টিকিট')

@section('page')
<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="fas fa-ticket-alt text-primary me-2"></i>
                সাপোর্ট টিকিট
            </h3>
            <p class="text-muted mb-0">
                প্রতিষ্ঠানের সমস্যাগুলো দেখুন এবং সাপোর্ট অনুরোধ পরিচালনা করুন।
            </p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">মোট টিকিট</div>
                    <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">নতুন টিকিট</div>
                    <h3 class="fw-bold text-primary mb-0">{{ $stats['open'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">চলমান</div>
                    <h3 class="fw-bold text-warning mb-0">{{ $stats['in_progress'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">সমাধান হয়েছে</div>
                    <h3 class="fw-bold text-success mb-0">{{ $stats['resolved'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">বন্ধ</div>
                    <h3 class="fw-bold text-secondary mb-0">{{ $stats['closed'] }}</h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('dashboard.support-tickets.index') }}">

                <div class="row g-3 align-items-end">

                    <div class="col-md-5">
                        <label for="search" class="form-label fw-semibold">
                            টিকিট খুঁজুন
                        </label>

                        <input
                            type="text"
                            id="search"
                            name="search"
                            class="form-control"
                            placeholder="টিকিট নম্বর অথবা বিষয় লিখুন"
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-3">
                        <label for="status" class="form-label fw-semibold">
                            স্ট্যাটাস
                        </label>

                        <select name="status" id="status" class="form-select">
                            <option value="">সব স্ট্যাটাস</option>

                            <option value="open"
                                @selected(request('status') === 'open')>
                                Open
                            </option>

                            <option value="in_progress"
                                @selected(request('status') === 'in_progress')>
                                In Progress
                            </option>

                            <option value="resolved"
                                @selected(request('status') === 'resolved')>
                                Resolved
                            </option>

                            <option value="closed"
                                @selected(request('status') === 'closed')>
                                Closed
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="priority" class="form-label fw-semibold">
                            অগ্রাধিকার
                        </label>

                        <select name="priority" id="priority" class="form-select">
                            <option value="">সব অগ্রাধিকার</option>

                            <option value="low"
                                @selected(request('priority') === 'low')>
                                Low
                            </option>

                            <option value="medium"
                                @selected(request('priority') === 'medium')>
                                Medium
                            </option>

                            <option value="high"
                                @selected(request('priority') === 'high')>
                                High
                            </option>

                            <option value="urgent"
                                @selected(request('priority') === 'urgent')>
                                Urgent
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-search me-1"></i>
                            খুঁজুন
                        </button>

                        <a href="{{ route('dashboard.support-tickets.index') }}"
                           class="btn btn-outline-secondary"
                           title="ফিল্টার মুছুন">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Ticket Table --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">টিকিটের তালিকা</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">টিকিট নম্বর</th>
                            <th>প্রতিষ্ঠান</th>
                            <th>বিষয়</th>
                            <th>অগ্রাধিকার</th>
                            <th>স্ট্যাটাস</th>
                            <th>দায়িত্বপ্রাপ্ত</th>
                            <th>তারিখ</th>
                            <th class="text-center">অ্যাকশন</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($tickets as $ticket)

                            <tr>
                                <td class="ps-3">
                                    <span class="fw-semibold">
                                        {{ $ticket->ticket_number }}
                                    </span>
                                </td>

                                <td>
                                    {{ $ticket->institution->name
                                        ?? $ticket->institution->institution_name
                                        ?? 'N/A' }}
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ \Illuminate\Support\Str::limit($ticket->subject, 45) }}
                                    </div>

                                    <small class="text-muted">
                                        {{ ucfirst($ticket->category) }}
                                    </small>
                                </td>

                                <td>
                                    @php
                                        $priorityClasses = [
                                            'low' => 'bg-secondary',
                                            'medium' => 'bg-info text-dark',
                                            'high' => 'bg-warning text-dark',
                                            'urgent' => 'bg-danger',
                                        ];
                                    @endphp

                                    <span class="badge {{ $priorityClasses[$ticket->priority] ?? 'bg-secondary' }}">
                                        {{ strtoupper($ticket->priority) }}
                                    </span>
                                </td>

                                <td>
                                    @php
                                        $statusClasses = [
                                            'open' => 'bg-primary',
                                            'in_progress' => 'bg-warning text-dark',
                                            'resolved' => 'bg-success',
                                            'closed' => 'bg-secondary',
                                        ];
                                    @endphp

                                    <span class="badge {{ $statusClasses[$ticket->status] ?? 'bg-secondary' }}">
                                        {{ strtoupper(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $ticket->assignedUser->name ?? 'বরাদ্দ করা হয়নি' }}
                                </td>

                                <td>
                                    {{ $ticket->created_at?->format('d M Y') }}
                                </td>

                                <td class="text-center">
                                    <a
                                        href="{{ route('dashboard.support-tickets.show', $ticket->id) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="বিস্তারিত দেখুন"
                                    >
                                        <i class="fas fa-eye me-1"></i>
                                        দেখুন
                                    </a>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>

                                    <h6 class="fw-bold">কোনো সাপোর্ট টিকিট পাওয়া যায়নি</h6>

                                    <p class="text-muted mb-0">
                                        নতুন টিকিট তৈরি হলে এখানে দেখা যাবে।
                                    </p>
                                </td>
                            </tr>

                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($tickets->hasPages())
            <div class="card-footer bg-white">
                {{ $tickets->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
