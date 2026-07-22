@extends('layouts.app')

@section('page-title', 'Employee-Customer Report')
@section('page-sub', 'Track which employee provided services to which customer.')

@section('content')
<style>
    .report-wrap {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .report-header {
        padding: 24px;
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        background: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filter-form {
        display: flex;
        gap: 12px;
        align-items: flex-end;
    }

    .f-grp {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .f-lbl {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .f-input {
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        transition: 0.2s;
    }

    .f-input:focus {
        border-color: #c9a800;
        box-shadow: 0 0 0 3px rgba(201, 168, 0, 0.1);
    }

    .btn-filter {
        padding: 10px 20px;
        background: #1e293b;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-filter:hover {
        background: #0f172a;
    }

    .emp-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .emp-head {
        padding: 16px 24px;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .emp-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #1e293b;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.9rem;
    }

    .emp-name {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
    }

    .emp-stats {
        margin-left: auto;
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        background: #fff;
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table th {
        text-align: left;
        padding: 12px 24px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #f1f5f9;
    }

    .report-table td {
        padding: 14px 24px;
        font-size: 0.85rem;
        color: #334155;
        border-bottom: 1px solid #f8fafc;
    }

    .report-table tr:hover td {
        background: #fafafa;
    }

    .cust-badge {
        font-weight: 700;
        color: #1e293b;
    }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: #94a3b8;
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e5e7eb;
    }
</style>

<div class="report-wrap">
    <div class="report-header">
        <div>
            <h2 style="margin:0; font-size:1.2rem; font-weight:800; color:#1e293b;">Employee-Customer Link Report</h2>
            <div style="font-size:0.8rem; color:#64748b; margin-top:4px;">Period: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</div>
        </div>
        <form class="filter-form" method="GET">
            <div class="f-grp">
                <label class="f-lbl">From</label>
                <input type="date" name="date_from" class="f-input" value="{{ $dateFrom }}">
            </div>
            <div class="f-grp">
                <label class="f-lbl">To</label>
                <input type="date" name="date_to" class="f-input" value="{{ $dateTo }}">
            </div>
            <button type="submit" class="btn-filter">Generate Report</button>
        </form>
    </div>

    @if(count($employeeRecords) > 0)
        @foreach($employeeRecords as $empName => $records)
            <div class="emp-card">
                <div class="emp-head">
                    <div class="emp-avatar">
                        {{ strtoupper(substr($empName, 0, 1)) }}
                    </div>
                    <div class="emp-name">{{ $empName }}</div>
                    <div class="emp-stats">
                        Total Customers Served: <span style="color:#1e293b; font-weight:800;">{{ count($records) }}</span>
                    </div>
                </div>
                <div style="padding: 0;">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Invoice #</th>
                                <th>Customer Name</th>
                                <th>Services / Items Provided</th>
                                <th style="text-align:right;">Invoice Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $rec)
                                <tr>
                                    <td style="color:#64748b; font-size:0.8rem;">{{ \Carbon\Carbon::parse($rec['date'])->format('M d, Y h:i A') }}</td>
                                    <td style="font-weight:700; color:#64748b;">{{ $rec['invoice_no'] }}</td>
                                    <td class="cust-badge">{{ $rec['customer'] }}</td>
                                    <td>{{ $rec['services'] }}</td>
                                    <td style="text-align:right; font-weight:700; color:#16a34a;">PKR {{ number_format($rec['invoice_total'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:12px; color:#cbd5e1;">
                <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <div style="font-weight:700; font-size:1.1rem; color:#64748b;">No Employee-Customer Records Found</div>
            <div style="font-size:0.85rem; margin-top:4px;">Try adjusting the date filters above.</div>
        </div>
    @endif
</div>
@endsection
