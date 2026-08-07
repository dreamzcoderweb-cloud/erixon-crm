@extends('layouts.master')
@section('title', 'Staff - Super Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session('success') || session('danger'))
            <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show mb-5"
                role="alert">
                <strong>{{ session('success') ? session('success') : session('danger') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3">
                <h5 class="card-header mb-0">Staff</h5>
                <div class="ms-auto">
                    <button class="btn btn-primary" onclick="location.href='{{ url('admin/add_staff') }}'">
                        + Add Staff
                    </button>
                </div>
            </div>
            <div class="table-responsive text-nowrap p-3">
                <table id="banners-table" class="table">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($staffs as $staff)
                            <tr>
                                <td>{{ $staff->name }}</td>
                                <td>{{ $staff->email }}</td>
                                <td>{{ $staff->roles->first()?->name ?? '-' }}</td>
                                <td>{{ $staff->created_at?->format('d-m-Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <a class="btn btn-outline-primary btn-edit"
                                            href="{{ url('admin/edit_staff/' . $staff->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-danger btn-delete" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal" data-id="{{ $staff->id }}"
                                            data-name="admin/delete_staff">
                                            <i class="bx bx-trash me-1"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

