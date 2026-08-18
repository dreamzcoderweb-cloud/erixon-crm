@extends('layouts.master')
@section('title', 'Dashboard - Super Admin')

@section('content')
<style>
    .dashboard-card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
        transition: all 0.25s ease-in-out;
    }
    .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px 0 rgba(67, 89, 113, 0.18);
    }
    .stat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .bg-label-primary {
        background-color: rgba(105, 108, 255, 0.12) !important;
        color: #696cff !important;
    }
    .bg-label-success {
        background-color: rgba(113, 221, 55, 0.12) !important;
        color: #71dd37 !important;
    }
    .bg-label-warning {
        background-color: rgba(255, 171, 0, 0.12) !important;
        color: #ffab00 !important;
    }
    .bg-label-info {
        background-color: rgba(3, 195, 236, 0.12) !important;
        color: #03c3ec !important;
    }
    .bg-label-danger {
        background-color: rgba(255, 62, 29, 0.12) !important;
        color: #ff3e1d !important;
    }
    .bg-label-dark {
        background-color: rgba(67, 89, 113, 0.12) !important;
        color: #435d78 !important;
    }
    .bg-label-pink {
        background-color: rgba(232, 62, 140, 0.12) !important;
        color: #e83e8c !important;
    }
    .bg-label-purple {
        background-color: rgba(111, 66, 193, 0.12) !important;
        color: #6f42c1 !important;
    }
    .quick-action-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.7rem 0.9rem;
        border-radius: 0.5rem;
        text-decoration: none;
        color: #566a7f;
        transition: all 0.2s ease;
        margin-bottom: 0.5rem;
        background-color: #f8f9fa;
    }
    .quick-action-item:hover {
        background-color: rgba(105, 108, 255, 0.08);
        color: #696cff;
        transform: translateX(4px);
    }
    .recent-table th {
        background-color: #eef1f6 !important;
        color: #566a7f;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        padding: 0.85rem 1rem;
    }
    .recent-table td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
    }
    .badge-pill-custom {
        font-weight: 600;
        padding: 0.35em 0.75em;
        border-radius: 50rem;
        font-size: 0.75rem;
    }
    .module-progress-bar {
        height: 6px;
        border-radius: 10px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Section -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Dashboard</h4>
            <div class="text-muted small">Live active count & overview of all project modules</div>
        </div>
        <div class="badge bg-white text-muted shadow-sm px-3 py-2 border rounded-pill d-flex align-items-center gap-1">
            <i class="bx bx-calendar text-primary fs-6"></i>
            <span class="fw-semibold">{{ now()->format('d M Y') }}</span>
        </div>
    </div>

    <!-- CORE MODULES STAT CARDS (ROW 1) -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- Module 1: Customers -->
        <div class="col-12 col-sm-6 col-xl-3">
            @can('customers.view')
                <a href="{{ route('admin.customers.index') }}" class="text-decoration-none d-block h-100">
            @else
                <a href="javascript:void(0);" class="text-decoration-none d-block h-100">
            @endcan
            <div class="card dashboard-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-box bg-label-primary">
                            <i class="bx bx-user"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Customers</div>
                            <div class="h3 fw-bold mb-0 text-dark">{{ number_format($totalcustomers ?? 0) }}</div>
                            <div class="text-muted small mt-1">
                                <span class="text-success fw-semibold">{{ $activecustomers ?? 0 }} Active</span> - {{ $inactivecustomers ?? 0 }} Inactive
                            </div>
                        </div>
                    </div>
                    @can('customers.view')
                        <i class="bx bx-chevron-right text-muted fs-4"></i>
                    @endcan
                </div>
            </div>
            </a>
        </div>

        <!-- Module 2: Leads -->
        <div class="col-12 col-sm-6 col-xl-3">
            @can('leads.view')
                <a href="{{ route('admin.leads.index') }}" class="text-decoration-none d-block h-100">
            @else
                <a href="javascript:void(0);" class="text-decoration-none d-block h-100">
            @endcan
            <div class="card dashboard-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-box bg-label-success">
                            <i class="bx bx-target-lock"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Leads</div>
                            <div class="h3 fw-bold mb-0 text-dark">{{ number_format($totalleads ?? 0) }}</div>
                            <div class="text-muted small mt-1">
                                <span class="text-success fw-semibold">{{ $activeleads ?? 0 }} Active Leads</span>
                            </div>
                        </div>
                    </div>
                    @can('leads.view')
                        <i class="bx bx-chevron-right text-muted fs-4"></i>
                    @endcan
                </div>
            </div>
            </a>
        </div>

        <!-- Module 3: Staff -->
        <div class="col-12 col-sm-6 col-xl-3">
            @can('staff.view')
                <a href="{{ route('admin.staff.index') }}" class="text-decoration-none d-block h-100">
            @else
                <a href="javascript:void(0);" class="text-decoration-none d-block h-100">
            @endcan
            <div class="card dashboard-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-box bg-label-warning">
                            <i class="bx bx-group"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Staff Members</div>
                            <div class="h3 fw-bold mb-0 text-dark">{{ number_format($totalstaff ?? 0) }}</div>
                            <div class="text-muted small mt-1">Excluding Admins</div>
                        </div>
                    </div>
                    @can('staff.view')
                        <i class="bx bx-chevron-right text-muted fs-4"></i>
                    @endcan
                </div>
            </div>
            </a>
        </div>

        <!-- Module 4: Roles / Access Control -->
        <div class="col-12 col-sm-6 col-xl-3">
            @can('roles.view')
                <a href="{{ route('admin.roles.index') }}" class="text-decoration-none d-block h-100">
            @else
                <a href="javascript:void(0);" class="text-decoration-none d-block h-100">
            @endcan
            <div class="card dashboard-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-box bg-label-info">
                            <i class="bx bx-shield-quarter"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Roles & Access</div>
                            <div class="h3 fw-bold mb-0 text-dark">{{ number_format($totalroles ?? 0) }}</div>
                            <div class="text-muted small mt-1">Permission Groups</div>
                        </div>
                    </div>
                    @can('roles.view')
                        <i class="bx bx-chevron-right text-muted fs-4"></i>
                    @endcan
                </div>
            </div>
            </a>
        </div>
    </div>

    <!-- MASTER MODULES STAT CARDS (ROW 2) -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- Module 5: Lead Sources -->
        <div class="col-12 col-sm-6 col-xl-3">
            @can('lead-sources.view')
                <a href="{{ route('admin.lead-sources.index') }}" class="text-decoration-none d-block h-100">
            @else
                <a href="javascript:void(0);" class="text-decoration-none d-block h-100">
            @endcan
            <div class="card dashboard-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-box bg-label-purple">
                            <i class="bx bx-git-repo-forked"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Lead Sources</div>
                            <div class="h3 fw-bold mb-0 text-dark">{{ number_format($totallead_sources ?? 0) }}</div>
                            <div class="text-muted small mt-1">
                                <span class="text-success fw-semibold">{{ $activelead_sources ?? 0 }} Active</span>
                            </div>
                        </div>
                    </div>
                    @can('lead-sources.view')
                        <i class="bx bx-chevron-right text-muted fs-4"></i>
                    @endcan
                </div>
            </div>
            </a>
        </div>

        <!-- Module 6: Lead Stages -->
        <div class="col-12 col-sm-6 col-xl-3">
            @can('lead-stages.view')
                <a href="{{ route('admin.lead-stages.index') }}" class="text-decoration-none d-block h-100">
            @else
                <a href="javascript:void(0);" class="text-decoration-none d-block h-100">
            @endcan
            <div class="card dashboard-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-box bg-label-pink">
                            <i class="bx bx-bar-chart-alt-2"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Lead Stages</div>
                            <div class="h3 fw-bold mb-0 text-dark">{{ number_format($totallead_stages ?? 0) }}</div>
                            <div class="text-muted small mt-1">
                                <span class="text-success fw-semibold">{{ $activelead_stages ?? 0 }} Active</span>
                            </div>
                        </div>
                    </div>
                    @can('lead-stages.view')
                        <i class="bx bx-chevron-right text-muted fs-4"></i>
                    @endcan
                </div>
            </div>
            </a>
        </div>

        <!-- Module 7: Lead Requirements -->
        <div class="col-12 col-sm-6 col-xl-3">
            @can('lead-requirements.view')
                <a href="{{ route('admin.lead-requirements.index') }}" class="text-decoration-none d-block h-100">
            @else
                <a href="javascript:void(0);" class="text-decoration-none d-block h-100">
            @endcan
            <div class="card dashboard-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-box bg-label-dark">
                            <i class="bx bx-list-check"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Lead Requirements</div>
                            <div class="h3 fw-bold mb-0 text-dark">{{ number_format($totallead_requirements ?? 0) }}</div>
                            <div class="text-muted small mt-1">
                                <span class="text-success fw-semibold">{{ $activelead_requirements ?? 0 }} Active</span>
                            </div>
                        </div>
                    </div>
                    @can('lead-requirements.view')
                        <i class="bx bx-chevron-right text-muted fs-4"></i>
                    @endcan
                </div>
            </div>
            </a>
        </div>

        <!-- Module 8: Follow-ups -->
        <div class="col-12 col-sm-6 col-xl-3">
            @can('followups.view')
                <a href="{{ route('admin.followups.index') }}" class="text-decoration-none d-block h-100">
            @else
                <a href="javascript:void(0);" class="text-decoration-none d-block h-100">
            @endcan
            <div class="card dashboard-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-box bg-label-danger">
                            <i class="bx bx-calendar-event"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Follow-ups</div>
                            <div class="h3 fw-bold mb-0 text-dark">{{ number_format($totalfollowups ?? 0) }}</div>
                            <div class="text-muted small mt-1">
                                <span class="text-danger fw-semibold">{{ $pendingfollowups ?? 0 }} Pending</span>
                            </div>
                        </div>
                    </div>
                    @can('followups.view')
                        <i class="bx bx-chevron-right text-muted fs-4"></i>
                    @endcan
                </div>
            </div>
            </a>
        </div>
    </div>

    <!-- MAIN SECTION (2 COLUMNS GRID) -->
    <div class="row g-4">
        <!-- Left Column: Recent Data Tables (~68% width) -->
        <div class="col-12 col-lg-8 d-flex flex-column gap-4">
            <!-- Recent Customers Table Card -->
            <div class="card dashboard-card border-0">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bx bx-user text-primary fs-4"></i>
                        <h5 class="card-title mb-0 fw-semibold text-dark">Recent Customers</h5>
                    </div>
                    @can('customers.view')
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-medium">View All</a>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 recent-table">
                        <thead>
                            <tr>
                                <th>Client ID</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCustomers as $client)
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-primary">{{ $loop->iteration }}</span>
                                    </td>
                                    <td class="fw-medium text-dark">{{ $client->name }}</td>
                                    <td class="text-muted">{{ $client->mobile }}</td>
                                    <td class="text-muted small">{{ $client->email ?? 'N/A' }}</td>
                                    <td>
                                        @if($client->status == 1)
                                            <span class="badge bg-label-success badge-pill-custom">Active</span>
                                        @else
                                            <span class="badge bg-label-danger badge-pill-custom">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No customers registered yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Leads Table Card -->
            <div class="card dashboard-card border-0">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bx bx-target-lock text-success fs-4"></i>
                        <h5 class="card-title mb-0 fw-semibold text-dark">Recent Leads</h5>
                    </div>
                    @can('leads.view')
                        <a href="{{ route('admin.leads.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-medium">View All</a>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 recent-table">
                        <thead>
                            <tr>
                                <th>Lead Title</th>
                                <th>Customer</th>
                                <th>Stage</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLeads as $lead)
                                <tr>
                                    <td class="fw-medium text-dark">{{ $lead->lead_title }}</td>
                                    <td class="text-muted">{{ $lead->customer?->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-label-info badge-pill-custom">{{ $lead->leadStage?->name ?? 'General' }}</span>
                                    </td>
                                    <td class="text-muted small">{{ $lead->assignedUser?->name ?? 'Unassigned' }}</td>
                                    <td>
                                        @if($lead->status == 1)
                                            <span class="badge bg-label-success badge-pill-custom">Active</span>
                                        @else
                                            <span class="badge bg-label-secondary badge-pill-custom">Closed</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No leads recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Module Overview & Quick Actions (~32% width) -->
        <div class="col-12 col-lg-4 d-flex flex-column gap-4">
            <!-- Quick Actions Card -->
            <div class="card dashboard-card border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-semibold text-dark">Quick Actions</h5>

                    @can('customers.view')
                        <a href="{{ route('admin.customers.index') }}" class="quick-action-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon-box bg-label-primary" style="width:36px; height:36px; font-size:1.1rem;">
                                    <i class="bx bx-user-plus"></i>
                                </div>
                                <span class="fw-medium">Manage Customers</span>
                            </div>
                            <i class="bx bx-chevron-right text-muted"></i>
                        </a>
                    @endcan

                    @can('leads.view')
                        <a href="{{ route('admin.leads.index') }}" class="quick-action-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon-box bg-label-success" style="width:36px; height:36px; font-size:1.1rem;">
                                    <i class="bx bx-target-lock"></i>
                                </div>
                                <span class="fw-medium">Manage Leads</span>
                            </div>
                            <i class="bx bx-chevron-right text-muted"></i>
                        </a>
                    @endcan

                    @can('staff.view')
                        <a href="{{ route('admin.staff.index') }}" class="quick-action-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon-box bg-label-warning" style="width:36px; height:36px; font-size:1.1rem;">
                                    <i class="bx bx-group"></i>
                                </div>
                                <span class="fw-medium">Manage Staff</span>
                            </div>
                            <i class="bx bx-chevron-right text-muted"></i>
                        </a>
                    @endcan

                    @can('followups.view')
                        <a href="{{ route('admin.followups.index') }}" class="quick-action-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon-box bg-label-danger" style="width:36px; height:36px; font-size:1.1rem;">
                                    <i class="bx bx-calendar-event"></i>
                                </div>
                                <span class="fw-medium">Follow-ups</span>
                            </div>
                            <i class="bx bx-chevron-right text-muted"></i>
                        </a>
                    @endcan

                    @can('general-settings.view')
                        <a href="{{ route('admin.settings.general') }}" class="quick-action-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon-box bg-label-info" style="width:36px; height:36px; font-size:1.1rem;">
                                    <i class="bx bx-cog"></i>
                                </div>
                                <span class="fw-medium">System Settings</span>
                            </div>
                            <i class="bx bx-chevron-right text-muted"></i>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="loginToast" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body text-dark">
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var loginToast = new bootstrap.Toast(document.getElementById('loginToast'));
            loginToast.show();
        });
    </script>
@endif
@endsection
