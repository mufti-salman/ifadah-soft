@extends('layouts.admin')

@section('title', 'ড্যাশবোর্ড')

@section('page')

<div class="container-fluid py-2">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-bold mb-1">
                <i class="fas fa-tachometer-alt text-primary me-2"></i>
                ড্যাশবোর্ড
            </h4>

            <p class="text-muted mb-0">
                প্রতিষ্ঠানের সার্বিক তথ্য ও কার্যক্রমের সংক্ষিপ্ত বিবরণ
            </p>

        </div>

        @if(session('institution_name_bn') || session('institution_name'))

            <span class="badge bg-primary px-3 py-2">

                <i class="fas fa-school me-1"></i>

                {{ session('institution_name_bn') ?: session('institution_name') }}

            </span>

        @endif

    </div>


    {{-- =========================================================
         ACADEMIC STATISTICS
    ========================================================== --}}

    <div class="row g-3">

        {{-- শিক্ষার্থী --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100 dashboard-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small mb-2">
                                মোট শিক্ষার্থী
                            </div>

                            <h2 class="fw-bold mb-1">
                                {{ number_format($studentsCount) }}
                            </h2>

                            <small class="text-primary">
                                নিবন্ধিত শিক্ষার্থী
                            </small>

                        </div>

                        <div class="dashboard-icon bg-primary-subtle text-primary">
                            <i class="fas fa-user-graduate"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- শিক্ষক --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100 dashboard-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small mb-2">
                                মোট শিক্ষক
                            </div>

                            <h2 class="fw-bold mb-1">
                                {{ number_format($teachersCount) }}
                            </h2>

                            <small class="text-success">
                                কর্মরত শিক্ষক
                            </small>

                        </div>

                        <div class="dashboard-icon bg-success-subtle text-success">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- পরীক্ষা --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100 dashboard-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small mb-2">
                                মোট পরীক্ষা
                            </div>

                            <h2 class="fw-bold mb-1">
                                {{ number_format($examsCount) }}
                            </h2>

                            <small class="text-warning">
                                পরীক্ষার রেকর্ড
                            </small>

                        </div>

                        <div class="dashboard-icon bg-warning-subtle text-warning">
                            <i class="fas fa-file-alt"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ফলাফল --}}
        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100 dashboard-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small mb-2">
                                মোট ফলাফল
                            </div>

                            <h2 class="fw-bold mb-1">
                                {{ number_format($resultsCount) }}
                            </h2>

                            <small class="text-info">
                                ফলাফল রেকর্ড
                            </small>

                        </div>

                        <div class="dashboard-icon bg-info-subtle text-info">
                            <i class="fas fa-chart-line"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FINANCIAL STATISTICS
    ========================================================== --}}

    <div class="row g-3 mt-1">

        {{-- মোট ফি --}}
        <div class="col-xl-4 col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="dashboard-small-icon bg-primary-subtle text-primary">

                        <i class="fas fa-money-bill-wave"></i>

                    </div>

                    <div class="ms-3">

                        <div class="text-muted small">
                            মোট ফি
                        </div>

                        <h4 class="fw-bold mb-0">
                            ৳ {{ number_format($totalFees, 2) }}
                        </h4>

                    </div>

                </div>

            </div>

        </div>


        {{-- আদায়কৃত --}}
        <div class="col-xl-4 col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="dashboard-small-icon bg-success-subtle text-success">

                        <i class="fas fa-hand-holding-usd"></i>

                    </div>

                    <div class="ms-3">

                        <div class="text-muted small">
                            আদায়কৃত ফি
                        </div>

                        <h4 class="fw-bold text-success mb-0">
                            ৳ {{ number_format($totalPaid, 2) }}
                        </h4>

                    </div>

                </div>

            </div>

        </div>


        {{-- বকেয়া --}}
        <div class="col-xl-4 col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="dashboard-small-icon bg-danger-subtle text-danger">

                        <i class="fas fa-exclamation-circle"></i>

                    </div>

                    <div class="ms-3">

                        <div class="text-muted small">
                            বকেয়া ফি
                        </div>

                        <h4 class="fw-bold text-danger mb-0">
                            ৳ {{ number_format($totalDue, 2) }}
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

        {{-- Fee Chart --}}
        <div class="col-xl-5">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-transparent border-0 pt-4 px-4">

                    <h5 class="fw-bold mb-1">

                        <i class="fas fa-chart-pie text-primary me-2"></i>

                        ফি সংগ্রহের অবস্থা

                    </h5>

                    <small class="text-muted">
                        আদায়কৃত ও বকেয়া ফি
                    </small>

                </div>

                <div class="card-body">

                    <div style="height:320px;">
                        <canvas id="feeChart"></canvas>
                    </div>

                </div>

            </div>

        </div>


        {{-- Academic Chart --}}
        <div class="col-xl-7">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-transparent border-0 pt-4 px-4">

                    <h5 class="fw-bold mb-1">

                        <i class="fas fa-chart-bar text-success me-2"></i>

                        একাডেমিক সারসংক্ষেপ

                    </h5>

                    <small class="text-muted">
                        শিক্ষার্থী, শিক্ষক, পরীক্ষা ও ফলাফলের তুলনা
                    </small>

                </div>

                <div class="card-body">

                    <div style="height:320px;">
                        <canvas id="academicChart"></canvas>
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         QUICK INFORMATION
    ========================================================== --}}

    <div class="card border-0 shadow-sm mt-4">

        <div class="card-header bg-transparent border-0 p-4">

            <h5 class="fw-bold mb-1">

                <i class="fas fa-info-circle text-primary me-2"></i>

                প্রতিষ্ঠানের সংক্ষিপ্ত তথ্য

            </h5>

            <small class="text-muted">
                ড্যাশবোর্ডের বর্তমান সারসংক্ষেপ
            </small>

        </div>

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-3 col-6">

                    <div class="p-3 bg-light rounded">

                        <small class="text-muted d-block">
                            শিক্ষার্থী
                        </small>

                        <strong class="fs-5">
                            {{ number_format($studentsCount) }}
                        </strong>

                    </div>

                </div>

                <div class="col-md-3 col-6">

                    <div class="p-3 bg-light rounded">

                        <small class="text-muted d-block">
                            শিক্ষক
                        </small>

                        <strong class="fs-5">
                            {{ number_format($teachersCount) }}
                        </strong>

                    </div>

                </div>

                <div class="col-md-3 col-6">

                    <div class="p-3 bg-light rounded">

                        <small class="text-muted d-block">
                            পরীক্ষা
                        </small>

                        <strong class="fs-5">
                            {{ number_format($examsCount) }}
                        </strong>

                    </div>

                </div>

                <div class="col-md-3 col-6">

                    <div class="p-3 bg-light rounded">

                        <small class="text-muted d-block">
                            ফলাফল
                        </small>

                        <strong class="fs-5">
                            {{ number_format($resultsCount) }}
                        </strong>

                    </div>

                </div>

            </div>

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
       FEE CHART
    ========================================================== */

    const feeCanvas =
        document.getElementById('feeChart');

    if (feeCanvas) {

        new Chart(feeCanvas, {

            type: 'doughnut',

            data: {

                labels: [
                    'আদায়কৃত ফি',
                    'বকেয়া ফি'
                ],

                datasets: [{

                    data: [
                        {{ (float) $totalPaid }},
                        {{ (float) $totalDue }}
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
       ACADEMIC CHART
    ========================================================== */

    const academicCanvas =
        document.getElementById('academicChart');

    if (academicCanvas) {

        new Chart(academicCanvas, {

            type: 'bar',

            data: {

                labels: [
                    'শিক্ষার্থী',
                    'শিক্ষক',
                    'পরীক্ষা',
                    'ফলাফল'
                ],

                datasets: [{

                    label: 'সংখ্যা',

                    data: [
                        {{ (int) $studentsCount }},
                        {{ (int) $teachersCount }},
                        {{ (int) $examsCount }},
                        {{ (int) $resultsCount }}
                    ],

                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#ffc107',
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