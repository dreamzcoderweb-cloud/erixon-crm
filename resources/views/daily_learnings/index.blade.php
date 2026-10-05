@extends('layouts.master')
@section('title', 'Daily Learning List')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div id="alert-container"></div>

    <!-- Header & Breadcrumbs -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Daily Learning</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="bx bx-book-open me-2 text-primary"></i>Daily Learning Management
            </h4>
        </div>
        @can('daily-learnings.create')
            <button class="btn btn-primary shadow-sm" id="openAddDailyLearningModalBtn">
                <i class="bx bx-plus me-1"></i> Add Daily Learning
            </button>
        @endcan
    </div>

    <!-- KPI Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1 fs-tiny text-uppercase fw-semibold">Total Entries</span>
                        <h4 class="mb-0 fw-bold" id="kpi_total">0</h4>
                    </div>
                    <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-book fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1 fs-tiny text-uppercase fw-semibold">Today's Entries</span>
                        <h4 class="mb-0 fw-bold text-success" id="kpi_today">0</h4>
                    </div>
                    <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-calendar-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1 fs-tiny text-uppercase fw-semibold">Active Staff</span>
                        <h4 class="mb-0 fw-bold text-info" id="kpi_staff_count">0</h4>
                    </div>
                    <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-user fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card with Filters & DataTable -->
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-list-ul me-2"></i>Daily Learning List</h5>
        </div>

        <!-- Filter Area: Date Period Buttons (Customer List Pattern) & Staff Dropdown -->
        <div class="p-3 bg-light border-bottom">
            <form id="dailyLearningFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-12">
                        <label class="form-label fw-semibold d-block">Date Period</label>
                        <div class="btn-group btn-group-sm flex-wrap" role="group" id="periodBtnGroup">
                            <button type="button" class="btn btn-outline-primary btn-period active" data-period="all">All Time</button>
                            <button type="button" class="btn btn-outline-primary btn-period" data-period="daily">Daily</button>
                            <button type="button" class="btn btn-outline-primary btn-period" data-period="weekly">Weekly</button>
                            <button type="button" class="btn btn-outline-primary btn-period" data-period="monthly">Monthly</button>
                            <button type="button" class="btn btn-outline-primary btn-period" data-period="custom">Custom</button>
                        </div>
                        <input type="hidden" name="filter_type" id="filter_period" value="all">
                    </div>

                    <div class="col-md-3 filter-date-group d-none" id="group_daily">
                        <label class="form-label fw-semibold">Date</label>
                        <input type="date" name="date" id="filter_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-md-3 filter-date-group d-none" id="group_monthly">
                        <label class="form-label fw-semibold">Month</label>
                        <input type="month" name="month" id="filter_month" class="form-control form-control-sm" value="{{ date('Y-m') }}">
                    </div>

                    <div class="col-md-3 filter-date-group d-none" id="group_custom_start">
                        <label class="form-label fw-semibold">Start Date</label>
                        <input type="date" name="start_date" id="filter_start_date" class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
                    </div>

                    <div class="col-md-3 filter-date-group d-none" id="group_custom_end">
                        <label class="form-label fw-semibold">End Date</label>
                        <input type="date" name="end_date" id="filter_end_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                    </div>

                    <!-- Staff Dropdown Filter -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Staff Person</label>
                        <select class="form-select form-select-sm" id="filter_staff_id" name="staff_id">
                            <option value="">-- All Staff --</option>
                            @if(isset($staffList))
                                @foreach($staffList as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }} {{ !empty($st->mobile_number) ? '(' . $st->mobile_number . ')' : '' }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1" id="filterSubmitBtn">
                                <i class="bx bx-filter-alt me-1"></i> Apply Filter
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="filterResetBtn" title="Reset Filters">
                                <i class="bx bx-refresh me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Container -->
        <div class="card-datatable table-responsive p-3">
            <table id="daily-learnings-table" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 200px;">Staff Name</th>
                        <th style="width: 140px;">Date</th>
                        <th>Learning Notes</th>
                        <th style="width: 150px;">Created At</th>
                        <th style="width: 100px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded via DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: ADD / EDIT DAILY LEARNING           -->
<!-- ========================================== -->
<div class="modal fade" id="dailyLearningModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="dailyLearningForm" class="modal-content border-0 shadow">
            @csrf
            <input type="hidden" name="id" id="daily_learning_id">

            <div class="modal-header bg-light border-bottom py-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm bg-label-primary rounded me-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-book-open fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="dailyLearningModalTitle">Add Daily Learning</h5>
                        <small class="text-muted" id="dailyLearningModalSubtitle">Record daily insights, training or learning notes</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3">
                    <!-- Staff Person -->
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="modal_staff_id">
                            Staff Person <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="modal_staff_id" name="user_id" required>
                            <option value="">-- Select Staff --</option>
                            @if(isset($staffList))
                                @foreach($staffList as $st)
                                    <option value="{{ $st->id }}" {{ Auth::id() == $st->id ? 'selected' : '' }}>
                                        {{ $st->name }} {{ !empty($st->mobile_number) ? '(' . $st->mobile_number . ')' : '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Date -->
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="modal_date">
                            Date <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bx bx-calendar"></i></span>
                            <input type="date" class="form-control" id="modal_date" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="modal_notes">
                            Learning Notes / Key Takeaways <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="modal_notes" name="notes" rows="4" placeholder="Enter key lessons, topics learned, product insights, or new skills..." required></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm px-4" id="saveDailyLearningBtn">
                    <span id="saveDailyLearningBtnText">Save Daily Learning</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: DELETE CONFIRMATION                 -->
<!-- ========================================== -->
<div class="modal fade" id="deleteDailyLearningModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-lg bg-label-danger mx-auto mb-3 rounded-circle p-2">
                    <i class="bx bx-trash fs-1 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-1">Delete Daily Learning?</h5>
                <p class="text-muted small mb-4">Are you sure you want to delete this daily learning entry? This action cannot be undone.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm px-3" id="confirmDeleteDailyLearningBtn">
                        <i class="bx bx-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
