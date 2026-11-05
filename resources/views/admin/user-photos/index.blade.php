@extends('layouts.admin')

@section('page-title', 'ব্যবহারকারী ছবি')

@section('page')
<div class="upm-page">

    <div class="upm-header">
        <div>
            <div class="upm-eyebrow">USER MANAGEMENT</div>
            <h1>ব্যবহারকারী ছবি</h1>
            <p>ব্যবহারকারীর প্রোফাইল ছবি আপলোড ও পরিচালনা করুন।</p>
        </div>

        <a href="{{ route('dashboard.user-photos.download-all') }}"
           class="upm-btn upm-btn-dark">
            <span>⇩</span> Download All Photos
        </a>
    </div>

    @if(session('success'))
        <div class="upm-alert upm-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="upm-alert upm-alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="upm-alert upm-alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div id="upmMessage" class="upm-alert" hidden></div>

    <div class="upm-grid">

        {{-- Upload panel --}}
        <section class="upm-card upm-upload-card">

            <div class="upm-card-heading">
                <div class="upm-heading-icon">↑</div>
                <div>
                    <h2>প্রোফাইল ছবি আপলোড</h2>
                    <p>একজন ব্যবহারকারীর ছবি নির্বাচন করুন এবং আপলোড করুন।</p>
                </div>
            </div>

            <form id="upmForm"
                  enctype="multipart/form-data"
                  data-search-url="{{ route('dashboard.user-photos.search') }}"
                  data-upload-url="{{ route('dashboard.user-photos.upload') }}">

                @csrf

                <div class="upm-field">
                    <label for="upmCode">ইউজার কোড <span>*</span></label>

                    <div class="upm-search-row">
                        <input type="text"
                               id="upmCode"
                               name="code"
                               placeholder="যেমন: 101"
                               autocomplete="off"
                               required>

                        <button type="button"
                                id="upmSearchBtn"
                                class="upm-btn upm-btn-primary">
                            খুঁজুন
                        </button>
                    </div>

                    <small>User Code লিখে খুঁজুন।</small>
                </div>

                <input type="hidden" id="upmUserId" name="user_id">

                <div class="upm-field">
                    <label for="upmName">ব্যবহারকারীর নাম <span>*</span></label>

                    <input type="text"
                           id="upmName"
                           placeholder="কোড দিয়ে ব্যবহারকারী খুঁজুন"
                           readonly
                           required>
                </div>

                <div class="upm-preview" id="upmPreviewBox">
                    <div class="upm-avatar-placeholder" id="upmPlaceholder">
                        <span>👤</span>
                    </div>

                    <img id="upmPreview"
                         src=""
                         alt="Profile preview"
                         hidden>
                </div>

                <div class="upm-dropzone" id="upmDropzone">
                    <div class="upm-upload-icon">↑</div>
                    <strong>এখানে ক্লিক করুন অথবা টেনে আনুন</strong>
                    <span>আপনার প্রোফাইল ছবিটি নির্বাচন করুন</span>
                    <small>JPG, JPEG, PNG, WEBP · সর্বোচ্চ ৫ MB</small>

                    <input type="file"
                           id="upmPhoto"
                           name="photo"
                           accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                           hidden>
                </div>

                <div id="upmSelectedFile" class="upm-selected-file" hidden></div>

                <div class="upm-form-actions">
                    <button type="button"
                            id="upmResetBtn"
                            class="upm-btn upm-btn-light">
                        ↺ রিসেট
                    </button>

                    <button type="submit"
                            id="upmUploadBtn"
                            class="upm-btn upm-btn-primary"
                            disabled>
                        ↑ ছবি আপলোড করুন
                    </button>
                </div>

            </form>
        </section>

        {{-- Information panel --}}
        <section class="upm-card upm-info-card">
            <div class="upm-card-heading">
                <div class="upm-heading-icon upm-heading-purple">i</div>
                <div>
                    <h2>ব্যবহারকারীর তথ্য</h2>
                    <p>ছবি আপলোড করার আগে তথ্য যাচাই করুন।</p>
                </div>
            </div>

            <div class="upm-info-illustration">
                <div class="upm-big-avatar">
                    <span>👤</span>
                    <i></i>
                </div>

                <h3>সঠিক ব্যবহারকারী নির্বাচন করুন</h3>

                <p>
                    ইউজার কোড দিয়ে খুঁজে ব্যবহারকারীর নাম ও বর্তমান
                    ছবি যাচাই করার পর নতুন ছবি আপলোড করুন।
                </p>
            </div>

            <div class="upm-tip">
                <span>✓</span>
                <div>
                    <strong>নিরাপদ ছবি আপলোড</strong>
                    <p>শুধু JPG, JPEG, PNG অথবা WEBP ফাইল গ্রহণ করা হবে।</p>
                </div>
            </div>

            <div class="upm-tip">
                <span>✓</span>
                <div>
                    <strong>সর্বোচ্চ ফাইল সাইজ ৫ MB</strong>
                    <p>ছবির সাইজ নির্ধারিত সীমার মধ্যে রাখুন।</p>
                </div>
            </div>
        </section>
    </div>

    {{-- Users table --}}
    <section class="upm-card upm-table-card">

        <div class="upm-table-heading">
            <div>
                <h2>ব্যবহারকারীর ছবি তালিকা</h2>
                <p>আপনার প্রতিষ্ঠানের ব্যবহারকারীদের ছবি পরিচালনা করুন।</p>
            </div>

            <div class="upm-total">
                মোট {{ $users->total() }} জন
            </div>
        </div>

        <div class="upm-table-wrap">
            <table class="upm-table">
                <thead>
                    <tr>
                        <th>অ্যাকশন</th>
                        <th>ইউজার কোড</th>
                        <th>ইউজার নাম</th>
                        <th>ছবি</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr data-user-id="{{ $user->id }}">
                            <td>
                                <div class="upm-row-actions">
                                    <button type="button"
                                            class="upm-icon-btn upm-select-user"
                                            title="ব্যবহারকারী নির্বাচন করুন"
                                            data-id="{{ $user->id }}"
                                            data-code="{{ $user->institution_user_id ?: $user->custom_id }}"
                                            data-name="{{ $user->name_bn ?: $user->name ?: $user->username }}"
                                            data-photo="{{ $user->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->photo) ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->photo) : '' }}">
                                        ✎
                                    </button>

                                    @if($user->photo)
                                        <button type="button"
                                                class="upm-icon-btn upm-delete-photo"
                                                title="ছবি মুছুন"
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->name_bn ?: $user->name ?: $user->username }}"
                                                data-delete-url="{{ route('dashboard.user-photos.destroy', $user->id) }}">
                                            ×
                                        </button>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <span class="upm-code">
                                    {{ $user->institution_user_id ?: $user->custom_id ?: '—' }}
                                </span>
                            </td>

                            <td>
                                <div class="upm-user-cell">
                                    <span class="upm-table-avatar">
                                        {{ mb_substr($user->name_bn ?: $user->name ?: 'U', 0, 1) }}
                                    </span>

                                    <div>
                                        <strong>
                                            {{ $user->name_bn ?: $user->name ?: $user->username }}
                                        </strong>
                                        <small>
                                            {{ $user->name && $user->name_bn ? $user->name : '' }}
                                        </small>
                                    </div>
                                </div>
                            </td>

