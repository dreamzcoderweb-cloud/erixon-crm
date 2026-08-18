@extends('layouts.master')
@section('title', 'Templates - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-file-blank me-2"></i>Templates Management</h5>
                @can('templates.create')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTemplateModal">
                        <i class="bx bx-plus me-1"></i> Add Template
                    </button>
                @endcan
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="templates-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Content Preview</th>
                            <th>Status</th>
                            <th>Created Date</th>
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

    <!-- Add Template Modal -->
    <div class="modal fade" id="addTemplateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addTemplateForm" method="POST" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-plus-circle me-1"></i> Add New Template</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Template Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="Email">Email</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                    <option value="SMS">SMS</option>
                                    <option value="Proposal">Proposal</option>
                                    <option value="Contract">Contract</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="Enter template title..." required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Template Content / Body <span class="text-danger">*</span></label>
                                <textarea name="content" class="form-control" rows="6" placeholder="Enter template text, variables or HTML content..." required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addTemplateSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Template Modal -->
    <div class="modal fade" id="editTemplateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editTemplateForm" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="template_id" id="edit_template_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-edit me-1"></i> Edit Template</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Template Type <span class="text-danger">*</span></label>
                                <select name="type" id="edit_template_type" class="form-select" required>
                                    <option value="Email">Email</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                    <option value="SMS">SMS</option>
                                    <option value="Proposal">Proposal</option>
                                    <option value="Contract">Contract</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="edit_template_status" class="form-select" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="edit_template_title" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Template Content / Body <span class="text-danger">*</span></label>
                                <textarea name="content" id="edit_template_content" class="form-control" rows="6" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editTemplateSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Template Modal -->
    <div class="modal fade" id="deleteTemplateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-trash me-1 text-danger"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this template?</p>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteTemplateBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
