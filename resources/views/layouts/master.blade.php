<!doctype html>

<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title', 'Super Admin')</title>

    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ company_favicon() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- DataTables CSS & JS -->
    <script defer src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script defer src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <script defer src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"></script>
    <script defer src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.bootstrap5.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script defer src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>
    <script defer src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"></script>
    <script defer src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.colVis.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.bootstrap5.css">

    <script>
        var APP_URL = {!! json_encode(url('/')) !!}
    </script>

    @php
        $themeColor = theme_color();
    @endphp
    <style>
        /* Global Dynamic Theme Color Styles */
        :root {
            --bs-primary: {{ $themeColor }};
            --theme-color: {{ $themeColor }};
        }

        /* Sidebar Background & Text */
        .bg-menu-theme {
            background-color: {{ $themeColor }} !important;
            color: #ffffff !important;
        }
        .bg-menu-theme .menu-link,
        .bg-menu-theme .menu-header,
        .bg-menu-theme .menu-icon,
        .bg-menu-theme .app-brand-text {
            color: rgba(255, 255, 255, 0.9) !important;
        }
        .bg-menu-theme .menu-item.active > .menu-link,
        .bg-menu-theme .menu-sub .menu-item.active > .menu-link {
            background-color: rgba(255, 255, 255, 0.22) !important;
            color: #ffffff !important;
            font-weight: 600 !important;
        }
        .bg-menu-theme .menu-item:not(.active) .menu-link:hover {
            background-color: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
        }
        .bg-menu-theme .menu-sub .menu-link:before {
            background-color: rgba(255, 255, 255, 0.6) !important;
        }

        /* Primary Buttons & Elements */
        .btn-primary {
            background-color: {{ $themeColor }} !important;
            border-color: {{ $themeColor }} !important;
            color: #ffffff !important;
            box-shadow: 0 0.125rem 0.25rem 0 {{ $themeColor }}55 !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: {{ $themeColor }} !important;
            border-color: {{ $themeColor }} !important;
            color: #ffffff !important;
            opacity: 0.9;
        }
        .btn-outline-primary {
            color: {{ $themeColor }} !important;
            border-color: {{ $themeColor }} !important;
        }
        .btn-outline-primary:hover {
            background-color: {{ $themeColor }} !important;
            color: #ffffff !important;
        }

        /* Nav Pills Active State */
        .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
            background-color: {{ $themeColor }} !important;
            color: #ffffff !important;
            box-shadow: 0 2px 4px 0 {{ $themeColor }}40 !important;
        }

        /* Form Controls Active & Highlights */
        .form-check-input:checked {
            background-color: {{ $themeColor }} !important;
            border-color: {{ $themeColor }} !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: {{ $themeColor }} !important;
            box-shadow: 0 0 0 0.25rem {{ $themeColor }}25 !important;
        }

        /* Text & Badges */
        .text-primary {
            color: {{ $themeColor }} !important;
        }
        .bg-primary {
            background-color: {{ $themeColor }} !important;
        }
        .bg-label-primary {
            background-color: {{ $themeColor }}18 !important;
            color: {{ $themeColor }} !important;
        }
        .page-item.active .page-link {
            background-color: {{ $themeColor }} !important;
            border-color: {{ $themeColor }} !important;
        }

        /* Form Validation Error Styling */
        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.85em;
            color: #ff3e1d;
            font-weight: 500;
        }
        .is-invalid ~ .invalid-feedback {
            display: block !important;
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('layouts.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('layouts.header')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    @yield('content')
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('layouts.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete ?</p>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Follow-up Reminder Popup Modal for Staff -->
    <div class="modal fade" id="todayFollowupReminderModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white d-flex align-items-center">
                        <i class="bx bx-bell bx-tada me-2 fs-4"></i> Today's Follow-up Reminders
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info d-flex align-items-center mb-3">
                        <i class="bx bx-info-circle me-2 fs-4"></i>
                        <div>You have scheduled client follow-ups for today. Please review and process them.</div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th>Client Name</th>
                                    <th>Contact No</th>
                                    <th>Date & Time</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="todayFollowupReminderTbody">
                                <!-- Loaded dynamically via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <a href="{{ url('admin/followups') }}" class="btn btn-primary me-2">
                        <i class="bx bx-list-ul me-1"></i> Go to All Follow-ups
                    </a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/custom-js/customer.js') }}"></script>
    <script src="{{ asset('assets/js/custom-js/lead_source.js') }}"></script>
    <script src="{{ asset('assets/js/custom-js/lead_stage.js') }}"></script>
    <script src="{{ asset('assets/js/custom-js/lead_requirement.js') }}"></script>
    <script src="{{ asset('assets/js/custom-js/lost_reason.js') }}"></script>
    <script src="{{ asset('assets/js/custom-js/followup.js') }}"></script>
    <script src="{{ asset('assets/js/custom-js/lead.js') }}"></script>
    <script src="{{ asset('assets/js/custom-js/role.js') }}"></script>
    <script src="{{ asset('assets/js/custom-js/staff.js') }}"></script>
    <script src="{{ asset('assets/js/custom-js/lead_document.js') }}"></script>
    <script src="{{ asset('assets/js/custom-js/template.js') }}"></script>
    <script src="{{ asset('assets/js/custom-js/call_recording.js') }}"></script>
    <script src="{{ asset('assets/js/custom-js/attendance.js') }}"></script>
    <script src="{{ asset('assets/js/custom-js/attendance_report.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>

    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>
