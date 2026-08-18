@extends('layouts.master')
@section('title', 'Follow-ups Management - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-phone-call me-2"></i>Follow-ups Management</h5>
                <div class="d-flex gap-2">
                    @can('followups.reassign')
                        <button class="btn btn-outline-secondary btn-view-reassignment-history">
                            <i class="bx bx-history me-1"></i> Audit History
                        </button>
                    @endcan
                    @can('followups.create')
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFollowupModal">
                            <i class="bx bx-plus me-1"></i> Add Follow-up
                        </button>
                    @endcan
                </div>
            </div>

            <!-- Follow-up Filter Bar & Nav Tabs -->
            <div class="px-3 pt-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom pb-3">
                    <ul class="nav nav-pills" id="followupFilterTabs">
                        <li class="nav-item">
                            <button class="nav-link active btn-filter-tab" data-filter="all">
                                <i class="bx bx-list-ul me-1"></i> All Follow-ups
                                <span class="badge bg-secondary ms-1" id="badge_count_all">0</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link btn-filter-tab" data-filter="today">
                                <i class="bx bx-calendar-event me-1"></i> Today's Follow-up
                                <span class="badge bg-danger ms-1" id="badge_count_today">0</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link btn-filter-tab" data-filter="upcoming">
                                <i class="bx bx-calendar me-1"></i> Tomorrow / Upcoming
                                <span class="badge bg-primary ms-1" id="badge_count_upcoming">0</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link btn-filter-tab" data-filter="overdue">
                                <i class="bx bx-error-circle me-1"></i> Overdue
                                <span class="badge bg-warning text-dark ms-1" id="badge_count_overdue">0</span>
                            </button>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-2">
                        <input type="hidden" id="filter_type_input" value="all">
                        <select id="filter_staff_id" class="form-select form-select-sm" style="width: 200px;">
                            <option value="">-- All Staff Members --</option>
                            @foreach ($staffs as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                        <input type="date" id="filter_custom_date" class="form-control form-control-sm" style="width: 160px;" title="Filter by Specific Follow-up Date">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="resetFollowupFiltersBtn" title="Reset Filters">
                            <i class="bx bx-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive text-nowrap p-3">
                <table id="followups-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Lead Info</th>
                            <th>Type</th>
                            <th>Duration</th>
                            <th>Next Follow-up Date</th>
                            <th>Status</th>
                            <th>Forward To</th>
                            <th>Created By</th>
                            <th>Remarks</th>
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

    <!-- Add Follow-up Modal -->
    <div class="modal fade" id="addFollowupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addFollowupForm" method="POST" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-plus-circle me-1"></i> Add New Follow-up</h5>
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
                                <label class="form-label">Follow-up Type <span class="text-danger">*</span></label>
                                <select name="followup_type" id="add_followup_type" class="form-select" required>
                                    <option value="Call">Call</option>
                                    <option value="Meeting">Meeting</option>
                                    <option value="Email">Email</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <!-- Dynamic Duration Field for Call -->
                            <div class="col-md-6" id="add_duration_container">
                                <label class="form-label">Duration <span class="text-danger">*</span></label>
                                <select name="duration" id="add_duration" class="form-select">
                                    <option value="">-- Select Duration --</option>
                                    <option value="5 minutes">5 minutes</option>
                                    <option value="10 minutes">10 minutes</option>
                                    <option value="15 minutes">15 minutes</option>
                                    <option value="30 minutes">30 minutes</option>
                                    <option value="45 minutes">45 minutes</option>
                                    <option value="1 hour">1 hour</option>
                                    <option value="1.5 hours">1.5 hours</option>
                                    <option value="2 hours">2 hours</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Next Follow-up Date</label>
                                <input type="datetime-local" name="next_followup_date" class="form-control">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="followup_status" class="form-select" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Forward To (Staff)</label>
                                <select name="forward_to" class="form-select">
                                    <option value="">-- None --</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}" {{ $staff->is_on_leave ? 'disabled' : '' }}>
                                            {{ $staff->name }} {{ $staff->is_on_leave ? '(On Leave)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Remarks / Discussion Details</label>
                                <textarea name="remarks" class="form-control" rows="3" placeholder="Enter follow-up remarks or notes..."></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addFollowupSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Follow-up
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Follow-up Modal -->
    <div class="modal fade" id="editFollowupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editFollowupForm" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="followups_id" id="edit_followups_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-edit me-1"></i> Edit Follow-up</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Lead <span class="text-danger">*</span></label>
                                <select name="lead_id" id="edit_followup_lead_id" class="form-select" required>
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
                                <label class="form-label">Follow-up Type <span class="text-danger">*</span></label>
                                <select name="followup_type" id="edit_followup_type" class="form-select" required>
                                    <option value="Call">Call</option>
                                    <option value="Meeting">Meeting</option>
                                    <option value="Email">Email</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Dynamic Duration Field for Edit -->
                            <div class="col-md-6" id="edit_duration_container">
                                <label class="form-label">Duration <span class="text-danger">*</span></label>
                                <select name="duration" id="edit_duration" class="form-select">
                                    <option value="">-- Select Duration --</option>
                                    <option value="5 minutes">5 minutes</option>
                                    <option value="10 minutes">10 minutes</option>
                                    <option value="15 minutes">15 minutes</option>
                                    <option value="30 minutes">30 minutes</option>
                                    <option value="45 minutes">45 minutes</option>
                                    <option value="1 hour">1 hour</option>
                                    <option value="1.5 hours">1.5 hours</option>
                                    <option value="2 hours">2 hours</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Next Follow-up Date</label>
                                <input type="datetime-local" name="next_followup_date" id="edit_followup_next_date" class="form-control">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="followup_status" id="edit_followup_status" class="form-select" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Forward To (Staff)</label>
                                <select name="forward_to" id="edit_followup_forward_to" class="form-select">
                                    <option value="">-- None --</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}" {{ $staff->is_on_leave ? 'disabled' : '' }}>
                                            {{ $staff->name }} {{ $staff->is_on_leave ? '(On Leave)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Remarks / Discussion Details</label>
                                <textarea name="remarks" id="edit_followup_remarks" class="form-control" rows="3" placeholder="Enter follow-up remarks or notes..."></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editFollowupSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Follow-up
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reassign Follow-up Modal -->
    <div class="modal fade" id="reassignFollowupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="reassignFollowupForm" method="POST">
                    @csrf
                    <input type="hidden" name="reassign_followup_id" id="reassign_followup_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-user-voice me-1 text-primary"></i> Reassign Staff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Current Assigned Staff:</label>
                            <span id="reassign_current_staff" class="badge bg-label-secondary">N/A</span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select New Staff <span class="text-danger">*</span></label>
                            <select name="new_staff_id" id="reassign_new_staff_id" class="form-select" required>
                                <option value="">-- Select Available Staff --</option>
                                @foreach ($availableStaffs as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reassignment Notes (Optional)</label>
                            <textarea name="notes" id="reassign_notes" class="form-control" rows="2" placeholder="Reason for reassignment..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="confirmReassignBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Reassign Staff
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reassignment Audit History Modal -->
    <div class="modal fade" id="reassignmentHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-history me-1"></i> Reassignment Audit Trail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Client / Lead</th>
                                    <th>Previous Staff</th>
                                    <th>New Staff</th>
                                    <th>Reassigned By</th>
                                    <th>Reassigned Date/Time</th>
                                </tr>
                            </thead>
                            <tbody id="reassignmentHistoryTbody">
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Follow-up Modal -->
    <div class="modal fade" id="deleteFollowupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-trash me-1 text-danger"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this follow-up record?</p>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteFollowupBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
