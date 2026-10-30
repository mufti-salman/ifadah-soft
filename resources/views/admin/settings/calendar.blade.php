@extends('layouts.admin')

@section('title', 'ক্যালেন্ডার সেটিংস')

@section('page')
<div class="container-fluid px-3 py-3">

    {{-- পেজের শিরোনাম --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">ক্যালেন্ডার সেটিংস</h4>
            <p class="text-muted mb-0">
                আপনার মাদ্রাসার জন্য পছন্দের ক্যালেন্ডার পদ্ধতি নির্বাচন করুন।
            </p>
        </div>

        <a href="{{ route('dashboard.settings.index') }}"
           class="btn btn-outline-secondary mt-2 mt-md-0">
            <i class="bi bi-arrow-left me-1"></i>
            সেটিংসে ফিরে যান
        </a>
    </div>

    {{-- সফলতার বার্তা --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="বন্ধ করুন">
            </button>
        </div>
    @endif

    {{-- ত্রুটির বার্তা --}}
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>অনুগ্রহ করে নিচের সমস্যাগুলো ঠিক করুন:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        {{-- ক্যালেন্ডার সেটিংস ফর্ম --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">ক্যালেন্ডার পছন্দ নির্ধারণ</h5>
                </div>

                <div class="card-body p-4">

                    <form action="{{ route('dashboard.settings.calendar.update') }}"
                          method="POST">

                        @csrf

                        {{-- ক্যালেন্ডারের ধরন --}}
                        <div class="mb-4">

                            <label for="calendar_mode" class="form-label fw-semibold">
                                ক্যালেন্ডার পদ্ধতি
                                <span class="text-danger">*</span>
                            </label>

                            <select name="calendar_mode"
                                    id="calendar_mode"
                                    class="form-select @error('calendar_mode') is-invalid @enderror"
                                    required>

                                <option value="english"
                                    {{ old('calendar_mode', $calendarSettings['calendar_mode']) === 'english' ? 'selected' : '' }}>
                                    ইংরেজি ক্যালেন্ডার (গ্রেগরিয়ান)
                                </option>

                                <option value="bangla"
                                    {{ old('calendar_mode', $calendarSettings['calendar_mode']) === 'bangla' ? 'selected' : '' }}>
                                    বাংলা ক্যালেন্ডার
                                </option>

                                <option value="hijri"
                                    {{ old('calendar_mode', $calendarSettings['calendar_mode']) === 'hijri' ? 'selected' : '' }}>
                                    হিজরি ক্যালেন্ডার (ইসলামি)
                                </option>

                            </select>

                            @error('calendar_mode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-text">
                                আপনার মাদ্রাসায় তারিখ প্রদর্শনের জন্য পছন্দের পদ্ধতি নির্বাচন করুন।
                            </div>

                        </div>

                        {{-- হিজরি তারিখ সমন্বয় --}}
                        <div class="mb-4" id="hijri-adjustment-wrapper">

                            <label for="hijri_adjustment" class="form-label fw-semibold">
                                হিজরি তারিখের সমন্বয়
                            </label>

                            <div class="input-group">

                                <input type="number"
                                       name="hijri_adjustment"
                                       id="hijri_adjustment"
                                       class="form-control @error('hijri_adjustment') is-invalid @enderror"
                                       value="{{ old('hijri_adjustment', $calendarSettings['hijri_adjustment']) }}"
                                       min="-30"
                                       max="30"
                                       step="1">

                                <span class="input-group-text">দিন</span>

                                @error('hijri_adjustment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                            </div>

                            <div class="form-text">
                                প্রয়োজন অনুযায়ী হিজরি তারিখ এক বা একাধিক দিন এগিয়ে
                                অথবা পিছিয়ে সমন্বয় করতে পারবেন। পরিবর্তনের প্রয়োজন
                                না হলে ০ রাখুন।
                            </div>

                        </div>

                        {{-- সংরক্ষণ ও বাতিল --}}
                        <div class="d-flex flex-wrap gap-2 pt-2">

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                সেটিংস সংরক্ষণ করুন
                            </button>

                            <a href="{{ route('dashboard.settings.index') }}"
                               class="btn btn-light border">
                                বাতিল করুন
                            </a>

                        </div>

                    </form>

                </div>
            </div>
        </div>

        {{-- ক্যালেন্ডার সম্পর্কিত তথ্য --}}
        <div class="col-lg-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">ক্যালেন্ডার সম্পর্কে জানুন</h5>
                </div>

                <div class="card-body p-4">

                    <div class="d-flex mb-4">

                        <div class="me-3 text-primary">
                            <i class="bi bi-calendar3" style="font-size: 28px;"></i>
                        </div>

                        <div>
                            <h6 class="mb-1">ইংরেজি ক্যালেন্ডার</h6>

                            <p class="text-muted small mb-0">
                                গ্রেগরিয়ান পদ্ধতি। বর্তমানে এটিই ডিফল্ট ক্যালেন্ডার।
                            </p>
                        </div>

                    </div>

                    <div class="d-flex mb-4">

                        <div class="me-3 text-success">
                            <i class="bi bi-calendar-week" style="font-size: 28px;"></i>
                        </div>

                        <div>
                            <h6 class="mb-1">বাংলা ক্যালেন্ডার</h6>

                            <p class="text-muted small mb-0">
                                বাংলা সনের মাস, দিন ও বছর অনুযায়ী তারিখ প্রদর্শনের জন্য।
                            </p>
                        </div>

                    </div>

                    <div class="d-flex">

                        <div class="me-3 text-warning">
                            <i class="bi bi-moon-stars" style="font-size: 28px;"></i>
                        </div>

                        <div>
                            <h6 class="mb-1">হিজরি ক্যালেন্ডার</h6>

                            <p class="text-muted small mb-0">
                                ইসলামি চান্দ্রবর্ষ অনুযায়ী তারিখ প্রদর্শনের জন্য।
                                প্রয়োজনে দিনের সমন্বয় করা যাবে।
                            </p>
                        </div>

                    </div>

                    <hr>

                    <div class="alert alert-info small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        প্রতিটি মাদ্রাসার ক্যালেন্ডার সেটিংস আলাদাভাবে সংরক্ষণ করা হয়।
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarMode = document.getElementById('calendar_mode');
    const adjustmentWrapper = document.getElementById('hijri-adjustment-wrapper');

    function toggleHijriAdjustment() {
        adjustmentWrapper.style.display =
            calendarMode.value === 'hijri' ? 'block' : 'none';
    }

    calendarMode.addEventListener('change', toggleHijriAdjustment);

    toggleHijriAdjustment();
});
</script>
@endsection