@extends('layouts.master')

@section('title', 'General Settings - Settings')

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
                        <a class="nav-link active" href="{{ route('admin.settings.general') }}">
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
                        <a class="nav-link" href="{{ route('admin.settings.credit_request') }}">
                            <i class="bx bx-credit-card me-1"></i> Credit Request Setting
                        </a>
                    </li>
                @endcan
                @can('demo-process-settings.view')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.settings.demo_process') }}">
                            <i class="bx bx-slideshow me-1"></i> Demo Process Setting
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </div>

    <!-- General Settings Form Card -->
    <div class="card mb-4">
        {{-- <h5 class="card-header border-bottom"> Settings</h5> --}}
        <div class="card-body pt-4">
            <form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!--  Logo & Favicon Previews -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <div class="position-relative">
                                @if(!empty($setting->logo) && file_exists(public_path($setting->logo)))
                                    <img src="{{ asset($setting->logo) }}" alt="company-logo" class="d-block rounded p-2 border" height="90" width="90" id="uploadedLogo" style="object-fit: contain;" />
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-label-primary rounded border" style="width: 90px; height: 90px;">
                                        <i class="bx bx-dumbbell display-6"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="button-wrapper">
                                <h6 class="mb-1 fw-bold">Logo</h6>
                                <p class="text-muted mb-0 small">Allowed JPG, PNG, WEBP or SVG. Max size 2MB.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <div class="position-relative">
                                @if(!empty($setting->favicon) && file_exists(public_path($setting->favicon)))
                                    <img src="{{ asset($setting->favicon) }}" alt="company-favicon" class="d-block rounded p-2 border" height="90" width="90" id="uploadedFavicon" style="object-fit: contain;" />
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-label-secondary rounded border" style="width: 90px; height: 90px;">
                                        <img src="{{ asset('assets/img/fav_icon.png') }}" alt="default-favicon" style="width: 48px; height: 48px; object-fit: contain;" />
                                    </div>
                                @endif
                            </div>
                            <div class="button-wrapper">
                                <h6 class="mb-1 fw-bold">Favicon</h6>
                                <p class="text-muted mb-0 small">Allowed ICO, PNG, JPG, WEBP, SVG. Max size 2MB.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Company Name -->
                    <div class="col-md-12 mb-3">
                        <label for="company_name" class="form-label fw-semibold">Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name', $setting->company_name) }}" placeholder="e.g." required />
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Upload New Logo -->
                    <div class="col-md-6 mb-3">
                        <label for="logo" class="form-label fw-semibold">Upload New Logo</label>
                        <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/png, image/jpeg, image/webp, image/svg+xml" />
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Upload New Favicon -->
                    <div class="col-md-6 mb-3">
                        <label for="favicon" class="form-label fw-semibold">Upload New Favicon</label>
                        <input type="file" class="form-control @error('favicon') is-invalid @enderror" id="favicon" name="favicon" accept="image/png, image/jpeg, image/webp, image/svg+xml, image/x-icon, image/vnd.microsoft.icon, .ico" />
                        @error('favicon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- WhatsApp No -->
                    <div class="col-md-12 mb-3">
                        <label for="whatsapp_no" class="form-label fw-semibold">WhatsApp No</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bxl-whatsapp text-success"></i></span>
                            <input type="text" class="form-control @error('whatsapp_no') is-invalid @enderror" id="whatsapp_no" name="whatsapp_no" value="{{ old('whatsapp_no', $setting->whatsapp_no) }}" placeholder="e.g. 8610747034" />
                        </div>
                        @error('whatsapp_no')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Theme Color -->
                    <div class="col-md-12 mb-4">
                        <label for="theme_color" class="form-label fw-semibold">Theme Color</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="color" class="form-control form-control-color" id="theme_color_picker" value="{{ old('theme_color', $setting->theme_color ?? '#00b2a9') }}" title="Choose your color" style="width: 50px; height: 38px; cursor: pointer;" />
                            <input type="text" class="form-control @error('theme_color') is-invalid @enderror" id="theme_color" name="theme_color" value="{{ old('theme_color', $setting->theme_color ?? '#00b2a9') }}" placeholder="#00b2a9" style="max-width: 150px;" required />
                        </div>
                        <div class="form-text mt-2 text-muted">
                            <span class="fw-semibold">Primary admin color:</span> This color is used for the sidebar, buttons, active menu states, and other admin highlights.
                        </div>
                        @error('theme_color')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @can('general-settings.edit')
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-save me-1"></i> Save Settings
                        </button>
                    </div>
                @endcan
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const picker = document.getElementById('theme_color_picker');
        const textInput = document.getElementById('theme_color');

        if (picker && textInput) {
            picker.addEventListener('input', function () {
                textInput.value = picker.value;
            });
            textInput.addEventListener('input', function () {
                if (/^#[0-9A-F]{6}$/i.test(textInput.value)) {
                    picker.value = textInput.value;
                }
            });
        }
    });
</script>
@endsection