<td>
    @if($user->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->photo))

        <img
            class="upm-table-photo"
            src="{{ asset('storage/' . $user->photo) }}"
            alt="{{ $user->name_bn ?: $user->name ?: 'User Photo' }}"
            loading="lazy"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
        >

        <div class="upm-no-photo" style="display:none;">
            <span>⚠️</span>
            <small>ছবি লোড হয়নি</small>
        </div>

    @else

        <div class="upm-no-photo">
            <span>👤</span>
            <small>ছবি নেই</small>
        </div>

    @endif
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="upm-empty">
                                    <span>☷</span>
                                    <strong>কোনো ব্যবহারকারী পাওয়া যায়নি</strong>
                                    <p>এই প্রতিষ্ঠানে এখনো কোনো ব্যবহারকারী নেই।</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

{{-- Custom Pagination --}}
@if($users->hasPages())
    <div class="upm-pagination">

        {{-- Previous --}}
        @if($users->onFirstPage())
            <span class="upm-page-btn upm-page-disabled">
                ← Previous
            </span>
        @else
            <a href="{{ $users->previousPageUrl() }}"
               class="upm-page-btn">
                ← Previous
            </a>
        @endif

        {{-- Page Numbers --}}
        <div class="upm-page-numbers">

            @php
                $startPage = max(1, $users->currentPage() - 2);
                $endPage = min($users->lastPage(), $users->currentPage() + 2);
            @endphp

            @for($page = $startPage; $page <= $endPage; $page++)

                @if($page == $users->currentPage())
                    <span class="upm-page-number active">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $users->url($page) }}"
                       class="upm-page-number">
                        {{ $page }}
                    </a>
                @endif

            @endfor

        </div>

        {{-- Next --}}
        @if($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}"
               class="upm-page-btn">
                Next →
            </a>
        @else
            <span class="upm-page-btn upm-page-disabled">
                Next →
            </span>
        @endif

    </div>
