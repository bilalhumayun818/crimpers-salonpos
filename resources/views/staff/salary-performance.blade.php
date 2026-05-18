@extends('layouts.app')
@section('title', $staff->name . ' - Salary & Performance')
@section('content')
<style>
    :root {
        --y1: #F7DF79;
        --y2: #FBEFBC;
        --yd: #c9a800;
        --ydark: #a07800;
        --ybg: #fffdf0;
    }

    .perf-wrap {
        max-width: 1000px;
        margin: 0 auto;
    }

    .pg-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 22px;
        gap: 16px;
        flex-wrap: wrap;
    }

    .back-btn {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        border: 1.5px solid #e4e4e7;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #71717a;
        text-decoration: none;
        transition: .2s;
        flex-shrink: 0;
    }

    .back-btn:hover {
        border-color: var(--y1);
        color: var(--ydark);
        background: var(--ybg);
    }

    .btn-pay {
        padding: 9px 20px;
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: #fff;
        border: none;
        border-radius: 9px;
        font-weight: 800;
        font-size: .85rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: .15s;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);
    }

    .btn-pay:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(22, 163, 74, 0.3);
    }

    /* Summary Stats */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #fff;
        border: 1.5px solid #f0e8a0;
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,.03);
    }

    .stat-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: var(--y2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--ydark);
        margin-bottom: 12px;
    }

    .stat-lbl {
        font-size: .68rem;
        font-weight: 700;
        color: #a1a1aa;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 4px;
    }

    .stat-val {
        font-size: 1.4rem;
        font-weight: 800;
        color: #18181b;
    }

    /* History Panel */
    .panel {
        background: #fff;
        border: 1.5px solid #f0e8a0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
        margin-bottom: 20px;
    }

    .panel-head {
        padding: 16px 20px;
        border-bottom: 1px solid #f4f4f5;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .panel-title {
        font-size: .9rem;
        font-weight: 800;
        color: #18181b;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .panel-icon {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        background: var(--y2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--ydark);
    }

    .panel-body {
        padding: 0;
    }

    /* Table */
    .history-table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table th {
        text-align: left;
        padding: 12px 20px;
        font-size: .7rem;
        font-weight: 700;
        color: #71717a;
        background: #fafafa;
        text-transform: uppercase;
        letter-spacing: .05em;
        border-bottom: 1px solid #f4f4f5;
    }

    .history-table td {
        padding: 14px 20px;
        font-size: .85rem;
        color: #27272a;
        border-bottom: 1px solid #f4f4f5;
    }

    .history-table tr:last-child td {
        border-bottom: none;
    }

    .history-table tr:hover td {
        background: #fafafa;
    }

    .client-badge {
        font-weight: 700;
        color: #18181b;
    }

    .invoice-link {
        font-weight: 700;
        color: var(--ydark);
        text-decoration: none;
    }

    .invoice-link:hover {
        text-decoration: underline;
    }

    .items-list {
        font-size: .78rem;
        color: #71717a;
    }

    .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #a1a1aa;
    }

    /* Confirm Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .45);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999;
        padding: 20px;
    }

    .modal-box {
        background: #fff;
        border-radius: 18px;
        width: 100%;
        max-width: 380px;
        padding: 26px;
        text-align: center;
        box-shadow: 0 20px 50px rgba(0, 0, 0, .2);
    }

    .modal-icon {
        width: 50px;
        height: 50px;
        background: #f0fdf4;
        color: #16a34a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
    }

    .modal-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #18181b;
        margin-bottom: 6px;
    }

    .modal-text {
        font-size: .82rem;
        color: #71717a;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .modal-footer {
        display: flex;
        gap: 10px;
    }

    .btn-m {
        flex: 1;
        padding: 10px;
        border-radius: 9px;
        font-size: .85rem;
        font-weight: 700;
        cursor: pointer;
        transition: .2s;
        border: none;
    }

    .btn-m-cancel {
        background: #f4f4f5;
        color: #52525b;
    }

    .btn-m-confirm {
        background: #16a34a;
        color: #fff;
    }
</style>

<div class="perf-wrap">
    
    {{-- Header --}}
    <div class="pg-header">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('staff.salary-dashboard') }}" class="back-btn">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <div>
                <div style="font-size:1.25rem;font-weight:800;color:#18181b;">{{ $staff->name }}</div>
                <div style="font-size:.78rem;color:#71717a;">Salary & Customer Served History</div>
            </div>
        </div>
        <div>
            @php
                $btnBg = $staff->days_since_last_payment >= 30 
                    ? 'linear-gradient(135deg, #16a34a, #15803d)' 
                    : 'linear-gradient(135deg, #94a3b8, #64748b)';
                $btnShadow = $staff->days_since_last_payment >= 30 
                    ? 'rgba(22, 163, 74, 0.2)' 
                    : 'rgba(100, 116, 139, 0.2)';
            @endphp
            <button type="button" class="btn-pay" style="background: {{ $btnBg }}; box-shadow: 0 4px 12px {{ $btnShadow }};" onclick="showPayModal()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Mark as Paid
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
            <div class="stat-lbl">Base Salary</div>
            <div class="stat-val">PKR {{ number_format($staff->base_salary, 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div class="stat-lbl">Earned Commission</div>
            <div class="stat-val" style="color: var(--ydark);">PKR {{ number_format($staff->total_earned_commission, 2) }}</div>
        </div>
        <div class="stat-card" style="background: var(--ybg);">
            <div class="stat-icon" style="background: var(--y1); color: var(--ydark);"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 8h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            <div class="stat-lbl">Total Outstanding</div>
            <div class="stat-val" style="color: #16a34a;">PKR {{ number_format($staff->base_salary + $staff->total_earned_commission, 2) }}</div>
        </div>
        <div class="stat-card" style="background: #f0f9ff; border-color: #bae6fd;">
            <div class="stat-icon" style="background: #bae6fd; color: #0369a1;"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div class="stat-lbl" style="color: #0369a1;">Cycle Duration</div>
            <div class="stat-val" style="color: #0369a1;">{{ $staff->days_since_last_payment }} Days</div>
        </div>
    </div>

    {{-- Customer Served History --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title">
                <div class="panel-icon"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
                Customer Served History
            </div>
        </div>
        <div class="panel-body">
            @if($invoices->count() > 0)
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Services / Products Performed</th>
                            <th>Total Invoice Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $inv)
                            <tr>
                                <td style="color:#71717a;">{{ $inv->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('invoices.show', $inv) }}" class="invoice-link" target="_blank">
                                        {{ $inv->invoice_no }}
                                    </a>
                                </td>
                                <td class="client-badge">{{ $inv->customer_name }}</td>
                                <td class="items-list">
                                    {{ implode(', ', $inv->items->pluck('custom_name')->toArray()) }}
                                </td>
                                <td style="font-weight:700;">PKR {{ number_format($inv->payable_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="padding: 16px 20px;">
                    {{ $invoices->links() }}
                </div>
            @else
                <div class="empty-state">
                    <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:10px; color:#d1d5db;"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>No customer service records linked yet.</div>
                    <div style="font-size:.75rem; color:#d1d5db; margin-top:2px;">Only new invoices generated after the system update will appear here.</div>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Pay Salary Confirmation Modal --}}
<div class="modal-overlay" id="payModal">
    <div class="modal-box">
        @if($staff->days_since_last_payment < 30)
            <div class="modal-icon" style="background: #fef2f2; color: #ef4444;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="modal-title" style="color: #b91c1c;">Pay Early Salary Warning!</div>
            <div class="modal-text">
                <strong>WARNING:</strong> Only <strong>{{ $staff->days_since_last_payment }} days</strong> have passed since this employee's last salary payment (less than 30 days expected). <br><br>
                Are you sure you want to pay <strong>{{ $staff->name }}</strong> early? This will reset their cycle to 0.
            </div>
            <div class="modal-footer">
                <button class="btn-m btn-m-cancel" onclick="hidePayModal()">Cancel</button>
                <button class="btn-m btn-m-confirm" style="background: #ef4444;" onclick="document.getElementById('pay-form').submit()">Yes, Pay Early</button>
            </div>
        @else
            <div class="modal-icon">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="modal-title">Pay Staff Salary?</div>
            <div class="modal-text">
                Are you sure you want to mark <strong>{{ $staff->name }}</strong>'s salary as paid? <br>
                This will reset their earned commission to <strong>PKR 0.00</strong> and start the calculation fresh for the next cycle.
            </div>
            <div class="modal-footer">
                <button class="btn-m btn-m-cancel" onclick="hidePayModal()">Cancel</button>
                <button class="btn-m btn-m-confirm" onclick="document.getElementById('pay-form').submit()">Yes, Mark Paid</button>
            </div>
        @endif
    </div>
</div>

<form id="pay-form" method="POST" action="{{ route('staff.pay-salary', $staff) }}" style="display:none;">
    @csrf
</form>

<script>
    function showPayModal() { document.getElementById('payModal').style.display = 'flex'; }
    function hidePayModal() { document.getElementById('payModal').style.display = 'none'; }
</script>
@endsection
