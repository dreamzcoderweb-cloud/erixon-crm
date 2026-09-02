@extends('layouts.master')

@section('title', 'Credit Request Settings - Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div id="settingsAlertContainer">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
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
                        <a class="nav-link active" href="{{ route('admin.settings.credit_request') }}">
                            <i class="bx bx-credit-card me-1"></i> Credit Request Setting
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </div>

    <!-- Credit Request Settings Vertical Layout -->
    <div class="row">
        <!-- Left Sidebar Navigation Menu -->
        <div class="col-md-3 mb-4 mb-md-0">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="nav flex-column nav-pills custom-lead-nav gap-1" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link text-start py-2 px-3 fw-medium active" id="v-pills-additional-fields-tab" data-bs-toggle="pill" data-bs-target="#v-pills-additional-fields" type="button" role="tab">
                            <i class="bx bx-list-plus me-2"></i> Additional Credit Request Fields
                        </button>
                        <button class="nav-link text-start py-2 px-3 fw-medium" id="v-pills-site-customization-tab" data-bs-toggle="pill" data-bs-target="#v-pills-site-customization" type="button" role="tab">
                            <i class="bx bx-customize me-2"></i> Credit Request List Customization
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content Panels -->
        <div class="col-md-9">
            <div class="tab-content p-0 bg-transparent shadow-none border-0" id="v-pills-tabContent">

                <!-- 1. Additional Credit Request Fields Tab -->
                <div class="tab-pane fade show active" id="v-pills-additional-fields" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0 fw-bold">Custom Credit Request Fields</h5>
                                <small class="text-muted">Additional Credit Request Fields</small>
                            </div>
                            @can('credit-request-settings.edit')
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#createFieldModal">
                                    <i class="bx bx-plus me-1"></i> Create Field
                                </button>
                            @endcan
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">LABEL</th>
                                            <th>TYPE</th>
                                            <th>OPTIONS</th>
                                            <th>REQUIRED</th>
                                            <th class="text-end pe-4">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($customFields as $field)
                                            <tr>
                                                <td class="ps-4 fw-semibold text-dark">{{ $field->field_label }}</td>
                                                <td><span class="badge bg-label-info">{{ $field->field_type }}</span></td>
                                                <td>{{ $field->field_options ? $field->field_options : '-' }}</td>
                                                <td>
                                                    @if ($field->is_required === 'Yes')
                                                        <span class="badge bg-label-danger">Yes</span>
                                                    @else
                                                        <span class="badge bg-label-secondary">No</span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-4">
                                                    @can('credit-request-settings.edit')
                                                        <div class="dropdown">
                                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                <i class="bx bx-dots-vertical-rounded"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a class="dropdown-item edit-field-btn" href="javascript:void(0);" data-id="{{ $field->id }}">
                                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                                </a>
                                                                <a class="dropdown-item text-danger delete-field-btn" href="javascript:void(0);" data-id="{{ $field->id }}">
                                                                    <i class="bx bx-trash me-1"></i> Delete
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted fs-7">-</span>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    <i class="bx bx-info-circle me-1"></i> No custom fields created yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Credit Request List Customization Tab -->
                <div class="tab-pane fade" id="v-pills-site-customization" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0 fw-bold">Credit Request List Customization</h5>
                                <small class="text-muted">Configure visible table columns and display order for Credit Request Management</small>
                            </div>
                            @can('credit-request-settings.edit')
                                <button type="button" class="btn btn-danger btn-sm" id="saveCreditRequestColumnsBtn">
                                    <i class="bx bx-save me-1"></i> Save
                                </button>
                            @endcan
                        </div>
                        <div class="card-body p-4">
                            <form id="creditRequestListColumnsForm">
                                @csrf
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark mb-2">Selected Fields (Drag to reorder)</label>
                                    <div id="selectedColumnsContainer" class="d-flex flex-wrap gap-2 p-3 bg-light rounded border min-height-60 align-items-center">
                                        @foreach ($selectedColumns as $colKey)
                                            @if (isset($allAvailableFields[$colKey]))
                                                @php $item = $allAvailableFields[$colKey]; @endphp
                                                <div class="draggable-pill badge bg-label-primary fs-6 py-2 px-3 d-flex align-items-center gap-2 cursor-move" data-key="{{ $colKey }}">
                                                    <span>{{ $item['label'] }}</span>
                                                    <i class="bx bx-x text-danger fs-5 remove-column-btn" style="cursor: pointer;" title="Remove field"></i>
                                                    <input type="hidden" name="columns[]" value="{{ $colKey }}">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label fw-bold text-dark mb-2">Available Fields</label>
                                    <div class="row g-3 p-3 bg-white rounded border">
                                        @foreach ($allAvailableFields as $key => $fieldItem)
                                            @php $isChecked = in_array($key, $selectedColumns); @endphp
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check">
                                                    <input class="form-check-input column-toggle-checkbox" type="checkbox" id="col_chk_{{ $key }}" data-key="{{ $key }}" data-label="{{ $fieldItem['label'] }}" {{ $isChecked ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-medium" for="col_chk_{{ $key }}">
                                                        {{ $fieldItem['label'] }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Create Field Modal -->
<div class="modal fade" id="createFieldModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="createFieldForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bx bx-plus-circle me-1 text-primary"></i> Create Custom Field</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Field Label <span class="text-danger">*</span></label>
                        <input type="text" name="field_label" class="form-control" placeholder="e.g. Credit Reason, Tax ID" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Field Type <span class="text-danger">*</span></label>
                        <select name="field_type" id="create_field_type" class="form-select" required>
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
                        <label class="form-label fw-semibold">Dropdown Options <span class="text-danger">*</span></label>
                        <textarea name="field_options" class="form-control" rows="2" placeholder="Option 1, Option 2, Option 3 (comma separated)"></textarea>
                        <small class="text-muted">Separate multiple options with commas.</small>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Is Required? <span class="text-danger">*</span></label>
                        <select name="is_required" class="form-select" required>
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="createFieldSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Field
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Field Modal -->
<div class="modal fade" id="editFieldModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="editFieldForm">
                @csrf
                <input type="hidden" name="field_id" id="edit_field_id">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bx bx-edit me-1 text-primary"></i> Edit Custom Field</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Field Label <span class="text-danger">*</span></label>
                        <input type="text" name="field_label" id="edit_field_label" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Field Type <span class="text-danger">*</span></label>
                        <select name="field_type" id="edit_field_type" class="form-select" required>
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
                        <label class="form-label fw-semibold">Dropdown Options <span class="text-danger">*</span></label>
                        <textarea name="field_options" id="edit_field_options" class="form-control" rows="2"></textarea>
                        <small class="text-muted">Separate multiple options with commas.</small>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Is Required? <span class="text-danger">*</span></label>
                        <select name="is_required" id="edit_is_required" class="form-select" required>
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="editFieldSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Field
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteFieldModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-trash me-1 text-danger"></i> Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete this custom field? This action cannot be undone.</p>
                <input type="hidden" id="delete_field_id">
            </div>
            <div class="modal-footer gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteFieldBtn">Delete Field</button>
            </div>
        </div>
    </div>
</div>

<style>
    .draggable-pill {
        user-select: none;
        transition: all 0.2s ease;
    }
    .draggable-pill.dragging {
        opacity: 0.5;
    }
    .cursor-move {
        cursor: move;
    }
    .min-height-60 {
        min-height: 60px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
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
            url: "{{ route('admin.settings.credit_request.custom_fields.store') }}",
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
            url: "{{ url('admin/settings/credit-request/custom-fields/edit') }}/" + id,
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
            url: "{{ url('admin/settings/credit-request/custom-fields/update') }}/" + id,
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
            url: "{{ url('admin/settings/credit-request/custom-fields/destroy') }}/" + id,
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

    // --- Drag and Drop Column Customization ---
    let container = document.getElementById('selectedColumnsContainer');
    if (container) {
        new Sortable(container, {
            animation: 150,
            ghostClass: 'dragging'
        });
    }

    // Toggle column pill on checkbox change
    $('.column-toggle-checkbox').on('change', function() {
        let key = $(this).data('key');
        let label = $(this).data('label');

        if ($(this).is(':checked')) {
            if (!$(`#selectedColumnsContainer .draggable-pill[data-key="${key}"]`).length) {
                let pillHtml = `
                    <div class="draggable-pill badge bg-label-primary fs-6 py-2 px-3 d-flex align-items-center gap-2 cursor-move" data-key="${key}">
                        <span>${label}</span>
                        <i class="bx bx-x text-danger fs-5 remove-column-btn" style="cursor: pointer;" title="Remove field"></i>
                        <input type="hidden" name="columns[]" value="${key}">
                    </div>
                `;
                $('#selectedColumnsContainer').append(pillHtml);
            }
        } else {
            $(`#selectedColumnsContainer .draggable-pill[data-key="${key}"]`).remove();
        }
    });

    // Remove pill click event
    $(document).on('click', '.remove-column-btn', function() {
        let pill = $(this).closest('.draggable-pill');
        let key = pill.data('key');
        pill.remove();
        $(`#col_chk_${key}`).prop('checked', false);
    });

    // Save List Customization Columns
    $('#saveCreditRequestColumnsBtn').on('click', function() {
        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.settings.credit_request.save_columns') }}",
            type: 'POST',
            data: $('#creditRequestListColumnsForm').serialize(),
            success: function(response) {
                if (response.status) {
                    showAlert('success', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'Failed to save Credit Request List customization.');
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    });

});
</script>
@endsection