@endif
    </section>

</div>

<style>
    .upm-page {
        --upm-primary: #4f46e5;
        --upm-primary-dark: #3730a3;
        --upm-text: #172033;
        --upm-muted: #758096;
        --upm-border: #e8ebf2;
        color: var(--upm-text);
        padding: 24px;
        font-family: inherit;
    }

    .upm-page * {
        box-sizing: border-box;
    }

    .upm-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    .upm-eyebrow {
        font-size: 11px;
        letter-spacing: 2px;
        font-weight: 800;
        color: var(--upm-primary);
        margin-bottom: 7px;
    }

    .upm-header h1 {
        font-size: 27px;
        font-weight: 800;
        margin: 0 0 7px;
        color: var(--upm-text);
    }

    .upm-header p,
    .upm-card-heading p,
    .upm-table-heading p {
        margin: 0;
        color: var(--upm-muted);
        font-size: 13px;
        line-height: 1.7;
    }

    .upm-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 9px;
        border: 0;
        border-radius: 10px;
        padding: 11px 17px;
        min-height: 43px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: .2s ease;
    }

    .upm-btn:disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .upm-btn-primary {
        background: var(--upm-primary);
        color: white;
    }

    .upm-btn-primary:hover:not(:disabled) {
        background: var(--upm-primary-dark);
        transform: translateY(-1px);
    }

    .upm-btn-dark {
        background: #172033;
        color: white;
    }

    .upm-btn-dark:hover {
        background: #2b3852;
        color: white;
    }

    .upm-btn-light {
        background: #f2f4f9;
        color: #374151;
        border: 1px solid var(--upm-border);
    }

    .upm-btn-light:hover {
        background: #e8ebf4;
    }

    .upm-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(280px, .85fr);
        gap: 22px;
        margin-bottom: 23px;
        align-items: stretch;
    }

    .upm-card {
        background: #fff;
        border: 1px solid var(--upm-border);
        border-radius: 17px;
        box-shadow: 0 5px 25px rgba(28, 39, 70, .035);
        min-width: 0;
    }

    .upm-upload-card,
    .upm-info-card {
        padding: 24px;
    }

    .upm-card-heading {
        display: flex;
        align-items: center;
        gap: 13px;
        margin-bottom: 25px;
    }

    .upm-heading-icon {
        width: 43px;
        height: 43px;
        flex: 0 0 43px;
        display: grid;
        place-items: center;
        border-radius: 13px;
        background: #eeefff;
        color: var(--upm-primary);
        font-size: 22px;
        font-weight: 800;
    }

    .upm-heading-purple {
        background: #f2eaff;
        color: #8b5cf6;
    }

    .upm-card-heading h2,
    .upm-table-heading h2 {
        margin: 0 0 5px;
        font-size: 17px;
        font-weight: 800;
    }

    .upm-field {
        margin-bottom: 19px;
    }

    .upm-field label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 750;
    }

    .upm-field label span {
        color: #ef4444;
    }

    .upm-field input[type="text"] {
        width: 100%;
        min-width: 0;
        height: 45px;
        padding: 0 13px;
        border: 1px solid #dfe4ee;
        border-radius: 9px;
        background: #fff;
        outline: none;
        font-size: 13px;
        color: var(--upm-text);
        transition: .2s;
    }

    .upm-field input:focus {
        border-color: var(--upm-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, .09);
    }

    .upm-field input[readonly] {
        background: #f8f9fc;
        color: #536079;
    }

    .upm-field small {
        display: block;
        margin-top: 7px;
        color: var(--upm-muted);
        font-size: 11px;
    }

    .upm-search-row {
        display: flex;
        gap: 9px;
    }

    .upm-search-row input {
        flex: 1;
    }

    .upm-search-row .upm-btn {
        flex: 0 0 auto;
    }

    .upm-preview {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 130px;
        margin: 0 0 17px;
    }

    .upm-avatar-placeholder,
    #upmPreview {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        border: 4px solid white;
        outline: 1px solid var(--upm-border);
        object-fit: cover;
    }

    .upm-avatar-placeholder {
        display: grid;
        place-items: center;
        background: linear-gradient(145deg, #e5e7ff, #f4eaff);
        font-size: 42px;
    }

    .upm-dropzone {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 175px;
        border: 1.5px dashed #b9bdf7;
        border-radius: 13px;
        background: #fafaff;
        text-align: center;
        padding: 20px;
        cursor: pointer;
        transition: .2s;
    }

    .upm-dropzone:hover,
    .upm-dropzone.upm-dragging {
        border-color: var(--upm-primary);
        background: #f0f0ff;
    }

    .upm-upload-icon {
        display: grid;
        place-items: center;
        width: 40px;
        height: 40px;
        margin-bottom: 3px;
        border-radius: 12px;
        background: #e8eaff;
        color: var(--upm-primary);
        font-size: 23px;
        font-weight: 800;
    }

    .upm-dropzone strong {
        font-size: 13px;
    }

    .upm-dropzone span {
        font-size: 12px;
        color: var(--upm-muted);
    }

    .upm-dropzone small {
        font-size: 11px;
        color: #858da0;
    }

    .upm-selected-file {
        margin-top: 10px;
        padding: 10px 12px;
        border: 1px solid #e4e7f0;
        border-radius: 9px;
        background: #fafbff;
        font-size: 12px;
        overflow-wrap: anywhere;
    }

    .upm-form-actions {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 19px;
    }

    .upm-info-illustration {
        text-align: center;
        padding: 5px 10px 22px;
    }

    .upm-big-avatar {
        width: 130px;
        height: 130px;
        margin: 0 auto 19px;
        position: relative;
        display: grid;
        place-items: center;
        border-radius: 50%;
        background: linear-gradient(140deg, #ecebff, #f9eaff);
        font-size: 54px;
    }

    .upm-big-avatar i {
        position: absolute;
        right: 7px;
        bottom: 8px;
        width: 23px;
        height: 23px;
        border: 4px solid white;
        border-radius: 50%;
        background: #22c55e;
    }

    .upm-info-illustration h3 {
        font-size: 15px;
        font-weight: 800;
        margin: 0 0 9px;
    }

    .upm-info-illustration p {
        max-width: 330px;
        margin: 0 auto;
        font-size: 12px;
        color: var(--upm-muted);
        line-height: 1.8;
    }

    .upm-tip {
        display: flex;
        gap: 11px;
        padding: 13px 0;
        border-top: 1px solid #f0f1f6;
    }

    .upm-tip > span {
        flex: 0 0 24px;
        width: 24px;
        height: 24px;
        display: grid;
        place-items: center;
        border-radius: 50%;
        background: #e7f9ee;
        color: #16834a;
        font-size: 12px;
        font-weight: 800;
    }

    .upm-tip strong {
        display: block;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .upm-tip p {
        margin: 0;
        font-size: 11px;
        color: var(--upm-muted);
        line-height: 1.7;
    }

    .upm-table-card {
        overflow: hidden;
    }

    .upm-table-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding: 22px 24px;
        border-bottom: 1px solid #edf0f5;
    }

    .upm-total {
        padding: 8px 12px;
        background: #f0f0ff;
        border-radius: 9px;
        color: var(--upm-primary);
        font-size: 12px;
        font-weight: 800;
    }

    .upm-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .upm-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        min-width: 590px;
    }

    .upm-table th {
        padding: 14px 20px;
        background: #fafbfe;
        border-bottom: 1px solid var(--upm-border);
        color: #778096;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-weight: 800;
        white-space: nowrap;
    }

    .upm-table td {
        padding: 13px 20px;
        border-bottom: 1px solid #f0f2f7;
        vertical-align: middle;
        font-size: 12px;
    }

    .upm-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .upm-table tbody tr:hover {
        background: #fcfcff;
    }

    .upm-row-actions {
        display: flex;
        gap: 7px;
    }

    .upm-icon-btn {
        width: 31px;
        height: 31px;
        display: grid;
        place-items: center;
        border: 1px solid #e7e9f1;
        border-radius: 8px;
        background: white;
        color: #526078;
        font-size: 17px;
        cursor: pointer;
        transition: .2s;
    }

    .upm-icon-btn:hover {
        border-color: #c7c9ff;
        background: #f0f0ff;
        color: var(--upm-primary);
    }

    .upm-delete-photo:hover {
        border-color: #fecaca;
        background: #fff1f2;
        color: #dc2626;
    }

    .upm-code {
        display: inline-block;
        padding: 6px 9px;
        border-radius: 6px;
        background: #f2f4f9;
        color: #3b4660;
        font-size: 11px;
        font-weight: 800;
    }

    .upm-user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .upm-table-avatar {
        flex: 0 0 35px;
        width: 35px;
        height: 35px;
        display: grid;
        place-items: center;
        border-radius: 11px;
        background: #eeefff;
        color: var(--upm-primary);
        font-weight: 800;
        font-size: 14px;
    }

    .upm-user-cell strong {
        display: block;
        font-size: 12px;
        font-weight: 750;
    }

    .upm-user-cell small {
        display: block;
        color: var(--upm-muted);
        margin-top: 4px;
        font-size: 11px;
    }

    .upm-table-photo {
        width: 43px;
        height: 43px;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid #e8ebf2;
    }

    .upm-no-photo {
        display: flex;
        align-items: center;
        gap: 7px;
        color: #98a0b1;
    }

    .upm-no-photo span {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        background: #f2f4f9;
        border-radius: 11px;
        font-size: 19px;
    }

    .upm-no-photo small {
        font-size: 10px;
    }

    .upm-empty {
        text-align: center;
        padding: 38px 15px;
        color: var(--upm-muted);
    }

    .upm-empty > span {
        display: block;
        font-size: 28px;
        margin-bottom: 10px;
    }

    .upm-empty strong {
        display: block;
        color: var(--upm-text);
        font-size: 13px;
    }

    .upm-empty p {
        font-size: 12px;
    }

/* =========================================
   CUSTOM PAGINATION
========================================= */

.upm-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;

    width: 100%;
    padding: 18px 20px;

    border-top: 1px solid #edf0f5;
    background: #fff;
}

