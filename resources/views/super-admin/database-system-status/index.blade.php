@extends('layouts.admin')

@section('title', 'Database & System Status')

@section('page')

<div class="container-fluid py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="fas fa-server text-primary me-2"></i>
                Database & System Status
            </h4>
            <p class="text-muted mb-0">
                আপনার অ্যাপ্লিকেশন, ডেটাবেজ এবং সার্ভারের অবস্থা পর্যবেক্ষণ করুন।
            </p>
        </div>

        <a href="{{ route('dashboard.database-system-status.index') }}"
           class="btn btn-outline-primary">
            <i class="fas fa-sync-alt me-1"></i>
            Refresh Status
        </a>
    </div>

    {{-- Overall Status --}}
    @php
        $allHealthy = $databaseStatus['connected']
            && $storageStatus['writable']
            && $storageStatus['framework_writable']
            && $storageStatus['logs_writable'];
    @endphp

    <div class="alert {{ $allHealthy ? 'alert-success' : 'alert-warning' }} d-flex align-items-center gap-3 mb-4">
        <i class="fas {{ $allHealthy ? 'fa-check-circle' : 'fa-exclamation-triangle' }} fa-2x"></i>
        <div>
            <h6 class="fw-bold mb-1">
                {{ $allHealthy ? 'System Operational' : 'System Needs Attention' }}
            </h6>
            <p class="mb-0">
                {{ $allHealthy
                    ? 'Database connection এবং প্রধান storage directory-গুলো সচল আছে।'
                    : 'এক বা একাধিক system component যাচাই করা প্রয়োজন। নিচের status দেখুন।'
                }}
            </p>
        </div>
    </div>

    {{-- Status Cards --}}
    <div class="row g-3 mb-4">

        {{-- Database --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-2">Database Connection</p>
                            <h5 class="fw-bold mb-2">
                                {{ $databaseStatus['connected'] ? 'Connected' : 'Disconnected' }}
                            </h5>
                        </div>
                        <div class="rounded-circle p-3 {{ $databaseStatus['connected'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                            <i class="fas fa-database fa-lg"></i>
                        </div>
                    </div>

                    <span class="badge {{ $databaseStatus['connected'] ? 'bg-success' : 'bg-danger' }}">
                        {{ $databaseStatus['connected'] ? 'Healthy' : 'Error' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Storage --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-2">Storage</p>
                            <h5 class="fw-bold mb-2">
                                {{ $storageStatus['writable'] ? 'Writable' : 'Not Writable' }}
                            </h5>
                        </div>
                        <div class="rounded-circle p-3 {{ $storageStatus['writable'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                            <i class="fas fa-folder-open fa-lg"></i>
                        </div>
                    </div>

                    <span class="badge {{ $storageStatus['writable'] ? 'bg-success' : 'bg-danger' }}">
                        {{ $storageStatus['writable'] ? 'Available' : 'Check Permissions' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Cache --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-2">Cache Driver</p>
                            <h5 class="fw-bold mb-2">
                                {{ strtoupper($cacheStatus['driver'] ?? 'N/A') }}
                            </h5>
                        </div>
                        <div class="rounded-circle p-3 bg-info-subtle text-info">
                            <i class="fas fa-bolt fa-lg"></i>
                        </div>
                    </div>

                    <span class="badge {{ $cacheStatus['configured'] ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $cacheStatus['configured'] ? 'Configured' : 'Not Configured' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Queue --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-2">Queue Driver</p>
                            <h5 class="fw-bold mb-2">
                                {{ strtoupper($queueStatus['driver'] ?? 'N/A') }}
                            </h5>
                        </div>
                        <div class="rounded-circle p-3 bg-warning-subtle text-warning">
                            <i class="fas fa-tasks fa-lg"></i>
                        </div>
                    </div>

                    <span class="badge {{ $queueStatus['configured'] ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $queueStatus['configured'] ? 'Configured' : 'Not Configured' }}
                    </span>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4">

        {{-- Database Information --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-database text-primary me-2"></i>
                        Database Information
                    </h6>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted">Connection Status</td>
                                    <td class="text-end">
                                        <span class="badge {{ $databaseStatus['connected'] ? 'bg-success' : 'bg-danger' }}">
                                            {{ $databaseStatus['connected'] ? 'Connected' : 'Disconnected' }}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-muted">Database Driver</td>
                                    <td class="text-end fw-semibold">
                                        {{ $databaseStatus['driver'] ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-muted">Database Name</td>
                                    <td class="text-end fw-semibold text-break">
                                        {{ $databaseStatus['database'] ?: 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-muted">Total Tables</td>
                                    <td class="text-end fw-semibold">
                                        {{ $databaseStatus['tables_count'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-muted">Connection Message</td>
                                    <td class="text-end small">
                                        {{ $databaseStatus['message'] }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Application Information --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-code text-primary me-2"></i>
                        Application Information
                    </h6>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted">Application Name</td>
                                    <td class="text-end fw-semibold">
                                        {{ $systemInfo['app_name'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-muted">Laravel Version</td>
                                    <td class="text-end fw-semibold">
                                        {{ $systemInfo['laravel_version'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-muted">PHP Version</td>
                                    <td class="text-end fw-semibold">
                                        {{ $systemInfo['php_version'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-muted">Environment</td>
                                    <td class="text-end">
                                        <span class="badge {{ $systemInfo['app_env'] === 'production' ? 'bg-success' : 'bg-info text-dark' }}">
                                            {{ strtoupper($systemInfo['app_env']) }}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-muted">Debug Mode</td>
                                    <td class="text-end">
                                        <span class="badge {{ $systemInfo['app_debug'] ? 'bg-warning text-dark' : 'bg-success' }}">
                                            {{ $systemInfo['app_debug'] ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-muted">Timezone</td>
                                    <td class="text-end fw-semibold">
                                        {{ $systemInfo['timezone'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-muted">Application Locale</td>
                                    <td class="text-end fw-semibold">
                                        {{ $systemInfo['locale'] }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($systemInfo['app_debug'] && $systemInfo['app_env'] === 'production')
                        <div class="alert alert-danger mt-3 mb-0 small">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Production environment-এ Debug Mode চালু রয়েছে। নিরাপত্তার জন্য এটি বন্ধ করুন।
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Storage Information --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-hdd text-primary me-2"></i>
                        Storage & Disk Information
                    </h6>
                </div>

                <div class="card-body">

                    @foreach([
                        'Main Storage Directory' => $storageStatus['writable'],
                        'Framework Storage' => $storageStatus['framework_writable'],
                        'Log Directory' => $storageStatus['logs_writable'],
                    ] as $label => $writable)

                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span>{{ $label }}</span>

                            <span class="badge {{ $writable ? 'bg-success' : 'bg-danger' }}">
                                {{ $writable ? 'Writable' : 'Not Writable' }}
                            </span>
                        </div>

                    @endforeach

                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <span>Default Filesystem</span>
                        <strong>{{ $storageStatus['public_disk'] ?? 'N/A' }}</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-3">
                        <span>Free Disk Space</span>
                        <strong>
                            @if(is_numeric($diskStatus['free_bytes']))
                                {{ number_format($diskStatus['free_bytes'] / 1073741824, 2) }} GB
                            @else
                                N/A
                            @endif
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-3 border-top">
                        <span>Total Disk Space</span>
                        <strong>
                            @if(is_numeric($diskStatus['total_bytes']))
                                {{ number_format($diskStatus['total_bytes'] / 1073741824, 2) }} GB
                            @else
                                N/A
                            @endif
                        </strong>
                    </div>

                </div>
            </div>
        </div>

        {{-- Runtime Configuration --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-cogs text-primary me-2"></i>
                        Runtime Configuration
                    </h6>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <span class="text-muted">Cache Configuration</span>
                        <strong>{{ $cacheStatus['driver'] ?? 'N/A' }}</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <span class="text-muted">Queue Configuration</span>
                        <strong>{{ $queueStatus['driver'] ?? 'N/A' }}</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <span class="text-muted">Maintenance Mode</span>

                        <span class="badge {{ $maintenanceStatus ? 'bg-warning text-dark' : 'bg-success' }}">
                            {{ $maintenanceStatus ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted">Application Status</span>

                        <span class="badge {{ $allHealthy ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $allHealthy ? 'Operational' : 'Needs Attention' }}
                        </span>
                    </div>

                    <div class="alert alert-info small mb-0 mt-3">
                        <i class="fas fa-info-circle me-1"></i>
                        এই পেজে বর্তমান configuration দেখানো হয়। Queue worker বা cache service বাস্তবে চলছে কি না, তা আলাদাভাবে পরীক্ষা করতে হবে।
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="text-muted small mt-4">
        <i class="fas fa-clock me-1"></i>
        Last checked: {{ now()->format('d M Y, h:i:s A') }}
    </div>

{{-- Database Backup Management --}}
<div class="card border-0 shadow-sm mt-4">

    <div class="card-header bg-white py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold mb-1">
                    <i class="fas fa-shield-alt text-primary me-2"></i>
                    Database Backup Management
                </h5>

                <p class="text-muted small mb-0">
                    ডেটাবেজের নিরাপদ ব্যাকআপ তৈরি ও পরিচালনা করুন।
                </p>
            </div>

            <span class="badge bg-primary">
                {{ $backupStats['total'] }} Backup(s)
            </span>
        </div>
    </div>

    <div class="card-body">

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-1"></i>
                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Error Message --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-1"></i>
                {{ session('error') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Backup Statistics --}}
        <div class="row g-3 mb-4">

            <div class="col-xl-4 col-md-6">
                <div class="border rounded-3 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small mb-2">
                                Total Backups
                            </div>

                            <h4 class="fw-bold mb-0">
                                {{ $backupStats['total'] }}
                            </h4>
                        </div>

                        <div class="rounded-circle bg-primary-subtle text-primary p-3">
                            <i class="fas fa-database fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="border rounded-3 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small mb-2">
                                Total Backup Storage
                            </div>

                            <h4 class="fw-bold mb-0">
                                @php
                                    $backupBytes = $backupStats['total_bytes'];
                                @endphp

                                @if($backupBytes >= 1073741824)
                                    {{ number_format($backupBytes / 1073741824, 2) }} GB
                                @elseif($backupBytes >= 1048576)
                                    {{ number_format($backupBytes / 1048576, 2) }} MB
                                @elseif($backupBytes >= 1024)
                                    {{ number_format($backupBytes / 1024, 2) }} KB
                                @else
                                    {{ $backupBytes }} bytes
                                @endif
                            </h4>
                        </div>

                        <div class="rounded-circle bg-info-subtle text-info p-3">
                            <i class="fas fa-hdd fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="border rounded-3 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small mb-2">
                                Latest Backup
                            </div>

                            <h6 class="fw-bold mb-1">
                                {{ $backupStats['latest']['created_at'] ?? 'No backup yet' }}
                            </h6>

                            <span class="text-muted small">
                                {{ $backupStats['latest']['size'] ?? '—' }}
                            </span>
                        </div>

                        <div class="rounded-circle bg-success-subtle text-success p-3">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Backup Warning --}}
        <div class="alert alert-warning small">
            <i class="fas fa-exclamation-triangle me-1"></i>

            Backup ফাইলগুলো private storage-এ রাখা হবে। তবে একই সার্ভারে রাখা
            ব্যাকআপ সার্ভার নষ্ট হওয়ার ঝুঁকি থেকে সম্পূর্ণ সুরক্ষা দেয় না।
            গুরুত্বপূর্ণ ডেটার আলাদা নিরাপদ off-site copy রাখুন।
        </div>

        {{-- Create Full Backup --}}
        <div class="border rounded-3 p-3 mb-4">
            <div class="row align-items-center g-3">

                <div class="col-lg">
                    <h6 class="fw-bold mb-1">
                        <i class="fas fa-database text-primary me-2"></i>
                        Full Database Backup
                    </h6>

                    <p class="text-muted small mb-0">
                        ডেটাবেজের সব টেবিল ও তাদের ডেটা একটি compressed backup-এ
                        সংরক্ষণ করুন।
                    </p>
                </div>

                <div class="col-lg-auto">
                    <form method="POST"
                          action="{{ route('dashboard.database-system-status.backup') }}"
                          onsubmit="return confirm('সম্পূর্ণ Database Backup তৈরি করতে চান?');">

                        @csrf

                        <input type="hidden"
                               name="backup_type"
                               value="full">

                        <button type="submit"
                                class="btn btn-primary"
                                {{ !$databaseStatus['connected'] || !$backupStats['directory_writable'] ? 'disabled' : '' }}>

                            <i class="fas fa-download me-1"></i>
                            Create Full Backup
                        </button>
                    </form>
                </div>

            </div>
        </div>

        {{-- Selected Table Backup --}}
        <div class="border rounded-3 p-3 mb-4">

            <h6 class="fw-bold mb-1">
                <i class="fas fa-table text-primary me-2"></i>
                Selected Table Backup
            </h6>

            <p class="text-muted small mb-3">
                প্রয়োজনীয় টেবিলগুলো নির্বাচন করে আলাদা backup তৈরি করুন।
            </p>

            @if(count($tableNames) > 0)

                <form method="POST"
                      action="{{ route('dashboard.database-system-status.backup') }}"
                      id="selectedTableBackupForm"
                      onsubmit="return confirm('নির্বাচিত টেবিলগুলোর Backup তৈরি করতে চান?');">

                    @csrf

                    <input type="hidden"
                           name="backup_type"
                           value="selected">

                    <div class="d-flex flex-wrap gap-2 mb-3">

                        <button type="button"
                                class="btn btn-sm btn-outline-primary"
                                id="selectAllBackupTables">
                            Select All
                        </button>

                        <button type="button"
                                class="btn btn-sm btn-outline-secondary"
                                id="clearBackupTables">
                            Clear Selection
                        </button>

                        <span class="text-muted small align-self-center"
                              id="selectedTableCount">
                            0 table(s) selected
                        </span>

                    </div>

                    <div class="row g-2 mb-3">

                        @foreach($tableNames as $tableName)

                            <div class="col-xl-3 col-lg-4 col-md-6">

                                <label class="border rounded-2 p-2 d-flex align-items-center gap-2 h-100"
                                       style="cursor:pointer;">

                                    <input type="checkbox"
                                           class="form-check-input backup-table-checkbox mt-0"
                                           name="selected_tables[]"
                                           value="{{ $tableName }}">

                                    <span class="small text-break">
                                        {{ $tableName }}
                                    </span>

                                </label>

                            </div>

                        @endforeach

                    </div>

                    @error('selected_tables')
                        <div class="text-danger small mb-2">
                            {{ $message }}
                        </div>
                    @enderror

                    @error('selected_tables.*')
                        <div class="text-danger small mb-2">
                            {{ $message }}
                        </div>
                    @enderror

                    <button type="submit"
                            class="btn btn-outline-primary"
                            {{ !$databaseStatus['connected'] || !$backupStats['directory_writable'] ? 'disabled' : '' }}>

                        <i class="fas fa-file-archive me-1"></i>
                        Backup Selected Tables
                    </button>

                </form>

            @else

                <div class="alert alert-secondary mb-0">
                    Database connection পাওয়া যায়নি অথবা কোনো table নেই।
                </div>

            @endif

        </div>

        {{-- Backup History --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">

            <div>
                <h6 class="fw-bold mb-1">
                    <i class="fas fa-history text-primary me-2"></i>
                    Backup History
                </h6>

                <p class="text-muted small mb-0">
                    আগে তৈরি করা backup ফাইলগুলো এখান থেকে ডাউনলোড বা মুছে ফেলতে পারবেন।
                </p>
            </div>

            <span class="badge bg-light text-dark border">
                {{ count($backupHistory) }} file(s)
            </span>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Backup File</th>
                        <th>Type</th>
                        <th>File Size</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($backupHistory as $backup)

                        <tr>

                            <td>
                                <div class="d-flex align-items-center gap-2">

                                    <div class="rounded bg-primary-subtle text-primary p-2">
                                        <i class="fas fa-file-archive"></i>
                                    </div>

                                    <div>
                                        <div class="fw-semibold text-break">
                                            {{ $backup['filename'] }}
                                        </div>

                                        <div class="text-muted small">
                                            Compressed SQL backup
                                        </div>
                                    </div>

                                </div>
                            </td>

                            <td>
                                <span class="badge {{ $backup['type'] === 'Full Database' ? 'bg-primary' : 'bg-info text-dark' }}">
                                    {{ $backup['type'] }}
                                </span>
                            </td>

                            <td>
                                {{ $backup['size'] }}
                            </td>

                            <td>
                                {{ $backup['created_at'] }}
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">

                                    <a href="{{ route('dashboard.database-system-status.backup.download', ['filename' => $backup['filename']]) }}"
                                       class="btn btn-sm btn-outline-success"
                                       title="Download Backup">

                                        <i class="fas fa-download"></i>
                                    </a>

                                    <form method="POST"
                                          action="{{ route('dashboard.database-system-status.backup.delete', ['filename' => $backup['filename']]) }}"
                                          onsubmit="return confirm('এই Backup ফাইলটি স্থায়ীভাবে মুছে ফেলতে চান?');">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete Backup">

                                            <i class="fas fa-trash"></i>
                                        </button>

                                    </form>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center py-5">

                                <i class="fas fa-database fa-3x text-muted mb-3"></i>

                                <h6 class="fw-bold">
                                    No Backups Found
                                </h6>

                                <p class="text-muted small mb-0">
                                    আপনার প্রথম Database Backup তৈরি করতে উপরের বাটন ব্যবহার করুন।
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- Selected table counter --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.backup-table-checkbox');
    const countElement = document.getElementById('selectedTableCount');
    const selectAllButton = document.getElementById('selectAllBackupTables');
    const clearButton = document.getElementById('clearBackupTables');

    function updateSelectedCount() {
        if (!countElement) {
            return;
        }

        const selectedCount = document.querySelectorAll(
            '.backup-table-checkbox:checked'
        ).length;

        countElement.textContent = selectedCount + ' table(s) selected';
    }

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    if (selectAllButton) {
        selectAllButton.addEventListener('click', function () {
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = true;
            });

            updateSelectedCount();
        });
    }

    if (clearButton) {
        clearButton.addEventListener('click', function () {
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });

            updateSelectedCount();
        });
    }

    updateSelectedCount();
});
</script>
@endpush

@endsection
