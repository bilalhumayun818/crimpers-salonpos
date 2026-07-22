@extends('layouts.app')

@section('page-title', 'Customer Purchases Report')
@section('page-sub', 'Track customer spending and pending balances.')

@section('content')
<style>
    .report-wrap {
        max-width: 1200px;
        margin: 0 auto;
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .report-header {
        padding: 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fafafa;
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

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table th {
        text-align: left;
        padding: 16px 24px;
        font-size: 0.75rem;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .report-table td {
        padding: 16px 24px;
        font-size: 0.85rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
    }

    .report-table tr:hover td {
        background: #fafafa;
    }

    .cust-name {
        font-weight: 800;
        color: #0f172a;
        font-size: 0.95rem;
    }

    .cust-phone {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 4px;
    }

    .items-list {
        margin: 0;
        padding-left: 16px;
        color: #475569;
        font-size: 0.8rem;
    }

    .amt-billed { font-weight: 800; color: #1e293b; }
    .amt-paid { font-weight: 700; color: #16a34a; }
    .amt-pending { font-weight: 800; color: #ef4444; }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: #94a3b8;
    }
</style>

<div class="report-wrap">
    <div class="report-header">
        <div>
            <h2 style="margin:0; font-size:1.2rem; font-weight:800; color:#1e293b;">Customer Purchases</h2>
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

    @if(count($customers) > 0)
        <table class="report-table">
            <thead>
                <tr>
                    <th>Customer Details</th>
                    <th>Products / Services Bought</th>
                    <th>Total Billed</th>
                    <th>Total Paid</th>
                    <th>Pending (Credit)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $cust)
                    <tr>
                        <td>
                            <div class="cust-name">{{ $cust['name'] }}</div>
                            <div class="cust-phone">{{ $cust['phone'] }}</div>
                        </td>
                        <td>
                            @if(count($cust['items_bought']) > 0)
                                <ul class="items-list">
                                    @foreach($cust['items_bought'] as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span style="color:#94a3b8; font-size:0.8rem;">No items recorded</span>
                            @endif
                        </td>
                        <td class="amt-billed">PKR {{ number_format($cust['total_billed'], 2) }}</td>
                        <td class="amt-paid">PKR {{ number_format($cust['total_paid'], 2) }}</td>
                        <td>
                            @if($cust['total_pending'] > 0)
                                <span class="amt-pending">PKR {{ number_format($cust['total_pending'], 2) }}</span>
                            @else
                                <span style="color:#64748b; font-size:0.8rem;">Cleared</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:12px; color:#cbd5e1;">
                <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <div style="font-weight:700; font-size:1.1rem; color:#64748b;">No Customer Purchases Found</div>
            <div style="font-size:0.85rem; margin-top:4px;">Try adjusting the date filters above.</div>
        </div>
    @endif
</div>
@endsection
