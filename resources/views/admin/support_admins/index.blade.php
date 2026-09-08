@extends('layouts.admin')

@section('title', 'সাপোর্ট এডমিন ও পারমিশন কন্ট্রোল')

@section('page')

<div class="container-fluid px-0">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-shield-lock me-2"></i>
                সাপোর্ট এডমিন ও পারমিশন কন্ট্রোল
            </h4>

            <p class="text-muted mb-0">
                সাপোর্ট এডমিনদের লগইন আইডি তৈরি করুন এবং পেজভিত্তিক পারমিশন নির্ধারণ করুন।
            </p>
        </div>

        <a href="{{ route('dashboard.support-admins.create') }}"
           class="btn btn-primary d-flex align-items-center gap-2">

            <i class="bi bi-plus-circle"></i>

            নতুন সাপোর্ট এডমিন
        </a>

    </div>


    {{-- =========================================================
        SUCCESS MESSAGE
    ========================================================== --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show shadow-sm"
             role="alert">

            <i class="bi bi-check-circle me-2"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- =========================================================
        ERROR MESSAGE
    ========================================================== --}}
    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show shadow-sm"
             role="alert">

            <i class="bi bi-exclamation-triangle me-2"></i>

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- =========================================================
        STATISTICS
    ========================================================== --}}
    <div class="row g-3 mb-4">

        {{-- Total --}}
        <div class="col-xl-4 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="rounded-3 bg-primary bg-opacity-10
                                text-primary d-flex align-items-center
                                justify-content-center"
                         style="width:52px;height:52px;">

                        <i class="bi bi-people fs-4"></i>

                    </div>

                    <div class="ms-3">

                        <div class="text-muted small">
                            মোট সাপোর্ট এডমিন
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ $totalSupportAdmins }}
                            <span class="fs-6 fw-normal text-muted">
                                জন
                            </span>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Active --}}
        <div class="col-xl-4 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="rounded-3 bg-success bg-opacity-10
                                text-success d-flex align-items-center
                                justify-content-center"
                         style="width:52px;height:52px;">

                        <i class="bi bi-person-check fs-4"></i>

                    </div>

                    <div class="ms-3">

                        <div class="text-muted small">
                            সক্রিয়
                        </div>

                        <div class="fs-4 fw-bold">

                            {{ $activeSupportAdmins }}

                            <span class="fs-6 fw-normal text-muted">
                                জন
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Inactive --}}
        <div class="col-xl-4 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="rounded-3 bg-warning bg-opacity-10
                                text-warning d-flex align-items-center
                                justify-content-center"
                         style="width:52px;height:52px;">

                        <i class="bi bi-person-x fs-4"></i>

                    </div>

                    <div class="ms-3">

                        <div class="text-muted small">
                            নিষ্ক্রিয়
                        </div>

                        <div class="fs-4 fw-bold">

                            {{ $totalSupportAdmins - $activeSupportAdmins }}

                            <span class="fs-6 fw-normal text-muted">
                                জন
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        SEARCH / FILTER
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('dashboard.support-admins.index') }}">

                <div class="row g-3 align-items-end">

                    {{-- Search --}}
                    <div class="col-lg-6">

                        <label class="form-label fw-semibold">
                            সাপোর্ট এডমিন খুঁজুন
                        </label>

                        <div class="input-group">

                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>

                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="form-control"
                                   placeholder="নাম, মোবাইল, ইউজারনেম বা ইমেইল...">

                        </div>

                    </div>


                    {{-- Status --}}
                    <div class="col-lg-3">

                        <label class="form-label fw-semibold">
                            স্ট্যাটাস
                        </label>

                        <select name="status"
                                class="form-select">

                            <option value="">
                                সকল স্ট্যাটাস
                            </option>

                            <option value="1"
                                {{ request('status') === '1' ? 'selected' : '' }}>
                                সক্রিয়
                            </option>

                            <option value="0"
                                {{ request('status') === '0' ? 'selected' : '' }}>
                                নিষ্ক্রিয়
                            </option>

                        </select>

                    </div>


                    {{-- Buttons --}}
                    <div class="col-lg-3">

                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-primary flex-fill">

                                <i class="bi bi-search me-1"></i>

                                খুঁজুন

                            </button>


                            <a href="{{ route('dashboard.support-admins.index') }}"
                               class="btn btn-light border"
                               title="রিসেট">

                                <i class="bi bi-arrow-counterclockwise"></i>

                            </a>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        SUPPORT ADMIN TABLE
    ========================================================== --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 py-3">

            <div class="d-flex flex-wrap justify-content-between
                        align-items-center gap-2">

                <div>

                    <h5 class="fw-bold mb-1">
                        সাপোর্ট এডমিন তালিকা
                    </h5>

                    <small class="text-muted">
                        সাপোর্ট এডমিনদের অ্যাকাউন্ট এবং অনুমোদিত মডিউলসমূহ
                    </small>

                </div>

                <span class="badge bg-light text-dark border">

                    মোট
                    {{ $supportAdmins->total() }}
                    জন

                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th class="ps-4">
                                #
                            </th>

                            <th>
                                এডমিনের নাম ও পদবী
                            </th>

                            <th>
                                মোবাইল
                            </th>

                            <th>
                                লগইন আইডি
                            </th>

                            <th>
                                অনুমোদিত মডিউল
                            </th>

                            <th>
                                স্ট্যাটাস
                            </th>

                            <th class="text-end pe-4">
                                অ্যাকশন
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @forelse($supportAdmins as $key => $admin)

                        <tr>

                            {{-- Serial --}}
                            <td class="ps-4">

                                {{ $supportAdmins->firstItem() + $key }}

                            </td>


                            {{-- Name --}}
                            <td>

                                <div class="d-flex align-items-center">

                                    {{-- Avatar --}}
                                    <div class="rounded-circle bg-primary
                                                bg-opacity-10 text-primary
                                                d-flex align-items-center
                                                justify-content-center
                                                fw-bold"
                                         style="width:42px;height:42px;">

                                        {{ mb_substr(
                                            $admin->name_bn ?: $admin->name,
                                            0,
                                            1
                                        ) }}

                                    </div>


                                    <div class="ms-3">

                                        <div class="fw-semibold">

                                            {{ $admin->name_bn ?: $admin->name }}

                                        </div>

                                        @if($admin->name_bn && $admin->name)
                                            <div class="small text-muted">
                                                {{ $admin->name }}
                                            </div>
                                        @endif

                                        <div class="small text-muted">

                                            <i class="bi bi-shield-check me-1"></i>

                                            সাপোর্ট এডমিন

                                        </div>

                                    </div>

                                </div>

                            </td>


                            {{-- Phone --}}
                            <td>

                                <div class="fw-medium">

                                    {{ $admin->phone ?: '—' }}

                                </div>

                            </td>


                            {{-- Username --}}
                            <td>

                                <span class="badge bg-light text-dark border">

                                    {{ $admin->username ?: '—' }}

                                </span>

                            </td>


                            {{-- Permissions --}}
                            <td>

                                @php

                                    $permissionCount =
                                        $admin->permissions
                                            ->filter(
                                                fn($permission) =>
                                                    (bool) $permission->pivot->allow
                                            )
                                            ->count();

                                @endphp


                                @if($permissionCount > 0)

                                    <span class="badge bg-primary bg-opacity-10
                                                 text-primary border border-primary
                                                 border-opacity-25">

                                        <i class="bi bi-key me-1"></i>

                                        {{ $permissionCount }}
                                        টি পারমিশন

                                    </span>

                                @else

                                    <span class="badge bg-light text-muted border">

                                        কোনো পারমিশন নেই

                                    </span>

                                @endif

                            </td>


                            {{-- Status --}}
                            <td>

                                @if($admin->status == 1)

                                    <span class="badge bg-success bg-opacity-10
                                                 text-success border
                                                 border-success border-opacity-25">

                                        <i class="bi bi-check-circle me-1"></i>

                                        সক্রিয়

                                    </span>

                                @else

                                    <span class="badge bg-danger bg-opacity-10
                                                 text-danger border
                                                 border-danger border-opacity-25">

                                        <i class="bi bi-x-circle me-1"></i>

                                        নিষ্ক্রিয়

                                    </span>

                                @endif

                            </td>


                            {{-- Actions --}}
                            <td class="text-end pe-4">

                                <div class="dropdown">

                                    <button type="button"
                                            class="btn btn-sm btn-light border"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">

                                        <i class="bi bi-three-dots-vertical"></i>

                                    </button>


                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">

                                        {{-- Edit --}}
                                        <li>

                                            <a class="dropdown-item"
                                               href="{{ route(
                                                   'dashboard.support-admins.edit',
                                                   $admin->id
                                               ) }}">

                                                <i class="bi bi-pencil me-2"></i>

                                                সম্পাদনা

                                            </a>

                                        </li>


                                        {{-- Toggle --}}
                                        <li>

                                            <form method="POST"
                                                  action="{{ route(
                                                      'dashboard.support-admins.toggle-status',
                                                      $admin->id
                                                  ) }}">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="dropdown-item">

                                                    @if($admin->status)

                                                        <i class="bi bi-person-x me-2"></i>

                                                        নিষ্ক্রিয় করুন

                                                    @else

                                                        <i class="bi bi-person-check me-2"></i>

                                                        সক্রিয় করুন

                                                    @endif

                                                </button>

                                            </form>

                                        </li>


                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>


                                        {{-- Delete --}}
                                        <li>

                                            <form method="POST"
                                                  action="{{ route(
                                                      'dashboard.support-admins.destroy',
                                                      $admin->id
                                                  ) }}"
                                                  onsubmit="return confirm(
                                                      'আপনি কি এই সাপোর্ট এডমিনকে ডিলিট করতে চান?'
                                                  );">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="dropdown-item text-danger">

                                                    <i class="bi bi-trash me-2"></i>

                                                    ডিলিট করুন

                                                </button>

                                            </form>

                                        </li>

                                    </ul>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7"
                                class="text-center py-5">

                                <div class="mb-3">

                                    <div class="rounded-circle bg-light
                                                d-inline-flex align-items-center
                                                justify-content-center"
                                         style="width:70px;height:70px;">

                                        <i class="bi bi-shield-x fs-2 text-muted"></i>

                                    </div>

                                </div>

                                <h6 class="fw-bold">
                                    কোনো সাপোর্ট এডমিন পাওয়া যায়নি
                                </h6>

                                <p class="text-muted mb-3">

                                    এখনো কোনো সাপোর্ট এডমিন তৈরি করা হয়নি।

                                </p>

                                <a href="{{ route(
                                    'dashboard.support-admins.create'
                                ) }}"
                                   class="btn btn-primary">

                                    <i class="bi bi-plus-circle me-1"></i>

                                    নতুন সাপোর্ট এডমিন তৈরি করুন

                                </a>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =====================================================
            PAGINATION
        ====================================================== --}}
        @if($supportAdmins->hasPages())

            <div class="card-footer bg-white border-0 py-3">

                <div class="d-flex justify-content-between
                            align-items-center flex-wrap gap-2">

                    <small class="text-muted">

                        দেখানো হচ্ছে

                        <strong>
                            {{ $supportAdmins->firstItem() }}
                        </strong>

                        থেকে

                        <strong>
                            {{ $supportAdmins->lastItem() }}
                        </strong>

                        পর্যন্ত

                    </small>


                    <div>

                        {{ $supportAdmins->links() }}

                    </div>

                </div>

            </div>

        @endif

    </div>

</div>

@endsection