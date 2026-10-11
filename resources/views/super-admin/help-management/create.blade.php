@extends('layouts.admin')

@section('title', 'নতুন হেল্প কনটেন্ট')

@section('page')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="fw-bold mb-1">নতুন হেল্প কনটেন্ট</h3>
            <p class="text-muted mb-0">
                নতুন Article অথবা FAQ তৈরি করুন।
            </p>
        </div>

        <a href="{{ route('dashboard.help-management.index') }}"
           class="btn btn-outline-secondary">

            <i class="fas fa-arrow-left me-1"></i>
            ফিরে যান

        </a>

    </div>


    @if($errors->any())

        <div class="alert alert-danger">

            <strong>ফর্মে কিছু সমস্যা রয়েছে:</strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form method="POST"
                  action="{{ route('dashboard.help-management.store') }}">

                @csrf

                <div class="row g-3">

                    <div class="col-md-8">

                        <label class="form-label">
                            শিরোনাম <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="title"
                               class="form-control"
                               value="{{ old('title') }}"
                               required
                               maxlength="255"
                               placeholder="যেমন: শিক্ষার্থী ভর্তি করার নিয়ম">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            কনটেন্টের ধরন <span class="text-danger">*</span>
                        </label>

                        <select name="type"
                                class="form-select"
                                required>

                            <option value="article"
                                @selected(old('type', 'article') === 'article')>
                                Help Article
                            </option>

                            <option value="faq"
                                @selected(old('type') === 'faq')>
                                FAQ
                            </option>

                        </select>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Category <span class="text-danger">*</span>
                        </label>

                        <select name="category"
                                class="form-select"
                                required>

                            <option value="general"
                                @selected(old('category', 'general') === 'general')>
                                General
                            </option>

                            <option value="admission"
                                @selected(old('category') === 'admission')>
                                Admission
                            </option>

                            <option value="students"
                                @selected(old('category') === 'students')>
                                Students
                            </option>

                            <option value="fees"
                                @selected(old('category') === 'fees')>
                                Fees & Payments
                            </option>

                            <option value="exams"
                                @selected(old('category') === 'exams')>
                                Exams & Results
                            </option>

                            <option value="account"
                                @selected(old('category') === 'account')>
                                Account & Login
                            </option>

                            <option value="technical"
                                @selected(old('category') === 'technical')>
                                Technical Support
                            </option>

                        </select>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">Sort Order</label>

                        <input type="number"
                               name="sort_order"
                               class="form-control"
                               min="0"
                               value="{{ old('sort_order', 0) }}">

                        <small class="text-muted">
                            কম সংখ্যা আগে দেখানো হবে।
                        </small>

                    </div>


                    <div class="col-12">

                        <label class="form-label">সংক্ষিপ্ত বিবরণ</label>

                        <textarea name="summary"
                                  class="form-control"
                                  rows="2"
                                  maxlength="2000"
                                  placeholder="কনটেন্ট সম্পর্কে সংক্ষিপ্ত বিবরণ">{{ old('summary') }}</textarea>

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            বিস্তারিত কনটেন্ট <span class="text-danger">*</span>
                        </label>

                        <textarea name="content"
                                  class="form-control"
                                  rows="10"
                                  required
                                  placeholder="এখানে বিস্তারিত নির্দেশনা বা FAQ-এর উত্তর লিখুন">{{ old('content') }}</textarea>

                    </div>


                    <div class="col-12">

                        <div class="form-check form-switch">

                            <input type="hidden"
                                   name="is_published"
                                   value="0">

                            <input type="checkbox"
                                   name="is_published"
                                   value="1"
                                   class="form-check-input"
                                   id="is_published"
                                   @checked(old('is_published'))>

                            <label class="form-check-label"
                                   for="is_published">

                                তৈরি করার পরই প্রকাশ করুন

                            </label>

                        </div>

                    </div>


                    <div class="col-12">

                        <hr>

                        <div class="d-flex justify-content-end gap-2">

                            <a href="{{ route('dashboard.help-management.index') }}"
                               class="btn btn-light border">

                                বাতিল

                            </a>

                            <button type="submit"
                                    class="btn btn-primary">

                                <i class="fas fa-save me-1"></i>
                                কনটেন্ট সংরক্ষণ

                            </button>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection
