@extends('layouts.admin')

@section('title', 'হেল্প কনটেন্ট সম্পাদনা')

@section('page')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="fw-bold mb-1">হেল্প কনটেন্ট সম্পাদনা</h3>
            <p class="text-muted mb-0">
                বিদ্যমান Article অথবা FAQ আপডেট করুন।
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
                  action="{{ route('dashboard.help-management.update', $helpArticle->id) }}">

                @csrf
                @method('PUT')

                <div class="row g-3">

                    <div class="col-md-8">

                        <label class="form-label">
                            শিরোনাম <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="title"
                               class="form-control"
                               value="{{ old('title', $helpArticle->title) }}"
                               required
                               maxlength="255">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            কনটেন্টের ধরন <span class="text-danger">*</span>
                        </label>

                        <select name="type"
                                class="form-select"
                                required>

                            <option value="article"
                                @selected(old('type', $helpArticle->type) === 'article')>
                                Help Article
                            </option>

                            <option value="faq"
                                @selected(old('type', $helpArticle->type) === 'faq')>
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

                            @php
                                $selectedCategory = old(
                                    'category',
                                    $helpArticle->category
                                );

                                $categories = [
                                    'general' => 'General',
                                    'admission' => 'Admission',
                                    'students' => 'Students',
                                    'fees' => 'Fees & Payments',
                                    'exams' => 'Exams & Results',
                                    'account' => 'Account & Login',
                                    'technical' => 'Technical Support',
                                ];
                            @endphp

                            @foreach($categories as $value => $label)

                                <option value="{{ $value }}"
                                    @selected($selectedCategory === $value)>

                                    {{ $label }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">Sort Order</label>

                        <input type="number"
                               name="sort_order"
                               class="form-control"
                               min="0"
                               value="{{ old('sort_order', $helpArticle->sort_order) }}">

                    </div>


                    <div class="col-12">

                        <label class="form-label">সংক্ষিপ্ত বিবরণ</label>

                        <textarea name="summary"
                                  class="form-control"
                                  rows="2"
                                  maxlength="2000">{{ old('summary', $helpArticle->summary) }}</textarea>

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            বিস্তারিত কনটেন্ট <span class="text-danger">*</span>
                        </label>

                        <textarea name="content"
                                  class="form-control"
                                  rows="10"
                                  required>{{ old('content', $helpArticle->content) }}</textarea>

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
                                   @checked(old('is_published', $helpArticle->is_published))>

                            <label class="form-check-label"
                                   for="is_published">

                                প্রকাশিত রাখুন

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
                                পরিবর্তন সংরক্ষণ

                            </button>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection
