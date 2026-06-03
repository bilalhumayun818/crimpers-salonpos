@extends('layouts.app')
@section('title', 'POS Sales Report')

@section('content')
<style>
    .metric-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }
    .metric-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #F5EFC0, #F7DF79);
    }
    .metric-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }
    .metric-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.2;
    }
    
    .table-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-top: 24px;
    }
    
    .filter-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        display: flex;
        gap: 16px;
        align-items: flex-end;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    .form-group label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
    }
    
    .form-control {
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 0.9rem;
        font-family: inherit;
        outline: none;
        transition: all 0.2s;
    }
    .form-control:focus {
        border-color: #F7DF79;
        box-shadow: 0 0 0 3px rgba(247, 223, 121, 0.2);
    }
    
    .btn-submit {
        background: #18181b;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        height: 41px;
    }
    .btn-submit:hover {
        background: #27272a;
        transform: translateY(-1px);
    }
    
    .badge {
        padding: 4px 10px;
        border-radius: 99px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }
    .badge-cash { background: #dcfce7; color: #166534; }
    .badge-card { background: #dbeafe; color: #1e40af; }
    .badge-jazzcash { background: #fef3c7; color: #92400e; }
    .badge-easypaisa { background: #f3e8ff; color: #6b21a8; }
    .badge-multiple { background: #f1f5f9; color: #475569; }

    .btn-export-csv {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 9px 18px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.25);
    }
    .btn-export-csv:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(59, 130, 246, 0.4);
    }

    .btn-export-xls {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 9px 18px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);
    }
    .btn-export-xls:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(16, 185, 129, 0.4);
    }
</style>

<div class="filter-card">
    <form action="{{ route('reports.pos') }}" method="GET" style="display: flex; gap: 16px; align-items: flex-end; width: 100%; flex-wrap: wrap;">
        <div class="form-group" style="flex: 1; min-width: 150px; max-width: 200px;">
            <label>Date From</label>
            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
        </div>
        <div class="form-group" style="flex: 1; min-width: 150px; max-width: 200px;">
            <label>Date To</label>
            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
        </div>
        <button type="submit" class="btn-submit">Generate Report</button>
        <a href="{{ route('reports.pos') }}" style="color: #64748b; font-size: 0.9rem; font-weight: 500; text-decoration: none; margin-bottom: 10px;">Clear Filter</a>
        
        <div style="margin-left: auto; display: flex; gap: 12px;">
            <a href="{{ route('reports.pos.export', ['date_from' => $dateFrom, 'date_to' => $dateTo, 'format' => 'csv']) }}" class="btn-export-csv">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Export CSV
            </a>
            <a href="{{ route('reports.pos.export', ['date_from' => $dateFrom, 'date_to' => $dateTo, 'format' => 'xls']) }}" class="btn-export-xls">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/><path d="M14 2v6h6M8 13h8M8 17h8M10 9h4"/></svg>
                Export XXL
            </a>
        </div>
    </form>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px;">
    <div class="metric-card">
        <div class="metric-title">Total POS Sales</div>
        <div class="metric-value">Rs {{ number_format($totalSales, 2) }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-title">Total Tax Collected</div>
        <div class="metric-value" style="color: #0ea5e9;">Rs {{ number_format($totalTax, 2) }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-title">Total Discounts Given</div>
        <div class="metric-value" style="color: #f43f5e;">Rs {{ number_format($totalDiscount, 2) }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-title">Total Transactions</div>
        <div class="metric-value" style="color: #8b5cf6;">{{ $invoices->count() }}</div>
    </div>
</div>

<div class="table-card">
    <div style="padding: 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #1e293b;">POS Transactions</h3>
        <div style="display: flex; gap: 10px;">
            @foreach($paymentMethods as $method => $amount)
                @if($amount > 0)
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; color: #475569;">
                        {{ ucfirst($method) }}: Rs {{ number_format($amount) }}
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <tr>
                    <th style="padding: 14px 20px; text-align: left; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Invoice #</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Date</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Customer</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Staff</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Payment</th>
                    <th style="padding: 14px 20px; text-align: right; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 14px 20px; font-size: 0.9rem; font-weight: 600; color: #1e293b;">{{ $invoice->invoice_no }}</td>
                    <td style="padding: 14px 20px; font-size: 0.9rem; color: #475569;">{{ $invoice->created_at->format('M d, Y h:i A') }}</td>
                    <td style="padding: 14px 20px; font-size: 0.9rem; color: #1e293b; font-weight: 500;">{{ $invoice->customer_name ?? ($invoice->customer->name ?? 'Walk-in') }}</td>
                    <td style="padding: 14px 20px; font-size: 0.9rem; color: #475569;">{{ $invoice->staff->name ?? 'N/A' }}</td>
                    <td style="padding: 14px 20px;">
                        <span class="badge badge-{{ strtolower($invoice->payment_method) }}">
                            {{ ucfirst($invoice->payment_method) }}
                        </span>
                    </td>
                    <td style="padding: 14px 20px; text-align: right; font-size: 0.9rem; font-weight: 700; color: #166534;">
                        Rs {{ number_format($invoice->payable_amount, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 40px; text-align: center; color: #94a3b8; font-size: 0.95rem;">
                        No POS transactions found for the selected date range.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
