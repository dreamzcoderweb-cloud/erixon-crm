<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Call Report</title>
    <style>
        @page {
            margin: 12mm 10mm 15mm 10mm;
            size: a4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-title {
            font-size: 14px;
            font-weight: 600;
            color: #2563eb;
            margin-top: 2px;
        }
        .meta-text {
            font-size: 10px;
            color: #64748b;
            text-align: right;
        }
        .meta-label {
            font-weight: bold;
            color: #334155;
        }

        /* KPI Cards */
        .kpi-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: separate;
            border-spacing: 6px 0;
        }
        .kpi-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px;
            text-align: center;
        }
        .kpi-title {
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }
        .kpi-value {
            font-size: 16px;
            font-weight: bold;
            margin-top: 2px;
        }
        .kpi-primary { color: #2563eb; border-left: 3px solid #2563eb; }
        .kpi-success { color: #16a34a; border-left: 3px solid #16a34a; }
        .kpi-warning { color: #d97706; border-left: 3px solid #d97706; }
        .kpi-danger  { color: #dc2626; border-left: 3px solid #dc2626; }
        .kpi-secondary { color: #475569; border-left: 3px solid #475569; }

        /* Main Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-align: left;
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            font-size: 9.5px;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-warning { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-danger  { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .badge-secondary { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .badge-info    { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

        .footer {
            position: fixed;
            bottom: -8mm;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            display: flex;
            justify-content: space-between;
        }
        .text-muted { color: #64748b; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="company-title">{{ config('app.name', 'ERIXON CRM') }}</div>
                <div class="report-title">Call Log Report</div>
                <div style="font-size: 9.5px; color: #475569; margin-top: 3px;">
                    <span class="meta-label">Period:</span> {{ $filters['date_range_text'] }}
                    @if(!empty($filters['status']) && strtolower($filters['status']) !== 'all')
                        | <span class="meta-label">Status:</span> {{ $filters['status'] }}
                    @endif
                    @if(!empty($filters['staff_name']))
                        | <span class="meta-label">Staff:</span> {{ $filters['staff_name'] }}
                    @endif
                    @if(!empty($filters['call_type']) && strtolower($filters['call_type']) !== 'all')
                        | <span class="meta-label">Type:</span> {{ $filters['call_type'] }}
                    @endif
                </div>
            </td>
            <td style="width: 40%; vertical-align: top; text-align: right;">
                <div class="meta-text">
                    <div><span class="meta-label">Generated:</span> {{ $generated_at }}</div>
                    <div><span class="meta-label">Generated By:</span> {{ $generated_by }}</div>
                    <div><span class="meta-label">Filter Mode:</span> {{ $filters['filter_mode'] }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Summary KPI Cards -->
    <table class="kpi-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 20%;">
                <div class="kpi-box kpi-primary">
                    <div class="kpi-title">Total Calls</div>
                    <div class="kpi-value">{{ $summary['total_calls'] }}</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-box kpi-success">
                    <div class="kpi-title">Answered</div>
                    <div class="kpi-value">{{ $summary['answered_calls'] }}</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-box kpi-warning">
                    <div class="kpi-title">Busy</div>
                    <div class="kpi-value">{{ $summary['busy_calls'] }}</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-box kpi-danger">
                    <div class="kpi-title">No Answer</div>
                    <div class="kpi-value">{{ $summary['no_answer_calls'] }}</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-box kpi-secondary">
                    <div class="kpi-title">Total Duration</div>
                    <div class="kpi-value">{{ $summary['total_duration'] }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Call Log Data Table -->
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">#</th>
                <th style="width: 14%;">Date & Time</th>
                <th style="width: 18%;">Customer / Phone</th>
                <th style="width: 16%;">Lead Info</th>
                <th style="width: 8%;">Type</th>
                <th style="width: 8%;">Duration</th>
                <th style="width: 10%; text-align: center;">Status</th>
                <th style="width: 10%;">Staff</th>
                <th style="width: 12%;">Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($calls as $index => $c)
                <tr>
                    <td style="text-align: center; font-weight: bold; color: #64748b;">{{ $index + 1 }}</td>
                    <td>
                        <div class="fw-bold">{{ $c['call_date'] }}</div>
                        <div class="text-muted" style="font-size: 9px;">{{ $c['call_time'] }}</div>
                    </td>
                    <td>
                        <div class="fw-bold">{{ $c['customer_name'] }}</div>
                        <div style="font-size: 9px; color: #0284c7;">{{ $c['phone_number'] }}</div>
                        @if(!empty($c['customer_id']))
                            <div class="text-muted" style="font-size: 8.5px;">{{ $c['customer_id'] }}</div>
                        @endif
                    </td>
                    <td>
                        @if(!empty($c['lead_title']))
                            <div class="fw-bold">{{ $c['lead_title'] }}</div>
                            @if(!empty($c['lead_id']))
                                <div class="text-muted" style="font-size: 8.5px;">Lead #{{ $c['lead_id'] }}</div>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ strtolower($c['call_type']) === 'inbound' ? 'badge-info' : 'badge-secondary' }}">
                            {{ $c['call_type'] }}
                        </span>
                    </td>
                    <td>
                        <span class="fw-bold">{{ $c['duration'] }}</span>
                    </td>
                    <td style="text-align: center;">
                        @php
                            $st = strtolower($c['status']);
                            $badgeClass = 'badge-secondary';
                            if (in_array($st, ['answered', 'completed'])) {
                                $badgeClass = 'badge-success';
                            } elseif (in_array($st, ['busy', 'line busy'])) {
                                $badgeClass = 'badge-warning';
                            } elseif (in_array($st, ['no answer', 'missed', 'rejected', 'declined', 'failed'])) {
                                $badgeClass = 'badge-danger';
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $c['status'] }}</span>
                    </td>
                    <td>
                        <div>{{ $c['staff_name'] }}</div>
                    </td>
                    <td>
                        <div style="font-size: 9px; word-break: break-word;">
                            {{ $c['notes'] ?: '—' }}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 25px; color: #64748b; font-size: 11px;">
                        No call records found for the selected criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left; color: #94a3b8;">
                    {{ config('app.name', 'ERIXON CRM') }} &copy; {{ date('Y') }} — Confidential Internal Report
                </td>
                <td style="text-align: right; color: #94a3b8;">
                    Total Records: {{ count($calls) }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
