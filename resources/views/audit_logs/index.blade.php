@extends('layouts.master')
@section('title', 'Audit Logs - Admin')

@section('content')
    <style>
        .audit-stat-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 2px 6px rgba(67, 89, 113, 0.12);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .audit-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 89, 113, 0.16);
        }

        .diff-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .diff-val-old {
            background-color: #fff5f5;
            color: #d92550;
            font-family: monospace;
            font-size: 0.825rem;
            word-break: break-word;
            white-space: pre-wrap;
        }

        .diff-val-new {
            background-color: #f0fdf4;
            color: #166534;
            font-family: monospace;
            font-size: 0.825rem;
            word-break: break-word;
            white-space: pre-wrap;
        }

        .badge-event-created {
            background-color: rgba(113, 221, 55, 0.15);
            color: #71dd37;
        }

        .badge-event-updated {
            background-color: rgba(3, 195, 236, 0.15);
            color: #03c3ec;
        }

        .badge-event-deleted {
            background-color: rgba(255, 62, 29, 0.15);
            color: #ff3e1d;
        }

        .badge-event-login {
            background-color: rgba(105, 108, 255, 0.15);
            color: #696cff;
        }

        .badge-event-logout {
            background-color: rgba(133, 146, 163, 0.15);
            color: #8592a3;
        }

        .badge-event-default {
            background-color: rgba(255, 171, 0, 0.15);
            color: #ffab00;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb & Header -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Audit Logs</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="bx bx-history text-primary me-2 fs-3 align-middle"></i>Audit Logs & Activity Trail
                </h4>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="btnRefreshAuditLogs" class="btn btn-outline-secondary">
                    <i class="bx bx-refresh me-1"></i> Refresh
                </button>
            </div>
        </div>

        <!-- KPI Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card audit-stat-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary rounded p-2 me-3">
                            <i class="bx bx-history fs-3"></i>
                        </div>
                        <div>
                            <span class="text-muted fw-semibold d-block">Total Audit Logs</span>
                            <h4 class="mb-0 fw-bold text-dark">{{ number_format($totalLogs) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card audit-stat-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-success rounded p-2 me-3">
                            <i class="bx bx-calendar-check fs-3"></i>
                        </div>
                        <div>
                            <span class="text-muted fw-semibold d-block">Activities Today</span>
                            <h4 class="mb-0 fw-bold text-dark">{{ number_format($todayLogs) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card audit-stat-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-warning rounded p-2 me-3">
                            <i class="bx bx-user-check fs-3"></i>
                        </div>
                        <div>
                            <span class="text-muted fw-semibold d-block">Top Active User</span>
                            <h5 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 160px;"
                                title="{{ $topUser['name'] ?? 'N/A' }}">
                                {{ $topUser['name'] ?? 'N/A' }}
                            </h5>
                            @if($topUser)
                                <small class="text-muted">{{ number_format($topUser['count']) }} actions</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card audit-stat-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-info rounded p-2 me-3">
                            <i class="bx bx-layer fs-3"></i>
                        </div>
                        <div>
                            <span class="text-muted fw-semibold d-block">Top Active Module</span>
                            <h5 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 160px;"
                                title="{{ $topModule['name'] ?? 'N/A' }}">
                                {{ $topModule['name'] ?? 'N/A' }}
                            </h5>
                            @if($topModule)
                                <small class="text-muted">{{ number_format($topModule['count']) }} changes</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="card shadow-sm border-0">
            <!-- Advanced Filters Toolbar -->
            <div class="card-header border-bottom bg-light py-3">
                <form id="auditLogFilterForm">
                    <div class="row g-3 align-items-end">
                        <!-- Period Buttons -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-muted text-uppercase mb-2">Time Period</label>
                            <div class="btn-group btn-group-sm flex-wrap" role="group" id="auditPeriodBtnGroup">
                                <button type="button" class="btn btn-outline-primary btn-audit-period active"
                                    data-period="all">All Time</button>
                                <button type="button" class="btn btn-outline-primary btn-audit-period"
                                    data-period="today">Today</button>
                                <button type="button" class="btn btn-outline-primary btn-audit-period"
                                    data-period="yesterday">Yesterday</button>
                                <button type="button" class="btn btn-outline-primary btn-audit-period"
                                    data-period="this_week">This Week</button>
                                <button type="button" class="btn btn-outline-primary btn-audit-period"
                                    data-period="this_month">This Month</button>
                                <button type="button" class="btn btn-outline-primary btn-audit-period"
                                    data-period="custom">Custom Range</button>
                            </div>
                            <input type="hidden" name="filter_type" id="audit_filter_period" value="all">
                        </div>

                        <!-- Custom Date Range inputs (hidden by default) -->
                        <div class="col-md-3 audit-date-group d-none" id="audit_group_custom_start">
                            <label class="form-label fw-semibold small">Start Date</label>
                            <input type="date" name="start_date" id="audit_filter_start_date"
                                class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
                        </div>

                        <div class="col-md-3 audit-date-group d-none" id="audit_group_custom_end">
                            <label class="form-label fw-semibold small">End Date</label>
                            <input type="date" name="end_date" id="audit_filter_end_date"
                                class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- User Filter -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">User / Actor</label>
                            <select name="user_id" id="audit_filter_user" class="form-select form-select-sm">
                                <option value="">All Users</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Module Filter -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Module</label>
                            <select name="module" id="audit_filter_module" class="form-select form-select-sm">
                                <option value="">All Modules</option>
                                @foreach($modules as $mod)
                                    <option value="{{ $mod }}">{{ $mod }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action / Event Filter -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small">Action</label>
                            <select name="event" id="audit_filter_event" class="form-select form-select-sm">
                                <option value="">All Actions</option>
                                <option value="created">Created</option>
                                <option value="updated">Updated</option>
                                <option value="deleted">Deleted</option>
                                <option value="login">Login</option>
                                <option value="logout">Logout</option>
                            </select>
                        </div>

                        <!-- Search Input -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Search</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input type="text" name="search" id="audit_filter_search"
                                    class="form-control form-control-sm" placeholder="Description, IP, etc.">
                            </div>
                        </div>

                        <!-- Filter Action Buttons -->
                        <div class="col-md-1 text-end">
                            <button type="button" id="btnResetAuditFilter" class="btn btn-sm btn-outline-secondary w-100"
                                title="Reset Filters">
                                <i class="bx bx-reset"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Data Table Container -->
            <div class="table-responsive text-nowrap p-3">
                <table id="audit-logs-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Date & Time</th>
                            <th>User / Actor</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th class="text-center" style="width: 80px;">Details</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Audit Log Detail / Diff Modal -->
    <div class="modal fade" id="auditLogDetailModal" tabindex="-1" aria-labelledby="auditLogDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark" id="auditLogDetailModalLabel">
                        <i class="bx bx-info-circle text-primary me-2"></i>Audit Log Details <span id="modalLogIdBadge"
                            class="badge bg-label-secondary ms-2 fs-tiny"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Metadata Summary -->
                    <div class="card bg-lighter border-0 mb-4">
                        <div class="card-body p-3">
                            <div class="row g-3 text-sm">
                                <div class="col-sm-6">
                                    <span class="text-muted d-block small">Actor / User</span>
                                    <span id="modalDetailUser" class="fw-bold text-dark">N/A</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block small">Action & Module</span>
                                    <span id="modalDetailActionBadge"></span>
                                    <span id="modalDetailModule" class="badge bg-label-dark ms-1"></span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block small">Date & Timestamp</span>
                                    <span id="modalDetailDate" class="fw-semibold text-secondary"></span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block small">IP Address & Browser</span>
                                    <span id="modalDetailIp" class="badge bg-label-info"></span>
                                    <small id="modalDetailUserAgent" class="text-muted d-block mt-1 text-truncate"></small>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted d-block small">Description</span>
                                    <p id="modalDetailDesc" class="mb-0 fw-semibold text-dark"></p>
                                </div>
                                <div class="col-12" id="modalDetailUrlRow">
                                    <span class="text-muted d-block small">Request URL</span>
                                    <code id="modalDetailUrl" class="small text-break"></code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Changes / Diff Section -->
                    <div>
                        <h6 class="fw-bold mb-3 text-dark d-flex align-items-center">
                            <i class="bx bx-transfer-alt text-primary me-2"></i>Field-Level Changes
                            <span id="modalChangesCountBadge" class="badge bg-label-primary ms-2 fs-tiny"></span>
                        </h6>
                        <div id="modalDiffContainer">
                            <!-- Populated dynamically -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection