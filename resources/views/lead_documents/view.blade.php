@extends('layouts.master')
@section('title', 'Lead Documents - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div id="alert-container"></div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="card-header p-0 m-0"><i class="bx bx-file me-2"></i>Lead Documents</h5>
                @can('lead-documents.create')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                        <i class="bx bx-plus me-1"></i> Upload Document
                    </button>
                @endcan
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="lead-documents-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Lead Info</th>
                            <th>Document Type</th>
                            <th>File Name</th>
                            <th>Uploaded By</th>
                            <th>Uploaded Date</th>
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

    <!-- Add Document Modal -->
    <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="addDocumentForm" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-cloud-upload me-1"></i> Upload Lead Document</h5>
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
                                <label class="form-label">Document Type <span class="text-danger">*</span></label>
                                <select name="document_type" class="form-select" required>
                                    <option value="">-- Select Document Type --</option>
                                    <option value="ID Proof">ID Proof</option>
                                    <option value="Address Proof">Address Proof</option>
                                    <option value="Proposal / Quote">Proposal / Quote</option>
                                    <option value="Contract / Agreement">Contract / Agreement</option>
                                    <option value="Invoice / Receipt">Invoice / Receipt</option>
                                    <option value="Requirement Doc">Requirement Doc</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">File Upload <span class="text-danger">*</span></label>
                                <input type="file" name="document_file" class="form-control" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.rar">
                                <small class="text-muted">Allowed types: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP (Max 10MB)</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addDocumentSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Upload Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Document Modal -->
    <div class="modal fade" id="editDocumentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editDocumentForm" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="lead_documents_id" id="edit_document_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bx bx-edit me-1"></i> Edit Lead Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Lead <span class="text-danger">*</span></label>
                                <select name="lead_id" id="edit_document_lead_id" class="form-select" required>
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
                                <label class="form-label">Document Type <span class="text-danger">*</span></label>
                                <select name="document_type" id="edit_document_type" class="form-select" required>
                                    <option value="">-- Select Document Type --</option>
                                    <option value="ID Proof">ID Proof</option>
                                    <option value="Address Proof">Address Proof</option>
                                    <option value="Proposal / Quote">Proposal / Quote</option>
                                    <option value="Contract / Agreement">Contract / Agreement</option>
                                    <option value="Invoice / Receipt">Invoice / Receipt</option>
                                    <option value="Requirement Doc">Requirement Doc</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Replace File (Optional)</label>
                                <input type="file" name="document_file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.rar">
                                <small class="text-muted">Leave blank to keep existing file (<span id="edit_current_filename" class="fw-semibold"></span>)</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="editDocumentSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Update Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Document Modal -->
    <div class="modal fade" id="deleteDocumentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-trash me-1 text-danger"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this document record?</p>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteDocumentBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
