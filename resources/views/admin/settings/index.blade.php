@extends('layouts.admin')

@section('title', 'সেটিংস')

@section('page')

<div class="container-fluid px-3 py-3">

    {{-- পেজের শিরোনাম --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">সেটিংস</h4>
            <p class="text-muted mb-0">
                আপনার মাদ্রাসার ক্যালেন্ডার ব্যবস্থাপনা ও পছন্দ নির্ধারণ করুন।
            </p>
        </div>
    </div>

    {{-- সফলতার বার্তা --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="বন্ধ করুন">
            </button>
        </div>
    @endif

    {{-- ক্যালেন্ডার সেটিংস --}}
    <div class="row g-3">

        <div class="col-md-6 col-xl-4">

            <div class="card h-100 border-primary shadow-sm">

                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-3">

                        <div
                            class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3"
                            style="width: 52px; height: 52px;">

                            <i class="bi bi-calendar3" style="font-size: 25px;"></i>

                        </div>

                        <div>
                            <h5 class="card-title mb-1">
                                ক্যালেন্ডার সেটিংস
                            </h5>

                            <span class="badge bg-primary">
                                প্রতিষ্ঠানের পছন্দ
                            </span>
                        </div>

                    </div>

                    <p class="text-muted mb-3">
                        আপনার মাদ্রাসায় তারিখ প্রদর্শনের জন্য পছন্দের
                        ক্যালেন্ডার পদ্ধতি নির্বাচন করুন।
                    </p>

                    <div class="mb-4">

                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-light text-dark border me-2">
                                ইংরেজি
                            </span>

                            <span class="text-muted small">
                                গ্রেগরিয়ান ক্যালেন্ডার
                            </span>
                        </div>

                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-light text-dark border me-2">
                                বাংলা
                            </span>

                            <span class="text-muted small">
                                বাংলা ক্যালেন্ডার
                            </span>
                        </div>

                        <div class="d-flex align-items-center">
                            <span class="badge bg-light text-dark border me-2">
                                হিজরি
                            </span>

                            <span class="text-muted small">
                                ইসলামি ক্যালেন্ডার
                            </span>
                        </div>

                    </div>

                    <a
                        href="{{ route('dashboard.settings.calendar') }}"
                        class="btn btn-primary w-100">

                        <i class="bi bi-gear me-1"></i>
                        ক্যালেন্ডার সেটিংস পরিবর্তন করুন

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection