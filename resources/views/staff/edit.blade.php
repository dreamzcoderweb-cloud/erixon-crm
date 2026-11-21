@extends('layouts.master')
@section('title', 'Edit Staff - Super Admin')

@push('css')
<style>
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d9dee3 !important;
        border-radius: 0.375rem !important;
        min-height: 40px !important;
        padding: 2px 6px !important;
        background-color: #fff !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #696cff !important;
        box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.25) !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #696cff !important;
        border: none !important;
        color: #ffffff !important;
        border-radius: 0.25rem !important;
        padding: 3px 8px !important;
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        margin-top: 4px !important;
        margin-right: 5px !important;
        display: inline-flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #ffffff !important;
        margin-right: 6px !important;
        border: none !important;
        background: transparent !important;
        font-weight: bold !important;
        cursor: pointer !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ffcccc !important;
    }
</style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y mx-auto" style="max-width: 85%;">
        <div class="row">
            <div class="col-xxl">
                <div class="card mb-6">
                    <div class="card-header d-flex align-items-center justify-content-between border-bottom mb-4">
                        <h5 class="mb-0"><i class="bx bx-edit me-2"></i>Edit Staff</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('admin/edit_staff/' . $staff->id) }}" method="POST" autocomplete="off">
                            @csrf
                            <div class="row">
                                <!-- Row 1 -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $staff->name) }}" placeholder="Enter full name" />
                                    <span class="text-danger small">{{ $errors->first('name') }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="mobile_number">Mobile Number</label>
                                    <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $staff->mobile_number) }}" placeholder="Enter mobile number" />
                                    <span class="text-danger small">{{ $errors->first('mobile_number') }}</span>
                                </div>

                                <!-- Row 2 -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $staff->email) }}" autocomplete="off" placeholder="Enter email address" />
                                    <span class="text-danger small">{{ $errors->first('email') }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="role_id">Role <span class="text-danger">*</span></label>
                                    <select class="form-select" id="role_id" name="role_id">
                                        <option value="" selected>Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ (string) old('role_id', $selectedRoleId) === (string) $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger small">{{ $errors->first('role_id') }}</span>
                                </div>

                                <!-- Branch & Team Row (Multi-Selection Enabled) -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="staff_branch_ids">
                                        <i class="bx bx-buildings me-1 text-primary"></i>Branch(es) <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select select2-multiple" id="staff_branch_ids" name="branch_ids[]" multiple required data-placeholder="Select one or more branches">
                                        @foreach ($branches as $branch)
                                            @php
                                                $isBranchSelected = in_array($branch->id, old('branch_ids', $assignedBranchIds ?? [$staff->branch_id]));
                                            @endphp
                                            <option value="{{ $branch->id }}" {{ $isBranchSelected ? 'selected' : '' }}>
                                                {{ $branch->name }} ({{ $branch->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text text-muted small"><i class="bx bx-info-circle me-1"></i>Select multiple branches if needed. The first branch will serve as Primary.</div>
                                    <span class="text-danger small">{{ $errors->first('branch_ids') }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="staff_team_ids">
                                        <i class="bx bx-group me-1 text-info"></i>Team(s) <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select select2-multiple" id="staff_team_ids" name="team_ids[]" multiple required data-placeholder="Select one or more teams">
                                        @if(isset($teams))
                                            @foreach ($teams as $team)
                                                @php
                                                    $isTeamSelected = in_array($team->id, old('team_ids', $assignedTeamIds ?? [$staff->team_id]));
                                                @endphp
                                                <option value="{{ $team->id }}" data-branch="{{ $team->branch_id }}" {{ $isTeamSelected ? 'selected' : '' }}>
                                                    {{ $team->name }} ({{ $team->code }}){{ $team->branch ? ' - ' . $team->branch->name : '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="form-text text-muted small"><i class="bx bx-info-circle me-1"></i>Teams from the selected branches appear here. You can select multiple teams.</div>
                                    <span class="text-danger small">{{ $errors->first('team_ids') }}</span>
                                </div>

                                <!-- Row 3 -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="staff_type">Staff Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="staff_type" name="staff_type">
                                        <option value="Permanent" {{ old('staff_type', $staff->staff_type ?? 'Permanent') == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                                        <option value="Temporary" {{ old('staff_type', $staff->staff_type) == 'Temporary' ? 'selected' : '' }}>Temporary</option>
                                    </select>
                                    <span class="text-danger small">{{ $errors->first('staff_type') }}</span>
                                </div>
                                <div class="col-md-6 mb-3" id="available_leave_count_wrapper">
                                    <label class="form-label" for="available_leave_count">Available Leave Count Per Month</label>
                                    <input type="number" step="0.5" min="0" class="form-control" id="available_leave_count" name="available_leave_count" placeholder="e.g. 1" value="{{ old('available_leave_count', $staff->available_leave_count) }}" />
                                    <span class="text-danger small">{{ $errors->first('available_leave_count') }}</span>
                                </div>

                                <!-- Row 4 -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="gender">Gender</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{ old('gender', $staff->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender', $staff->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender', $staff->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <span class="text-danger small">{{ $errors->first('gender') }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="date_of_birth">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $staff->date_of_birth?->format('Y-m-d')) }}" />
                                    <span class="text-danger small">{{ $errors->first('date_of_birth') }}</span>
                                </div>

                                <!-- Row 5 -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="date_of_joining">Date of Joining</label>
                                    <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" value="{{ old('date_of_joining', $staff->date_of_joining?->format('Y-m-d')) }}" />
                                    <span class="text-danger small">{{ $errors->first('date_of_joining') }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="designation">Designation</label>
                                    <input type="text" class="form-control" id="designation" name="designation" placeholder="e.g. Sales Executive, Manager" value="{{ old('designation', $staff->designation) }}" />
                                    <span class="text-danger small">{{ $errors->first('designation') }}</span>
                                </div>

                                <!-- Row 6 -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="base_salary">Base Salary (₹)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="base_salary" name="base_salary" placeholder="e.g. 10000" value="{{ old('base_salary', $staff->base_salary) }}" />
                                    <span class="text-danger small">{{ $errors->first('base_salary') }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="allow_check_in_time">Allow Check-in Time</label>
                                    <input type="time" class="form-control" id="allow_check_in_time" name="allow_check_in_time" value="{{ old('allow_check_in_time', $staff->allow_check_in_time ? \Carbon\Carbon::parse($staff->allow_check_in_time)->format('H:i') : '09:10') }}" />
                                    <span class="text-danger small">{{ $errors->first('allow_check_in_time') }}</span>
                                </div>

                                <!-- Row 7 -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="check_in_time">Assigned Check-In Time</label>
                                    <input type="time" class="form-control" id="check_in_time" name="check_in_time" value="{{ old('check_in_time', $staff->check_in_time ? \Carbon\Carbon::parse($staff->check_in_time)->format('H:i') : '') }}" />
                                    <span class="text-danger small">{{ $errors->first('check_in_time') }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="check_out_time">Assigned Check-Out Time</label>
                                    <input type="time" class="form-control" id="check_out_time" name="check_out_time" value="{{ old('check_out_time', $staff->check_out_time ? \Carbon\Carbon::parse($staff->check_out_time)->format('H:i') : '') }}" />
                                    <span class="text-danger small">{{ $errors->first('check_out_time') }}</span>
                                </div>

                                <!-- Row 8 -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="late_attendance_count">Late Attendance Count</label>
                                    <input type="number" min="0" class="form-control" id="late_attendance_count" name="late_attendance_count" placeholder="e.g. 3" value="{{ old('late_attendance_count', $staff->late_attendance_count ?? 3) }}" />
                                    <span class="text-danger small">{{ $errors->first('late_attendance_count') }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="increment_amount">Increment Amount (₹)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="increment_amount" name="increment_amount" placeholder="e.g. 1000" value="{{ old('increment_amount', $staff->increment_amount ?? 0) }}" />
                                    <span class="text-danger small">{{ $errors->first('increment_amount') }}</span>
                                </div>

                                <!-- Row 9 -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="increment_date">Increment Date</label>
                                    <input type="date" class="form-control" id="increment_date" name="increment_date" value="{{ old('increment_date', $staff->increment_date?->format('Y-m-d')) }}" />
                                    <span class="text-danger small">{{ $errors->first('increment_date') }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter address">{{ old('address', $staff->address) }}</textarea>
                                    <span class="text-danger small">{{ $errors->first('address') }}</span>
                                </div>

                                <!-- Row 10 -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="1" {{ (string) old('status', $staff->status ?? 1) === '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ (string) old('status', $staff->status ?? 1) === '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <span class="text-danger small">{{ $errors->first('status') }}</span>
                                </div>

                                <!-- Row 11 -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="password">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password" autocomplete="new-password" placeholder="Leave blank to keep current" />
                                    <span class="text-danger small">{{ $errors->first('password') }}</span>
                                    <div class="form-text">Leave blank to keep current password</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="password_confirmation">Confirm New Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password" placeholder="Confirm new password" />
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary px-4"><i class="bx bx-check me-1"></i>Update</button>
                                    <a href="{{ url('admin/staff') }}" class="btn btn-danger px-4"><i class="bx bx-x me-1"></i>Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Select2 on multiple select dropdowns
        $('#staff_branch_ids').select2({
            placeholder: 'Select one or more branches',
            allowClear: true,
            width: '100%'
        });

        $('#staff_team_ids').select2({
            placeholder: 'Select one or more teams',
            allowClear: true,
            width: '100%'
        });

        const staffTypeSelect = document.getElementById('staff_type');
        const leaveWrapper = document.getElementById('available_leave_count_wrapper');

        function toggleLeaveCount() {
            if (staffTypeSelect && leaveWrapper) {
                if (staffTypeSelect.value === 'Temporary') {
                    leaveWrapper.style.display = 'none';
                } else {
                    leaveWrapper.style.display = 'block';
                }
            }
        }

        if (staffTypeSelect) {
            staffTypeSelect.addEventListener('change', toggleLeaveCount);
            toggleLeaveCount();
        }

        // Dynamic Team loading based on Branch multi-selection
        $('#staff_branch_ids').on('change', function () {
            const selectedBranches = $(this).val() || [];
            const currentlySelectedTeams = $('#staff_team_ids').val() || [];

            if (selectedBranches.length === 0) {
                $('#staff_team_ids').empty().trigger('change');
                return;
            }

            const branchQuery = selectedBranches.join(',');
            fetch('{{ url("admin/teams/by-branch") }}/' + branchQuery)
                .then(response => response.json())
                .then(res => {
                    let optionsHtml = '';
                    if (res.status && res.data.length > 0) {
                        res.data.forEach(t => {
                            const isSelected = currentlySelectedTeams.map(String).includes(String(t.id)) ? 'selected' : '';
                            const branchLabel = t.branch ? ` - ${t.branch.name}` : '';
                            optionsHtml += `<option value="${t.id}" data-branch="${t.branch_id}" ${isSelected}>${t.name} (${t.code})${branchLabel}</option>`;
                        });
                    }
                    $('#staff_team_ids').html(optionsHtml).trigger('change');
                })
                .catch(err => {
                    console.error('Failed to load teams:', err);
                });
        });
    });
</script>
@endpush
