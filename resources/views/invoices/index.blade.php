@extends('layouts.app')
@php
    $tab = request('tab', 'sales');
    $title = match($tab) {
        'purchases' => 'Purchase History',
        'reconciliation' => 'Reconciliation History',
        'expenses' => 'Expense History',
        default => 'Sales History'
    };
@endphp
@section('title', $title)

@section('content')
<style>
/* ── Tabs ── */
.history-tabs{display:flex;gap:12px;margin-bottom:24px;border-bottom:1px solid #e2e8f0;padding-bottom:12px;}
.tab-btn{padding:10px 20px;border-radius:12px;font-size:.85rem;font-weight:700;text-decoration:none;color:#64748b;background:#f8fafc;border:1.5px solid #e2e8f0;transition:.2s;display:flex;align-items:center;gap:8px;}
.tab-btn.active{background:#F5EFC0;color:#7A5C00;border-color:#D4B800;box-shadow:0 2px 8px rgba(212,184,0,.15);}

/* ── Header ── */
.inv-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;gap:16px;flex-wrap:wrap;}
.inv-title{font-size:1.45rem;font-weight:800;color:#0f172a;letter-spacing:-.02em;margin-bottom:4px;}
.inv-sub{font-size:.85rem;color:#64748b;}
.header-actions{display:flex;gap:8px;flex-wrap:wrap;}
.btn-export{padding:8px 14px;border-radius:10px;font-size:.8rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:.2s;font-family:'Outfit',sans-serif;cursor:pointer;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;}
.btn-export:hover{border-color:#F7DF79;color:#c9a800;background:#fffdf0;}
.btn-export.pdf{border-color:#fca5a5;color:#dc2626;background:#fef2f2;}
.btn-export.pdf:hover{background:#fee2e2;}

/* ── Stats ── */
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;}
.stat-card{background:#fff;border:1px solid #f0e8a0;border-radius:14px;padding:16px 18px;box-shadow:0 1px 4px rgba(0,0,0,.04);display:flex;align-items:center;gap:12px;}
.stat-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.stat-val{font-size:1.4rem;font-weight:800;color:#0f172a;line-height:1;margin-bottom:3px;}
.stat-label{font-size:.72rem;color:#64748b;font-weight:500;}

/* ── Filter panel ── */
.filter-panel{background:#fff;border:1px solid #f0e8a0;border-radius:14px;padding:18px 20px;margin-bottom:18px;box-shadow:0 1px 4px rgba(0,0,0,.04);}
.filter-title{font-size:.78rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.09em;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;}
.filter-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:14px;}
.filter-grid-2{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;}
.f-label{display:block;font-size:.75rem;font-weight:600;color:#374151;margin-bottom:5px;}
.f-input{width:100%;padding:8px 11px;border:1.5px solid #e5e7eb;border-radius:9px;font-size:.82rem;font-family:'Outfit',sans-serif;color:#1e293b;background:#fafafa;outline:none;transition:.2s;}
.f-input:focus{border-color:#F7DF79;background:#fff;box-shadow:0 0 0 3px rgba(247,223,121,.1);}
.filter-footer{display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid #f1f5f9;}
.btn-apply{padding:8px 18px;border:none;background:linear-gradient(135deg,#F7DF79,#c9a800);border-radius:9px;color:#18181b;font-size:.82rem;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;transition:.2s;box-shadow:0 2px 8px rgba(247,223,121,.2);}
.btn-apply:hover{transform:translateY(-1px);}
.btn-clear{font-size:.8rem;color:#94a3b8;text-decoration:none;font-weight:500;transition:.15s;}
.btn-clear:hover{color:#ef4444;}

/* ── Table ── */
.table-wrap{background:#fff;border:1px solid #f0e8a0;border-radius:16px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.04);}
.inv-table{width:100%;border-collapse:collapse;}
.inv-table thead tr{background:#f8fafc;}
.inv-table thead th{padding:12px 18px;font-size:.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;text-align:left;border-bottom:1px solid #f1f5f9;}
.inv-table tbody tr{border-bottom:1px solid #f8fafc;transition:background .15s;}
.inv-table tbody tr:hover{background:#fafffe;}
.inv-table tbody tr:last-child{border-bottom:none;}
.inv-table td{padding:13px 18px;font-size:.875rem;color:#374151;vertical-align:middle;}
.inv-no{font-family:monospace;font-size:.82rem;font-weight:700;color:#a07800;background:#fffdf0;border:1px solid #F7DF79;padding:3px 9px;border-radius:7px;}
.pill{display:inline-flex;align-items:center;padding:3px 9px;border-radius:99px;font-size:.7rem;font-weight:700;}
.pill-green{background:#FBEFBC;color:#a07800;}
.pill-amber{background:#fef3c7;color:#92400e;}
.pill-red{background:#fee2e2;color:#b91c1c;}
.pill-gray{background:#f1f5f9;color:#64748b;}
.pill-blue{background:#dbeafe;color:#1d4ed8;}
.action-link{display:inline-flex;align-items:center;justify-content:center;gap:5px;color:#18181b;font-size:.78rem;font-weight:700;text-decoration:none;padding:6px 14px;border-radius:8px;transition:.15s;background:#F0F2F5;border:1.5px solid #E8EAED;white-space:nowrap;width:auto;}
.action-link:hover{background:#E8EAED;color:#18181b;border-color:#D8DBE0;}

.empty-state{padding:60px 20px;text-align:center;color:#cbd5e1;}
.empty-state svg{margin:0 auto 14px;display:block;opacity:.3;}
.empty-state p{font-size:.9rem;font-weight:500;}
.pagination-wrap{padding:16px 18px;border-top:1px solid #f1f5f9;display:flex;justify-content:center;}

.date-group { background: #fff; border: 1.5px solid #f0e8b0; border-radius: 16px; margin-bottom: 12px; overflow: hidden; }
.date-header { padding: 16px 24px; background: #fffdf0; cursor: pointer; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f0e8b0; }
.date-title { font-size: 1rem; font-weight: 800; color: #1e293b; }
.date-summary { font-size: 0.75rem; color: #c9a800; font-weight: 700; }
.date-content { display: none; padding: 0; }
.date-group.active .date-content { display: block; }
.date-group.active .drop-arrow { transform: rotate(180deg); }
.drop-arrow { transition: 0.3s; color: #c9a800; }

.toggle-group-wrap { display: flex; align-items: center; gap: 8px; background: #f8fafc; padding: 8px 16px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 16px; width: fit-content; }
.toggle-label-text { font-size: 0.8rem; font-weight: 700; color: #475569; }
</style>

{{-- Tabs --}}
<div class="history-tabs">
    <a href="{{ route('invoices.index', ['tab' => 'sales']) }}" class="tab-btn {{ $tab === 'sales' ? 'active' : '' }}">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        Sales History
    </a>

    <a href="{{ route('invoices.index', ['tab' => 'purchases']) }}" class="tab-btn {{ $tab === 'purchases' ? 'active' : '' }}">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        Purchase History
    </a>

    <a href="{{ route('invoices.index', ['tab' => 'reconciliation']) }}" class="tab-btn {{ $tab === 'reconciliation' ? 'active' : '' }}">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        Reconciliation History
    </a>

    <a href="{{ route('invoices.index', ['tab' => 'expenses']) }}" class="tab-btn {{ $tab === 'expenses' ? 'active' : '' }}">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        Expense History
    </a>
</div>

{{-- Header --}}
<div class="inv-header">
    <div>
        <div class="inv-title">{{ $title }}</div>
        <div class="inv-sub">View and filter historical {{ strtolower($title) }} records</div>
    </div>
    @if($tab === 'sales')
    <div class="header-actions">
        <a href="{{ route('invoices.export', request()->query()) }}" class="btn-export">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export CSV
        </a>
        <a href="{{ route('invoices.export', array_merge(request()->query(), ['format'=>'pdf'])) }}" class="btn-export pdf">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Export PDF
        </a>
    </div>
    @endif
</div>

{{-- Content Sections --}}
@if($tab === 'sales')
    @if(!$canViewSales)
        <div class="empty-state" style="background:#fff; border-radius:16px; border:2px dashed #fee2e2;">
            <svg width="48" height="48" fill="none" stroke="#ef4444" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <h3 style="color:#1e293b; font-weight:800; margin-bottom:8px;">Access Denied</h3>
            <p style="color:#64748b;">You do not have permission to view Sales History. Please contact your administrator.</p>
        </div>
    @else
    {{-- Stats (Only for Sales for now) --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="stat-val">{{ $totalInvoices }}</div>
                <div class="stat-label">Total Invoices</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fffdf0;color:#F7DF79;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <div>
                <div class="stat-val" style="color:#c9a800;">PKR {{ number_format($totalSales, 2) }}</div>
                <div class="stat-label">Total Sales</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f5f3ff;color:#8b5cf6;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div class="stat-val">PKR {{ $totalInvoices > 0 ? number_format($totalSales / $totalInvoices, 2) : '0.00' }}</div>
                <div class="stat-label">Avg. Sale</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fffbeb;color:#f59e0b;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <div class="stat-val">{{ $periodInvoices }}</div>
                <div class="stat-label">{{ request('period') ? ucwords(str_replace('_',' ',request('period'))) : 'Today' }}</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-panel">
        <div class="filter-title">
            <span>Filters & View Options</span>
        </div>
        <form method="GET" action="{{ route('invoices.index') }}">
            <input type="hidden" name="tab" value="sales">
            <input type="hidden" name="filter_applied" value="1">
            <div class="toggle-group-wrap">
                @php $isGrouped = request()->has('filter_applied') ? request('group_by_date') : true; @endphp
                <input type="checkbox" name="group_by_date" value="1" {{ $isGrouped ? 'checked' : '' }} onchange="this.form.submit()" id="group-toggle">
                <label for="group-toggle" class="toggle-label-text">Group by Date (Accordion View)</label>
            </div>
            <div class="filter-grid">
                <div>
                    <label class="f-label">Period</label>
                    <select name="period" class="f-input">
                        <option value="">All Time</option>
                        @foreach(['today'=>'Today','yesterday'=>'Yesterday','this_week'=>'This Week','last_week'=>'Last Week','this_month'=>'This Month','last_month'=>'Last Month','this_year'=>'This Year','last_year'=>'Last Year'] as $val=>$label)
                        <option value="{{ $val }}" {{ request('period')===$val?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="f-label">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="f-input">
                </div>
                <div>
                    <label class="f-label">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="f-input">
                </div>
                <div>
                    <label class="f-label">Payment Method</label>
                    <select name="payment_method" class="f-input">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('payment_method')==='cash'?'selected':'' }}>Cash</option>
                        <option value="card" {{ request('payment_method')==='card'?'selected':'' }}>Card</option>
                        <option value="qr" {{ request('payment_method')==='qr'?'selected':'' }}>QR</option>
                        <option value="split" {{ request('payment_method')==='split'?'selected':'' }}>Split</option>
                        <option value="online" {{ request('payment_method')==='online'?'selected':'' }}>Online</option>
                    </select>
                </div>
            </div>
            <div class="filter-grid-2">
                <div>
                    <label class="f-label">Status</label>
                    <select name="status" class="f-input">
                        <option value="">All Status</option>
                        <option value="paid" {{ request('status')==='paid'?'selected':'' }}>Paid</option>
                        <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
                        <option value="cancelled" {{ request('status')==='cancelled'?'selected':'' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="f-label">Customer Name</label>
                    <input type="text" name="customer" value="{{ request('customer') }}" class="f-input" placeholder="Search name…">
                </div>
                <div>
                    <label class="f-label">Customer Phone</label>
                    <input type="text" name="customer_phone" value="{{ request('customer_phone') }}" class="f-input" placeholder="Search phone…">
                </div>
                <div>
                    <label class="f-label">Invoice #</label>
                    <input type="text" name="invoice_no" value="{{ request('invoice_no') }}" class="f-input" placeholder="e.g. INV-ABC123">
                </div>
                <div>
                    <label class="f-label">Employee Name</label>
                    <input type="text" name="staff_name" value="{{ request('staff_name') }}" class="f-input" placeholder="Search employee…">
                </div>
            </div>
            <div class="filter-footer">
                <a href="{{ route('invoices.index', ['tab' => 'sales']) }}" class="btn-clear">Reset</a>
                <button type="submit" class="btn-apply">Apply Filters</button>
            </div>
        </form>
    </div>

    @if($isGrouped)
        @foreach($invoices->groupBy(function($inv){ return $inv->created_at->format('Y-m-d'); }) as $date => $dailyInvoices)
        <div class="date-group">
            <div class="date-header" onclick="this.parentElement.classList.toggle('active')">
                <div class="date-info">
                    <span class="date-title">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                    <span style="margin-left:12px; color:#94a3b8; font-size:0.8rem;">({{ $dailyInvoices->count() }} Invoices)</span>
                </div>
                <div style="display:flex; align-items:center; gap:20px;">
                    <span class="date-summary">PKR {{ number_format($dailyInvoices->sum('payable_amount'), 2) }}</span>
                    <div class="drop-arrow">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                    </div>
                </div>
            </div>
            <div class="date-content">
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Time</th>
                            <th>Customer</th>
                            <th>Employee</th>
                            <th>Total Sales</th>
                            <th>Total Cost</th>
                            <th>Profit</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyInvoices as $invoice)
                        @php 
                            $totalCost = $invoice->totalCost(); 
                            $profit = $invoice->payable_amount - $totalCost;
                        @endphp
                        <tr>
                            <td><span class="inv-no">{{ $invoice->invoice_no }}</span></td>
                            <td>
                                <div style="font-size:.85rem;color:#94a3b8;font-weight:500;">{{ $invoice->created_at->format('H:i') }}</div>
                            </td>
                            <td>
                                <div style="font-weight:600;color:#1e293b;">
                                    {{ $invoice->customer ? $invoice->customer->name : ($invoice->customer_name ?? 'Walk-in Customer') }}
                                </div>
                                @if($invoice->customer && $invoice->customer->phone)
                                <div style="font-size:0.75rem; color:#64748b;">
                                    {{ $invoice->customer->phone }}
                                </div>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight:600;color:#1e293b;font-size:0.85rem;">
                                    {{ $invoice->staff_names ?? ($invoice->staff?->name ?? '-') }}
                                </div>
                            </td>
                            <td style="font-weight:800;color:#c9a800;">PKR {{ number_format($invoice->payable_amount, 2) }}</td>
                            <td style="font-weight:600;color:#64748b;">PKR {{ number_format($totalCost, 2) }}</td>
                            <td style="font-weight:800;color:{{ $profit >= 0 ? '#10b981' : '#ef4444' }};">PKR {{ number_format($profit, 2) }}</td>
                            <td>
                                <span class="pill pill-gray">{{ ucfirst($invoice->payment_method) }}</span>
                                @if(in_array($invoice->payment_method, ['bank','split']) && $invoice->bank_name)
                                    <div style="font-size:0.7rem;color:#64748b;margin-top:2px;">{{ $invoice->bank_name }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="pill {{ $invoice->status === 'paid' ? 'pill-green' : 'pill-amber' }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="action-link">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    @else
        <div class="table-wrap">
            <table class="inv-table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date & Time</th>
                        <th>Customer</th>
                        <th>Employee</th>
                        <th>Total Sales</th>
                        <th>Total Cost</th>
                        <th>Profit</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    @php 
                        $totalCost = $invoice->totalCost(); 
                        $profit = $invoice->payable_amount - $totalCost;
                    @endphp
                    <tr>
                        <td><span class="inv-no">{{ $invoice->invoice_no }}</span></td>
                        <td>
                            <div style="font-weight:500;color:#1e293b;font-size:.85rem;">{{ $invoice->created_at->format('M d, Y') }}</div>
                            <div style="font-size:.72rem;color:#94a3b8;">{{ $invoice->created_at->format('H:i') }}</div>
                        </td>
                        <td>
                            <div style="font-weight:600;color:#1e293b;">
                                {{ $invoice->customer ? $invoice->customer->name : ($invoice->customer_name ?? 'Walk-in Customer') }}
                            </div>
                            @if($invoice->customer && $invoice->customer->phone)
                            <div style="font-size:0.75rem; color:#64748b;">
                                {{ $invoice->customer->phone }}
                            </div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600;color:#1e293b;font-size:0.85rem;">
                                {{ $invoice->staff_names ?? ($invoice->staff?->name ?? '-') }}
                            </div>
                        </td>
                        <td style="font-weight:800;color:#c9a800;">PKR {{ number_format($invoice->payable_amount, 2) }}</td>
                        <td style="font-weight:600;color:#64748b;">PKR {{ number_format($totalCost, 2) }}</td>
                        <td style="font-weight:800;color:{{ $profit >= 0 ? '#10b981' : '#ef4444' }};">PKR {{ number_format($profit, 2) }}</td>
                        <td>
                            <span class="pill pill-gray">{{ ucfirst($invoice->payment_method) }}</span>
                            @if(in_array($invoice->payment_method, ['bank','split']) && $invoice->bank_name)
                                <div style="font-size:0.7rem;color:#64748b;margin-top:2px;">{{ $invoice->bank_name }}</div>
                            @endif
                        </td>
                        <td><span class="pill pill-green">{{ ucfirst($invoice->status) }}</span></td>
                        <td>
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="action-link">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="empty-state">No sales records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($invoices->hasPages()) <div class="pagination-wrap">{{ $invoices->links() }}</div> @endif
        </div>
    @endif
    @endif
@elseif($tab === 'purchases')
    @if(!$canViewPurchases)
        <div class="empty-state" style="background:#fff; border-radius:16px; border:2px dashed #fee2e2;">
            <svg width="48" height="48" fill="none" stroke="#ef4444" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <h3 style="color:#1e293b; font-weight:800; margin-bottom:8px;">Access Denied</h3>
            <p style="color:#64748b;">You do not have permission to view Purchase History. Please contact your administrator.</p>
        </div>
    @else
    <div class="filter-panel">
        <div class="filter-title"><span>Filters</span></div>
        <form method="GET" action="{{ route('invoices.index') }}">
            <input type="hidden" name="tab" value="purchases">
            <div class="filter-grid">
                <div>
                    <label class="f-label">Supplier (List)</label>
                    <select name="supplier_id" class="f-input">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="f-label">Search Supplier Name</label>
                    <input type="text" name="supplier_name" value="{{ request('supplier_name') }}" class="f-input" placeholder="Type name…">
                </div>
                <div>
                    <label class="f-label">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="f-input">
                </div>
                <div>
                    <label class="f-label">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="f-input">
                </div>
            </div>
            <div class="filter-grid" style="margin-top:12px;">
                <div>
                    <label class="f-label">Status</label>
                    <select name="status" class="f-input">
                        <option value="">All Status</option>
                        <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="ordered" {{ request('status') === 'ordered' ? 'selected' : '' }}>Ordered</option>
                    </select>
                </div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="filter-footer">
                <a href="{{ route('invoices.index', ['tab' => 'purchases']) }}" class="btn-clear">Reset</a>
                <button type="submit" class="btn-apply">Apply Filters</button>
            </div>
        </form>
    </div>

    <div class="table-wrap">
        <table class="inv-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Order Date</th>
                    <th>Supplier</th>
                    <th>Products Adjusted</th>
                    <th>Total Cost</th>
                    <th>Exp. Revenue</th>
                    <th>Potential Profit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $pur)
                @php
                    $revenue = $pur->expectedRevenue();
                    $profit = $pur->potentialProfit();
                @endphp
                <tr>
                    <td><span class="inv-no">{{ $pur->purchase_order_number }}</span></td>
                    <td>{{ $pur->order_date->format('M d, Y') }}</td>
                    <td>
                        <div style="font-weight:600;color:#1e293b;">{{ $pur->supplier ? $pur->supplier->name : 'Walk-in Supplier' }}</div>
                    </td>
                    <td>
                        <div style="font-size:0.8rem; color:#475569; max-height: 60px; overflow-y: auto;">
                        @forelse($pur->purchaseItems as $item)
                            @php $quantity = $item->quantity_received ?? $item->quantity_ordered; @endphp
                            <div>{{ $item->product->name ?? 'Unknown' }} <strong style="color:{{ $quantity < 0 ? '#ef4444' : '#10b981' }};">({{ $quantity > 0 ? '+' : '' }}{{ $quantity }})</strong></div>
                        @empty
                            <span style="color:#94a3b8;">No items</span>
                        @endforelse
                        </div>
                    </td>
                    <td style="font-weight:700;color:#64748b;">PKR {{ number_format($pur->total_amount, 2) }}</td>
                    <td style="font-weight:800;color:#c9a800;">PKR {{ number_format($revenue, 2) }}</td>
                    <td style="font-weight:800;color:{{ $profit >= 0 ? '#10b981' : '#ef4444' }};">PKR {{ number_format($profit, 2) }}</td>
                    <td><span class="pill {{ $pur->status === 'received' ? 'pill-green' : 'pill-amber' }}">{{ ucfirst($pur->status) }}</span></td>
                    <td>
                        <a href="{{ route('purchases.show', $pur->id) }}" class="action-link">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="empty-state">No purchase records found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($purchases->hasPages()) <div class="pagination-wrap">{{ $purchases->links() }}</div> @endif
    </div>
    @endif

@elseif($tab === 'reconciliation')
    @if(!$canViewReconciliation)
        <div class="empty-state" style="background:#fff; border-radius:16px; border:2px dashed #fee2e2;">
            <svg width="48" height="48" fill="none" stroke="#ef4444" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <h3 style="color:#1e293b; font-weight:800; margin-bottom:8px;">Access Denied</h3>
            <p style="color:#64748b;">You do not have permission to view Reconciliation History. Please contact your administrator.</p>
        </div>
    @else
    <div class="filter-panel">
        <div class="filter-title"><span>Filters</span></div>
        <form method="GET" action="{{ route('invoices.index') }}">
            <input type="hidden" name="tab" value="reconciliation">
            <div class="filter-grid">
                <div>
                    <label class="f-label">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="f-input">
                </div>
                <div>
                    <label class="f-label">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="f-input">
                </div>
                <div>
                    <label class="f-label">Status</label>
                    <select name="status" class="f-input">
                        <option value="">All Status</option>
                        <option value="balanced" {{ request('status') === 'balanced' ? 'selected' : '' }}>Balanced</option>
                        <option value="unbalanced" {{ request('status') === 'unbalanced' ? 'selected' : '' }}>Unbalanced</option>
                    </select>
                </div>
            </div>
            <div class="filter-footer">
                <a href="{{ route('invoices.index', ['tab' => 'reconciliation']) }}" class="btn-clear">Reset</a>
                <button type="submit" class="btn-apply">Apply Filters</button>
            </div>
        </form>
    </div>

    <div class="table-wrap">
        <table class="inv-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Expected Cash</th>
                    <th>Actual Cash</th>
                    <th>Difference</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reconciliations as $rec)
                <tr>
                    <td style="font-weight:700; color:#1e293b;">{{ \Carbon\Carbon::parse($rec->date)->format('M d, Y') }}</td>
                    <td>{{ $rec->user->name }}</td>
                    <td>PKR {{ number_format($rec->expected_cash, 2) }}</td>
                    <td>PKR {{ number_format($rec->actual_cash, 2) }}</td>
                    <td style="font-weight:700; color:{{ $rec->difference < 0 ? '#ef4444' : ($rec->difference > 0 ? '#10b981' : '#c9a800') }}">
                        PKR {{ number_format($rec->difference, 2) }}
                    </td>
                    <td><span class="pill {{ $rec->status === 'balanced' ? 'pill-green' : 'pill-red' }}">{{ ucfirst($rec->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="empty-state">No reconciliation records found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($reconciliations->hasPages()) <div class="pagination-wrap">{{ $reconciliations->links() }}</div> @endif
    </div>
    @endif
@elseif($tab === 'expenses')
    @if(!$canViewExpenses)
        <div class="empty-state" style="background:#fff; border-radius:16px; border:2px dashed #fee2e2;">
            <svg width="48" height="48" fill="none" stroke="#ef4444" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <h3 style="color:#1e293b; font-weight:800; margin-bottom:8px;">Access Denied</h3>
            <p style="color:#64748b;">You do not have permission to view Expense History. Please contact your administrator.</p>
        </div>
    @else
    <div class="filter-panel">
        <div class="filter-title"><span>Filters</span></div>
        <form method="GET" action="{{ route('invoices.index') }}">
            <input type="hidden" name="tab" value="expenses">
            <div class="filter-grid">
                <div style="grid-column: span 2;">
                    <label class="f-label">Search (Description or Staff Name)</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="f-input" placeholder="e.g. tea, ali, salary, rent...">
                </div>
                <div>
                    <label class="f-label">Expense Type</label>
                    <select name="expense_type" class="f-input">
                        <option value="">All Types</option>
                        <option value="daily" {{ request('expense_type') === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="fixed" {{ request('expense_type') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                        <option value="staff" {{ request('expense_type') === 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                </div>
                <div>
                    <label class="f-label">Deducted from Drawer</label>
                    <select name="deducted_from_drawer" class="f-input">
                        <option value="">All Expenses</option>
                        <option value="1" {{ request('deducted_from_drawer') === '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ request('deducted_from_drawer') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <div>
                            <label class="f-label">Date From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="f-input">
                        </div>
                        <div>
                            <label class="f-label">Date To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="f-input">
                        </div>
                    </div>
                </div>
            </div>
            <div class="filter-footer">
                <a href="{{ route('invoices.index', ['tab' => 'expenses']) }}" class="btn-clear">Reset</a>
                <button type="submit" class="btn-apply">Apply Filters</button>
            </div>
        </form>
    </div>

    <div class="table-wrap">
        <table class="inv-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Staff</th>
                    <th>Recorded By</th>
                    <th>Drawer</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $exp)
                <tr>
                    <td style="font-weight:700; color:#1e293b;">{{ $exp->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        @if($exp->expense_type === 'daily')
                            <span class="pill" style="background:#dbeafe;color:#0284c7;display:inline-flex;align-items:center;gap:5px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                Daily
                            </span>
                        @elseif($exp->expense_type === 'fixed')
                            <span class="pill" style="background:#fef3c7;color:#b45309;display:inline-flex;align-items:center;gap:5px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 1 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                                Fixed
                            </span>
                        @else
                            <span class="pill" style="background:#f3e8ff;color:#7c3aed;display:inline-flex;align-items:center;gap:5px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/></svg>
                                Staff
                            </span>
                        @endif
                    </td>
                    <td style="font-size:.85rem;color:#64748b;">{{ $exp->category ? ucfirst(str_replace('_', ' ', $exp->category)) : '—' }}</td>
                    <td style="font-size:.9rem;max-width:200px;overflow:hidden;text-overflow:ellipsis;">{{ $exp->description ?: '—' }}</td>
                    <td>{{ $exp->staff?->name ?? '—' }}</td>
                    <td>{{ $exp->user->name ?? 'System' }}</td>
                    <td>
                        @if($exp->deducted_from_drawer)
                            <span class="pill pill-amber">Yes</span>
                        @else
                            <span class="pill pill-gray">No</span>
                        @endif
                    </td>
                    <td style="font-weight:800; color:#ef4444;">PKR {{ number_format($exp->amount, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="empty-state">No expense records found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($expenses->hasPages()) <div class="pagination-wrap">{{ $expenses->links() }}</div> @endif
    </div>
    @endif
@endif
@endsection
