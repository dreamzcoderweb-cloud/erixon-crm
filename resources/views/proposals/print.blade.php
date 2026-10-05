<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal - {{ $proposal->proposal_number }}</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <style>
        body {
            background: #fff;
            color: #333;
            font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
        }
        .proposal-container {
            max-width: 850px;
            margin: 20px auto;
            padding: 40px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }
        .invoice-header {
            border-bottom: 2px solid #696cff;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .table-custom thead th {
            background-color: #f5f5f9;
            color: #566a7f;
            font-weight: 600;
            border-bottom: 2px solid #d9dee3;
        }
        .table-custom td, .table-custom th {
            padding: 12px 14px;
        }
        @media print {
            body {
                background: #fff !important;
                margin: 0;
            }
            .proposal-container {
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="proposal-container shadow-sm">
        <!-- Action Toolbar for screen only -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom no-print">
            <a href="{{ route('admin.proposals.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to Proposals
            </a>
            <button onclick="window.print()" class="btn btn-sm btn-primary">
                <i class="bx bx-printer me-1"></i> Print / Save as PDF
            </button>
        </div>

        <!-- Header -->
        <div class="row invoice-header align-items-center">
            <div class="col-sm-7">
                <div class="d-flex align-items-center mb-2">
                    @if(company_logo())
                        <img src="{{ company_logo() }}" alt="Company Logo" height="42" class="me-2" style="object-fit: contain;">
                    @else
                        <i class="bx bx-file text-primary fs-1 me-2"></i>
                    @endif
                    <h3 class="mb-0 fw-bold text-dark">{{ company_name() ?: 'ERIXON CRM' }}</h3>
                </div>
                <div class="text-muted small">
                    Professional Solutions & Business Proposals
                </div>
            </div>
            <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
                <span class="badge bg-label-primary fs-tiny text-uppercase px-3 py-2 mb-2">Official Proposal</span>
                <h4 class="fw-bold mb-0 text-primary">{{ $proposal->proposal_number }}</h4>
                <div class="text-muted small mt-1">Date: <strong>{{ $proposal->created_at->format('d M, Y') }}</strong></div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6">
                <div class="p-3 bg-light rounded border h-100">
                    <span class="text-muted text-uppercase small fw-bold d-block mb-2">Proposal Prepared For:</span>
                    <h5 class="fw-bold text-dark mb-1">{{ $proposal->customer_name }}</h5>
                    <div class="text-muted mb-1"><i class="bx bx-phone me-1"></i> {{ $proposal->customer_mobile }}</div>
                    @if(!empty($proposal->lead_requirement_name))
                        <div class="text-muted small mt-2">
                            <span class="badge bg-label-info"><i class="bx bx-tag me-1"></i>Requirement: {{ $proposal->lead_requirement_name }}</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-sm-6">
                <div class="p-3 bg-light rounded border h-100">
                    <span class="text-muted text-uppercase small fw-bold d-block mb-2">Sales Manager Contact:</span>
                    <h5 class="fw-bold text-dark mb-1">{{ $proposal->sales_manager_name ?: 'Sales Team' }}</h5>
                    <div class="text-muted mb-1"><i class="bx bx-mobile me-1"></i> {{ $proposal->sales_manager_mobile ?: 'N/A' }}</div>
                    <div class="text-muted small"><i class="bx bx-user me-1"></i> Prepared By: {{ $proposal->creator?->name ?: 'Staff' }}</div>
                </div>
            </div>
        </div>

        <!-- Product Packages Table -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-custom mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Product Package</th>
                        <th class="text-end" style="width: 140px;">Paisa (Price ₹)</th>
                        <th class="text-center" style="width: 100px;">Tax (%)</th>
                        <th class="text-end" style="width: 130px;">Tax Amount (₹)</th>
                        <th class="text-end" style="width: 150px;">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proposal->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong class="text-dark">{{ $item->product_package }}</strong>
                            </td>
                            <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                            <td class="text-center">{{ number_format($item->tax_percentage, 0) }}%</td>
                            <td class="text-end text-muted">₹{{ number_format($item->tax_amount, 2) }}</td>
                            <td class="text-end fw-bold text-dark">₹{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No product items specified.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end fw-bold">Subtotal (Paisa):</th>
                        <th class="text-end fw-bold text-dark">₹{{ number_format($proposal->subtotal, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-end fw-bold">Total Tax:</th>
                        <th class="text-end fw-bold text-muted">₹{{ number_format($proposal->total_tax, 2) }}</th>
                    </tr>
                    <tr style="background-color: #f0f2ff;">
                        <th colspan="5" class="text-end fw-bolder fs-5 text-primary">Grand Total Amount:</th>
                        <th class="text-end fw-bolder fs-5 text-primary">₹{{ number_format($proposal->total_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Notes & Terms -->
        @if(!empty($proposal->notes))
            <div class="mb-4 p-3 border rounded bg-light">
                <h6 class="fw-bold text-dark mb-1"><i class="bx bx-notepad me-1 text-primary"></i> Notes / Terms & Conditions:</h6>
                <p class="text-muted small mb-0" style="white-space: pre-wrap;">{{ $proposal->notes }}</p>
            </div>
        @endif

        <!-- Footer / Signatures -->
        <div class="row pt-4 mt-5 border-top align-items-end">
            <div class="col-6">
                <p class="text-muted small mb-1">Customer Acceptance Signature:</p>
                <div style="border-bottom: 1px dashed #aaa; width: 220px; height: 40px;"></div>
                <small class="text-muted mt-1 d-block">Authorized Signatory</small>
            </div>
            <div class="col-6 text-end">
                <p class="text-muted small mb-1">Sales Manager Signature:</p>
                <div class="ms-auto" style="border-bottom: 1px dashed #aaa; width: 220px; height: 40px;"></div>
                <small class="text-muted mt-1 d-block">{{ $proposal->sales_manager_name ?: 'Authorized Representative' }}</small>
            </div>
        </div>

        <div class="text-center text-muted small mt-5 pt-3 border-top">
            Thank you for considering our proposal!
        </div>
    </div>
</body>
</html>
