<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme mb-5"
id="layout-navbar">
<div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
        <i class="bx bx-menu bx-md"></i>
    </a>
</div>

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center">
        <div class="nav-item d-flex align-items-center">
            <i class="bx bx-search bx-md"></i>
            <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2"
                placeholder="Search..." aria-label="Search..." />
        </div>
    </div>
    <!-- /Search -->

    <ul class="navbar-nav flex-row align-items-center ms-auto">
        <!-- Notifications -->
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" id="notificationDropdownToggle">
                <i class="bx bx-bell bx-sm"></i>
                <span class="badge bg-danger rounded-pill badge-notifications d-none" id="notification-badge-count">0</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end py-0" style="min-width: 320px; max-width: 380px;">
                <li class="dropdown-menu-header border-bottom">
                    <div class="dropdown-header d-flex align-items-center py-3">
                        <h6 class="text-body mb-0 me-auto fw-bold"><i class="bx bx-bell me-1"></i> Notifications</h6>
                        <a href="javascript:void(0)" class="dropdown-notifications-all text-body" id="mark-all-read-btn" title="Mark all as read">
                            <span class="badge bg-label-primary fs-tiny">Mark all as read</span>
                        </a>
                    </div>
                </li>
                <li class="dropdown-notifications-list scrollable-container" style="max-height: 320px; overflow-y: auto;">
                    <ul class="list-group list-group-flush" id="notification-list-container">
                        <li class="list-group-item text-center text-muted py-3">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span> Loading...
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
        <!--/ Notifications -->

        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);"
                data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    <img src="{{ Auth::user()?->profile_image_url }}" alt="avatar" class="w-px-40 h-auto rounded-circle" style="object-fit: cover; width: 40px; height: 40px;" />
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ url('admin/profile') }}">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    <img src="{{ Auth::user()?->profile_image_url }}" alt="avatar" class="w-px-40 h-auto rounded-circle" style="object-fit: cover; width: 40px; height: 40px;" />
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ Auth::user()?->name ?? 'Admin' }}</h6>
                                <small class="text-muted">{{ Auth::user()?->roles?->first()?->name ?? Auth::user()?->email }}</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider my-1"></div>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ url('admin/profile') }}">
                        <i class="bx bx-user bx-md me-3"></i><span>My Profile</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ url('admin/logout') }}">
                        <i class="bx bx-power-off bx-md me-3"></i><span>Log Out</span>
                    </a>
                </li>
            </ul>
        </li>
        <!--/ User -->
    </ul>
</div>
</nav>
