@extends('layouts.admin')

@section('title', 'ড্যাশবোর্ড')

@section('page')

<style>
    /* =====================================================
       DASHBOARD BASE
    ===================================================== */

    .dashboard-wrapper {
        padding: 8px 0 30px;
        color: #253047;
    }

    .dashboard-wrapper * {
        box-sizing: border-box;
    }

    .dashboard-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 28px;
    }

    .dashboard-heading h3 {
        font-size: 24px;
        font-weight: 750;
        margin: 0 0 6px;
        color: #19243a;
        letter-spacing: -.4px;
    }

    .dashboard-heading p {
        font-size: 13px;
        color: #929bad;
        margin: 0;
    }

    .dashboard-year-badge {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 10px 16px;
        border-radius: 12px;
        background: #eff4ff;
        border: 1px solid #e2eaff;
        color: #4778d8;
        font-size: 13px;
        font-weight: 650;
    }

    .dashboard-year-badge i {
        font-size: 15px;
    }


    /* =====================================================
       SECTION
    ===================================================== */

    .dashboard-section {
        margin-top: 28px;
    }

    .dashboard-section-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 17px;
    }

    .dashboard-section-heading h5 {
        font-size: 17px;
        font-weight: 750;
        margin: 0;
        color: #26334b;
    }

    .dashboard-section-heading p {
        font-size: 12px;
        color: #9aa3b2;
        margin: 5px 0 0;
    }


    /* =====================================================
       STAT CARDS
    ===================================================== */

    .dashboard-stat-card {
        position: relative;
        height: 100%;
        min-height: 170px;
        padding: 21px;
        border: 1px solid transparent;
        border-radius: 17px;
        overflow: hidden;
        transition:
            transform .28s ease,
            box-shadow .28s ease,
            border-color .28s ease;
        isolation: isolate;
    }

    .dashboard-stat-card::before {
        content: '';
        position: absolute;
        width: 125px;
        height: 125px;
        right: -42px;
        bottom: -48px;
        border-radius: 50%;
        background: rgba(255,255,255,.55);
        z-index: -1;
        transition: transform .3s ease;
    }

    .dashboard-stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 14px 32px rgba(31, 45, 75, .09);
    }

    .dashboard-stat-card:hover::before {
        transform: scale(1.25);
    }

    .dashboard-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 17px;
    }

    .dashboard-stat-icon {
        width: 47px;
        height: 47px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: transform .25s ease;
    }

    .dashboard-stat-card:hover .dashboard-stat-icon {
        transform: scale(1.08);
    }

    .dashboard-stat-label {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 7px;
        color: #667085;
    }

    .dashboard-stat-value {
        font-size: 29px;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 8px;
        color: #202c42;
        letter-spacing: -.6px;
    }

    .dashboard-stat-footer {
        font-size: 11px;
        color: #8d98aa;
        font-weight: 500;
    }


    /* =====================================================
       CARD COLOR THEMES
    ===================================================== */

    .stat-blue {
        background: #f0f5ff;
        border-color: #e5edff;
    }

    .stat-blue .dashboard-stat-icon {
        background: #dce8ff;
        color: #4778e8;
    }

    .stat-green {
        background: #effaf4;
        border-color: #e0f3e8;
    }

    .stat-green .dashboard-stat-icon {
        background: #d9f3e4;
        color: #20a66a;
    }

    .stat-purple {
        background: #f6f1ff;
        border-color: #eee5ff;
    }

    .stat-purple .dashboard-stat-icon {
        background: #e9ddff;
        color: #8960d9;
    }

    .stat-orange {
        background: #fff7ed;
        border-color: #ffedda;
    }

    .stat-orange .dashboard-stat-icon {
        background: #ffead2;
        color: #e99438;
    }

    .stat-cyan {
        background: #effafd;
        border-color: #dff3f8;
    }

    .stat-cyan .dashboard-stat-icon {
        background: #d9f2f7;
        color: #1c9eb4;
    }

    .stat-red {
        background: #fff3f3;
        border-color: #ffe5e5;
    }

    .stat-red .dashboard-stat-icon {
        background: #ffdfdf;
        color: #e45c65;
    }


    /* =====================================================
       DASHBOARD PANEL
    ===================================================== */

    .dashboard-panel {
        background: #fff;
        border: 1px solid #edf0f5;
        border-radius: 17px;
        padding: 22px;
        height: 100%;
        box-shadow: 0 3px 12px rgba(30, 50, 90, .025);
    }


    /* =====================================================
       EXAM LIST
    ===================================================== */

    .exam-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .exam-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 13px;
        background: #f8faff;
        border-radius: 13px;
        border: 1px solid #edf1fa;
        transition: all .25s ease;
    }

    .exam-item:hover {
        background: #f0f5ff;
        border-color: #dce7ff;
        transform: translateX(3px);
    }

    .exam-date {
        min-width: 49px;
        height: 51px;
        border-radius: 12px;
        background: #e8efff;
        color: #4778e8;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .exam-date strong {
        font-size: 18px;
        line-height: 1;
        font-weight: 800;
    }

    .exam-date small {
        font-size: 9px;
        margin-top: 5px;
        font-weight: 650;
    }

    .exam-details {
        flex: 1;
        min-width: 0;
    }

    .exam-details strong {
        display: block;
        font-size: 13px;
        color: #303b50;
        margin-bottom: 6px;
        font-weight: 700;
    }

    .exam-details small {
        font-size: 11px;
        color: #9aa3b2;
    }


    /* =====================================================
       STATUS BADGES
    ===================================================== */

    .dashboard-status {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-success {
        color: #15945d;
        background: #e9f9f1;
    }

    .status-warning {
        color: #d68b2d;
        background: #fff4e5;
    }

    .status-danger {
        color: #d84e58;
        background: #fff0f0;
    }

    .status-info {
        color: #3974d6;
        background: #edf3ff;
    }


    /* =====================================================
       EMPTY STATE
    ===================================================== */

    .dashboard-empty {
        text-align: center;
        padding: 35px 15px;
        color: #a0a8b6;
        font-size: 12px;
    }

    .dashboard-empty i {
        display: block;
        font-size: 29px;
        margin-bottom: 12px;
        opacity: .5;
    }


    /* =====================================================
       EXAM SUMMARY
    ===================================================== */

    .exam-summary {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-top: 23px;
        padding-top: 18px;
        border-top: 1px solid #edf0f5;
    }

    .exam-summary-item {
        flex: 1;
        padding: 12px;
        border-radius: 11px;
        background: #f8faff;
    }

    .exam-summary-item small {
        display: block;
        color: #929bad;
        font-size: 10px;
        margin-bottom: 7px;
    }

    .exam-summary-item strong {
        font-size: 19px;
        font-weight: 800;
        color: #34415a;
    }


    /* =====================================================
       SUBSCRIPTION
    ===================================================== */

    .subscription-panel {
        background: linear-gradient(135deg, #304778 0%, #405f9e 100%);
        border-radius: 17px;
        padding: 24px;
        color: #fff;
        height: 100%;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(48, 71, 120, .13);
    }

    .subscription-panel::after {
        content: '';
        position: absolute;
        width: 180px;
        height: 180px;
        right: -65px;
        top: -75px;
        border-radius: 50%;
        background: rgba(255,255,255,.06);
        pointer-events: none;
    }

    .subscription-label {
        font-size: 12px;
        opacity: .8;
        margin-bottom: 9px;
    }

    .subscription-plan {
        font-size: 24px;
        font-weight: 800;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .subscription-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        border-top: 1px solid rgba(255,255,255,.13);
        padding: 13px 0;
        font-size: 12px;
    }

    .subscription-info span:first-child {
        opacity: .72;
    }

    .subscription-info strong {
        font-weight: 650;
        text-align: right;
    }

    .subscription-progress {
        height: 7px;
        border-radius: 10px;
        background: rgba(255,255,255,.2);
        overflow: hidden;
        margin-top: 12px;
    }

    .subscription-progress div {
        height: 100%;
        background: #72e0ae;
        border-radius: 10px;
    }


    /* =====================================================
       RESPONSIVE
    ===================================================== */

    @media(max-width: 1199px) {

        .dashboard-stat-value {
            font-size: 26px;
        }

    }

    @media(max-width: 767px) {

        .dashboard-wrapper {
            padding-top: 3px;
        }

        .dashboard-heading {
            margin-bottom: 20px;
        }

        .dashboard-heading h3 {
            font-size: 20px;
        }

        .dashboard-stat-card {
            padding: 16px;
            min-height: 155px;
        }

        .dashboard-stat-icon {
            width: 42px;
            height: 42px;
            font-size: 18px;
        }

        .dashboard-stat-value {
            font-size: 24px;
        }

        .dashboard-section {
            margin-top: 22px;
        }

        .dashboard-panel {
            padding: 16px;
        }

        .subscription-panel {
            padding: 19px;
        }

        .subscription-plan {
            font-size: 21px;
        }

        .exam-summary {
            gap: 7px;
        }

        .exam-summary-item {
            padding: 9px;
        }

        .exam-summary-item strong {
            font-size: 17px;
        }

    }

    @media (prefers-reduced-motion: reduce) {

        .dashboard-stat-card,
        .dashboard-stat-icon,
        .exam-item {
            transition: none;
        }

    }

</style>


<div class="dashboard-wrapper">

    {{-- =====================================================
         DASHBOARD HEADER
    ====================================================== --}}

    <div class="dashboard-heading">

        <div>
            <h3>ড্যাশবোর্ড</h3>
            <p>আপনার প্রতিষ্ঠানের সামগ্রিক কার্যক্রম ও পরিসংখ্যান</p>
        </div>

        @if($currentAcademicYear)

            <div class="dashboard-year-badge">
                <i class="fas fa-calendar-alt"></i>

                <span>
                    শিক্ষাবর্ষ:
                    {{ $currentAcademicYear->full_name }}
                </span>
            </div>

        @else

        @endif

    </div>


    {{-- =====================================================
         ACADEMIC STATISTICS
    ====================================================== --}}

    <div class="dashboard-section">

        <div class="dashboard-section-heading">

            <div>
                <h5>একাডেমিক পরিসংখ্যান</h5>
                <p>প্রতিষ্ঠানের বর্তমান একাডেমিক তথ্য</p>
            </div>

        </div>


        <div class="row g-3">

            {{-- Students --}}

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">

                <div class="dashboard-stat-card stat-blue">

                    <div class="dashboard-stat-top">

                        <div class="dashboard-stat-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>

                    </div>

                    <div class="dashboard-stat-label">
                        মোট শিক্ষার্থী
                    </div>

                    <div class="dashboard-stat-value">
                        {{ number_format($studentsCount) }}
                    </div>

                    <div class="dashboard-stat-footer">
                        প্রতিষ্ঠানে নিবন্ধিত শিক্ষার্থী
                    </div>

                </div>

            </div>


            {{-- Teachers --}}

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">

                <div class="dashboard-stat-card stat-green">

                    <div class="dashboard-stat-top">

                        <div class="dashboard-stat-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>

                    </div>

                    <div class="dashboard-stat-label">
                        মোট শিক্ষক
                    </div>

                    <div class="dashboard-stat-value">
                        {{ number_format($teachersCount) }}
                    </div>

                    <div class="dashboard-stat-footer">
                        নিবন্ধিত শিক্ষক ও প্রশিক্ষক
                    </div>

                </div>

            </div>


            {{-- Classes --}}

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">

                <div class="dashboard-stat-card stat-purple">

                    <div class="dashboard-stat-top">

                        <div class="dashboard-stat-icon">
                            <i class="fas fa-school"></i>
                        </div>

                    </div>

                    <div class="dashboard-stat-label">
                        মোট শ্রেণি
                    </div>

                    <div class="dashboard-stat-value">
                        {{ number_format($classesCount) }}
                    </div>

                    <div class="dashboard-stat-footer">
                        প্রতিষ্ঠানের সকল শ্রেণি
                    </div>

                </div>

            </div>


            {{-- Sections --}}

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">

                <div class="dashboard-stat-card stat-orange">

                    <div class="dashboard-stat-top">

                        <div class="dashboard-stat-icon">
                            <i class="fas fa-layer-group"></i>
                        </div>

                    </div>

                    <div class="dashboard-stat-label">
                        মোট শাখা
                    </div>

                    <div class="dashboard-stat-value">
                        {{ number_format($sectionsCount) }}
                    </div>

                    <div class="dashboard-stat-footer">
                        শ্রেণিভিত্তিক শাখার সংখ্যা
                    </div>

                </div>

            </div>


            {{-- Exams --}}

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">

                <div class="dashboard-stat-card stat-cyan">

                    <div class="dashboard-stat-top">

                        <div class="dashboard-stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>

                    </div>

                    <div class="dashboard-stat-label">
                        মোট পরীক্ষা
                    </div>

                    <div class="dashboard-stat-value">
                        {{ number_format($examsCount) }}
                    </div>

                    <div class="dashboard-stat-footer">
                        সকল নির্ধারিত পরীক্ষা
                    </div>

                </div>

            </div>


            {{-- Results --}}

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">

                <div class="dashboard-stat-card stat-purple">

                    <div class="dashboard-stat-top">

                        <div class="dashboard-stat-icon">
                            <i class="fas fa-poll"></i>
                        </div>

                    </div>

                    <div class="dashboard-stat-label">
                        মোট ফলাফল
                    </div>

                    <div class="dashboard-stat-value">
                        {{ number_format($resultsCount) }}
                    </div>

                    <div class="dashboard-stat-footer">
                        সংরক্ষিত ফলাফল রেকর্ড
                    </div>

                </div>

            </div>


            {{-- Admissions --}}

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">

                <div class="dashboard-stat-card stat-green">

                    <div class="dashboard-stat-top">

                        <div class="dashboard-stat-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>

                    </div>

                    <div class="dashboard-stat-label">
                        মোট ভর্তি
                    </div>

                    <div class="dashboard-stat-value">
                        {{ number_format($admissionsCount) }}
                    </div>

                    <div class="dashboard-stat-footer">
                        সর্বমোট ভর্তি রেকর্ড
                    </div>

                </div>

            </div>


            {{-- Active Admissions --}}

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">

                <div class="dashboard-stat-card stat-orange">

                    <div class="dashboard-stat-top">

                        <div class="dashboard-stat-icon">
                            <i class="fas fa-user-check"></i>
                        </div>

                    </div>

                    <div class="dashboard-stat-label">
                        সক্রিয় ভর্তি
                    </div>

                    <div class="dashboard-stat-value">
                        {{ number_format($activeAdmissionsCount) }}
                    </div>

                    <div class="dashboard-stat-footer">
                        বর্তমানে অধ্যয়নরত শিক্ষার্থী
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         UPCOMING EXAMS & SUBSCRIPTION
    ====================================================== --}}

    <div class="dashboard-section">

        <div class="row g-3">

            {{-- Upcoming Exams --}}

            <div class="col-xl-7 col-lg-7">

                <div class="dashboard-panel">

                    <div class="dashboard-section-heading">

                        <div>
                            <h5>আসন্ন পরীক্ষা</h5>
                            <p>সামনে অনুষ্ঠিতব্য পরীক্ষার তালিকা</p>
                        </div>

                        <span class="dashboard-status status-info">
                            {{ number_format($upcomingExamsCount) }} টি আসন্ন
                        </span>

                    </div>


                    <div class="exam-list">

                        @forelse($upcomingExams as $exam)

                            <div class="exam-item">

                                <div class="exam-date">

                                    <strong>
                                        {{ $exam->start_date->format('d') }}
                                    </strong>

                                    <small>
                                        {{ $exam->start_date->format('M') }}
                                    </small>

                                </div>


                                <div class="exam-details">

                                    <strong>
                                        {{ $exam->name_bn ?: $exam->name }}
                                    </strong>

                                    <small>

                                        <i class="far fa-calendar me-1"></i>

                                        {{ $exam->start_date->format('d M Y') }}

                                        @if($exam->end_date)

                                            - {{ $exam->end_date->format('d M Y') }}

                                        @endif

                                    </small>

                                </div>


                                <span class="dashboard-status status-info">
                                    আসন্ন
                                </span>

                            </div>

                        @empty

                            <div class="dashboard-empty">

                                <i class="fas fa-calendar-check"></i>

                                বর্তমানে কোনো আসন্ন পরীক্ষা নেই।

                            </div>

                        @endforelse

                    </div>


                    {{-- Exam Summary --}}

                    <div class="exam-summary">

                        <div class="exam-summary-item">

                            <small>চলমান পরীক্ষা</small>

                            <strong class="text-primary">
                                {{ number_format($ongoingExamsCount) }}
                            </strong>

                        </div>


                        <div class="exam-summary-item">

                            <small>সমাপ্ত পরীক্ষা</small>

                            <strong class="text-success">
                                {{ number_format($completedExamsCount) }}
                            </strong>

                        </div>


                        <div class="exam-summary-item">

                            <small>মোট পরীক্ষা</small>

                            <strong>
                                {{ number_format($examsCount) }}
                            </strong>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Subscription --}}

            <div class="col-xl-5 col-lg-5">

                <div class="subscription-panel">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <div class="subscription-label">
                            <i class="fas fa-crown me-1"></i>
                            সাবস্ক্রিপশন তথ্য
                        </div>


                        @if($currentSubscription)

                            @if(in_array($currentSubscription->status, ['active', 'trialing'], true))

                                <span class="dashboard-status status-success">

                                    {{ $currentSubscription->status === 'trialing' ? 'ট্রায়াল' : 'সক্রিয়' }}

                                </span>

                            @else

                                <span class="dashboard-status status-danger">
                                    {{ ucfirst($currentSubscription->status) }}
                                </span>

                            @endif

                        @endif

                    </div>


                    @if($currentSubscription)

                        <div class="subscription-plan">

                            {{ optional($currentSubscription->plan)->name ?? 'নির্ধারিত প্যাকেজ' }}

                        </div>


                        <div class="subscription-info">

                            <span>বিলিং পদ্ধতি</span>

                            <strong>
                                {{ ucfirst($currentSubscription->billing_cycle ?? 'N/A') }}
                            </strong>

                        </div>


                        <div class="subscription-info">

                            <span>শুরুর তারিখ</span>

                            <strong>

                                {{ $currentSubscription->starts_at
                                    ? $currentSubscription->starts_at->format('d M Y')
                                    : 'N/A' }}

                            </strong>

                        </div>


                        <div class="subscription-info">

                            <span>মেয়াদ শেষ</span>

                            <strong>

                                {{ $currentSubscription->ends_at
                                    ? $currentSubscription->ends_at->format('d M Y')
                                    : 'নির্দিষ্ট নয়' }}

                            </strong>

                        </div>


                        <div class="subscription-info">

                            <span>অবশিষ্ট সময়</span>

                            <strong>

                                @if($subscriptionRemainingDays !== null)

                                    {{ $subscriptionRemainingDays }} দিন

                                @else

                                    নির্দিষ্ট নয়

                                @endif

                            </strong>

                        </div>


                        @if($subscriptionRemainingDays !== null)

                            <div class="subscription-progress">

                                <div style="width: {{ min(100, max(0, ($subscriptionRemainingDays / 365) * 100)) }}%"></div>

                            </div>

                        @endif


                    @else

                        <div class="subscription-plan">
                            কোনো প্যাকেজ নেই
                        </div>

                        <p class="small mb-0" style="opacity:.75">

                            এই প্রতিষ্ঠানের জন্য বর্তমানে কোনো সাবস্ক্রিপশন পাওয়া যায়নি।

                        </p>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@endsection