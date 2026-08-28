@extends('layouts.master')

@section('title', 'Lead Settings - Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Settings Navigation Tabs (Top) -->
    <div class="row mb-4">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row gap-2">
                @can('general-settings.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.settings.general') }}">
                            <i class="bx bx-cog me-1"></i> General Settings
                        </a>
                    </li>
                @endcan
                @can('lead-settings.view')
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.settings.lead') }}">
                            <i class="bx bx-gift me-1"></i> Lead Setting
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </div>

    <!-- Lead Settings Vertical Layout -->
    <div class="row">
        <!-- Left Sidebar Navigation Menu -->
        <div class="col-md-3 mb-4 mb-md-0">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="nav flex-column nav-pills custom-lead-nav gap-1" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link text-start py-2 px-3 fw-medium" id="v-pills-auto-assignment-tab" data-bs-toggle="pill" data-bs-target="#v-pills-auto-assignment" type="button" role="tab">
                            <i class="bx bx-user-check me-2"></i> Auto Assignment
                        </button>
                        <button class="nav-link text-start py-2 px-3 fw-medium" id="v-pills-auto-sync-tab" data-bs-toggle="pill" data-bs-target="#v-pills-auto-sync" type="button" role="tab">
                            <i class="bx bx-sync me-2"></i> Automatic Lead Sync
                        </button>
                        <button class="nav-link text-start py-2 px-3 fw-medium" id="v-pills-form-layout-tab" data-bs-toggle="pill" data-bs-target="#v-pills-form-layout" type="button" role="tab">
                            <i class="bx bx-layout me-2"></i> Form Layout
                        </button>
                        <button class="nav-link text-start py-2 px-3 fw-medium active" id="v-pills-additional-fields-tab" data-bs-toggle="pill" data-bs-target="#v-pills-additional-fields" type="button" role="tab">
                            <i class="bx bx-list-plus me-2"></i> Additional Lead Fields
                        </button>
                        <button class="nav-link text-start py-2 px-3 fw-medium" id="v-pills-dynamic-intake-tab" data-bs-toggle="pill" data-bs-target="#v-pills-dynamic-intake" type="button" role="tab">
                            <i class="bx bx-select-multiple me-2"></i> Dynamic Intake platform
                        </button>
                        <button class="nav-link text-start py-2 px-3 fw-medium" id="v-pills-lead-structure-tab" data-bs-toggle="pill" data-bs-target="#v-pills-lead-structure" type="button" role="tab">
                            <i class="bx bx-sitemap me-2"></i> Lead Structure
                        </button>
                        <button class="nav-link text-start py-2 px-3 fw-medium" id="v-pills-site-customization-tab" data-bs-toggle="pill" data-bs-target="#v-pills-site-customization" type="button" role="tab">
                            <i class="bx bx-customize me-2"></i> Lead Site Customization
                        </button>
                        <button class="nav-link text-start py-2 px-3 fw-medium" id="v-pills-lead-allocation-tab" data-bs-toggle="pill" data-bs-target="#v-pills-lead-allocation" type="button" role="tab">
                            <i class="bx bx-slider-alt me-2"></i> Lead Allocation Settings
                        </button>
                        <button class="nav-link text-start py-2 px-3 fw-medium" id="v-pills-lead-points-tab" data-bs-toggle="pill" data-bs-target="#v-pills-lead-points" type="button" role="tab">
                            <i class="bx bx-gift me-2"></i> Lead Points Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content Panes -->
        <div class="col-md-9">
            <div class="tab-content p-0 shadow-none bg-transparent" id="v-pills-tabContent">
                
                <!-- Additional Lead Fields (Default Active) -->
                <div class="tab-pane fade show active" id="v-pills-additional-fields" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom d-flex align-items-center justify-content-between py-3">
                            <div>
                                <h5 class="card-title mb-0 fw-bold text-dark">Custom Lead Fields</h5>
                                <span class="text-muted small">Additional Lead Fields</span>
                            </div>
                            <button type="button" class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createFieldModal" style="background-color: #00a65a; border-color: #00a65a;">
                                <i class="bx bx-plus me-1"></i> + Create Field
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="fw-semibold text-muted text-uppercase small py-3">Label</th>
                                            <th class="fw-semibold text-muted text-uppercase small py-3">Type</th>
                                            <th class="fw-semibold text-muted text-uppercase small py-3">Options</th>
                                            <th class="fw-semibold text-muted text-uppercase small py-3">Required</th>
                                            <th class="fw-semibold text-muted text-uppercase small py-3 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0" id="customFieldsTableBody">
                                        <tr>
                                            <td class="fw-medium text-dark">Gst Number</td>
                                            <td><span class="badge bg-label-info text-dark">Number</span></td>
                                            <td class="text-muted">-</td>
                                            <td><span class="badge bg-label-secondary">No</span></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-icon btn-sm btn-outline-success me-1" title="Edit Field">
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-icon btn-sm btn-outline-danger" title="Delete Field">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium text-dark">Passcode</td>
                                            <td><span class="badge bg-label-info text-dark">Number</span></td>
                                            <td class="text-muted">-</td>
                                            <td><span class="badge bg-label-secondary">No</span></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-icon btn-sm btn-outline-success me-1" title="Edit Field">
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-icon btn-sm btn-outline-danger" title="Delete Field">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Auto Assignment -->
                <div class="tab-pane fade" id="v-pills-auto-assignment" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <h5 class="card-header border-bottom fw-bold">Auto Assignment</h5>
                        <div class="card-body pt-4">
                            <p class="text-muted">Configure automatic lead distribution rules and round-robin assignment among staff members.</p>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="autoAssignSwitch" checked>
                                <label class="form-check-label fw-semibold" for="autoAssignSwitch">Enable Round-Robin Auto Assignment</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Automatic Lead Sync -->
                <div class="tab-pane fade" id="v-pills-auto-sync" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <h5 class="card-header border-bottom fw-bold">Automatic Lead Sync</h5>
                        <div class="card-body pt-4">
                            <p class="text-muted">Manage real-time synchronization settings for incoming leads from third-party sources and webhooks.</p>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="autoSyncSwitch" checked>
                                <label class="form-check-label fw-semibold" for="autoSyncSwitch">Enable Automatic Real-time Sync</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Layout -->
                <div class="tab-pane fade" id="v-pills-form-layout" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <h5 class="card-header border-bottom fw-bold">Form Layout Settings</h5>
                        <div class="card-body pt-4">
                            <p class="text-muted">Customize lead creation and update form sections and layout arrangements.</p>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Intake Platform -->
                <div class="tab-pane fade" id="v-pills-dynamic-intake" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <h5 class="card-header border-bottom fw-bold">Dynamic Intake Platform</h5>
                        <div class="card-body pt-4">
                            <p class="text-muted">Configure intake forms and dynamic questionnaire options for incoming prospects.</p>
                        </div>
                    </div>
                </div>

                <!-- Lead Structure -->
                <div class="tab-pane fade" id="v-pills-lead-structure" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <h5 class="card-header border-bottom fw-bold">Lead Structure</h5>
                        <div class="card-body pt-4">
                            <p class="text-muted">Set up lead categories, hierarchy, and structural rules.</p>
                        </div>
                    </div>
                </div>

                <!-- Lead Site Customization -->
                <div class="tab-pane fade" id="v-pills-site-customization" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <h5 class="card-header border-bottom fw-bold">Lead Site Customization</h5>
                        <div class="card-body pt-4">
                            <p class="text-muted">Customize client-facing landing pages and web capture forms.</p>
                        </div>
                    </div>
                </div>

                <!-- Lead Allocation Settings -->
                <div class="tab-pane fade" id="v-pills-lead-allocation" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <h5 class="card-header border-bottom fw-bold">Lead Allocation Settings</h5>
                        <div class="card-body pt-4">
                            <p class="text-muted">Define lead quota limits and allocation parameters per sales executive.</p>
                        </div>
                    </div>
                </div>

                <!-- Lead Points Settings (Existing Referral/Lead Points Form) -->
                <div class="tab-pane fade" id="v-pills-lead-points" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <h5 class="card-header border-bottom fw-bold">Lead Points Settings</h5>
                        <div class="card-body pt-4">
                            <form action="{{ route('admin.settings.lead.update') }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-12 mb-3">
                                        <label for="referral_points" class="form-label fw-semibold">Referral Points <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text"><i class="bx bx-gift text-primary"></i></span>
                                            <input type="number" class="form-control @error('referral_points') is-invalid @enderror" id="referral_points" name="referral_points" value="{{ old('referral_points', $setting->referral_points) }}" placeholder="100" min="0" required />
                                        </div>
                                        <div class="form-text mt-2 text-muted">
                                            Points rewarded to members for successful client referrals.
                                        </div>
                                        @error('referral_points')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @can('lead-settings.edit')
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="bx bx-save me-1"></i> Save Settings
                                        </button>
                                    </div>
                                @endcan
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal: + Create Field -->
<div class="modal fade" id="createFieldModal" tabindex="-1" aria-labelledby="createFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="createFieldModalLabel">Create New Custom Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createCustomFieldForm" onsubmit="event.preventDefault(); addNewFieldRow();">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label for="field_label" class="form-label fw-semibold">Field Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="field_label" placeholder="e.g. GST Number, Alternate Phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="field_type" class="form-label fw-semibold">Field Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="field_type" required>
                            <option value="Text">Text</option>
                            <option value="Number" selected>Number</option>
                            <option value="Dropdown">Dropdown / Select</option>
                            <option value="Textarea">Textarea</option>
                            <option value="Date">Date</option>
                            <option value="Checkbox">Checkbox</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="field_options" class="form-label fw-semibold">Options</label>
                        <input type="text" class="form-control" id="field_options" placeholder="e.g. Option 1, Option 2 (Comma separated for dropdown)">
                        <div class="form-text text-muted">Leave blank if not applicable.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Is Required?</label>
                        <div class="d-flex gap-4 pt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_required" id="req_no" value="No" checked>
                                <label class="form-check-label" for="req_no">No</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_required" id="req_yes" value="Yes">
                                <label class="form-check-label" for="req_yes">Yes</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4" style="background-color: #00a65a; border-color: #00a65a;">Save Field</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-lead-nav .nav-link {
        color: #566a7f;
        border-radius: 0.375rem;
        transition: all 0.2s ease-in-out;
    }
    .custom-lead-nav .nav-link:hover {
        background-color: #f5f5f9;
        color: #696cff;
    }
    .custom-lead-nav .nav-link.active {
        background-color: #e7e7ff;
        color: #696cff;
        font-weight: 600 !important;
    }
</style>

<script>
    function addNewFieldRow() {
        const label = document.getElementById('field_label').value.trim();
        const type = document.getElementById('field_type').value;
        const options = document.getElementById('field_options').value.trim() || '-';
        const required = document.querySelector('input[name="is_required"]:checked').value;

        if (!label) return;

        const tableBody = document.getElementById('customFieldsTableBody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td class="fw-medium text-dark">${label}</td>
            <td><span class="badge bg-label-info text-dark">${type}</span></td>
            <td class="text-muted">${options}</td>
            <td><span class="badge ${required === 'Yes' ? 'bg-label-primary' : 'bg-label-secondary'}">${required}</span></td>
            <td class="text-center">
                <button type="button" class="btn btn-icon btn-sm btn-outline-success me-1" title="Edit Field">
                    <i class="bx bx-edit-alt"></i>
                </button>
                <button type="button" class="btn btn-icon btn-sm btn-outline-danger" onclick="this.closest('tr').remove();" title="Delete Field">
                    <i class="bx bx-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(newRow);

        // Reset form and close modal
        document.getElementById('createCustomFieldForm').reset();
        const modalEl = document.getElementById('createFieldModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
</script>
@endsection