.upm-page-numbers {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 6px;
}

.upm-page-btn,
.upm-page-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 38px;
    height: 38px;
    padding: 0 12px;

    border: 1px solid #e5e7eb;
    border-radius: 9px;

    background: #fff;
    color: #475569;

    font-size: 12px;
    font-weight: 700;
    text-decoration: none !important;

    white-space: nowrap;
    transition: all .2s ease;
}

.upm-page-btn:hover,
.upm-page-number:hover {
    background: #eef0ff;
    border-color: #c7c9ff;
    color: #4f46e5;
}

.upm-page-number.active {
    background: #4f46e5;
    border-color: #4f46e5;
    color: #fff;
    cursor: default;
}

.upm-page-disabled {
    background: #f8fafc;
    color: #c0c6d2;
    border-color: #edf0f5;
    cursor: not-allowed;
    pointer-events: none;
}

/* Mobile Responsive */
@media (max-width: 480px) {

    .upm-pagination {
        gap: 7px;
        padding: 14px 10px;
    }

    .upm-page-btn {
        min-width: 35px;
        height: 35px;
        padding: 0 9px;
        font-size: 11px;
    }

    .upm-page-number {
        min-width: 34px;
        height: 35px;
        padding: 0 8px;
    }

    .upm-page-numbers {
        gap: 4px;
    }
}

    .upm-alert {
        padding: 12px 15px;
        margin-bottom: 18px;
        border: 1px solid #e4e7f0;
        border-radius: 10px;
        font-size: 13px;
        line-height: 1.7;
    }

    .upm-alert-success {
        background: #edfcf3;
        border-color: #c4f0d5;
        color: #166534;
    }

    .upm-alert-error {
        background: #fff1f2;
        border-color: #fecdd3;
        color: #9f1239;
    }

    @media (max-width: 900px) {
        .upm-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .upm-page {
            padding: 13px;
        }

        .upm-header h1 {
            font-size: 23px;
        }

        .upm-upload-card,
        .upm-info-card {
            padding: 17px;
        }

        .upm-card-heading h2,
        .upm-table-heading h2 {
            font-size: 15px;
        }

        .upm-search-row {
            align-items: stretch;
        }

        .upm-search-row .upm-btn {
            padding-left: 12px;
            padding-right: 12px;
        }

        .upm-form-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .upm-table-heading {
            padding: 18px;
        }
    }
