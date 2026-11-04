@extends('layouts.admin')

@section('title', 'সেটিংস')

@section('page')

<div class="container-fluid px-3 py-3">


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

</div>

@endsection