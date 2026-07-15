@extends('layouts.app')

@section('page-title', 'Sales History Detail')
@section('page-sub', 'Professional administrative record.')

@section('content')
    {{--
    USER DEMAND: Professional Sales History Page (NO TICKET)
    This is an administrative review page. No thermal ticket logic here.
    --}}
    @php
        $customerName = $invoice->customer->name ?? $invoice->customer_name ?? 'Walk-in Customer';
        $customerPhone = $invoice->customer?->phone ?? '—';
        $staffName = $invoice->staff?->name ?? $invoice->user?->name ?? 'System Admin';
        $totalItems = $invoice->items->count();
    @endphp

    <style>
        .history-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
            font-family: 'Outfit', sans-serif;
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 20px 30px;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .history-id-val {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
        }

        .history-stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 16px;
        }

        .h-stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .h-stat-label {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
        }

        .h-stat-val {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
        }

        .history-main-card {
            background: #fff;
            border-radius: 24px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .h-card-head {
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
            background: #fafafa;
            font-size: 12px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
        }

        .h-card-body {
            padding: 24px;
        }

        .h-table {
            width: 100%;
            border-collapse: collapse;
        }

        .h-table th {
            text-align: left;
            padding: 12px;
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            border-bottom: 2px solid #f1f5f9;
        }

        .h-table td {
            padding: 16px 12px;
            font-size: 14px;
            border-bottom: 1px solid #f8fafc;
        }

        .h-summary-box {
            background: #f8fafc;
            border-radius: 20px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            border: 1px solid #eef2f6;
        }

        .h-sum-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #64748b;
        }

        .h-sum-row b {
            color: #111827;
        }

        .h-sum-total {
            border-top: 2px solid #e2e8f0;
            padding-top: 12px;
            margin-top: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .h-total-val {
            font-size: 22px;
            font-weight: 900;
            color: #c9a800;
        }

        .btn-h-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            padding: 0 20px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            background: #f4f4f5;
            color: #52525b;
            transition: 0.2s;
        }

        .btn-h-back:hover {
            background: #e4e4e7;
            color: #18181b;
        }
    </style>

    <div class="history-container">
        <div class="history-header">
            <div class="history-id-block">
                <span class="h-stat-label">Administrative Record</span>
                <div class="history-id-val">INV #{{ $invoice->invoice_no }}</div>
            </div>
            <a href="{{ route('invoices.index') }}" class="btn-h-back">
                <i data-lucide="arrow-left"></i> Back To History
            </a>
        </div>

        <div class="history-stats-row">
            <div class="h-stat-card">
                <span class="h-stat-label">Client</span>
                <span class="h-stat-val">{{ $customerName }}</span>
            </div>
            <div class="h-stat-card">
                <span class="h-stat-label">Client Mobile</span>
                <span class="h-stat-val">{{ $customerPhone }}</span>
            </div>
            <div class="h-stat-card">
                <span class="h-stat-label">Date/Time</span>
                <span class="h-stat-val">{{ $invoice->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div class="h-stat-card">
                <span class="h-stat-label">Served By</span>
                <span class="h-stat-val">{{ $staffName }}</span>
            </div>
            <div class="h-stat-card">
                <span class="h-stat-label">Status</span>
                <span class="h-stat-val" style="color:#16a34a">PAID</span>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 350px; gap:24px;">
            <div class="history-main-card">
                <div class="h-card-head">Order Line Items</div>
                <div class="h-card-body" style="padding:0">
                    <table class="h-table">
                        <thead>
                            <tr>
                                <th style="padding-left:24px">#</th>
                                <th>Description</th>
                                <th style="text-align:right">Qty</th>
                                <th style="text-align:right">Rate</th>
                                <th style="text-align:right; padding-right:24px">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $idx => $item)
                                <tr>
                                    <td style="padding-left:24px; color:#94a3b8;">{{ $idx + 1 }}</td>
                                    <td>
                                        <div style="font-weight:700; color:#111827;">
                                            {{ $item->custom_name ?? $item->itemizable->name ?? 'Item' }}
                                        </div>
                                        <div style="font-size:11px; color:#94a3b8;">{{ class_basename($item->itemizable_type) }}
                                        </div>
                                    </td>
                                    <td style="text-align:right">{{ $item->quantity }}</td>
                                    <td style="text-align:right">PKR {{ number_format($item->price, 2) }}</td>
                                    <td style="text-align:right; font-weight:800; color:#111827; padding-right:24px">PKR
                                        {{ number_format($item->subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="history-main-card">
                <div class="h-card-head">Financial Summary</div>
                <div class="h-card-body">
                    <div class="h-summary-box">
                        <div class="h-sum-row"><span>Subtotal</span><b>PKR
                                {{ number_format($invoice->total_amount, 2) }}</b></div>
                        <div class="h-sum-row"><span>GST (5%)</span><b>PKR {{ number_format($invoice->tax, 2) }}</b></div>
                        @if($invoice->discount > 0)
                            <div class="h-sum-row"><span>Discount</span><b style="color:#ef4444;">- PKR
                                    {{ number_format($invoice->discount, 2) }}</b></div>
                        @endif
                        <div class="h-sum-total">
                            <span style="font-size:14px; font-weight:800;">Payable</span>
                            <span class="h-total-val">PKR {{ number_format($invoice->payable_amount, 2) }}</span>
                        </div>

                        @if($invoice->cash_received !== null)
                            <div
                                style="margin-top: 10px; border-top: 1px dashed #e2e8f0; padding-top: 10px; display: flex; flex-direction: column; gap: 6px;">
                                <div class="h-sum-row"><span>Amount Received</span><b>PKR
                                        {{ number_format($invoice->cash_received, 2) }}</b></div>
                                <div class="h-sum-row"><span>Change Returned</span><b>PKR
                                        {{ number_format($invoice->change_returned ?? 0, 2) }}</b></div>
                            </div>
                        @endif
                    </div>
                    <div style="margin-top:20px; font-size:12px; color:#64748b; text-align:center;">
                        Payment via {{ strtoupper($invoice->payment_method ?? 'cash') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
