@extends('layouts.master')
@section('title', 'Call Recordings - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-microphone me-2"></i>Call Recordings</h5>
                @can('call-recordings.create')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRecordingModal">
                        <i class="bx bx-plus me-1"></i> Upload Call Recording
                    </button>
                @endcan
            </div>

            <!-- Call Recordings Filter Bar -->
            <div class="p-3 bg-light border-bottom">
                <form id="recordingFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12">
                            <label class="form-label fw-semibold d-block">Date Period</label>
                            <div class="btn-group btn-group-sm flex-wrap" role="group" id="recordingPeriodBtnGroup">
                                <button type="button" class="btn btn-outline-primary btn-recording-period active" data-period="all">All Time</button>
                                <button type="button" class="btn btn-outline-primary btn-recording-period" data-period="daily">Daily</button>
                                <button type="button" class="btn btn-outline-primary btn-recording-period" data-period="weekly">Weekly</button>
                                <button type="button" class="btn btn-outline-primary btn-recording-period" data-period="monthly">Monthly</button>
                                <button type="button" class="btn btn-outline-primary btn-recording-period" data-period="yearly">Yearly</button>
                                <button type="button" class="btn btn-outline-primary btn-recording-period" data-period="custom">Custom</button>
                            </div>
                            <input type="hidden" name="filter_type" id="recording_filter_period" value="all">
                        </div>

                        <!-- Daily Datepicker -->
                        <div class="col-md-3 recording-filter-date-group d-none" id="recording_group_daily">
                            <label class="form-label fw-semibold">Select Date</label>
                            <input type="date" name="date" id="recording_filter_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Weekly Datepicker -->
                        <div class="col-md-3 recording-filter-date-group d-none" id="recording_group_weekly">
                            <label class="form-label fw-semibold">Select Week</label>
                            <input type="week" name="week" id="recording_filter_week" class="form-control form-control-sm" value="{{ date('Y-\WW') }}">
                        </div>

                        <!-- Monthly Datepicker -->
                        <div class="col-md-3 recording-filter-date-group d-none" id="recording_group_monthly">
                            <label class="form-label fw-semibold">Select Month</label>
                            <input type="month" name="month" id="recording_filter_month" class="form-control form-control-sm" value="{{ date('Y-m') }}">
                        </div>

                        <!-- Yearly Datepicker -->
                        <div class="col-md-3 recording-filter-date-group d-none" id="recording_group_yearly">
                            <label class="form-label fw-semibold">Select Year</label>
                            <select name="year" id="recording_filter_year" class="form-select form-select-sm">
                                @php
                                    $curYear = (int)date('Y');
                                @endphp
                                @for ($y = $curYear + 1; $y >= $curYear - 5; $y--)
                                    <option value="{{ $y }}" {{ $y == $curYear ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Custom Datepicker: Start Date -->
                        <div class="col-md-3 recording-filter-date-group d-none" id="recording_group_custom_start">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" name="start_date" id="recording_filter_start_date" class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
                        </div>

                        <!-- Custom Datepicker: End Date -->
                        <div class="col-md-3 recording-filter-date-group d-none" id="recording_group_custom_end">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" name="end_date" id="recording_filter_end_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- All Staff Dropdown -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Staff</label>
                            <select name="user_id" id="recording_filter_user_id" class="form-select form-select-sm">
                                <option value="">-- All Staff --</option>
                                @if(isset($staffs) && count($staffs) > 0)
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Lead Dropdown -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Lead</label>
                            <select name="lead_id" id="recording_filter_lead_id" class="form-select form-select-sm">
                                <option value="">-- All Leads --</option>
                                @if(isset($leads) && count($leads) > 0)
                                    @foreach ($leads as $lead)
                                        <option value="{{ $lead->lead_id }}">{{ $lead->lead_title }} ({{ $lead->customer->name ?? 'N/A' }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Customer Dropdown Searchable (Name or Phone) -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Customer</label>
                            <select name="customer_id" id="recording_filter_customer_id" class="form-select form-select-sm select2-search">
                                <option value="">-- All Customers --</option>
                                @if(isset($customers) && count($customers) > 0)
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->customer_id }}">{{ $cust->name }} ({{ $cust->mobile }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="bx bx-filter-alt me-1"></i> Apply Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="resetRecordingFilterBtn" title="Reset Filters">
                                    <i class="bx bx-refresh me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="call-recordings-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Lead Info</th>
                            <th>Duration</th>
                            <th>Audio Player</th>
                            <th>Uploaded By</th>
                            <th>Recorded Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loaded via AJAX DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Call Recording Modal -->
    <div class="modal fade" id="addRecordingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addRecordingForm" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-cloud-upload me-1"></i> Upload Call Recording</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Lead <span class="text-danger">*</span></label>
                                <select name="lead_id" class="form-select" required>
                                    <option value="">-- Select Lead --</option>
                                    @foreach ($leads as $lead)
                                        <option value="{{ $lead->lead_id }}">
                                            {{ $lead->lead_title }} ({{ $lead->customer->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Duration</label>
                                <select name="duration" class="form-select">
                                    <option value="">-- Select Duration --</option>
                                    <option value="1 minute">1 minute</option>
                                    <option value="2 minutes">2 minutes</option>
                                    <option value="5 minutes">5 minutes</option>
                                    <option value="10 minutes">10 minutes</option>
                                    <option value="15 minutes">15 minutes</option>
                                    <option value="30 minutes">30 minutes</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Audio File <span class="text-danger">*</span></label>
                                <input type="file" name="recording_file" class="form-control" accept="audio/*,.mp3,.wav,.m4a,.ogg,.aac" required>
                                <small class="text-muted">Allowed audio formats: MP3, WAV, M4A, OGG, AAC (Max 20MB)</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addRecordingSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Upload Recording
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Call Recording Modal -->
    <div class="modal fade" id="editRecordingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editRecordingForm" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="call_id" id="edit_call_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-edit me-1"></i> Edit Call Recording</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Lead <span class="text-danger">*</span></label>
                                <select name="lead_id" id="edit_recording_lead_id" class="form-select" required>
                                    <option value="">-- Select Lead --</option>
                                    @foreach ($leads as $lead)
                                        <option value="{{ $lead->lead_id }}">
                                            {{ $lead->lead_title }} ({{ $lead->customer->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Duration</label>
                                <select name="duration" id="edit_recording_duration" class="form-select">
                                    <option value="">-- Select Duration --</option>
                                    <option value="1 minute">1 minute</option>
                                    <option value="2 minutes">2 minutes</option>
                                    <option value="5 minutes">5 minutes</option>
                                    <option value="10 minutes">10 minutes</option>
                                    <option value="15 minutes">15 minutes</option>
                                    <option value="30 minutes">30 minutes</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Replace Audio File (Optional)</label>
                                <input type="file" name="recording_file" class="form-control" accept="audio/*,.mp3,.wav,.m4a,.ogg,.aac">
                                <small class="text-muted">Leave blank to keep existing file</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editRecordingSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Recording
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Recording Modal -->
    <div class="modal fade" id="deleteRecordingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-trash me-1 text-danger"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this call recording?</p>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteRecordingBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
