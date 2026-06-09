@extends('layouts.app')
@section('title', 'Purchase Order Details')

@section('content')
<style>
.page-header{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px;padding:16px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;}
.page-header h2{font-size:1.25rem;font-weight:700;color:#1e293b;margin:0;}
.page-header .po-number{color:#2563eb;font-weight:600;}
.btn-back,.btn-edit,.btn-receive{padding:8px 16px;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:10px;text-decoration:none;font-weight:600;cursor:pointer;font-family:'Outfit',sans-serif;display:inline-block;}
.btn-receive{background:#22c55e;color:#fff;border-color:#22c55e;}
.btn-receive:hover{background:#16a34a;}

.details-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;margin-bottom:24px;}
.detail-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;}
.detail-card h3{font-size:1rem;font-weight:700;color:#1e293b;margin:0 0 12px 0;}
.detail-row{display:flex;justify-content:space-between;margin-bottom:8px;}
.detail-label{color:#64748b;font-size:.85rem;}
.detail-value{color:#1e293b;font-weight:500;}
.status-badge{display:inline-block;padding:4px 10px;border-radius:99px;font-size:.75rem;font-weight:600;}
.status-ordered{background:#dbeafe;color:#1e40af;}
.status-partially{background:#fef3c7;color:#92400e;}
.status-received{background:#dcfce7;color:#166534;}
.status-cancelled{background:#fee2e2;color:#991b1b;}

.items-table{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:24px;}
.items-head{background:#f8fafc;padding:12px 16px;border-bottom:1px solid #e2e8f0;display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr 1fr;gap:12px;font-size:.8rem;font-weight:700;color:#64748b;text-transform:uppercase;}
.items-row{padding:12px 16px;border-bottom:1px solid #f1f5f9;display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr 1fr;gap:12px;align-items:center;}
.items-row:last-child{border-bottom:none;}

.product-name{font-weight:500;color:#1e293b;}
.quantity{font-size:.85rem;color:#64748b;}
.received-badge{display:inline-block;padding:2px 6px;border-radius:4px;font-size:.7rem;font-weight:600;background:#dcfce7;color:#166534;}
.pending-badge{background:#fee2e2;color:#991b1b;}

.total-row{background:#f8fafc;padding:16px;border-top:2px solid #e2e8f0;display:flex;justify-content:flex-end;gap:20px;font-weight:700;color:#1e293b;}
</style>

<div class="page-header">
    <div>
        <h2>Purchase Order Details</h2>
        <div class="po-number">PO #{{ $purchase->purchase_order_number }}</div>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('purchases.index') }}" class="btn-back">← Back to Orders</a>
        @if($purchase->status === 'ordered')
            <a href="{{ route('purchases.edit', $purchase) }}" class="btn-edit">Edit Order</a>
            <a href="{{ route('purchases.receive', $purchase) }}" class="btn-receive">Receive Goods</a>
        @elseif($purchase->status === 'partially_received')
            <a href="{{ route('purchases.receive', $purchase) }}" class="btn-receive">Complete Receipt</a>
        @endif
    </div>
</div>

<div class="details-grid">
    <div class="detail-card">
        <h3>Order Information</h3>
        <div class="detail-row">
            <span class="detail-label">Order Date:</span>
            <span class="detail-value">{{ $purchase->order_date->format('M d, Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Expected Delivery:</span>
            <span class="detail-value">{{ $purchase->expected_delivery_date ? $purchase->expected_delivery_date->format('M d, Y') : 'Not specified' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Actual Delivery:</span>
            <span class="detail-value">{{ $purchase->actual_delivery_date ? $purchase->actual_delivery_date->format('M d, Y') : 'Not yet received' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Status:</span>
            <span class="status-badge status-{{ $purchase->status }}">{{ ucfirst(str_replace('_', ' ', $purchase->status)) }}</span>
        </div>
    </div>

    <div class="detail-card">
        <h3>Supplier Information</h3>
        <div class="detail-row">
            <span class="detail-label">Supplier:</span>
            <span class="detail-value">{{ $purchase->supplier->name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Contact Person:</span>
            <span class="detail-value">{{ $purchase->supplier->contact_person ?: 'Not specified' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Phone:</span>
            <span class="detail-value">{{ $purchase->supplier->phone ?: 'Not specified' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Email:</span>
            <span class="detail-value">{{ $purchase->supplier->email ?: 'Not specified' }}</span>
        </div>
    </div>
</div>

<div class="items-table">
    <div class="items-head" style="grid-template-columns:2fr 1.5fr 1fr 1fr 1fr 1fr 1fr;">
        <span>Product</span>
        <span>Prices (Cost / Sell)</span>
        <span>Ordered</span>
        <span>Received</span>
        <span>Unit Cost</span>
        <span>Line Total</span>
        <span>Status</span>
    </div>
    @foreach($purchase->purchaseItems as $item)
    <div class="items-row" style="grid-template-columns:2fr 1.5fr 1fr 1fr 1fr 1fr 1fr;">
        <span class="product-name">{{ $item->product->name }}</span>
        <span style="font-size: 0.8rem; line-height: 1.4;">
            <div style="color: #64748b;">Cost: <b>PKR {{ number_format($item->product->cost_price, 2) }}</b></div>
            <div style="color: #64748b;">Sell: <b>PKR {{ number_format($item->product->selling_price, 2) }}</b></div>
            @php
                $history = $item->product->priceHistories->first();
            @endphp
            @if($history)
                <div style="color: #f59e0b; font-size: 0.7rem; margin-top: 4px;">
                    Changed from: Cost <b>{{ $history->old_cost_price + 0 }}</b>, Sell <b>{{ $history->old_selling_price + 0 }}</b>
                </div>
            @endif
        </span>
        <span class="quantity">{{ $item->quantity_ordered }}</span>
        <span class="quantity">{{ $item->quantity_received ?: 0 }}</span>
        <span class="quantity">PKR {{ number_format($item->unit_cost, 2) }}</span>
        <span class="quantity">PKR {{ number_format($item->line_total, 2) }}</span>
        <span>
            @if($item->quantity_received >= $item->quantity_ordered)
                <span class="received-badge">Complete</span>
            @elseif($item->quantity_received > 0)
                <span class="received-badge">Partial</span>
            @else
                <span class="pending-badge">Pending</span>
            @endif
        </span>
    </div>
    @endforeach
    <div class="total-row">
        <span>Total Amount: PKR {{ number_format($purchase->total_amount, 2) }}</span>
    </div>
</div>
@endsection