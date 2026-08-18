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
