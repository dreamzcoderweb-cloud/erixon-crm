<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Proposal - {{ $proposal->proposal_number }}</title>
    <style>
        @page {
            margin: 15mm 12mm 15mm 12mm;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #2d3748;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #696cff;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #1e293b;
            letter-spacing: 0.5px;
        }
        .company-sub {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .proposal-badge {
            background-color: #696cff;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 3px;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 4px;
        }
        .proposal-no {
            font-size: 16px;
            font-weight: bold;
            color: #696cff;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: separate;
            border-spacing: 10px 0;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 10px 12px;
            vertical-align: top;
            width: 50%;
        }
        .card-label {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .card-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .card-row {
            font-size: 10px;
            color: #475569;
            margin-top: 2px;
        }
        .type-badge {
            background-color: #e0e7ff;
            color: #4338ca;
            font-size: 9px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            text-transform: uppercase;
            margin-left: 4px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 7px 8px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .items-table td {
            padding: 7px 8px;
            font-size: 10px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .totals-table td {
            padding: 5px 8px;
            font-size: 10px;
            border: none;
        }
        .grand-total-row td {
            background-color: #eef2ff !important;
            font-size: 12px !important;
            font-weight: bold !important;
            color: #4338ca !important;
            border-top: 2px solid #696cff !important;
            border-bottom: 2px solid #696cff !important;
            padding: 8px !important;
        }
        .notes-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 10px 12px;
            margin-bottom: 20px;
        }
        .notes-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .notes-content {
            font-size: 10px;
            color: #475569;
            white-space: pre-wrap;
        }
        .signatures-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signature-line {
            width: 180px;
            border-bottom: 1px dashed #94a3b8;
            height: 35px;
            margin-bottom: 4px;
        }
        .footer-text {
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            margin-top: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="company-name">{{ company_name() ?: 'ERIXON CRM' }}</div>
                <div class="company-sub">Professional Solutions & Business Proposals</div>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <div class="proposal-badge">Official Proposal</div>
                <div class="proposal-no">{{ $proposal->proposal_number }}</div>
                <div style="font-size: 10px; color: #64748b; margin-top: 2px;">
                    Date: <strong>{{ $proposal->created_at ? $proposal->created_at->format('d M, Y') : date('d M, Y') }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- Metadata Grid (Customer & Sales Manager) -->
    <table class="info-table" style="margin-left: -10px; margin-right: -10px;">
        <tr>
            <!-- Customer Information -->
            <td class="info-card">
                <div class="card-label">Proposal Prepared For:</div>
                <div class="card-title">
                    {{ $proposal->customer_name }}
                    @if(!empty($proposal->customer_type))
                        <span class="type-badge">{{ ucfirst($proposal->customer_type) }}</span>
                    @endif
                </div>
                <div class="card-row"><strong>Mobile:</strong> {{ $proposal->customer_mobile ?: '-' }}</div>
                @if(!empty($proposal->customer_email))
                    <div class="card-row"><strong>Email:</strong> {{ $proposal->customer_email }}</div>
                @endif
                @if(!empty($proposal->lead_requirement_name))
                    <div class="card-row" style="margin-top: 4px;">
                        <strong>Requirement:</strong> <span style="color: #696cff; font-weight: bold;">{{ $proposal->lead_requirement_name }}</span>
                    </div>
                @endif
            </td>

            <!-- Sales Manager Information -->
            <td class="info-card">
                <div class="card-label">Sales Representative:</div>
                <div class="card-title">{{ $proposal->sales_manager_name ?: 'Sales Team' }}</div>
                <div class="card-row"><strong>Contact:</strong> {{ $proposal->sales_manager_mobile ?: 'N/A' }}</div>
                <div class="card-row"><strong>Prepared By:</strong> {{ $proposal->creator?->name ?: 'Staff' }}</div>
                <div class="card-row"><strong>Status:</strong> <span style="font-weight: bold; color: #16a34a;">{{ $proposal->status ?: 'Active' }}</span></div>
            </td>
        </tr>
    </table>

    <!-- Product Packages Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">#</th>
                <th>Product Package / Description</th>
                <th style="width: 100px;" class="text-end">Price (₹)</th>
                <th style="width: 60px;" class="text-center">Tax (%)</th>
                <th style="width: 90px;" class="text-end">Tax Amt (₹)</th>
                <th style="width: 105px;" class="text-end">Total (₹)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proposal->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $item->product_package }}</strong></td>
                    <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                    <td class="text-center">{{ number_format($item->tax_percentage, 0) }}%</td>
                    <td class="text-end">₹{{ number_format($item->tax_amount, 2) }}</td>
                    <td class="text-end fw-bold">₹{{ number_format($item->amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 12px; color: #94a3b8;">No product packages specified.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="border: none;"></td>
                <td class="text-end fw-bold" style="background-color: #f8fafc;">Subtotal:</td>
                <td class="text-end fw-bold" style="background-color: #f8fafc;">₹{{ number_format($proposal->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" style="border: none;"></td>
                <td class="text-end fw-bold" style="background-color: #f8fafc;">Total Tax:</td>
                <td class="text-end fw-bold" style="background-color: #f8fafc;">₹{{ number_format($proposal->total_tax, 2) }}</td>
            </tr>
            <tr class="grand-total-row">
                <td colspan="4" style="border: none; background: transparent !important;"></td>
                <td class="text-end">Grand Total:</td>
                <td class="text-end">₹{{ number_format($proposal->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Notes & Terms -->
    @if(!empty($proposal->notes))
        <div class="notes-box">
            <div class="notes-title">Notes / Terms & Conditions:</div>
            <div class="notes-content">{{ $proposal->notes }}</div>
        </div>
    @endif

    <!-- Signatures -->
    <table class="signatures-table">
        <tr>
            <td style="width: 50%;">
                <div class="signature-line"></div>
                <div style="font-size: 10px; font-weight: bold; color: #334155;">Customer Acceptance Signature</div>
                <div style="font-size: 9px; color: #64748b;">Authorized Signatory</div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="signature-line" style="margin-left: auto;"></div>
                <div style="font-size: 10px; font-weight: bold; color: #334155;">Sales Manager Signature</div>
                <div style="font-size: 9px; color: #64748b;">{{ $proposal->sales_manager_name ?: 'Authorized Representative' }}</div>
            </td>
        </tr>
    </table>

    <div class="footer-text">
        Thank you for your business! This is a computer-generated proposal document.
    </div>

</body>
</html>
