@extends('layouts.admin')

@section('page-title', 'ব্যবহারকারী প্রতিবেদন')

@section('page')

@php
    $filters = $validated ?? [];

    $genderLabels = [
        'male' => 'পুরুষ',
        'female' => 'মহিলা',
        'all' => 'সকল',
    ];

    $statusLabels = [
        'active' => 'সক্রিয়',
        'inactive' => 'নিষ্ক্রিয়',
        'all' => 'সকল',
    ];

    $genderLabel = $genderLabels[$filters['gender'] ?? 'all'] ?? 'সকল';
    $statusLabel = $statusLabels[$filters['status'] ?? 'all'] ?? 'সকল';

    $madrasaName = $institution->display_name ?: 'মাদরাসার নাম';
    $madrasaAddress = $institution->address ?? '';
    $madrasaPhone = $institution->phone ?? '';

    $logoUrl = null;

    if ($institution->logo) {
        $logoUrl = \Illuminate\Support\Str::startsWith(
            $institution->logo,
            ['http://', 'https://']
        )
            ? $institution->logo
            : \Illuminate\Support\Facades\Storage::url($institution->logo);
    }

    $printedAt = now()->format('d-m-Y h:i A');

    $student = $selectedStudent;
@endphp

<style>
    .user-report-page {
        max-width: 1200px;
        margin: 0 auto;
    }

    .report-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .report-toolbar .right-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .report-sheet {
        background: #fff;
        color: #172033;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 28px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, .06);
    }

    .report-header {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 18px;
        text-align: center;
        border-bottom: 2px solid #334155;
        padding-bottom: 18px;
        margin-bottom: 20px;
    }

    .report-logo {
        width: 80px;
        height: 80px;
        object-fit: contain;
        flex-shrink: 0;
    }

    .report-institution-name {
        margin: 0 0 7px;
        font-size: 25px;
        font-weight: 700;
        color: #172554;
    }

    .report-institution-info {
        margin: 3px 0;
        font-size: 13px;
        line-height: 1.7;
    }

    .report-title {
        text-align: center;
        font-size: 20px;
        font-weight: 700;
        margin: 20px 0;
        color: #172554;
    }

    .report-meta {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        font-size: 12px;
        margin-bottom: 14px;
        color: #475569;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
    }

    .report-table th,
    .report-table td {
        border: 1px solid #cbd5e1;
        padding: 8px;
        font-size: 12px;
        vertical-align: middle;
    }

    .report-table th {
        text-align: center;
        background: #eef2f7;
        color: #172033;
        font-weight: 700;
    }

    .report-table td.center {
        text-align: center;
    }

    .report-empty {
        padding: 25px !important;
        text-align: center;
        color: #64748b;
    }

    .statistics-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin: 22px 0;
    }

    .statistics-card {
        border: 1px solid #dbe3ee;
        background: #f8fafc;
        border-radius: 8px;
        padding: 18px;
        text-align: center;
    }

    .statistics-label {
        color: #64748b;
        font-size: 13px;
        margin-bottom: 8px;
    }

    .statistics-value {
        font-size: 27px;
        font-weight: 700;
        color: #172554;
    }

    .admission-photo-row {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 15px;
    }

    .student-photo {
        width: 100px;
        height: 120px;
        object-fit: cover;
        border: 1px solid #cbd5e1;
        padding: 3px;
    }

    .admission-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0 24px;
    }

    .admission-field {
        padding: 12px 4px;
        min-height: 44px;
        border-bottom: 1px dotted #94a3b8;
        font-size: 13px;
        overflow-wrap: anywhere;
    }

    .admission-field strong {
        margin-right: 5px;
    }

    .report-signatures {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-top: 65px;
        page-break-inside: avoid;
    }

    .report-signature {
        width: 190px;
        text-align: center;
        padding-top: 8px;
        border-top: 1px solid #475569;
        font-size: 12px;
    }

    .report-footer {
        border-top: 1px solid #cbd5e1;
        margin-top: 28px;
        padding-top: 10px;
        text-align: center;
        color: #64748b;
        font-size: 11px;
    }

    @media (max-width: 768px) {
        .report-sheet {
            padding: 14px;
            overflow-x: auto;
        }

        .report-header {
            flex-direction: column;
        }

        .report-institution-name {
            font-size: 20px;
        }

        .statistics-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .admission-form-grid {
            grid-template-columns: 1fr;
        }
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body * {
            visibility: hidden !important;
        }

        #printable-report,
        #printable-report * {
            visibility: visible !important;
        }

        #printable-report {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            border: none;
            border-radius: 0;
            box-shadow: none;
        }

        .no-print {
            display: none !important;
        }

        .report-sheet {
            border: none;
            box-shadow: none;
        }

        .report-table thead {
            display: table-header-group;
        }

        .report-table tr {
            page-break-inside: avoid;
        }
    }
