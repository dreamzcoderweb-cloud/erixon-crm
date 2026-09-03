@extends('layouts.master')

@section('title', 'Lead Settings - Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div id="settingsAlertContainer">
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
    </div>

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
                            <i class="bx bx-target-lock me-1"></i> Lead Setting
                        </a>
                    </li>
                @endcan
                @can('customer-settings.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.settings.customer') }}">
                            <i class="bx bx-user me-1"></i> Customer Setting
                        </a>
                    </li>
                @endcan
                @can('followup-settings.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.settings.followup') }}">
                            <i class="bx bx-calendar-event me-1"></i> Followup Setting
                        </a>
                    </li>
                @endcan
                @can('credit-request-settings.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.settings.credit_request') }}">
                            <i class="bx bx-credit-card me-1"></i> Credit Request Setting
                        </a>
                    </li>
                @endcan
                @can('demo-process-settings.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.settings.demo_process') }}">
                            <i class="bx bx-slideshow me-1"></i> Demo Process Setting
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
                        <button class="nav-link text-start py-2 px-3 fw-medium active" id="v-pills-additional-fields-tab" data-bs-toggle="pill" data-bs-target="#v-pills-additional-fields" type="button" role="tab">
                            <i class="bx bx-list-plus me-2"></i> Additional Lead Fields
                        </button>
                        <button class="nav-link text-start py-2 px-3 fw-medium" id="v-pills-site-customization-tab" data-bs-toggle="pill" data-bs-target="#v-pills-site-customization" type="button" role="tab">
                            <i class="bx bx-customize me-2"></i> Lead List Customization
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
                            @can('lead-settings.edit')
                                <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createFieldModal" >
                                    <i class="bx bx-plus me-1"></i> Create Field
                                </button>
                            @endcan
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
                                        @forelse ($customFields as $field)
                                            <tr>
                                                <td class="fw-medium text-dark">{{ $field->field_label }}</td>
                                                <td><span class="badge bg-label-info text-dark">{{ $field->field_type }}</span></td>
                                                <td class="text-muted">{{ $field->field_options ?: '-' }}</td>
                                                <td>
                                                    <span class="badge {{ $field->is_required === 'Yes' ? 'bg-label-primary' : 'bg-label-secondary' }}">
                                                        {{ $field->is_required }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="dropdown">
                                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                            <i class="bx bx-dots-vertical-rounded"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item btn-edit-field" href="javascript:void(0);" data-id="{{ $field->id }}">
                                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                                            </a>
                                                            <a class="dropdown-item text-danger btn-delete-field" href="javascript:void(0);" data-id="{{ $field->id }}" data-label="{{ $field->field_label }}">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">No custom fields created yet. Click "Create Field" to add one.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lead List Customization -->
                <div class="tab-pane fade" id="v-pills-site-customization" role="tabpanel">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header border-bottom bg-white d-flex align-items-center justify-content-between py-3 px-4">
                            <h5 class="card-title mb-0 fw-bold text-dark fs-5">Lead List Customization</h5>
                            @can('lead-settings.edit')
                                <button type="button" class="btn btn-primary fw-semibold shadow-sm" id="saveLeadListColumnsBtn"
                                    <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                                    Save
                                </button>
                            @endcan
                        </div>
                        <div class="card-body p-4">
                            <!-- Section: Selected Fields (Drag) -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark mb-3 fs-6">Selected Fields (Drag)</h6>
                                <div id="selectedFieldsContainer" class="d-flex flex-wrap gap-2 align-items-center p-3 rounded-3 border bg-light min-height-100">
                                    @forelse ($selectedColumns as $key)
                                        @if (isset($allAvailableFields[$key]))
                                            @php $item = $allAvailableFields[$key]; @endphp
                                            <div class="selected-field-chip d-inline-flex align-items-center rounded-pill px-3 py-2 border bg-green-pill cursor-move shadow-sm" draggable="true" data-key="{{ $key }}">
                                                <span class="fw-medium text-dark small me-2">{{ $item['label'] }}</span>
                                                <span class="remove-chip-icon text-muted cursor-pointer remove-field-chip-btn" data-key="{{ $key }}" title="Remove">&times;</span>
                                            </div>
                                        @endif
                                    @empty
                                        <div class="text-muted small no-fields-msg py-2">No fields selected. Select fields from Available Fields below.</div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Section: Available Fields -->
                            <div>
                                <h6 class="fw-bold text-dark mb-3 fs-6">Available Fields</h6>
                                <div class="row row-cols-1 row-cols-md-2 g-3" id="availableFieldsList">
                                    @foreach ($allAvailableFields as $key => $item)
                                        @php $isChecked = in_array($key, $selectedColumns); @endphp
                                        <div class="col">
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input available-field-checkbox me-2" type="checkbox" value="{{ $key }}" id="chk_col_{{ $key }}" {{ $isChecked ? 'checked' : '' }} style="width: 1.1rem; height: 1.1rem;">
                                                <label class="form-check-label fw-medium text-dark cursor-pointer select-none" for="chk_col_{{ $key }}">
                                                    {{ $item['label'] }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
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
            <form id="createCustomFieldForm" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label for="field_label" class="form-label fw-semibold">Field Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="field_label" id="field_label" placeholder="e.g. GST Number, Alternate Phone" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="field_type" class="form-label fw-semibold">Field Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="field_type" id="field_type" required>
                            <option value="Text">Text</option>
                            <option value="Number">Number</option>
                            <option value="Dropdown">Dropdown / Select</option>
                            <option value="Textarea">Textarea</option>
                            <option value="Date">Date</option>
                            <option value="Checkbox">Checkbox</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="field_options" class="form-label fw-semibold">Options</label>
                        <input type="text" class="form-control" name="field_options" id="field_options" placeholder="e.g. Option 1, Option 2 (Comma separated for dropdown)">
                        <div class="form-text text-muted">Leave blank if not applicable.</div>
                        <div class="invalid-feedback"></div>
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
                        <div class="invalid-feedback d-block"></div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveCustomFieldBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Field
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Field -->
<div class="modal fade" id="editFieldModal" tabindex="-1" aria-labelledby="editFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="editFieldModalLabel">Edit Custom Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCustomFieldForm" method="POST">
                @csrf
                <input type="hidden" name="field_id" id="edit_field_id">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label for="edit_field_label" class="form-label fw-semibold">Field Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="field_label" id="edit_field_label" placeholder="e.g. GST Number" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_field_type" class="form-label fw-semibold">Field Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="field_type" id="edit_field_type" required>
                            <option value="Text">Text</option>
                            <option value="Number">Number</option>
                            <option value="Dropdown">Dropdown / Select</option>
                            <option value="Textarea">Textarea</option>
                            <option value="Date">Date</option>
                            <option value="Checkbox">Checkbox</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_field_options" class="form-label fw-semibold">Options</label>
                        <input type="text" class="form-control" name="field_options" id="edit_field_options" placeholder="e.g. Option 1, Option 2 (Comma separated for dropdown)">
                        <div class="form-text text-muted">Leave blank if not applicable.</div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Is Required?</label>
                        <div class="d-flex gap-4 pt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_required" id="edit_req_no" value="No">
                                <label class="form-check-label" for="edit_req_no">No</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_required" id="edit_req_yes" value="Yes">
                                <label class="form-check-label" for="edit_req_yes">Yes</label>
                            </div>
                        </div>
                        <div class="invalid-feedback d-block"></div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateCustomFieldBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Field
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Delete Field -->
<div class="modal fade" id="deleteFieldModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-danger"><i class="bx bx-trash me-1"></i> Delete Custom Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0">Are you sure you want to delete the custom field <strong id="delete_field_label"></strong>?</p>
            </div>
            <div class="modal-footer border-top py-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteFieldBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Delete
                </button>
            </div>
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
        color: var(--theme-color, #696cff);
    }
    .custom-lead-nav .nav-link.active {
        background-color: var(--theme-color, #696cff) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    .cursor-move {
        cursor: grab;
    }
    .cursor-move:active {
        cursor: grabbing;
    }
    .bg-green-pill {
        background-color: #dcf5e7 !important;
        border: 1px solid #7edca6 !important;
        color: #1b5e35 !important;
        transition: all 0.2s ease-in-out;
    }
    .bg-green-pill:hover {
        background-color: #ceefdc !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    }
    .remove-chip-icon {
        font-size: 1.2rem;
        line-height: 1;
        font-weight: bold;
        color: #2e7d32 !important;
        padding-left: 4px;
        transition: color 0.15s ease;
    }
    .remove-chip-icon:hover {
        color: #d32f2f !important;
    }
    .min-height-100 {
        min-height: 90px;
    }
    .select-none {
        user-select: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function showSettingsAlert(type, message) {
            let html = `
                <div class="alert alert-${type} alert-dismissible fade show mb-4" role="alert">
                    <i class="bx bx-${type === 'success' ? 'check-circle' : 'error-circle'} me-1"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            $('#settingsAlertContainer').html(html);
            setTimeout(function () {
                $('#settingsAlertContainer .alert').alert('close');
            }, 4000);
        }

        function clearModalErrors(form) {
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
        }

        function showModalErrors(form, errors) {
            clearModalErrors(form);
            $.each(errors, function (field, messages) {
                let input = form.find(`[name="${field}"]`);
                if (input.length) {
                    input.addClass('is-invalid');
                    let errDiv = input.siblings('.invalid-feedback');
                    if (!errDiv.length) {
                        errDiv = input.parent().find('.invalid-feedback');
                    }
                    errDiv.text(messages[0]);
                }
            });
        }

        $('.modal').on('hidden.bs.modal', function () {
            let form = $(this).find('form');
            if (form.length) {
                form[0].reset();
                clearModalErrors(form);
            }
        });

        // Submit Create Custom Field
        $('#createCustomFieldForm').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            let submitBtn = $('#saveCustomFieldBtn');
            let spinner = submitBtn.find('.spinner-border');

            submitBtn.prop('disabled', true);
            spinner.removeClass('d-none');
            clearModalErrors(form);

            $.ajax({
                url: "{{ route('admin.settings.lead.custom-fields.store') }}",
                type: "POST",
                data: form.serialize(),
                success: function (response) {
                    if (response.status) {
                        $('#createFieldModal').modal('hide');
                        showSettingsAlert('success', response.message);
                        setTimeout(function () {
                            location.reload();
                        }, 800);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        showModalErrors(form, xhr.responseJSON.errors);
                    } else {
                        showSettingsAlert('danger', 'An error occurred while creating custom field.');
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });

        // Open Edit Modal
        $(document).on('click', '.btn-edit-field', function () {
            let fieldId = $(this).data('id');
            let form = $('#editCustomFieldForm');
            clearModalErrors(form);

            $.ajax({
                url: APP_URL + '/admin/settings/lead/custom-fields/edit/' + fieldId,
                type: 'GET',
                success: function (response) {
                    if (response.status) {
                        let data = response.data;
                        $('#edit_field_id').val(data.id);
                        $('#edit_field_label').val(data.field_label);
                        $('#edit_field_type').val(data.field_type);
                        $('#edit_field_options').val(data.field_options || '');
                        if (data.is_required === 'Yes') {
                            $('#edit_req_yes').prop('checked', true);
                        } else {
                            $('#edit_req_no').prop('checked', true);
                        }
                        $('#editFieldModal').modal('show');
                    }
                },
                error: function () {
                    showSettingsAlert('danger', 'Failed to fetch field details.');
                }
            });
        });

        // Submit Update Custom Field
        $('#editCustomFieldForm').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            let fieldId = $('#edit_field_id').val();
            let submitBtn = $('#updateCustomFieldBtn');
            let spinner = submitBtn.find('.spinner-border');

            submitBtn.prop('disabled', true);
            spinner.removeClass('d-none');
            clearModalErrors(form);

            $.ajax({
                url: APP_URL + '/admin/settings/lead/custom-fields/update/' + fieldId,
                type: 'POST',
                data: form.serialize(),
                success: function (response) {
                    if (response.status) {
                        $('#editFieldModal').modal('hide');
                        showSettingsAlert('success', response.message);
                        setTimeout(function () {
                            location.reload();
                        }, 800);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        showModalErrors(form, xhr.responseJSON.errors);
                    } else {
                        showSettingsAlert('danger', 'An error occurred while updating custom field.');
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });

        // Open Delete Modal
        let deleteFieldId = null;
        $(document).on('click', '.btn-delete-field', function () {
            deleteFieldId = $(this).data('id');
            let label = $(this).data('label');
            $('#delete_field_label').text(label);
            $('#deleteFieldModal').modal('show');
        });

        // Confirm Delete Field
        $('#confirmDeleteFieldBtn').on('click', function () {
            if (!deleteFieldId) return;

            let btn = $(this);
            let spinner = btn.find('.spinner-border');

            btn.prop('disabled', true);
            spinner.removeClass('d-none');

            $.ajax({
                url: APP_URL + '/admin/settings/lead/custom-fields/delete/' + deleteFieldId,
                type: 'DELETE',
                success: function (response) {
                    if (response.status) {
                        $('#deleteFieldModal').modal('hide');
                        showSettingsAlert('success', response.message);
                        setTimeout(function () {
                            location.reload();
                        }, 800);
                    }
                },
                error: function () {
                    showSettingsAlert('danger', 'Failed to delete custom field.');
                },
                complete: function () {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                    deleteFieldId = null;
                }
            });
        });

        /* --- Lead List Customization (Drag & Drop Horizontal Chips + 2-Col Checkboxes) --- */
        let dragItem = null;

        $('#selectedFieldsContainer').on('dragstart', '.selected-field-chip', function (e) {
            dragItem = this;
            $(this).css('opacity', '0.5');
            if (e.originalEvent && e.originalEvent.dataTransfer) {
                e.originalEvent.dataTransfer.effectAllowed = 'move';
            }
        });

        $('#selectedFieldsContainer').on('dragend', '.selected-field-chip', function () {
            $(this).css('opacity', '1');
            dragItem = null;
        });

        $('#selectedFieldsContainer').on('dragover', function (e) {
            e.preventDefault();
            if (e.originalEvent && e.originalEvent.dataTransfer) {
                e.originalEvent.dataTransfer.dropEffect = 'move';
            }
            let target = e.target.closest('.selected-field-chip');
            if (target && target !== dragItem) {
                let rect = target.getBoundingClientRect();
                let next = (e.clientX - rect.left) / (rect.right - rect.left) > 0.5;
                let container = document.getElementById('selectedFieldsContainer');
                container.insertBefore(dragItem, next ? target.nextSibling : target);
            }
        });

        // Available Fields Checkbox Toggle
        $(document).on('change', '.available-field-checkbox', function () {
            let key = $(this).val();
            let isChecked = $(this).is(':checked');

            if (isChecked) {
                if ($('#selectedFieldsContainer').find(`[data-key="${key}"]`).length === 0) {
                    let label = $(this).siblings('label').text().trim();
                    let chipHtml = `
                        <div class="selected-field-chip d-inline-flex align-items-center rounded-pill px-3 py-2 border bg-green-pill cursor-move shadow-sm" draggable="true" data-key="${key}">
                            <span class="fw-medium text-dark small me-2">${label}</span>
                            <span class="remove-chip-icon text-muted cursor-pointer remove-field-chip-btn" data-key="${key}" title="Remove">&times;</span>
                        </div>
                    `;
                    $('#selectedFieldsContainer').append(chipHtml);
                    $('.no-fields-msg').remove();
                }
            } else {
                $('#selectedFieldsContainer').find(`[data-key="${key}"]`).remove();
            }
        });

        // Remove field via chip x button
        $(document).on('click', '.remove-field-chip-btn', function () {
            let key = $(this).data('key');
            $(`#chk_col_${key}`).prop('checked', false).trigger('change');
        });

        // Save Customization Order
        $('#saveLeadListColumnsBtn').on('click', function () {
            let columns = [];
            $('#selectedFieldsContainer .selected-field-chip').each(function () {
                columns.push($(this).data('key'));
            });

            let btn = $(this);
            let spinner = btn.find('.spinner-border');
            btn.prop('disabled', true);
            spinner.removeClass('d-none');

            $.ajax({
                url: "{{ route('admin.settings.lead.list-columns.save') }}",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    columns: columns
                },
                success: function (response) {
                    if (response.status) {
                        showSettingsAlert('success', response.message);
                    }
                },
                error: function () {
                    showSettingsAlert('danger', 'Failed to save Lead List customization.');
                },
                complete: function () {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });
    });
</script>
@endsection
