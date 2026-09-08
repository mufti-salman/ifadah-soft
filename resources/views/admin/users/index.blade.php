@extends('layouts.admin')

@section('title', 'ইউজার তালিকা')

@section('page')

<style>
    /* ─── Google Fonts ─── */
    @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap');

    .user-page * {
        font-family: 'Hind Siliguri', sans-serif;
    }

    /* ─── Page Background ─── */
    .user-page {
        background: #f0f4ff;
        min-height: 100vh;
        padding: 28px 24px;
    }

    /* ─── Header ─── */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
    }

    .page-header h5 {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 2px;
    }

    .page-header small {
        color: #7a8aaa;
        font-size: 0.85rem;
    }

    .btn-new-user {
        background: #1e3a8a;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 10px 20px;
        font-size: 0.9rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: background 0.2s;
    }

    .btn-new-user:hover {
        background: #1e40af;
        color: #fff;
    }

    .btn-new-user i {
        font-size: 1rem;
    }

    /* ─── Search & Filter Card ─── */
    .search-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(30, 58, 138, 0.07);
        padding: 16px 20px;
        margin-bottom: 20px;
    }

    .search-wrapper {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .search-input-group {
        flex: 1;
        position: relative;
    }

    .search-input-group .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 0.95rem;
    }

    .search-input-group input {
        width: 100%;
        border: 1.5px solid #e5e9f5;
        border-radius: 10px;
        padding: 10px 14px 10px 40px;
        font-size: 0.9rem;
        color: #374151;
        background: #f8faff;
        outline: none;
        transition: border-color 0.2s;
        font-family: 'Hind Siliguri', sans-serif;
    }

    .search-input-group input:focus {
        border-color: #6b8cde;
        background: #fff;
    }

    .search-input-group input::placeholder {
        color: #b0bac9;
    }

    .filter-icon-btn {
        border: 1.5px solid #e5e9f5;
        border-radius: 10px;
        padding: 10px 14px;
        background: #f8faff;
        color: #6b7280;
        cursor: pointer;
        transition: background 0.2s;
    }

    .filter-icon-btn:hover {
        background: #eef2ff;
    }

    .filter-select {
        border: 1.5px solid #e5e9f5;
        border-radius: 10px;
        padding: 10px 36px 10px 14px;
        font-size: 0.9rem;
        color: #374151;
        background: #f8faff;
        outline: none;
        min-width: 180px;
        cursor: pointer;
        font-family: 'Hind Siliguri', sans-serif;
        appearance: none;
        -webkit-appearance: none;

        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");

        background-repeat: no-repeat;
        background-position: right 12px center;
        transition: border-color 0.2s;
    }

    .filter-select:focus {
        border-color: #6b8cde;
    }

    /* ─── Table Card ─── */
    .table-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(30, 58, 138, 0.07);
        overflow: hidden;
    }

    .user-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .user-table thead tr {
        background: #f8faff;
        border-bottom: 2px solid #eef2fb;
    }

    .user-table thead th {
        padding: 14px 16px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #6b7280;
        text-align: center;
        white-space: nowrap;
        letter-spacing: 0.02em;
    }

    .user-table thead th.text-start {
        text-align: left;
    }

    .user-table tbody tr {
        border-bottom: 1px solid #f0f4ff;
        transition: background 0.15s;
    }

    .user-table tbody tr:hover {
        background: #f8faff;
    }

    .user-table tbody tr:last-child {
        border-bottom: none;
    }

    .user-table td {
        padding: 14px 16px;
        vertical-align: middle;
        text-align: center;
        font-size: 0.9rem;
        color: #374151;
    }

    .user-table td.text-start {
        text-align: left;
    }

    /* ─── ID Badge ─── */
    .id-badge {
        background: #eef2fb;
        color: #3b5bdb;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 5px 12px;
        border-radius: 8px;
        display: inline-block;
    }

    /* ─── Name Cell ─── */
    .name-primary {
        font-weight: 700;
        font-size: 0.95rem;
        color: #1a1a2e;
    }

    .name-secondary {
        font-size: 0.8rem;
        color: #9ca3af;
        margin-top: 1px;
    }

    /* ─── Phone ─── */
    .phone-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: #4b5563;
    }

    .phone-cell i {
        color: #9ca3af;
        font-size: 0.8rem;
    }

    /* ─── Role Badges ─── */

    .role-badge {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .role-super-admin {
        background: #fee2e2;
        color: #991b1b;
    }

    .role-support-admin {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .role-user-admin {
        background: #fef3c7;
        color: #92400e;
    }

    .role-user {
        background: #d1fae5;
        color: #065f46;
    }

    .role-student {
        background: #ede9fe;
        color: #6d28d9;
    }

    .role-default {
        background: #e5e7eb;
        color: #374151;
    }

    /* ─── Status ─── */
    .status-active {
        color: #16a34a;
        font-weight: 600;
        font-size: 0.88rem;
    }

    .status-inactive {
        color: #dc2626;
        font-weight: 600;
        font-size: 0.88rem;
    }

    /* ─── Action Buttons ─── */
    .action-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-view {
        background: transparent;
        border: none;
        padding: 6px 8px;
        border-radius: 8px;
        color: #9ca3af;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
        text-decoration: none;
        font-size: 1rem;
    }

    .btn-view:hover {
        background: #f0f4ff;
        color: #3b5bdb;
    }

    /* ─── Three Dot Dropdown ─── */
    .dropdown-dot-btn {
        background: transparent;
        border: none;
        padding: 6px 8px;
        border-radius: 8px;
        color: #9ca3af;
        cursor: pointer;
        font-size: 1.1rem;
        line-height: 1;
        transition: background 0.15s;
        position: relative;
    }

    .dropdown-dot-btn:hover {
        background: #f0f4ff;
        color: #374151;
    }

    .dropdown-menu-custom {
        position: absolute;
        right: 0;
        top: calc(100% + 6px);
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(30, 58, 138, 0.14);
        min-width: 190px;
        z-index: 999;
        overflow: hidden;
        display: none;
        border: 1px solid #eef2fb;
    }

    .dropdown-menu-custom.show {
        display: block;
        animation: dropFade 0.15s ease;
    }

    @keyframes dropFade {
        from {
            opacity: 0;
            transform: translateY(-6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-menu-custom a,
    .dropdown-menu-custom button {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 18px;
        font-size: 0.88rem;
        color: #374151;
        text-decoration: none;
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
        transition: background 0.12s;
        font-family: 'Hind Siliguri', sans-serif;
    }

    .dropdown-menu-custom a:hover,
    .dropdown-menu-custom button:hover {
        background: #f8faff;
    }

    .dropdown-menu-custom a i,
    .dropdown-menu-custom button i {
        width: 16px;
        font-size: 0.85rem;
        color: #9ca3af;
    }

    .dropdown-menu-custom .item-active i {
        color: #16a34a;
    }

    .dropdown-menu-custom .item-active {
        color: #16a34a;
        font-weight: 600;
    }

    .dropdown-menu-custom .item-delete i {
        color: #dc2626;
    }

    .dropdown-menu-custom .item-delete {
        color: #dc2626;
    }

    .dropdown-wrapper {
        position: relative;
        display: inline-block;
    }

    /* ─── Pagination ─── */
    .pagination-bar {
        padding: 14px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f0f4ff;
    }

    .pagination-bar small {
        color: #9ca3af;
        font-size: 0.85rem;
    }

    /* ─── Empty State ─── */
    .empty-state {
        padding: 50px 20px;
        text-align: center;
        color: #9ca3af;
    }

    /* ─── Responsive ─── */
    @media (max-width: 768px) {

        .user-page {
            padding: 20px 12px;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .btn-new-user {
            width: 100%;
            justify-content: center;
        }

        .search-wrapper {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-icon-btn,
        .filter-select {
            width: 100%;
        }

        .table-card {
            overflow-x: auto;
        }

        .user-table {
            min-width: 1000px;
        }

        .pagination-bar {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }
    }
</style>

<div class="user-page">

{{-- ── Header ── --}}
<div class="page-header">

    <div>
        <h5>ইউজার এন্ট্রি ও ব্যবস্থাপনা</h5>

        <small>
            সকল ইউজারদের তালিকা ও তথ্য ব্যবস্থাপনা করুন
        </small>
    </div>


    {{-- নতুন ইউজার --}}
    @if(
        auth()->user()->is_super_admin ||
        auth()->user()->is_support_admin ||
        auth()->user()->is_user_admin
    )

        <a
            href="{{ route('dashboard.user.create') }}"
            class="btn-new-user"
        >
            <i class="fas fa-user-plus"></i>
            নতুন ইউজার এন্ট্রি
        </a>

    @endif

</div>


{{-- ── Search & Filter ── --}}
<div class="search-card">

    <form
        action="{{ route('dashboard.user.index') }}"
        method="GET"
    >

        <div class="search-wrapper">

            {{-- Search --}}
            <div class="search-input-group">

                <i class="fas fa-search search-icon"></i>

                <input
                    type="text"
                    name="search"
                    placeholder="নাম, আইডি বা মোবাইল দিয়ে খুঁজুন..."
                    value="{{ request('search') }}"
                >

            </div>


            {{-- Filter Button --}}
            <button
                type="submit"
                class="filter-icon-btn"
                title="ফিল্টার"
            >
                <i class="fas fa-filter"></i>
            </button>


            {{-- Role Filter --}}
            <select
                name="user_type"
                class="filter-select"
                onchange="this.form.submit()"
            >

                <option value="">
                    সকল ধরণ
                </option>

                <option
                    value="super_admin"
                    {{ request('user_type') == 'super_admin' ? 'selected' : '' }}
                >
                    সুপার অ্যাডমিন
                </option>

                <option
                    value="support_admin"
                    {{ request('user_type') == 'support_admin' ? 'selected' : '' }}
                >
                    সাপোর্ট অ্যাডমিন
                </option>

                <option
                    value="user_admin"
                    {{ request('user_type') == 'user_admin' ? 'selected' : '' }}
                >
                    ইউজার অ্যাডমিন
                </option>

                <option
                    value="user"
                    {{ request('user_type') == 'user' ? 'selected' : '' }}
                >
                    ইউজার
                </option>

                <option
                    value="student"
                    {{ request('user_type') == 'student' ? 'selected' : '' }}
                >
                    শিক্ষার্থী
                </option>

            </select>

        </div>

    </form>

</div>


{{-- ── Table ── --}}
<div class="table-card">

    <div class="table-responsive">

        <table class="user-table">

            <thead>

                <tr>

                    <th>
                        আইডি
                    </th>

                    <th class="text-start">
                        ইউজার নাম
                    </th>

                    <th>
                        পিতার নাম
                    </th>

                    <th>
                        স্টুডেন্ট আইডি
                    </th>

                    <th>
                        মোবাইল নাম্বার
                    </th>

                    <th>
                        ইউজার ধরণ
                    </th>

                    <th>
                        স্ট্যাটাস
                    </th>

                    <th>
                        অ্যাকশন
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($users as $user)

                    @php

                        $roleSlug = $user->role->slug ?? '';

                        $isStudent = ($roleSlug === 'student');

                    @endphp


                    <tr>

                        {{-- ID --}}
                        <td>

                            <span class="id-badge">
                                {{ $user->id }}
                            </span>

                        </td>


                        {{-- Name --}}
                        <td class="text-start">

                            <div class="name-primary">

                                {{ $user->name_bn ?: $user->name }}

                            </div>


                            @if($user->name_bn)

                                <div class="name-secondary">

                                    {{ $user->name }}

                                </div>

                            @endif

                        </td>


                        {{-- Father Name --}}
                        <td>

                            @if($isStudent)

                                {{ $user->father_name ?? '—' }}

                            @else

                                —

                            @endif

                        </td>


                        {{-- Student ID --}}
                        <td>

                            @if($isStudent && $user->student)

                                <span class="id-badge">

                                    {{ $user->institution_user_id }}

                                </span>

                            @else

                                —

                            @endif

                        </td>


                        {{-- Phone --}}
                        <td>

                            <div class="phone-cell">

                                <i class="fas fa-phone"></i>

                                {{ $user->phone }}

                            </div>

                        </td>


                        {{-- Role --}}
                        <td>

                            @php

                                $roleClass = match($roleSlug) {

                                    'super_admin'   => 'role-super-admin',

                                    'support_admin' => 'role-support-admin',

                                    'user_admin'    => 'role-user-admin',

                                    'user'          => 'role-user',

                                    'student'       => 'role-student',

                                    default         => 'role-default',

                                };

                            @endphp


                            <span class="role-badge {{ $roleClass }}">

                                {{ $user->role->role_name ?? 'N/A' }}

                            </span>

                        </td>


                        {{-- Status --}}
                        <td>

                            @if((int) $user->status === 1)

                                <span class="status-active">
                                    ● সক্রিয়
                                </span>

                            @else

                                <span class="status-inactive">
                                    ● নিষ্ক্রিয়
                                </span>

                            @endif

                        </td>


                        {{-- Actions --}}
                        <td>

                            <div class="action-cell">

                                {{-- Profile View --}}
                                <a
                                    href="{{ route('dashboard.user.show', $user->id) }}"
                                    class="btn-view"
                                    title="প্রোফাইল ভিউ"
                                >
                                    <i class="fas fa-eye"></i>
                                </a>


                                {{-- Dropdown --}}
                                <div class="dropdown-wrapper">

                                    <button
                                        type="button"
                                        class="dropdown-dot-btn"
                                        onclick="toggleDropdown(this)"
                                    >
                                        ⋮
                                    </button>


                                    <div class="dropdown-menu-custom">

                                        {{-- Admission & Fee --}}
                                        @if($isStudent && $user->student)

                                            <a
                                                href="{{ route(
                                                    'dashboard.admissions.index',
                                                    ['student_id' => $user->student->id]
                                                ) }}"
                                            >

                                                <i class="fas fa-graduation-cap"></i>

                                                এডমিশন এন্ড ফি

                                            </a>

                                        @endif


                                        {{-- Profile View --}}
                                        <a
                                            href="{{ route(
                                                'dashboard.user.show',
                                                $user->id
                                            ) }}"
                                        >

                                            <i class="fas fa-eye"></i>

                                            প্রোফাইল ভিউ

                                        </a>


                                        {{-- Edit --}}
                                        <a
                                            href="{{ route(
                                                'dashboard.user.edit',
                                                $user->id
                                            ) }}"
                                        >

                                            <i class="fas fa-edit"></i>

                                            এডিট করুন

                                        </a>


                                        {{-- Toggle Status --}}
                                        <a
                                            href="#"
                                            class="item-active"
                                            onclick="
                                                event.preventDefault();
                                                document.getElementById(
                                                    'toggle-form-{{ $user->id }}'
                                                ).submit();
                                            "
                                        >

                                            <i class="fas fa-power-off"></i>

                                            @if((int) $user->status === 1)

                                                নিষ্ক্রিয় করুন

                                            @else

                                                সক্রিয় করুন

                                            @endif

                                        </a>


                                        {{-- Toggle Status Form --}}
                                        <form
                                            id="toggle-form-{{ $user->id }}"
                                            action="{{ route(
                                                'dashboard.user.toggle-status',
                                                $user->id
                                            ) }}"
                                            method="POST"
                                            style="display:none;"
                                        >

                                            @csrf

                                            @method('PATCH')

                                        </form>


                                        {{-- Delete --}}
                                        <form
                                            action="{{ route(
                                                'dashboard.user.destroy',
                                                $user->id
                                            ) }}"
                                            method="POST"
                                            style="margin:0; padding:0;"
                                            onsubmit="
                                                return confirm(
                                                    'এই ইউজার ডিলিট করবেন?'
                                                )
                                            "
                                        >

                                            @csrf

                                            @method('DELETE')


                                            <button
                                                type="submit"
                                                class="item-delete"
                                            >

                                                <i class="fas fa-trash"></i>

                                                ডিলিট করুন

                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td colspan="8">

                            <div class="empty-state">

                                <i class="fas fa-users fa-2x mb-3 d-block"></i>

                                কোনো ইউজার পাওয়া যায়নি

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- ── Pagination ── --}}
    <div class="pagination-bar">

        <small>
            মোট {{ $users->total() }} জন
        </small>

        {{ $users->withQueryString()->links('pagination::bootstrap-5') }}

    </div>

</div>

</div>

{{-- ── Dropdown JS ── --}}

<script>

    function toggleDropdown(btn) {

        const menu = btn.nextElementSibling;

        const allMenus = document.querySelectorAll(
            '.dropdown-menu-custom'
        );

        allMenus.forEach(function (m) {

            if (m !== menu) {

                m.classList.remove('show');

            }

        });

        menu.classList.toggle('show');
    }


    /*
    |--------------------------------------------------------------------------
    | Close Dropdown On Outside Click
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (e) {

        if (!e.target.closest('.dropdown-wrapper')) {

            document
                .querySelectorAll('.dropdown-menu-custom')
                .forEach(function (menu) {

                    menu.classList.remove('show');

                });

        }

    });

</script>

@endsection
