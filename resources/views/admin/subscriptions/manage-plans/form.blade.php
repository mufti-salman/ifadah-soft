
@extends('layouts.admin')

@section('title', $isEdit ? 'Edit Subscription Package' : 'Create Subscription Package')

@section('page')
<div class="container-fluid py-3">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                {{ $isEdit ? 'Edit Package' : 'Create Package' }}
            </h3>
            <p class="text-muted mb-0">
                Configure pricing, limits and trial settings.
            </p>
        </div>

        <a href="{{ route('dashboard.subscription.manage-plans.index') }}"
           class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Plans
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $featureValue = old(
            'features',
            is_array($plan->features)
                ? implode("\n", $plan->features)
                : ''
        );
    @endphp

    <form method="POST"
          action="{{ $isEdit
              ? route('dashboard.subscription.manage-plans.update', $plan)
              : route('dashboard.subscription.manage-plans.store') }}">

        @csrf

        @if($isEdit)
            @method('PUT')
        @endif

        <div class="row g-4">

            <div class="col-12 col-xl-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Package Information</h5>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Package Name *</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name', $plan->name) }}"
                                   maxlength="150"
                                   required
                                   placeholder="e.g. Basic Starter">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="3"
                                      maxlength="2000"
                                      placeholder="Describe what this package offers">{{ old('description', $plan->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Package Features</label>
                            <textarea name="features"
                                      class="form-control"
                                      rows="5"
                                      placeholder="Student management&#10;Attendance management&#10;Results and reports">{{ $featureValue }}</textarea>
                            <div class="form-text">
                                Enter one feature per line.
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Monthly Price (BDT) *</label>
                                <input type="number"
                                       name="price_monthly"
                                       class="form-control"
                                       value="{{ old('price_monthly', $plan->price_monthly ?? 0) }}"
                                       min="0"
                                       max="99999999.99"
                                       step="0.01"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Yearly Price (BDT) *</label>
                                <input type="number"
                                       name="price_yearly"
                                       class="form-control"
                                       value="{{ old('price_yearly', $plan->price_yearly ?? 0) }}"
                                       min="0"
                                       max="99999999.99"
                                       step="0.01"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Annual Discount (%) *</label>
                                <input type="number"
                                       name="annual_discount"
                                       class="form-control"
                                       value="{{ old('annual_discount', $plan->annual_discount ?? 20) }}"
                                       min="0"
                                       max="100"
                                       step="1"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Sort Order *</label>
                                <input type="number"
                                       name="sort_order"
                                       class="form-control"
                                       value="{{ old('sort_order', $plan->sort_order ?? 0) }}"
                                       min="0"
                                       step="1"
                                       required>
                                <div class="form-text">
                                    Lower numbers appear first.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Limits & Status</h5>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Student Limit *</label>
                            <input type="number"
                                   name="student_limit"
                                   class="form-control"
                                   value="{{ old('student_limit', $plan->student_limit ?? 200) }}"
                                   min="0"
                                   step="1"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">SMS Limit *</label>
                            <input type="number"
                                   name="sms_limit"
                                   class="form-control"
                                   value="{{ old('sms_limit', $plan->sms_limit ?? 500) }}"
                                   min="0"
                                   step="1"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Trial Days *</label>
                            <input type="number"
                                   name="trial_days"
                                   class="form-control"
                                   value="{{ old('trial_days', $plan->trial_days ?? 7) }}"
                                   min="0"
                                   max="365"
                                   step="1"
                                   required>
                        </div>

                        <input type="hidden" name="is_active" value="0">

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input"
                                   type="checkbox"
                                   role="switch"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   @checked((bool) old('is_active', $isEdit ? $plan->is_active : true))>

                            <label class="form-check-label fw-semibold"
                                   for="is_active">
                                Active Package
                            </label>
                        </div>

                        <div class="alert alert-info small">
                            Inactive packages will no longer appear in the institution's available plan selection.
                        </div>
                    </div>

                    <div class="card-footer bg-white">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i>
                            {{ $isEdit ? 'Save Changes' : 'Create Package' }}
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection