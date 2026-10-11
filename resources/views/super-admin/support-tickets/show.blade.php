@extends('layouts.admin')

@section('title', 'সাপোর্ট টিকিটের বিস্তারিত')

@section('page')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="fas fa-ticket-alt text-primary me-2"></i>
                সাপোর্ট টিকিটের বিস্তারিত
            </h3>

            <p class="text-muted mb-0">
                টিকিট নম্বর: {{ $supportTicket->ticket_number }}
            </p>
        </div>

        <a href="{{ route('dashboard.support-tickets.index') }}"
           class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            টিকিটের তালিকায় ফিরুন
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>অনুগ্রহ করে নিচের সমস্যাগুলো ঠিক করুন:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        {{-- Main Content --}}
        <div class="col-lg-8">

            {{-- Ticket Information --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <small class="text-muted">
                                {{ strtoupper($supportTicket->category) }}
                            </small>

                            <h4 class="fw-bold mt-1 mb-0">
                                {{ $supportTicket->subject }}
                            </h4>
                        </div>

                        @php
                            $statusClasses = [
                                'open' => 'bg-primary',
                                'in_progress' => 'bg-warning text-dark',
                                'resolved' => 'bg-success',
                                'closed' => 'bg-secondary',
                            ];

                            $priorityClasses = [
                                'low' => 'bg-secondary',
                                'medium' => 'bg-info text-dark',
                                'high' => 'bg-warning text-dark',
                                'urgent' => 'bg-danger',
                            ];
                        @endphp

                        <span class="badge {{ $statusClasses[$supportTicket->status] ?? 'bg-secondary' }}">
                            {{ strtoupper(str_replace('_', ' ', $supportTicket->status)) }}
                        </span>
                    </div>
                </div>

                <div class="card-body">

                    <div class="row g-3 mb-4">

                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block mb-1">
                                    টিকিট নম্বর
                                </small>

                                <strong>
                                    {{ $supportTicket->ticket_number }}
                                </strong>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block mb-1">
                                    অগ্রাধিকার
                                </small>

                                <span class="badge {{ $priorityClasses[$supportTicket->priority] ?? 'bg-secondary' }}">
                                    {{ strtoupper($supportTicket->priority) }}
                                </span>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block mb-1">
                                    তৈরি করা হয়েছে
                                </small>

                                <strong>
                                    {{ $supportTicket->created_at?->format('d M Y, h:i A') }}
                                </strong>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block mb-1">
                                    সর্বশেষ আপডেট
                                </small>

                                <strong>
                                    {{ $supportTicket->updated_at?->format('d M Y, h:i A') }}
                                </strong>
                            </div>
                        </div>

                    </div>

                    <hr>

                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-comments text-primary me-2"></i>
                        Conversation
                    </h5>

                    @forelse($supportTicket->replies as $reply)

                        <div class="border rounded p-3 mb-3">

                            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">

                                <div>
                                    <strong>
                                        {{ $reply->user->name ?? 'Unknown User' }}
                                    </strong>

                                    @if($reply->user_id === $supportTicket->user_id)
                                        <span class="badge bg-info text-dark ms-1">
                                            Ticket Creator
                                        </span>
                                    @elseif($reply->user_id === $supportTicket->assigned_to)
                                        <span class="badge bg-primary ms-1">
                                            Assigned Support
                                        </span>
                                    @endif
                                </div>

                                <small class="text-muted">
                                    {{ $reply->created_at?->format('d M Y, h:i A') }}
                                </small>

                            </div>

                            <div style="white-space: pre-wrap; overflow-wrap: anywhere;">{{ $reply->message }}</div>

                            @if($reply->attachment_path)
                                <div class="mt-3">
                                    <i class="fas fa-paperclip me-1"></i>
                                    <span class="text-muted">
                                        সংযুক্ত ফাইল:
                                    </span>

                                    <span class="small">
                                        {{ basename($reply->attachment_path) }}
                                    </span>
                                </div>
                            @endif

                        </div>

                    @empty

                        <div class="text-center py-5 border rounded">
                            <i class="fas fa-comments fa-2x text-muted mb-3"></i>

                            <h6 class="fw-bold">
                                এখনো কোনো Reply নেই
                            </h6>

                            <p class="text-muted mb-0">
                                এই টিকিটে উত্তর যোগ করা হলে এখানে দেখা যাবে।
                            </p>
                        </div>

                    @endforelse

                </div>
            </div>

   
{{-- Reply Form --}}
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-reply me-2"></i>
            রিপ্লাই পাঠান
        </h5>
    </div>

    <div class="card-body">

        <form
            action="{{ route('dashboard.support-tickets.reply', $supportTicket->id) }}"
            method="POST"
        >
            @csrf

            <div class="mb-3">
                <label for="message" class="form-label">
                    আপনার মেসেজ
                </label>

                <textarea
                    name="message"
                    id="message"
                    rows="5"
                    class="form-control @error('message') is-invalid @enderror"
                    placeholder="এখানে আপনার রিপ্লাই লিখুন..."
                    required
                >{{ old('message') }}</textarea>

                @error('message')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-1"></i>
                রিপ্লাই পাঠান
            </button>
        </form>

    </div>
</div>

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            {{-- Institution Information --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-school text-primary me-2"></i>
                        প্রতিষ্ঠানের তথ্য
                    </h5>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">
                            প্রতিষ্ঠানের নাম
                        </small>

                        <strong>
                            {{ $supportTicket->institution->name
                                ?? $supportTicket->institution->institution_name
                                ?? 'N/A' }}
                        </strong>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">
                            টিকিট তৈরি করেছেন
                        </small>

                        <strong>
                            {{ $supportTicket->creator->name ?? 'N/A' }}
                        </strong>

                        @if($supportTicket->creator?->email)
                            <div class="small text-muted mt-1">
                                {{ $supportTicket->creator->email }}
                            </div>
                        @endif
                    </div>

                    <hr>

                    <div>
                        <small class="text-muted d-block mb-1">
                            দায়িত্বপ্রাপ্ত Support Admin
                        </small>

                        @if($supportTicket->assignedUser)
                            <strong>
                                {{ $supportTicket->assignedUser->name }}
                            </strong>

                            @if($supportTicket->assignedUser->email)
                                <div class="small text-muted mt-1">
                                    {{ $supportTicket->assignedUser->email }}
                                </div>
                            @endif
                        @else
                            <span class="badge bg-secondary">
                                এখনো Assign করা হয়নি
                            </span>
                        @endif
                    </div>

                </div>
            </div>

{{-- Ticket Management --}}
<div class="card border-0 shadow-sm">

    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0">
            <i class="fas fa-sliders-h text-primary me-2"></i>
            টিকিট পরিচালনা
        </h5>
    </div>

    <div class="card-body">

        <form
            action="{{ route('dashboard.support-tickets.update', $supportTicket->id) }}"
            method="POST"
        >
            @csrf
            @method('PATCH')

            {{-- Status --}}
            <div class="mb-3">
                <label for="status" class="form-label fw-semibold">
                    Ticket Status
                </label>

                <select
                    name="status"
                    id="status"
                    class="form-select @error('status') is-invalid @enderror"
                    required
                >
                    <option value="open"
                        @selected(old('status', $supportTicket->status) === 'open')>
                        Open
                    </option>

                    <option value="in_progress"
                        @selected(old('status', $supportTicket->status) === 'in_progress')>
                        In Progress
                    </option>

                    <option value="resolved"
                        @selected(old('status', $supportTicket->status) === 'resolved')>
                        Resolved
                    </option>

                    <option value="closed"
                        @selected(old('status', $supportTicket->status) === 'closed')>
                        Closed
                    </option>
                </select>

                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Priority --}}
            <div class="mb-3">
                <label for="priority" class="form-label fw-semibold">
                    Priority
                </label>

                <select
                    name="priority"
                    id="priority"
                    class="form-select @error('priority') is-invalid @enderror"
                    required
                >
                    <option value="low"
                        @selected(old('priority', $supportTicket->priority) === 'low')>
                        Low
                    </option>

                    <option value="medium"
                        @selected(old('priority', $supportTicket->priority) === 'medium')>
                        Medium
                    </option>

                    <option value="high"
                        @selected(old('priority', $supportTicket->priority) === 'high')>
                        High
                    </option>

                    <option value="urgent"
                        @selected(old('priority', $supportTicket->priority) === 'urgent')>
                        Urgent
                    </option>
                </select>

                @error('priority')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Assign Support Admin --}}
