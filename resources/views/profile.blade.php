@extends('layouts.master')

@section('title', 'My Profile')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Profile Details Card -->
        <div class="card mb-4">
            <h5 class="card-header border-bottom"><i class="bx bx-user me-2"></i>Profile Information</h5>
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Profile Image Upload & Preview -->
                    <div class="d-flex align-items-start align-items-sm-center gap-4 mb-4 pb-2 border-bottom">
                        <div class="position-relative">
                            <img src="{{ $user?->profile_image_url }}" alt="user-avatar"
                                class="d-block rounded-circle p-1 border" height="100" width="100"
                                id="uploadedAvatar" style="object-fit: cover;" />
                        </div>
                        <div class="button-wrapper">
                            <label for="profile_image" class="btn btn-primary me-2 mb-2" tabindex="0">
                                <span class="d-none d-sm-inline-block"><i class="bx bx-upload me-1"></i> Upload New Photo</span>
                                <i class="bx bx-upload d-sm-none"></i>
                                <input type="file" id="profile_image" name="profile_image" class="account-file-input" hidden
                                    accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewImage(this);" />
                            </label>
                            <p class="text-muted mb-0 small">Allowed JPG, JPEG, PNG or WEBP. Max size 2MB.</p>
                            @error('profile_image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Name Field -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                value="{{ old('name', $user?->name) }}" placeholder="Enter name" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                value="{{ old('email', $user?->email) }}" placeholder="Enter email address" required />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-save me-1"></i> Save Profile Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @can('profile.password')
            <!-- Change Password Card -->
            <div class="card">
                <h5 class="card-header border-bottom"><i class="bx bx-lock-alt me-2"></i>Change Password</h5>
                <div class="card-body pt-4">
                    <form method="POST" action="{{ route('admin.profile.password') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                                <input type="password" name="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror" placeholder="••••••••" required />
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required />
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required />
                            </div>
                        </div>

                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-key me-1"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('uploadedAvatar').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
