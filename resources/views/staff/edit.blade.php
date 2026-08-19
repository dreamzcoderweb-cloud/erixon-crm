@extends('layouts.master')
@section('title', 'Edit Staff - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y mx-auto" style="max-width: 75%;">
        <div class="row">
            <div class="col-xxl">
                <div class="card mb-6">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Edit Staff</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('admin/edit_staff/' . $staff->id) }}" method="POST" autocomplete="off">
                            @csrf
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="name">Name <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $staff->name) }}" />
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="mobile_number">Mobile Number <span
                                        class="text-danger"></span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="mobile_number" name="mobile_number"
                                        value="{{ old('mobile_number', $staff->mobile_number) }}" />
                                    <span class="text-danger">{{ $errors->first('mobile_number') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="email">Email <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', $staff->email) }}" autocomplete="off" />
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="role_id">Role <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select" id="role_id" name="role_id">
                                        <option value="" selected>Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ (string) old('role_id', $selectedRoleId) === (string) $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{ $errors->first('role_id') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="gender">Gender</label>
                                <div class="col-sm-10">
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{ old('gender', $staff->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender', $staff->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender', $staff->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <span class="text-danger">{{ $errors->first('gender') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="date_of_birth">Date of Birth</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                        value="{{ old('date_of_birth', $staff->date_of_birth?->format('Y-m-d')) }}" />
                                    <span class="text-danger">{{ $errors->first('date_of_birth') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="date_of_joining">Date of Joining</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" id="date_of_joining" name="date_of_joining"
                                        value="{{ old('date_of_joining', $staff->date_of_joining?->format('Y-m-d')) }}" />
                                    <span class="text-danger">{{ $errors->first('date_of_joining') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="designation">Designation</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="designation" name="designation"
                                        placeholder="e.g. Sales Executive, Manager"
                                        value="{{ old('designation', $staff->designation) }}" />
                                    <span class="text-danger">{{ $errors->first('designation') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="base_salary">Base Salary (₹)</label>
                                <div class="col-sm-10">
                                    <input type="number" step="0.01" min="0" class="form-control" id="base_salary" name="base_salary"
                                        placeholder="e.g. 10000"
                                        value="{{ old('base_salary', $staff->base_salary) }}" />
                                    <span class="text-danger">{{ $errors->first('base_salary') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="available_leave_count">Available Leave Count</label>
                                <div class="col-sm-10">
                                    <input type="number" step="0.5" min="0" class="form-control" id="available_leave_count" name="available_leave_count"
                                        placeholder="e.g. 1"
                                        value="{{ old('available_leave_count', $staff->available_leave_count) }}" />
                                    <span class="text-danger">{{ $errors->first('available_leave_count') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="check_in_time">Assigned Check-In Time</label>
                                <div class="col-sm-10">
                                    <input type="time" class="form-control" id="check_in_time" name="check_in_time"
                                        value="{{ old('check_in_time', $staff->check_in_time ? \Carbon\Carbon::parse($staff->check_in_time)->format('H:i') : '') }}" />
                                    <span class="text-danger">{{ $errors->first('check_in_time') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="check_out_time">Assigned Check-Out Time</label>
                                <div class="col-sm-10">
                                    <input type="time" class="form-control" id="check_out_time" name="check_out_time"
                                        value="{{ old('check_out_time', $staff->check_out_time ? \Carbon\Carbon::parse($staff->check_out_time)->format('H:i') : '') }}" />
                                    <span class="text-danger">{{ $errors->first('check_out_time') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="address">
                                    Address <span class="text-danger"></span>
                                </label>

                                <div class="col-sm-10">
                                    <textarea
                                        class="form-control"
                                        id="address"
                                        name="address"
                                        rows="3">{{ old('address', $staff->address) }}</textarea>

                                    <span class="text-danger">{{ $errors->first('address') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="password">New Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="password" name="password" autocomplete="new-password" />
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                    <div class="pt-1"><strong>Note:</strong> Leave blank to keep current password</div>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="password_confirmation">Confirm Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" autocomplete="new-password" />
                                </div>
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-sm-10 text-end">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ url('admin/staff') }}" class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

