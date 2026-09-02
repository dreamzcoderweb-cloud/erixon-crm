@extends('layouts.master')
@section('title', 'Add Role - Super Admin')

@section('content')
<style>
    .role-form-card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 2px 8px rgba(67, 89, 113, 0.1);
    }
    .module-card {
        border: 1px solid #e9ecef;
        border-radius: 0.65rem;
        transition: all 0.2s ease-in-out;
        background: #fff;
    }
    .module-card:hover {
        border-color: rgba(105, 108, 255, 0.4);
        box-shadow: 0 4px 12px rgba(105, 108, 255, 0.08);
    }
    .module-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 0.75rem 1rem;
        border-top-left-radius: 0.65rem;
        border-top-right-radius: 0.65rem;
    }
    .perm-badge {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        padding: 0.2rem 0.45rem;
        border-radius: 4px;
        margin-left: 0.35rem;
    }
    .badge-view { background: rgba(3, 195, 236, 0.12); color: #03c3ec; }
    .badge-create { background: rgba(113, 221, 55, 0.12); color: #71dd37; }
    .badge-edit { background: rgba(255, 171, 0, 0.12); color: #ffab00; }
    .badge-delete { background: rgba(255, 62, 29, 0.12); color: #ff3e1d; }
    .badge-other { background: rgba(105, 108, 255, 0.12); color: #696cff; }

    .form-check-label {
        font-size: 0.875rem;
        color: #566a7f;
        cursor: pointer;
        user-select: none;
    }
    .form-check-input:checked + .form-check-label {
        color: #696cff;
        font-weight: 600;
    }
    .perm-item {
        padding: 0.4rem 0.6rem;
        border-radius: 0.4rem;
        transition: background 0.15s ease;
    }
    .perm-item:hover {
        background-color: rgba(105, 108, 255, 0.04);
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Access Control</a></li>
                    <li class="breadcrumb-item active">Add Role</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-dark">Add New Role</h4>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bx bx-arrow-back me-1"></i> Back to Roles
        </a>
    </div>

    <form action="{{ url('admin/add_role') }}" method="POST" id="roleForm">
        @csrf

        <!-- Role Information Section -->
        <div class="card role-form-card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <label for="name" class="form-label fw-semibold text-dark fs-6">
                            Role Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bx bx-shield text-primary"></i></span>
                            <input type="text" class="form-control border-start-0 ps-2 @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Sales Manager, Accountant, HR Admin" required />
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1"><i class="bx bx-error-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mt-3 mt-md-0 text-md-end">
                        <span class="badge bg-label-primary px-3 py-2 fs-6 rounded-pill" id="selectedCountBadge">
                            <i class="bx bx-check-double me-1"></i> <span id="selectedCount">0</span> Permissions Selected
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Header Controls -->
        <div class="card role-form-card mb-4">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <!-- Master Select All -->
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="masterSelectAll" style="width: 2.5em; height: 1.3em; cursor: pointer;">
                        <label class="form-check-label fw-bold text-dark ms-2 align-middle fs-6" for="masterSelectAll">
                            Select All Permissions
                        </label>
                    </div>

                    <!-- Search Filter -->
                    <div class="position-relative" style="min-width: 260px;">
                        <input type="text" class="form-control form-control-sm ps-4 rounded-pill" id="permSearch" placeholder="" />
                        <i class="bx bx-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
                    </div>
                </div>
            </div>
        </div>

        @php
            // Define module icons and pretty titles
            $moduleMeta = [
                'customers' => ['title' => 'Customers Management', 'icon' => 'bx-user', 'badge' => 'bg-label-primary'],
                'leads' => ['title' => 'Leads Management', 'icon' => 'bx-target-lock', 'badge' => 'bg-label-success'],
                'lead-sources' => ['title' => 'Lead Sources', 'icon' => 'bx-git-repo-forked', 'badge' => 'bg-label-purple'],
                'coordinations' => ['title' => 'Coordination', 'icon' => 'bx-link-external', 'badge' => 'bg-label-info'],
                'lead-stages' => ['title' => 'Lead Stages', 'icon' => 'bx-bar-chart-alt-2', 'badge' => 'bg-label-pink'],
                'lead-requirements' => ['title' => 'Lead Requirements', 'icon' => 'bx-list-check', 'badge' => 'bg-label-dark'],
                'lost-reasons' => ['title' => 'Lost Reasons', 'icon' => 'bx-dislike', 'badge' => 'bg-label-danger'],
                'followups' => ['title' => 'Follow-ups Management', 'icon' => 'bx-calendar-event', 'badge' => 'bg-label-danger'],
                'staff' => ['title' => 'Staff Management', 'icon' => 'bx-group', 'badge' => 'bg-label-warning'],
                'roles' => ['title' => 'Roles & Access Control', 'icon' => 'bx-shield-quarter', 'badge' => 'bg-label-info'],
                'general-settings' => ['title' => 'General Settings', 'icon' => 'bx-cog', 'badge' => 'bg-label-secondary'],
                'lead-settings' => ['title' => 'Lead Settings', 'icon' => 'bx-gift', 'badge' => 'bg-label-primary'],
                'customer-settings' => ['title' => 'Customer Settings', 'icon' => 'bx-user', 'badge' => 'bg-label-info'],
                'followup-settings' => ['title' => 'Followup Settings', 'icon' => 'bx-calendar-event', 'badge' => 'bg-label-warning'],
                'credit-request-settings' => ['title' => 'Credit Request Settings', 'icon' => 'bx-credit-card', 'badge' => 'bg-label-primary'],
                'profile' => ['title' => 'Profile Settings', 'icon' => 'bx-user-circle', 'badge' => 'bg-label-dark'],
                'dashboard' => ['title' => 'Dashboard Access', 'icon' => 'bx-home-smile', 'badge' => 'bg-label-primary'],
            ];

            // Group permissions by prefix
            $grouped = $permissions->groupBy(function($item) {
                $parts = explode('.', $item->name);
                return count($parts) > 1 ? $parts[0] : 'general';
            });
        @endphp

        <!-- Grouped Permission Cards Matrix -->
        <div class="row g-4" id="permissionsContainer">
            @foreach($grouped as $module => $modulePerms)
                @php
                    $meta = $moduleMeta[$module] ?? [
                        'title' => ucfirst(str_replace('-', ' ', $module)),
                        'icon' => 'bx-key',
                        'badge' => 'bg-label-primary'
                    ];
                @endphp
                <div class="col-12 col-md-6 col-xl-4 module-col" data-module="{{ strtolower($meta['title'] . ' ' . $module) }}">
                    <div class="module-card h-100">
                        <div class="module-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge {{ $meta['badge'] }} p-2 rounded">
                                    <i class="bx {{ $meta['icon'] }} fs-5"></i>
                                </span>
                                <h6 class="mb-0 fw-bold text-dark">{{ $meta['title'] }}</h6>
                            </div>
                            <div class="form-check mb-0">
                                <input class="form-check-input module-toggle" type="checkbox" id="mod_toggle_{{ Str::slug($module) }}" data-module-group="{{ Str::slug($module) }}" title="Select All in {{ $meta['title'] }}">
                                <label class="form-check-label small fw-semibold text-muted" for="mod_toggle_{{ Str::slug($module) }}">Select All</label>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2">
                                @foreach($modulePerms as $perm)
                                    @php
                                        $action = Str::afterLast($perm->name, '.');
                                        $badgeClass = match($action) {
                                            'view' => 'badge-view',
                                            'create' => 'badge-create',
                                            'edit' => 'badge-edit',
                                            'delete' => 'badge-delete',
                                            default => 'badge-other'
                                        };
                                        $isChecked = in_array($perm->id, old('permissions', []));
                                    @endphp
                                    <div class="col-12 perm-item-col" data-perm-name="{{ strtolower($perm->name) }}">
                                        <div class="perm-item d-flex align-items-center justify-content-between">
                                            <div class="form-check mb-0 flex-grow-1">
                                                <input class="form-check-input perm-check mod-group-{{ Str::slug($module) }}"
                                                    type="checkbox"
                                                    id="perm_{{ $perm->id }}"
                                                    name="permissions[]"
                                                    value="{{ $perm->id }}"
                                                    {{ $isChecked ? 'checked' : '' }}>
                                                <label class="form-check-label d-inline-block text-truncate" for="perm_{{ $perm->id }}" style="max-width: 180px;">
                                                    {{ $perm->name }}
                                                </label>
                                            </div>
                                            <span class="perm-badge {{ $badgeClass }}">{{ $action }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @error('permissions')
            <div class="alert alert-danger mt-3">{{ $message }}</div>
        @enderror

        <!-- Sticky Footer Form Actions Bar -->
        <div class="card role-form-card mt-4 mb-4">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <span class="text-muted small">Double-check all selected permissions before saving the role.</span>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">
                        <i class="bx bx-save me-1"></i> Save Role
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const permChecks = document.querySelectorAll('.perm-check');
    const masterSelectAll = document.getElementById('masterSelectAll');
    const moduleToggles = document.querySelectorAll('.module-toggle');
    const selectedCountSpan = document.getElementById('selectedCount');
    const permSearch = document.getElementById('permSearch');

    // Update Counter & Master Toggle State
    function updateCounter() {
        const checked = document.querySelectorAll('.perm-check:checked');
        selectedCountSpan.textContent = checked.length;

        // Master toggle sync
        if(masterSelectAll) {
            masterSelectAll.checked = (checked.length === permChecks.length && permChecks.length > 0);
            masterSelectAll.indeterminate = (checked.length > 0 && checked.length < permChecks.length);
        }

        // Module toggles sync
        moduleToggles.forEach(toggle => {
            const group = toggle.getAttribute('data-module-group');
            const groupChecks = document.querySelectorAll('.mod-group-' + group);
            const groupChecked = document.querySelectorAll('.mod-group-' + group + ':checked');

            toggle.checked = (groupChecked.length === groupChecks.length && groupChecks.length > 0);
            toggle.indeterminate = (groupChecked.length > 0 && groupChecked.length < groupChecks.length);
        });
    }

    // Master Select All Event
    if(masterSelectAll) {
        masterSelectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            permChecks.forEach(cb => { cb.checked = isChecked; });
            moduleToggles.forEach(mt => { mt.checked = isChecked; mt.indeterminate = false; });
            updateCounter();
        });
    }

    // Module Select All Toggle Event
    moduleToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const group = this.getAttribute('data-module-group');
            const isChecked = this.checked;
            document.querySelectorAll('.mod-group-' + group).forEach(cb => {
                cb.checked = isChecked;
            });
            updateCounter();
        });
    });

    // Individual Permission Check Event
    permChecks.forEach(cb => {
        cb.addEventListener('change', updateCounter);
    });

    // Live Search Filter
    if(permSearch) {
        permSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const moduleCols = document.querySelectorAll('.module-col');

            moduleCols.forEach(col => {
                const moduleName = col.getAttribute('data-module');
                const permCols = col.querySelectorAll('.perm-item-col');
                let hasVisiblePerms = false;

                permCols.forEach(permCol => {
                    const permName = permCol.getAttribute('data-perm-name');
                    if(permName.includes(query) || moduleName.includes(query)) {
                        permCol.style.display = '';
                        hasVisiblePerms = true;
                    } else {
                        permCol.style.display = 'none';
                    }
                });

                if(hasVisiblePerms) {
                    col.style.display = '';
                } else {
                    col.style.display = 'none';
                }
            });
        });
    }

    // Initial counter calc
    updateCounter();
});
</script>
@endsection
