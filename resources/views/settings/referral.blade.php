@extends('layouts.master')

@section('title', 'Referral Settings - Settings')

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
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Settings Navigation Tabs -->
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
                {{-- @can('referral-settings.view')
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.settings.referral') }}">
                            <i class="bx bx-gift me-1"></i> Referral Settings
                        </a>
                    </li>
                @endcan --}}
            </ul>
        </div>
    </div>

    <!-- Referral Settings Form Card -->
    <div class="card mb-4">
        <h5 class="card-header border-bottom">Referral Points Settings</h5>
        <div class="card-body pt-4">
            <form action="{{ route('admin.settings.referral.update') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <!-- Referral Points -->
                    <div class="col-md-12 mb-3">
                        <label for="referral_points" class="form-label fw-semibold">Referral Points <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-gift text-primary"></i></span>
                            <input type="number" class="form-control @error('referral_points') is-invalid @enderror" id="referral_points" name="referral_points" value="{{ old('referral_points', $setting->referral_points) }}" placeholder="100" min="0" required />
                        </div>
                        <div class="form-text mt-2 text-muted">
                            Points rewarded to members for successful client referrals.
                        </div>
                        @error('referral_points')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @can('referral-settings.edit')
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-save me-1"></i> Save Settings
                        </button>
                    </div>
                @endcan
            </form>
        </div>
    </div>
</div>
@endsection