</style>

<script>
(function () {
    'use strict';

    const form = document.getElementById('upmForm');

    if (!form) return;

    const codeInput = document.getElementById('upmCode');
    const nameInput = document.getElementById('upmName');
    const userIdInput = document.getElementById('upmUserId');
    const photoInput = document.getElementById('upmPhoto');
    const dropzone = document.getElementById('upmDropzone');
    const preview = document.getElementById('upmPreview');
    const placeholder = document.getElementById('upmPlaceholder');
    const selectedFile = document.getElementById('upmSelectedFile');
    const searchBtn = document.getElementById('upmSearchBtn');
    const uploadBtn = document.getElementById('upmUploadBtn');
    const resetBtn = document.getElementById('upmResetBtn');
    const messageBox = document.getElementById('upmMessage');

    const searchUrl = form.dataset.searchUrl;
    const uploadUrl = form.dataset.uploadUrl;
    const csrfToken = form.querySelector('input[name="_token"]').value;

    let currentUser = null;
    let selectedFileUrl = null;
    let searchSequence = 0;

    function showMessage(message, type = 'success') {
        messageBox.textContent = message;
        messageBox.className = 'upm-alert ' +
            (type === 'error' ? 'upm-alert-error' : 'upm-alert-success');
        messageBox.hidden = false;
        messageBox.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }

    function clearMessage() {
        messageBox.hidden = true;
        messageBox.textContent = '';
    }

    function updatePreview(url) {
        if (url) {
            preview.src = url;
            preview.hidden = false;
            placeholder.hidden = true;
        } else {
            preview.removeAttribute('src');
            preview.hidden = true;
            placeholder.hidden = false;
        }
    }

    function clearSelectedFile() {
        photoInput.value = '';
        selectedFile.hidden = true;
        selectedFile.textContent = '';

        if (selectedFileUrl) {
            URL.revokeObjectURL(selectedFileUrl);
            selectedFileUrl = null;
        }
    }

    function resetForm() {
        searchSequence++;
        currentUser = null;
        codeInput.value = '';
        nameInput.value = '';
        userIdInput.value = '';
        clearSelectedFile();
        updatePreview(null);
        uploadBtn.disabled = true;
        clearMessage();
    }

    async function searchUser() {
        clearMessage();

        const code = codeInput.value.trim();

        if (!code) {
            showMessage('প্রথমে User Code লিখুন।', 'error');
            codeInput.focus();
            return;
        }

        const sequence = ++searchSequence;

        searchBtn.disabled = true;
        searchBtn.textContent = 'খোঁজা হচ্ছে...';

        try {
            const response = await fetch(
                searchUrl + '?code=' + encodeURIComponent(code),
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            const data = await response.json();

            if (sequence !== searchSequence) return;

            if (!response.ok || !data.success) {
                currentUser = null;
                userIdInput.value = '';
                nameInput.value = '';
                updatePreview(null);
                uploadBtn.disabled = true;

                showMessage(
                    data.message || 'ব্যবহারকারী খুঁজে পাওয়া যায়নি।',
                    'error'
                );

                return;
            }

            currentUser = data.user;
            userIdInput.value = data.user.id;
            codeInput.value = data.user.code || code;
            nameInput.value = data.user.name || '';

            updatePreview(data.user.photo || null);

            uploadBtn.disabled = !photoInput.files.length;

            showMessage('ব্যবহারকারী পাওয়া গেছে। এখন ছবি নির্বাচন করুন।');

        } catch (error) {
            showMessage('সার্ভারের সঙ্গে যোগাযোগ করা যায়নি। আবার চেষ্টা করুন।', 'error');
        } finally {
            if (sequence === searchSequence) {
                searchBtn.disabled = false;
                searchBtn.textContent = 'খুঁজুন';
            }
        }
    }

    function setSelectedFile(file) {
        clearMessage();

        if (!file) return;

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        const extension = file.name.split('.').pop().toLowerCase();
        const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (
            !allowedTypes.includes(file.type) ||
            !allowedExtensions.includes(extension)
        ) {
            photoInput.value = '';
            selectedFile.hidden = true;
            showMessage('শুধু JPG, JPEG, PNG অথবা WEBP ছবি নির্বাচন করুন।', 'error');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            photoInput.value = '';
            selectedFile.hidden = true;
            showMessage('ছবির সাইজ সর্বোচ্চ ৫ MB হতে পারবে।', 'error');
            return;
        }

        if (selectedFileUrl) {
            URL.revokeObjectURL(selectedFileUrl);
        }

        selectedFileUrl = URL.createObjectURL(file);

        selectedFile.textContent =
            file.name + ' · ' + (file.size / (1024 * 1024)).toFixed(2) + ' MB';

        selectedFile.hidden = false;

        updatePreview(selectedFileUrl);

        uploadBtn.disabled = !currentUser;

        if (!currentUser) {
            showMessage('ছবি আপলোড করার আগে User Code দিয়ে ব্যবহারকারী খুঁজুন।');
        }
    }

    searchBtn.addEventListener('click', searchUser);

    codeInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchUser();
        }
    });

    codeInput.addEventListener('input', function () {
        currentUser = null;
        userIdInput.value = '';
        nameInput.value = '';
        uploadBtn.disabled = true;
        updatePreview(null);
    });

    dropzone.addEventListener('click', function () {
        photoInput.click();
    });

    photoInput.addEventListener('change', function () {
        setSelectedFile(photoInput.files[0]);
    });

    dropzone.addEventListener('dragover', function (event) {
        event.preventDefault();
        dropzone.classList.add('upm-dragging');
    });

    dropzone.addEventListener('dragleave', function () {
        dropzone.classList.remove('upm-dragging');
    });

    dropzone.addEventListener('drop', function (event) {
        event.preventDefault();
        dropzone.classList.remove('upm-dragging');

        const file = event.dataTransfer.files[0];

        if (!file) return;

        const transfer = new DataTransfer();
        transfer.items.add(file);
        photoInput.files = transfer.files;

        setSelectedFile(file);
    });

    resetBtn.addEventListener('click', resetForm);

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        clearMessage();

        if (!currentUser || !userIdInput.value) {
            showMessage('প্রথমে User Code দিয়ে ব্যবহারকারী খুঁজুন।', 'error');
            return;
        }

        if (!photoInput.files.length) {
            showMessage('আপলোড করার জন্য একটি ছবি নির্বাচন করুন।', 'error');
            return;
        }

        const formData = new FormData(form);

        uploadBtn.disabled = true;
        uploadBtn.textContent = 'আপলোড হচ্ছে...';

        try {
            const response = await fetch(uploadUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                let errorMessage = data.message || 'ছবি আপলোড করা যায়নি।';

                if (data.errors) {
                    errorMessage = Object.values(data.errors).flat().join(' ');
                }

                showMessage(errorMessage, 'error');
                return;
            }

            updatePreview(data.photo);
            clearSelectedFile();

            showMessage(data.message || 'ছবি আপলোড সফল হয়েছে।');

            /*
             * Refresh to update the table photo and action buttons.
             */
            window.setTimeout(function () {
                window.location.reload();
            }, 900);

        } catch (error) {
            showMessage('আপলোডের সময় সমস্যা হয়েছে। আবার চেষ্টা করুন।', 'error');
        } finally {
            uploadBtn.textContent = '↑ ছবি আপলোড করুন';
            uploadBtn.disabled = !currentUser || !photoInput.files.length;
        }
    });

    document.querySelectorAll('.upm-select-user').forEach(function (button) {
        button.addEventListener('click', function () {
            resetForm();

            codeInput.value = button.dataset.code || '';
            nameInput.value = button.dataset.name || '';

            currentUser = {
                id: button.dataset.id,
                code: button.dataset.code,
                name: button.dataset.name,
                photo: button.dataset.photo || null
            };

            userIdInput.value = currentUser.id;
            updatePreview(currentUser.photo);
            uploadBtn.disabled = true;

            document.querySelector('.upm-upload-card').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    });

    document.querySelectorAll('.upm-delete-photo').forEach(function (button) {
        button.addEventListener('click', async function () {
            const name = button.dataset.name || 'এই ব্যবহারকারী';

            if (!confirm(name + '-এর প্রোফাইল ছবি মুছে ফেলতে চান?')) {
                return;
            }

            button.disabled = true;

            try {
                const response = await fetch(button.dataset.deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    showMessage(data.message || 'ছবি মুছে ফেলা যায়নি।', 'error');
                    button.disabled = false;
                    return;
                }

                showMessage(data.message || 'ছবি মুছে ফেলা হয়েছে।');

                window.setTimeout(function () {
                    window.location.reload();
                }, 600);

            } catch (error) {
                showMessage('সার্ভারের সঙ্গে যোগাযোগ করা যায়নি।', 'error');
                button.disabled = false;
            }
        });
    });
})();
</script>
@endsection