</style>

<div class="user-report-page">

    {{-- Toolbar --}}
    <div class="report-toolbar no-print">

        <a href="{{ route('dashboard.user-reports.index') }}"
           class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            ফিরে যান
        </a>

        <div class="right-actions">
            <button type="button"
                    class="btn btn-outline-primary"
                    onclick="window.print()">
                <i class="fas fa-print"></i>
                প্রিন্ট
            </button>

            <button type="button"
                    class="btn btn-primary"
                    id="download-pdf-button"
                    onclick="downloadReportPdf()">
                <i class="fas fa-file-pdf"></i>
                PDF ডাউনলোড
            </button>
        </div>

    </div>

    {{-- Printable Area --}}
    <div class="report-sheet" id="printable-report">

        {{-- Institution Header --}}
        <div class="report-header">

            @if($logoUrl)
                <img src="{{ $logoUrl }}"
                     alt="মাদরাসার লোগো"
                     class="report-logo">
            @endif

            <div>
                <h1 class="report-institution-name">
                    {{ $madrasaName }}
                </h1>

                @if($madrasaAddress)
                    <p class="report-institution-info">
                        ঠিকানা: {{ $madrasaAddress }}
                    </p>
                @endif

                @if($madrasaPhone)
                    <p class="report-institution-info">
                        মোবাইল: {{ $madrasaPhone }}
                    </p>
                @endif
            </div>

        </div>

        <h2 class="report-title">
            {{ $reportTitle }}
        </h2>

        <div class="report-meta">
            <span>
                রিপোর্টের তারিখ: {{ now()->format('d-m-Y') }}
            </span>

            <span>
                @if($reportType === 1)
                    মোট রেকর্ড: {{ $users->count() }}
                @elseif($reportType === 2)
                    মোট ব্যবহারকারী: {{ $statistics['total'] ?? 0 }}
                @elseif($reportType === 3)
                    মোট রেকর্ড: {{ $student ? 1 : 0 }}
                @elseif($reportType === 4)
                    মোট রেকর্ড: {{ $students->count() }}
                @endif
            </span>
        </div>

        {{-- ================================================== --}}
        {{-- Report 1: User Data List                           --}}
        {{-- ================================================== --}}

        @if($reportType === 1)

            <div class="report-meta">
                <span>
                    ব্যবহারকারীর ধরন:
                    @php
                        $typeLabels = [
                            'student' => 'শিক্ষার্থী',
                            'teacher' => 'শিক্ষক',
                            'guardian' => 'অভিভাবক',
                            'user' => 'সাধারণ ব্যবহারকারী',
                            'user_admin' => 'ইউজার অ্যাডমিন',
                            'support_admin' => 'সাপোর্ট অ্যাডমিন',
                        ];
                    @endphp

                    {{ $typeLabels[$filters['user_type'] ?? '']
                        ?? 'সকল' }}
                </span>

                <span>লিঙ্গ: {{ $genderLabel }}</span>
                <span>অবস্থা: {{ $statusLabel }}</span>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th>ক্রমিক</th>
                        <th>আইডি নং</th>
                        <th>ইউজার নাম</th>
                        <th>ধরন</th>
                        <th>লিঙ্গ</th>
                        <th>মোবাইল</th>
                        <th>অবস্থা</th>
                        <th>এন্ট্রি তারিখ</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="center">{{ $loop->iteration }}</td>

                            <td class="center">
                                {{ $user->institution_user_id
                                    ?: $user->custom_id
                                    ?: $user->id }}
                            </td>

                            <td>
                                {{ $user->name_bn ?: $user->name ?: '—' }}
                            </td>

                            <td>
                                {{ $user->report_type_label ?? 'ব্যবহারকারী' }}
                            </td>

                            <td class="center">
                                @switch($user->gender)
                                    @case('male')
                                        পুরুষ
                                        @break

                                    @case('female')
                                        মহিলা
                                        @break

                                    @default
                                        —
                                @endswitch
                            </td>

                            <td>{{ $user->phone ?: '—' }}</td>

                            <td class="center">
                                {{ (int) $user->status === 1
                                    ? 'সক্রিয়'
                                    : 'নিষ্ক্রিয়' }}
                            </td>

                            <td class="center">
                                {{ $user->created_at
                                    ? $user->created_at->format('d-m-Y')
                                    : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="report-empty">
                                কোনো ব্যবহারকারীর তথ্য পাওয়া যায়নি।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        {{-- ================================================== --}}
        {{-- Report 2: User Statistics                          --}}
        {{-- ================================================== --}}

        @elseif($reportType === 2)

            <div class="report-meta">
                <span>লিঙ্গ: {{ $genderLabel }}</span>
                <span>অবস্থা: {{ $statusLabel }}</span>
            </div>

            <div class="statistics-grid">

                <div class="statistics-card">
                    <div class="statistics-label">মোট ব্যবহারকারী</div>
                    <div class="statistics-value">
                        {{ $statistics['total'] ?? 0 }}
                    </div>
                </div>

                <div class="statistics-card">
                    <div class="statistics-label">সক্রিয় ব্যবহারকারী</div>
                    <div class="statistics-value">
                        {{ $statistics['active'] ?? 0 }}
                    </div>
                </div>

                <div class="statistics-card">
                    <div class="statistics-label">নিষ্ক্রিয় ব্যবহারকারী</div>
                    <div class="statistics-value">
                        {{ $statistics['inactive'] ?? 0 }}
                    </div>
                </div>

                <div class="statistics-card">
                    <div class="statistics-label">পুরুষ</div>
                    <div class="statistics-value">
                        {{ $statistics['male'] ?? 0 }}
                    </div>
                </div>

                <div class="statistics-card">
                    <div class="statistics-label">মহিলা</div>
                    <div class="statistics-value">
                        {{ $statistics['female'] ?? 0 }}
                    </div>
                </div>

            </div>

            @if(!empty($statistics['by_role']))
                <h3 style="font-size: 16px; margin-top: 25px;">
                    ব্যবহারকারীর ধরন অনুযায়ী পরিসংখ্যান
                </h3>

                <table class="report-table">
                    <thead>
                        <tr>
                            <th>ক্রমিক</th>
                            <th>ব্যবহারকারীর ধরন</th>
                            <th>সংখ্যা</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($statistics['by_role'] as $type => $count)
                            <tr>
                                <td class="center">{{ $loop->iteration }}</td>
                                <td>{{ $type }}</td>
                                <td class="center">{{ $count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        {{-- ================================================== --}}
        {{-- Report 3: Student Admission Form                   --}}
        {{-- ================================================== --}}

        @elseif($reportType === 3)

            @if($student)

                @php
                    $studentUser = $student->user;
                    $latestAdmission = $student->admissions->first();

                    $studentPhotoUrl = null;

                    if ($studentUser?->photo) {
                        $studentPhotoUrl = \Illuminate\Support\Str::startsWith(
                            $studentUser->photo,
                            ['http://', 'https://']
                        )
                            ? $studentUser->photo
                            : \Illuminate\Support\Facades\Storage::url(
                                $studentUser->photo
                            );
                    }
                @endphp

                <div class="admission-photo-row">
                    @if($studentPhotoUrl)
                        <img src="{{ $studentPhotoUrl }}"
                             class="student-photo"
                             alt="শিক্ষার্থীর ছবি">
                    @endif
                </div>

                <div class="admission-form-grid">

                    <div class="admission-field">
                        <strong>শিক্ষার্থীর নাম:</strong>
                        {{ $studentUser?->name_bn
                            ?: $studentUser?->name
                            ?: '—' }}
                    </div>

                    <div class="admission-field">
                        <strong>শিক্ষার্থী আইডি:</strong>
                        {{ $studentUser?->institution_user_id
                            ?: $student->student_id
                            ?: '—' }}
                    </div>

                    <div class="admission-field">
                        <strong>ভর্তি নম্বর:</strong>
                        {{ $latestAdmission?->admission_no ?? '—' }}
                    </div>

                    <div class="admission-field">
                        <strong>ভর্তির তারিখ:</strong>
                        {{ $latestAdmission?->admission_date
                            ? $latestAdmission->admission_date->format('d-m-Y')
                            : '—' }}
                    </div>

                    <div class="admission-field">
                        <strong>পিতার নাম:</strong>
                        {{ $studentUser?->father_name ?: '—' }}
                    </div>

                    <div class="admission-field">
                        <strong>মাতার নাম:</strong>
                        {{ $studentUser?->mother_name ?: '—' }}
                    </div>

                    <div class="admission-field">
                        <strong>মোবাইল:</strong>
                        {{ $studentUser?->phone ?: '—' }}
                    </div>

                    <div class="admission-field">
                        <strong>লিঙ্গ:</strong>
                        {{ $genderLabels[$studentUser?->gender ?? ''] ?? '—' }}
                    </div>

                    <div class="admission-field">
                        <strong>শ্রেণি:</strong>
                        {{ $student->class?->name_bn
                            ?? $student->class?->name
                            ?? '—' }}
                    </div>

                    <div class="admission-field">
                        <strong>শিক্ষাবর্ষ:</strong>
                        {{ $student->academicYear?->name_bn
                            ?? $student->academicYear?->name
                            ?? $student->academicYear?->year
                            ?? '—' }}
                    </div>

                    <div class="admission-field">
                        <strong>অভিভাবকের নাম:</strong>
                        {{ $student->guardian?->name_bn
                            ?? $student->guardian?->name
                            ?? $studentUser?->guardian_name
                            ?? '—' }}
                    </div>

                    <div class="admission-field">
                        <strong>অভিভাবকের মোবাইল:</strong>
                        {{ $student->guardian?->phone
                            ?? $studentUser?->guardian_phone
                            ?? '—' }}
                    </div>

                    <div class="admission-field">
                        <strong>ভর্তির ধরন:</strong>
                        @switch($latestAdmission?->admission_type)
                            @case('new')
                                নতুন
                                @break

                            @case('old')
                                পুরাতন
                                @break

                            @default
                                —
                        @endswitch
                    </div>

                    <div class="admission-field">
                        <strong>আর্থিক অবস্থা:</strong>
                        @switch($latestAdmission?->financial_status)
                            @case('solvent')
                                সচ্ছল
                                @break

                            @case('insolvent')
                                অসচ্ছল
                                @break

                            @case('orphan')
                                এতিম
                                @break

                            @case('helpless')
                                অসহায়
                                @break

                            @default
                                —
                        @endswitch
                    </div>

                    <div class="admission-field">
                        <strong>আবাসিক অবস্থা:</strong>
                        @switch($latestAdmission?->residence_status)
                            @case('resident')
                                আবাসিক
                                @break

                            @case('non-resident')
                                অনাবাসিক
                                @break

                            @case('daycare')
                                ডে-কেয়ার
                                @break

                            @case('nightcare')
                                নাইট-কেয়ার
                                @break

                            @default
                                —
                        @endswitch
                    </div>

                    <div class="admission-field">
                        <strong>ভর্তি অবস্থা:</strong>
                        {{ $latestAdmission?->status_label
                            ?? $latestAdmission?->status
                            ?? '—' }}
                    </div>

                </div>

                <div class="report-signatures">
                    <div class="report-signature">অভিভাবকের স্বাক্ষর</div>
                    <div class="report-signature">অফিস সহকারীর স্বাক্ষর</div>
                    <div class="report-signature">অধ্যক্ষের স্বাক্ষর</div>
                </div>

            @else

                <div class="alert alert-warning">
                    শিক্ষার্থীর তথ্য পাওয়া যায়নি।
                </div>

            @endif

        {{-- ================================================== --}}
        {{-- Report 4: Students Without Active Admission        --}}
        {{-- ================================================== --}}

        @elseif($reportType === 4)

            <div class="report-meta">
                <span>লিঙ্গ: {{ $genderLabel }}</span>
                <span>ব্যবহারকারীর অবস্থা: {{ $statusLabel }}</span>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th>ক্রমিক</th>
                        <th>আইডি নং</th>
                        <th>ইউজার নাম</th>
                        <th>পিতার নাম</th>
                        <th>মাতার নাম</th>
                        <th>মোবাইল</th>
                        <th>এন্ট্রি তারিখ</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($students as $studentRow)
                        @php
                            $rowUser = $studentRow->user;
                        @endphp

                        <tr>
                            <td class="center">{{ $loop->iteration }}</td>

                            <td class="center">
                                {{ $rowUser?->institution_user_id
                                    ?: $rowUser?->custom_id
                                    ?: $studentRow->student_id
                                    ?: '—' }}
                            </td>

                            <td>
                                {{ $rowUser?->name_bn
                                    ?: $rowUser?->name
                                    ?: '—' }}
                            </td>

                            <td>{{ $rowUser?->father_name ?: '—' }}</td>

                            <td>{{ $rowUser?->mother_name ?: '—' }}</td>

                            <td>{{ $rowUser?->phone ?: '—' }}</td>

                            <td class="center">
                                {{ $rowUser?->created_at
                                    ? $rowUser->created_at->format('d-m-Y')
                                    : '—' }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="report-empty">
                                ভর্তি বিহীন কোনো শিক্ষার্থী পাওয়া যায়নি।
                            </td>
                        </tr>

                    @endforelse
                </tbody>
            </table>

        @endif

        <div class="report-footer">
            {{ $madrasaName }}
            <br>
            প্রতিবেদন তৈরি: {{ $printedAt }}
        </div>

    </div>
</div>

{{-- PDF libraries --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    async function downloadReportPdf() {
        const report = document.getElementById('printable-report');
        const button = document.getElementById('download-pdf-button');

        if (!report) {
            alert('রিপোর্ট পাওয়া যায়নি।');
            return;
        }

        if (!window.html2canvas || !window.jspdf) {
            alert('PDF লাইব্রেরি লোড হয়নি। ইন্টারনেট সংযোগ পরীক্ষা করুন।');
            return;
        }

        const originalText = button.innerHTML;

        try {
            button.disabled = true;
            button.innerHTML = 'PDF তৈরি হচ্ছে...';

            // Wait for report images to load.
            const images = Array.from(report.querySelectorAll('img'));

            await Promise.all(
                images.map(img => {
                    if (img.complete) {
                        return Promise.resolve();
                    }

                    return new Promise(resolve => {
                        img.onload = resolve;
                        img.onerror = resolve;
                    });
                })
            );

            const canvas = await html2canvas(report, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff',
                scrollY: 0
            });

            const { jsPDF } = window.jspdf;

            const isAdmissionForm = @json($reportType === 3);

            const pdf = new jsPDF({
                orientation: isAdmissionForm ? 'portrait' : 'landscape',
                unit: 'mm',
                format: 'a4'
            });

            const margin = 8;

            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            const contentWidth = pageWidth - (margin * 2);
            const contentHeight = pageHeight - (margin * 2);

            const imageData = canvas.toDataURL('image/jpeg', 0.95);

            const imageHeight = canvas.height * contentWidth / canvas.width;

            let remainingHeight = imageHeight;
            let position = margin;

            pdf.addImage(
                imageData,
                'JPEG',
                margin,
                position,
                contentWidth,
                imageHeight
            );

            remainingHeight -= contentHeight;

            while (remainingHeight > 0) {
                pdf.addPage();

                position = margin - (imageHeight - remainingHeight);

                pdf.addImage(
                    imageData,
                    'JPEG',
                    margin,
                    position,
                    contentWidth,
                    imageHeight
                );

                remainingHeight -= contentHeight;
            }

            const reportName = @json(
                \Illuminate\Support\Str::slug($reportTitle, '-')
            );

            const fileName = reportName
                + '-'
                + new Date().toISOString().slice(0, 10)
                + '.pdf';

            pdf.save(fileName);

        } catch (error) {
            console.error('PDF error:', error);

            alert(
                'PDF তৈরি করা যায়নি। Print বাটনে ক্লিক করে Save as PDF ব্যবহার করুন।'
            );

        } finally {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }
</script>

@endsection