<div class="card mt-3">
    <div class="card-header">
        <h5 class="mb-0">Assign Support Admin</h5>
    </div>

    <div class="card-body">

        <form
            action="{{ route('dashboard.support-tickets.assign', $supportTicket->id) }}"
            method="POST"
        >
            @csrf

            <div class="mb-3">
                <label for="assigned_to" class="form-label">
                    Support Admin নির্বাচন করুন
                </label>

                <select
                    name="assigned_to"
                    id="assigned_to"
                    class="form-select @error('assigned_to') is-invalid @enderror"
                    required
                >
                    <option value="">-- Support Admin নির্বাচন করুন --</option>

                    @foreach ($supportAdmins as $admin)
                        <option
                            value="{{ $admin->id }}"
                            {{ (string) old('assigned_to', $supportTicket->assigned_to) === (string) $admin->id ? 'selected' : '' }}
                        >
                            {{ $admin->name_bn ?: $admin->name }}
                            @if ($admin->phone)
                                — {{ $admin->phone }}
                            @endif
                        </option>
                    @endforeach
                </select>

                @error('assigned_to')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if ($supportAdmins->isEmpty())
                <div class="alert alert-warning">
                    বর্তমানে কোনো Active Support Admin নেই।
                </div>
            @endif

            <button
                type="submit"
                class="btn btn-primary"
                {{ $supportAdmins->isEmpty() ? 'disabled' : '' }}
            >
                <i class="fas fa-user-check me-1"></i>
                Assign করুন
            </button>
        </form>

        @if ($supportTicket->assignedUser)
            <div class="alert alert-success mt-3 mb-0">
                <strong>বর্তমানে Assigned:</strong>
                {{ $supportTicket->assignedUser->name_bn ?: $supportTicket->assignedUser->name }}
            </div>
        @else
            <div class="text-muted small mt-3">
                এই টিকিটে এখনো কোনো Support Admin Assign করা হয়নি।
            </div>
        @endif

    </div>
</div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-save me-1"></i>
                পরিবর্তন সংরক্ষণ করুন
            </button>

        </form>

    </div>
</div>

        </div>

    </div>
</div>
@endsection
