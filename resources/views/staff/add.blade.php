@extends('layouts.master')
@section('title', 'Add Staff - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y mx-auto" style="max-width: 75%;">
        <div class="row">
            <div class="col-xxl">
                <div class="card mb-6">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Add Staff</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('admin/add_staff') }}" method="POST" autocomplete="off">
                            @csrf
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="name">Name <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}" />
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="mobile_number">Mobile Number <span
                                        class="text-danger"></span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="mobile_number" name="mobile_number"
                                        value="{{ old('mobile_number') }}" />
                                    <span class="text-danger">{{ $errors->first('mobile_number') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="email">Email <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email') }}" autocomplete="off" />
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
                                                {{ old('role_id') == $role->id ? 'selected' : '' }}>
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
                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <span class="text-danger">{{ $errors->first('gender') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="date_of_birth">Date of Birth</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                        value="{{ old('date_of_birth') }}" />
                                    <span class="text-danger">{{ $errors->first('date_of_birth') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="date_of_joining">Date of Joining</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" id="date_of_joining" name="date_of_joining"
                                        value="{{ old('date_of_joining') }}" />
                                    <span class="text-danger">{{ $errors->first('date_of_joining') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="designation">Designation</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="designation" name="designation"
                                        placeholder="e.g. Sales Executive, Manager"
                                        value="{{ old('designation') }}" />
                                    <span class="text-danger">{{ $errors->first('designation') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="base_salary">Base Salary (₹)</label>
                                <div class="col-sm-10">
                                    <input type="number" step="0.01" min="0" class="form-control" id="base_salary" name="base_salary"
                                        placeholder="e.g. 10000"
                                        value="{{ old('base_salary', '0') }}" />
                                    <span class="text-danger">{{ $errors->first('base_salary') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="available_leave_count">Available Leave Count</label>
                                <div class="col-sm-10">
                                    <input type="number" step="0.5" min="0" class="form-control" id="available_leave_count" name="available_leave_count"
                                        placeholder="e.g. 1"
                                        value="{{ old('available_leave_count', '1') }}" />
                                    <span class="text-danger">{{ $errors->first('available_leave_count') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="check_in_time">Assigned Check-In Time</label>
                                <div class="col-sm-10">
                                    <input type="time" class="form-control" id="check_in_time" name="check_in_time"
                                        value="{{ old('check_in_time', '09:00') }}" />
                                    <span class="text-danger">{{ $errors->first('check_in_time') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="check_out_time">Assigned Check-Out Time</label>
                                <div class="col-sm-10">
                                    <input type="time" class="form-control" id="check_out_time" name="check_out_time"
                                        value="{{ old('check_out_time', '18:00') }}" />
                                    <span class="text-danger">{{ $errors->first('check_out_time') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="address">
                                    Address <span class="text-danger"></span>
                                </label>

                                <div class="col-sm-10">
                                    <textarea class="form-control" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                    <span class="text-danger">{{ $errors->first('address') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="password">Password <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="password" name="password"
                                        autocomplete="new-password" />
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="password_confirmation">Confirm Password
                                    <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" autocomplete="new-password" />
                                </div>
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-sm-10 text-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
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

