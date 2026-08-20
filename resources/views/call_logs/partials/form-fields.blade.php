@php
    $fieldPrefix = $prefix ?? 'add';
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Lead</label>
        <select name="lead_id" id="{{ $fieldPrefix }}_call_log_lead_id" class="form-select">
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
        <label class="form-label">Staff</label>
        <select name="user_id" id="{{ $fieldPrefix }}_call_log_user_id" class="form-select">
            <option value="">-- Select Staff --</option>
            @foreach ($staffs as $staff)
                <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
            @endforeach
        </select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone <span class="text-danger">*</span></label>
        <input type="text" name="phone" id="{{ $fieldPrefix }}_call_log_phone" class="form-control" required>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Call Date</label>
        <input type="datetime-local" name="created_at" id="{{ $fieldPrefix }}_call_log_created_at" class="form-control">
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Call Type <span class="text-danger">*</span></label>
        <select name="call_type" id="{{ $fieldPrefix }}_call_log_call_type" class="form-select" required>
            <option value="">-- Select Type --</option>
            <option value="Inbound">Inbound</option>
            <option value="Outbound">Outbound</option>
            <option value="Missed">Missed</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Call Status <span class="text-danger">*</span></label>
        <select name="call_status" id="{{ $fieldPrefix }}_call_log_call_status" class="form-select" required>
            <option value="">-- Select Status --</option>
            <option value="Completed">Completed</option>
            <option value="Missed">Missed</option>
            <option value="No Answer">No Answer</option>
            <option value="Busy">Busy</option>
            <option value="Failed">Failed</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Duration</label>
        <input type="text" name="duration" id="{{ $fieldPrefix }}_call_log_duration" class="form-control" placeholder="Example: 5 minutes">
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Recording</label>
        <select name="recording_id" id="{{ $fieldPrefix }}_call_log_recording_id" class="form-select">
            <option value="">-- Select Recording --</option>
            @foreach ($recordings as $recording)
                <option value="{{ $recording->call_id }}">
                    #{{ $recording->call_id }} - {{ $recording->lead->lead_title ?? 'Lead N/A' }} - {{ $recording->duration ?? 'No duration' }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback"></div>
    </div>
</div>
