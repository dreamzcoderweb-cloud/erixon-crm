@extends('layouts.master')

@section('title', 'Demo Process Settings - Settings')

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
                        <a class="nav-link" href="{{ route('admin.settings.lead') }}">
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
                        <a class="nav-link active" href="{{ route('admin.settings.demo_process') }}">
                            <i class="bx bx-slideshow me-1"></i> Demo Process Setting
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </div>

    <!-- Demo Process Settings Vertical Layout -->
    <div class="row">
        <!-- Left Sidebar Navigation Menu -->
        <div class="col-md-3 mb-4 mb-md-0">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="nav flex-column nav-pills custom-lead-nav gap-1" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link text-start py-2 px-3 fw-medium active" id="v-pills-additional-fields-tab" data-bs-toggle="pill" data-bs-target="#v-pills-additional-fields" type="button" role="tab">
                            <i class="bx bx-list-plus me-2"></i> Additional Demo Process Fields
                        </button>
                        <button class="nav-link text-start py-2 px-3 fw-medium" id="v-pills-site-customization-tab" data-bs-toggle="pill" data-bs-target="#v-pills-site-customization" type="button" role="tab">
                            <i class="bx bx-customize me-2"></i> Demo Process List Customization
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content Panes -->
        <div class="col-md-9">
            <div class="tab-content p-0 shadow-none bg-transparent" id="v-pills-tabContent">

                <!-- Additional Demo Process Fields (Default Active) -->
                <div class="tab-pane fade show active" id="v-pills-additional-fields" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom d-flex align-items-center justify-content-between py-3">
                            <div>
                                <h5 class="card-title mb-0 fw-bold text-dark">Custom Demo Process Fields</h5>
                                <span class="text-muted small">Additional Demo Process Fields</span>
                            </div>
                            @can('demo-process-settings.edit')
                                <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createFieldModal">
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
                                                            @can('demo-process-settings.edit')
                                                                <a class="dropdown-item edit-field-btn" href="javascript:void(0);" data-id="{{ $field->id }}">
                                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                                </a>
                                                                <a class="dropdown-item text-danger delete-field-btn" href="javascript:void(0);" data-id="{{ $field->id }}" data-label="{{ $field->field_label }}">
                                                                    <i class="bx bx-trash me-1"></i> Delete
                                                                </a>
                                                            @endcan
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

                <!-- Demo Process List Customization -->
                <div class="tab-pane fade" id="v-pills-site-customization" role="tabpanel">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header border-bottom bg-white d-flex align-items-center justify-content-between py-3 px-4">
                            <h5 class="card-title mb-0 fw-bold text-dark fs-5">Demo Process List Customization</h5>
                            @can('demo-process-settings.edit')
                                <button type="button" class="btn btn-primary fw-semibold shadow-sm" id="saveDemoProcessColumnsBtn">
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
                                        <span class="text-muted small no-fields-msg">No fields selected. Select available fields below.</span>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Section: Available Fields (Checkbox) -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark mb-3 fs-6">Available Fields (Checkbox)</h6>
                                <div class="row g-3">
                                    @php
                                        $allFieldsList = array_values($allAvailableFields);
                                        $halfCount = ceil(count($allFieldsList) / 2);
                                        $col1 = array_slice($allFieldsList, 0, $halfCount);
                                        $col2 = array_slice($allFieldsList, $halfCount);
                                    @endphp

                                    <!-- Column 1 -->
                                    <div class="col-md-6">
                                        <div class="d-flex flex-column gap-2">
                                            @foreach ($col1 as $fieldItem)
                                                @php $isChecked = in_array($fieldItem['key'], $selectedColumns); @endphp
                                                <div class="form-check form-check-custom py-1">
                                                    <input class="form-check-input available-field-checkbox" type="checkbox" value="{{ $fieldItem['key'] }}" id="chk_col_{{ $fieldItem['key'] }}" {{ $isChecked ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-medium text-dark ms-2 select-none" for="chk_col_{{ $fieldItem['key'] }}">
                                                        {{ $fieldItem['label'] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Column 2 -->
                                    <div class="col-md-6">
                                        <div class="d-flex flex-column gap-2">
                                            @foreach ($col2 as $fieldItem)
                                                @php $isChecked = in_array($fieldItem['key'], $selectedColumns); @endphp
                                                <div class="form-check form-check-custom py-1">
                                                    <input class="form-check-input available-field-checkbox" type="checkbox" value="{{ $fieldItem['key'] }}" id="chk_col_{{ $fieldItem['key'] }}" {{ $isChecked ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-medium text-dark ms-2 select-none" for="chk_col_{{ $fieldItem['key'] }}">
                                                        {{ $fieldItem['label'] }}
                                                    </label>
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
    </div>

</div>

<!-- Modal: Create Custom Field -->
<div class="modal fade" id="createFieldModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Create Demo Process Custom Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createFieldForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Field Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="field_label" placeholder="e.g. Alternate Contact, Demo Mode" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Field Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="field_type" id="create_field_type" required>
                            <option value="Text">Text</option>
                            <option value="Number">Number</option>
                            <option value="Dropdown">Dropdown</option>
                            <option value="Textarea">Textarea</option>
                            <option value="Date">Date</option>
                            <option value="Checkbox">Checkbox</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 d-none" id="create_options_container">
                        <label class="form-label fw-semibold">Options <span class="text-muted small">(Comma separated)</span></label>
                        <textarea class="form-control" name="field_options" rows="2" placeholder="Online, Offline, In-Person"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Is Required? <span class="text-danger">*</span></label>
                        <select class="form-select" name="is_required" required>
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="createFieldSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                        Create Field
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Custom Field -->
<div class="modal fade" id="editFieldModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Custom Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editFieldForm">
                @csrf
                <input type="hidden" name="field_id" id="edit_field_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Field Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="field_label" id="edit_field_label" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Field Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="field_type" id="edit_field_type" required>
                            <option value="Text">Text</option>
                            <option value="Number">Number</option>
                            <option value="Dropdown">Dropdown</option>
                            <option value="Textarea">Textarea</option>
                            <option value="Date">Date</option>
                            <option value="Checkbox">Checkbox</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 d-none" id="edit_options_container">
                        <label class="form-label fw-semibold">Options <span class="text-muted small">(Comma separated)</span></label>
                        <textarea class="form-control" name="field_options" id="edit_field_options" rows="2"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Is Required? <span class="text-danger">*</span></label>
                        <select class="form-select" name="is_required" id="edit_is_required" required>
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="editFieldSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                        Update Field
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Delete Confirmation -->
<div class="modal fade" id="deleteFieldModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-4">
                <div class="text-danger mb-3">
                    <i class="bx bx-trash fs-1"></i>
                </div>
                <h5 class="fw-bold mb-2">Delete Custom Field?</h5>
                <p class="text-muted small mb-0">Are you sure you want to delete this custom field? This action cannot be undone.</p>
                <input type="hidden" id="delete_field_id">
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteFieldBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-green-pill {
        background-color: #d1f2d9 !important;
        border-color: #a3e4b3 !important;
    }
    .cursor-move {
        cursor: move;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .min-height-100 {
        min-height: 80px;
    }
    .select-none {
        user-select: none;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Toggle options container on field type select
    function toggleOptions(typeSelect, optionsContainer) {
        if (typeSelect.val() === 'Dropdown') {
            optionsContainer.removeClass('d-none');
        } else {
            optionsContainer.addClass('d-none');
        }
    }

    $('#create_field_type').on('change', function() {
        toggleOptions($(this), $('#create_options_container'));
    });

    $('#edit_field_type').on('change', function() {
        toggleOptions($(this), $('#edit_options_container'));
    });

    // Helper functions for alerts & errors
    function showAlert(type, message) {
        let alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show mb-4" role="alert">
                <i class="bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-circle'} me-1"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#settingsAlertContainer').html(alertHtml);
    }

    function showValidationErrors(form, errors) {
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');
        $.each(errors, function(field, messages) {
            let input = form.find(`[name="${field}"]`);
            if (input.length) {
                input.addClass('is-invalid');
                input.siblings('.invalid-feedback').text(messages[0]);
            }
        });
    }

    // Submit Create Custom Field Form
    $('#createFieldForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = $('#createFieldSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');

        $.ajax({
            url: "{{ route('admin.settings.demo_process.custom-fields.store') }}",
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.status) {
                    $('#createFieldModal').modal('hide');
                    form[0].reset();
                    showAlert('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 600);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'An error occurred while saving custom field.');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Open Edit Custom Field Modal
    $(document).on('click', '.edit-field-btn', function() {
        let id = $(this).data('id');
        $.ajax({
            url: "{{ url('admin/settings/demo-process/custom-fields/edit') }}/" + id,
            type: 'GET',
            success: function(response) {
                if (response.status) {
                    let field = response.data;
                    $('#edit_field_id').val(field.id);
                    $('#edit_field_label').val(field.field_label);
                    $('#edit_field_type').val(field.field_type);
                    $('#edit_field_options').val(field.field_options || '');
                    $('#edit_is_required').val(field.is_required);

                    toggleOptions($('#edit_field_type'), $('#edit_options_container'));
                    $('#editFieldModal').modal('show');
                }
            },
            error: function() {
                showAlert('danger', 'Failed to fetch field details.');
            }
        });
    });

    // Submit Edit Custom Field Form
    $('#editFieldForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let id = $('#edit_field_id').val();
        let submitBtn = $('#editFieldSubmitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');

        $.ajax({
            url: "{{ url('admin/settings/demo-process/custom-fields/update') }}/" + id,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.status) {
                    $('#editFieldModal').modal('hide');
                    showAlert('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 600);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showValidationErrors(form, xhr.responseJSON.errors);
                } else {
                    showAlert('danger', 'An error occurred while updating custom field.');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Open Delete Custom Field Modal
    $(document).on('click', '.delete-field-btn', function() {
        let id = $(this).data('id');
        $('#delete_field_id').val(id);
        $('#deleteFieldModal').modal('show');
    });

    // Confirm Delete Custom Field
    $('#confirmDeleteFieldBtn').on('click', function() {
        let id = $('#delete_field_id').val();
        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ url('admin/settings/demo-process/custom-fields/delete') }}/" + id,
            type: 'DELETE',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.status) {
                    $('#deleteFieldModal').modal('hide');
                    showAlert('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 600);
                }
            },
            error: function() {
                showAlert('danger', 'Failed to delete custom field.');
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    });

    /* --- Demo Process List Customization (Drag & Drop Horizontal Chips + Checkboxes) --- */
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
    $('#saveDemoProcessColumnsBtn').on('click', function () {
        let columns = [];
        $('#selectedFieldsContainer .selected-field-chip').each(function () {
            columns.push($(this).data('key'));
        });

        let btn = $(this);
        let spinner = btn.find('.spinner-border');
        btn.prop('disabled', true);
        spinner.removeClass('d-none');

        $.ajax({
            url: "{{ route('admin.settings.demo_process.list-columns.save') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                columns: columns
            },
            success: function (response) {
                if (response.status) {
                    showAlert('success', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Failed to save Demo Process List customization.');
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
