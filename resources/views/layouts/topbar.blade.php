    <div class="admin-topbar">
      <div class="d-flex align-items-center gap-2">
        <button class="btn-icon" type="button" id="sidebarToggle" title="Toggle menu">
          <i class="fas fa-bars"></i>
        </button>
@php
    $authUser = auth()->user();

    $activeInstitutionId = session('active_institution_id');

    $activeInstitution = $activeInstitutionId
        ? \App\Models\Madrasa::find($activeInstitutionId)
        : $authUser?->institution;

    $defaultTitle = $activeInstitution
        ? ($activeInstitution->name_bn ?: $activeInstitution->name)
        : 'Dashboard';

    $pageTitle = trim($__env->yieldContent('title'));

    $finalTitle = $pageTitle ?: $defaultTitle;
@endphp

<h6 class="mb-0 fw-bold text-dark">
    {{ $finalTitle }}
</h6>
      </div>

      <div class="d-flex align-items-center gap-2">
        
        {{-- Notifications --}}
        <div class="dropdown">
          <button class="btn-icon position-relative" id="notiBtn" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="far fa-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                  id="notiBadge" style="display:none;">0</span>
          </button>

          <ul class="dropdown-menu dropdown-menu-end p-0" style="width:320px;" id="notiMenu">
            <li class="notification_topbar d-flex justify-content-between align-items-center px-3 py-2">
              <span class="fw-bold">Notification</span>
              <button class="btn btn-link p-0 text-decoration-none" type="button" id="clearAllBtn">Clear All</button>
            </li>
            <li><div class="px-3 py-3 text-muted">No notifications</div></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-center small" href="{{ url('dashboard/notifications') }}">View all</a></li>
          </ul>
        </div>

        {{-- Theme Toggle --}}
<button class="btn-icon me-2" id="themeToggle" type="button" title="Toggle theme">
  <i class="fas fa-moon" id="themeMoon"></i>
  <i class="fas fa-sun d-none" id="themeSun"></i>
</button>

{{-- User Dropdown --}}
<div class="dropdown">

  <button class="user-dd-btn" data-bs-toggle="dropdown" aria-expanded="false">

    <span class="avatar p-0 overflow-hidden">
      @if(!empty($authUser->photo))
        <img src="{{ asset('uploads/users/'.$authUser->photo) }}"
             alt="User"
             style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
      @else
        <i class="fas fa-user"></i>
      @endif
    </span>

    <span class="user-meta d-none d-md-block">
      <span class="name">{{ $authUser->name ?? 'User' }}</span><br>
      <span class="role">
    {{ optional($authUser->role)->role_name }}
</span>
    </span>

    <i class="fas fa-chevron-down text-muted ms-1"></i>
  </button>

  <ul class="dropdown-menu dropdown-menu-end">
    <li>
      <a class="dropdown-item" href="{{ route('dashboard.profile') }}">
        <i class="fas fa-user me-2"></i> Profile
      </a>
    </li>
    <li>
      <a class="dropdown-item" href="{{ route('dashboard.settings.index') }}">
        <i class="fas fa-cog me-2"></i> Manage Settings
      </a>
    </li>
    <li><hr class="dropdown-divider"></li>
    <li>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="dropdown-item">
          <i class="fas fa-sign-out-alt me-2"></i> Logout
        </button>
      </form>
    </li>
  </ul>

</div>


</div>
</div>