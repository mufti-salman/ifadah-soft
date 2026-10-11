@extends('layouts.admin')

@section('title', 'হেল্প ম্যানেজমেন্ট')

@section('page')

<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="fas fa-question-circle text-primary me-2"></i>
                হেল্প ম্যানেজমেন্ট
            </h3>

            <p class="text-muted mb-0">
                Help Article ও FAQ পরিচালনা করুন।
            </p>
        </div>

        <a href="{{ route('dashboard.help-management.create') }}"
           class="btn btn-primary">

            <i class="fas fa-plus me-1"></i>
            নতুন কনটেন্ট তৈরি

        </a>

    </div>


    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Statistics --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">মোট কনটেন্ট</div>
                    <h3 class="fw-bold mb-0">
                        {{ $stats['total'] }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">প্রকাশিত</div>
                    <h3 class="fw-bold text-success mb-0">
                        {{ $stats['published'] }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Draft</div>
                    <h3 class="fw-bold text-warning mb-0">
                        {{ $stats['drafts'] }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">FAQ</div>
                    <h3 class="fw-bold text-info mb-0">
                        {{ $stats['faqs'] }}
                    </h3>
                </div>
            </div>
        </div>

    </div>


    {{-- Search and Filters --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('dashboard.help-management.index') }}">

                <div class="row g-3">

                    <div class="col-md-5">

                        <label class="form-label">Search</label>

                        <input type="text"
                               name="search"
                               class="form-control"
                               value="{{ request('search') }}"
                               placeholder="শিরোনাম বা কনটেন্ট খুঁজুন">

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">কনটেন্টের ধরন</label>

                        <select name="type" class="form-select">

                            <option value="">সব ধরন</option>

                            <option value="article"
                                @selected(request('type') === 'article')>
                                Help Article
                            </option>

                            <option value="faq"
                                @selected(request('type') === 'faq')>
                                FAQ
                            </option>

                        </select>

                    </div>

                    <div class="col-md-2">

                        <label class="form-label">Status</label>

                        <select name="status" class="form-select">

                            <option value="">সব Status</option>

                            <option value="published"
                                @selected(request('status') === 'published')>
                                Published
                            </option>

                            <option value="draft"
                                @selected(request('status') === 'draft')>
                                Draft
                            </option>

                        </select>

                    </div>

                    <div class="col-md-2 d-flex align-items-end gap-2">

                        <button type="submit"
                                class="btn btn-primary flex-fill">

                            <i class="fas fa-search me-1"></i>
                            খুঁজুন

                        </button>

                        <a href="{{ route('dashboard.help-management.index') }}"
                           class="btn btn-outline-secondary"
                           title="Reset Filter">

                            <i class="fas fa-redo"></i>

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Content Table --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0 fw-bold">
                হেল্প কনটেন্টের তালিকা
            </h5>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th class="ps-3">#</th>
                            <th>শিরোনাম</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th>তারিখ</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($articles as $article)

                            <tr>

                                <td class="ps-3">
                                    {{ $articles->firstItem() + $loop->index }}
                                </td>

                                <td>

                                    <div class="fw-semibold">
                                        {{ $article->title }}
                                    </div>

                                    @if($article->summary)
                                        <small class="text-muted">
                                            {{ \Illuminate\Support\Str::limit($article->summary, 70) }}
                                        </small>
                                    @endif

                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $article->category }}
                                    </span>
                                </td>

                                <td>

                                    @if($article->type === 'faq')
                                        <span class="badge bg-info-subtle text-info">
                                            FAQ
                                        </span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary">
                                            Article
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    @if($article->is_published)
                                        <span class="badge bg-success">
                                            Published
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            Draft
                                        </span>
                                    @endif

                                </td>

                                <td>{{ $article->sort_order }}</td>

                                <td>
                                    {{ $article->created_at?->format('d M Y') }}
                                </td>

                                <td class="text-end pe-3">

                                    <div class="d-inline-flex gap-1">

                                        <a href="{{ route('dashboard.help-management.edit', $article->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Edit">

                                            <i class="fas fa-edit"></i>

                                        </a>

                                        <form method="POST"
                                              action="{{ route('dashboard.help-management.toggle-status', $article->id) }}">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-success"
                                                    title="{{ $article->is_published ? 'Unpublish' : 'Publish' }}">

                                                <i class="fas {{ $article->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>

                                            </button>

                                        </form>

                                        <form method="POST"
                                              action="{{ route('dashboard.help-management.destroy', $article->id) }}"
                                              onsubmit="return confirm('আপনি কি এই কনটেন্টটি মুছে ফেলতে চান?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete">

                                                <i class="fas fa-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8"
                                    class="text-center py-5">

                                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>

                                    <h6 class="fw-bold">
                                        কোনো কনটেন্ট পাওয়া যায়নি
                                    </h6>

                                    <p class="text-muted mb-3">
                                        নতুন Help Article অথবা FAQ তৈরি করুন।
                                    </p>

                                    <a href="{{ route('dashboard.help-management.create') }}"
                                       class="btn btn-primary btn-sm">

                                        <i class="fas fa-plus me-1"></i>
                                        নতুন কনটেন্ট

                                    </a>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if($articles->hasPages())

            <div class="card-footer bg-white">
                {{ $articles->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
