@extends('layouts.admin')

@section('title', 'ড্যাশবোর্ড')

@section('page')

<div class="container-fluid py-2">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="fas fa-tachometer-alt text-primary me-2"></i>
                সুপার অ্যাডমিন ড্যাশবোর্ড
            </h4>

            <p class="text-muted mb-0">
                সম্পূর্ণ সিস্টেমের সার্বিক তথ্য ও পরিসংখ্যান
            </p>
        </div>

        <div class="mt-2 mt-md-0">
            <span class="badge bg-primary px-3 py-2">
                <i class="fas fa-shield-alt me-1"></i>
                সিস্টেম প্রশাসন
            </span>
        </div>

    </div>


    {{-- =========================================================
         MAIN STATISTICS
    ========================================================== --}}

    <div class="row g-3">

        {{-- মোট প্রতিষ্ঠান --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100 dashboard-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small mb-2">
                                মোট প্রতিষ্ঠান
                            </div>

                            <h2 class="fw-bold mb-1">
                                {{ number_format($institutionsCount) }}
                            </h2>

                            <small class="text-primary">
                                নিবন্ধিত প্রতিষ্ঠান
                            </small>

                        </div>

                        <div class="dashboard-icon bg-primary-subtle text-primary">
                            <i class="fas fa-school"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- সক্রিয় প্রতিষ্ঠান --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100 dashboard-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small mb-2">
                                সক্রিয় প্রতিষ্ঠান
                            </div>

                            <h2 class="fw-bold mb-1">
                                {{ number_format($activeInstitutionsCount) }}
                            </h2>

                            <small class="text-success">
                                বর্তমানে সক্রিয়
                            </small>

                        </div>

                        <div class="dashboard-icon bg-success-subtle text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- নিষ্ক্রিয় প্রতিষ্ঠান --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100 dashboard-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small mb-2">
                                নিষ্ক্রিয় প্রতিষ্ঠান
                            </div>

                            <h2 class="fw-bold mb-1">
                                {{ number_format($inactiveInstitutionsCount) }}
                            </h2>

                            <small class="text-danger">
                                বর্তমানে নিষ্ক্রিয়
                            </small>

                        </div>

                        <div class="dashboard-icon bg-danger-subtle text-danger">
                            <i class="fas fa-times-circle"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- মোট ব্যবহারকারী --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100 dashboard-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small mb-2">
                                মোট ব্যবহারকারী
                            </div>

                            <h2 class="fw-bold mb-1">
                                {{ number_format($totalUsers) }}
                            </h2>

                            <small class="text-info">
                                সকল ব্যবহারকারী
                            </small>

                        </div>

                        <div class="dashboard-icon bg-info-subtle text-info">
                            <i class="fas fa-users"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         SECOND STATISTICS
    ========================================================== --}}

    <div class="row g-3 mt-1">

        {{-- শিক্ষার্থী --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="dashboard-small-icon bg-warning-subtle text-warning">
                        <i class="fas fa-user-graduate"></i>
                    </div>

                    <div class="ms-3">

                        <div class="text-muted small">
                            মোট শিক্ষার্থী
                        </div>

                        <h4 class="fw-bold mb-0">
                            {{ number_format($studentsCount) }}
                        </h4>

                    </div>

                </div>

            </div>

        </div>


        {{-- শিক্ষক --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="dashboard-small-icon bg-success-subtle text-success">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>

                    <div class="ms-3">

                        <div class="text-muted small">
                            মোট শিক্ষক
                        </div>

                        <h4 class="fw-bold mb-0">
                            {{ number_format($teachersCount) }}
                        </h4>

                    </div>

                </div>

            </div>

        </div>


        {{-- Super Admin --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="dashboard-small-icon bg-primary-subtle text-primary">
                        <i class="fas fa-user-shield"></i>
                    </div>

                    <div class="ms-3">

                        <div class="text-muted small">
                            সুপার অ্যাডমিন
                        </div>

                        <h4 class="fw-bold mb-0">
                            {{ number_format($superAdminsCount) }}
                        </h4>

                    </div>

                </div>

            </div>

        </div>


        {{-- Support Admin --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="dashboard-small-icon bg-info-subtle text-info">
                        <i class="fas fa-headset"></i>
                    </div>

                    <div class="ms-3">

                        <div class="text-muted small">
                            সাপোর্ট অ্যাডমিন
                        </div>

                        <h4 class="fw-bold mb-0">
                            {{ number_format($supportAdminsCount) }}
                        </h4>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         CHARTS
    ========================================================== --}}

    <div class="row g-3 mt-3">

        {{-- Pie Chart --}}
        <div class="col-xl-5">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-transparent border-0 pt-4 px-4">

                    <h5 class="fw-bold mb-1">
                        <i class="fas fa-chart-pie text-primary me-2"></i>
                        প্রতিষ্ঠানের অবস্থা
                    </h5>

                    <small class="text-muted">
                        সক্রিয় ও নিষ্ক্রিয় প্রতিষ্ঠানের তুলনা
                    </small>

                </div>

                <div class="card-body">

                    <div style="height:320px;">
                        <canvas id="institutionStatusChart"></canvas>
                    </div>

                </div>

            </div>

        </div>


        {{-- Bar Chart --}}
        <div class="col-xl-7">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-transparent border-0 pt-4 px-4">

                    <h5 class="fw-bold mb-1">
                        <i class="fas fa-chart-bar text-success me-2"></i>
                        সিস্টেমের সারসংক্ষেপ
                    </h5>

                    <small class="text-muted">
                        সিস্টেমের গুরুত্বপূর্ণ সংখ্যাগুলোর তুলনা
                    </small>

                </div>

                <div class="card-body">

                    <div style="height:320px;">
                        <canvas id="systemOverviewChart"></canvas>
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         RECENT INSTITUTIONS
    ========================================================== --}}

    <div class="card border-0 shadow-sm mt-4">

        <div class="card-header bg-transparent border-0 p-4">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="fw-bold mb-1">
                        <i class="fas fa-school text-primary me-2"></i>
                        সাম্প্রতিক প্রতিষ্ঠান
                    </h5>

                    <small class="text-muted">
                        সর্বশেষ যুক্ত হওয়া প্রতিষ্ঠান
                    </small>

                </div>

                <a
                    href="{{ route('dashboard.institutions.index') }}"
                    class="btn btn-sm btn-outline-primary">

                    সব প্রতিষ্ঠান
                    <i class="fas fa-arrow-right ms-1"></i>

                </a>

            </div>

        </div>


        <div class="card-body p-0">

            @if($recentInstitutions->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th class="px-4">#</th>
                                <th>প্রতিষ্ঠান</th>
                                <th>কোড</th>
                                <th>স্ট্যাটাস</th>
                                <th>তৈরির তারিখ</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($recentInstitutions as $institution)

                                <tr>

                                    <td class="px-4">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>

                                        <div class="fw-semibold">

                                            {{ $institution->name_bn ?: $institution->name }}

                                        </div>

                                        @if($institution->name_bn && $institution->name)

                                            <small class="text-muted">
                                                {{ $institution->name }}
                                            </small>

                                        @endif

                                    </td>

                                    <td>

                                        {{ $institution->madrasa_code ?: '—' }}

                                    </td>

                                    <td>

                                        @if($institution->status)

                                            <span class="badge bg-success-subtle text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                সক্রিয়
                                            </span>

                                        @else

                                            <span class="badge bg-danger-subtle text-danger">
                                                <i class="fas fa-times-circle me-1"></i>
                                                নিষ্ক্রিয়
                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        {{ optional($institution->created_at)->format('d M Y') }}

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="text-center text-muted py-5">

                    <i class="fas fa-school fa-2x mb-3"></i>

                    <p class="mb-0">
                        এখনো কোনো প্রতিষ্ঠান পাওয়া যায়নি।
                    </p>

                </div>

            @endif

        </div>

    </div>

</div>


<style>

.dashboard-card {
    transition: all .25s ease;
}

.dashboard-card:hover {
    transform: translateY(-3px);
}

.dashboard-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 23px;
}

.dashboard-small-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
}

</style>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    if (typeof Chart === 'undefined') {
        console.error('Chart.js পাওয়া যায়নি।');
        return;
    }


    /* =========================================================
       Institution Status Chart
    ========================================================== */

    const institutionCanvas =
        document.getElementById('institutionStatusChart');

    if (institutionCanvas) {

        new Chart(institutionCanvas, {

            type: 'doughnut',

            data: {

                labels: [
                    'সক্রিয় প্রতিষ্ঠান',
                    'নিষ্ক্রিয় প্রতিষ্ঠান'
                ],

                datasets: [{

                    data: [
                        {{ (int) $activeInstitutionsCount }},
                        {{ (int) $inactiveInstitutionsCount }}
                    ],

                    backgroundColor: [
                        '#198754',
                        '#dc3545'
                    ],

                    borderWidth: 0

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '65%',

                plugins: {

                    legend: {
                        position: 'bottom'
                    }

                }

            }

        });

    }


    /* =========================================================
       System Overview Chart
    ========================================================== */

    const overviewCanvas =
        document.getElementById('systemOverviewChart');

    if (overviewCanvas) {

        new Chart(overviewCanvas, {

            type: 'bar',

            data: {

                labels: [
                    'ব্যবহারকারী',
                    'শিক্ষার্থী',
                    'শিক্ষক',
                    'সুপার অ্যাডমিন',
                    'সাপোর্ট অ্যাডমিন'
                ],

                datasets: [{

                    label: 'সংখ্যা',

                    data: [
                        {{ (int) $totalUsers }},
                        {{ (int) $studentsCount }},
                        {{ (int) $teachersCount }},
                        {{ (int) $superAdminsCount }},
                        {{ (int) $supportAdminsCount }}
                    ],

                    backgroundColor: [
                        '#0d6efd',
                        '#ffc107',
                        '#198754',
                        '#6f42c1',
                        '#0dcaf0'
                    ],

                    borderRadius: 7

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        display: false
                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        }

                    }

                }

            }

        });

    }

});

</script>

@endpush