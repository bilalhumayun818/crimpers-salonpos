@extends('layouts.app')
@section('title', 'Product Details')
@section('content')
<style>
:root{--y1:#F7DF79;--y2:#FBEFBC;--yd:#c9a800;--ydark:#a07800;--ybg:#fffdf0;}
.pg-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;gap:16px;flex-wrap:wrap;}
.pg-title{font-size:1.4rem;font-weight:800;color:#18181b;letter-spacing:-.02em;margin-bottom:3px;}
.pg-sub{font-size:.85rem;color:#71717a;}
.hdr-actions{display:flex;gap:8px;flex-wrap:wrap;}
.btn-back{padding:8px 14px;background:#f4f4f5;color:#52525b;border:1.5px solid #e4e4e7;border-radius:9px;text-decoration:none;font-weight:600;font-size:.82rem;display:inline-flex;align-items:center;gap:5px;transition:.15s;}
.btn-back:hover{background:#e4e4e7;color:#18181b;}
.btn-edit{padding:8px 14px;background:var(--y2);color:var(--ydark);border:1.5px solid var(--y1);border-radius:9px;text-decoration:none;font-weight:700;font-size:.82rem;display:inline-flex;align-items:center;gap:5px;transition:.15s;}
.btn-edit:hover{background:var(--y1);}
.btn-adjust{padding:8px 14px;background:#18181b;color:#fff;border:none;border-radius:9px;text-decoration:none;font-weight:700;font-size:.82rem;display:inline-flex;align-items:center;gap:5px;transition:.15s;cursor:pointer;font-family:'Outfit',sans-serif;}
.btn-adjust:hover{background:#3f3f46;}
.btn-del{padding:8px 14px;background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca;border-radius:9px;font-weight:700;font-size:.82rem;cursor:pointer;font-family:'Outfit',sans-serif;transition:.15s;}
.btn-del:hover{background:#fee2e2;}

.status-row{display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;}
.sbadge{padding:4px 12px;border-radius:99px;font-size:.72rem;font-weight:700;}
.sb-retail{background:var(--y2);color:var(--ydark);}
.sb-service{background:#f3e8ff;color:#7c3aed;}
.sb-good{background:var(--y2);color:var(--ydark);}
.sb-low{background:#fef3c7;color:#92400e;}
.sb-out{background:#fee2e2;color:#991b1b;}

.detail-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;margin-bottom:20px;}
.dcard{background:#fff;border:1.5px solid #f0e8a0;border-radius:14px;padding:18px;box-shadow:0 1px 4px rgba(0,0,0,.04);}
.dcard-title{font-size:.78rem;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.09em;margin-bottom:14px;display:flex;align-items:center;gap:7px;}
.dcard-icon{width:24px;height:24px;border-radius:7px;background:var(--y2);display:flex;align-items:center;justify-content:center;color:var(--ydark);}
.drow{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f4f4f5;}
.drow:last-child{border-bottom:none;}
.dlbl{font-size:.8rem;color:#71717a;font-weight:500;}
.dval{font-size:.85rem;font-weight:700;color:#18181b;}

.inv-card{background:#fff;border:1.5px solid #f0e8a0;border-radius:14px;padding:18px;margin-bottom:20px;box-shadow:0 1px 4px rgba(0,0,0,.04);}
.inv-title{font-size:.78rem;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.09em;margin-bottom:14px;}
.inv-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px;}
.istat{text-align:center;padding:14px 10px;background:var(--ybg);border-radius:11px;border:1px solid #f0e8a0;}
.istat-val{font-size:1.4rem;font-weight:800;color:var(--ydark);margin-bottom:3px;}
.istat-lbl{font-size:.65rem;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.07em;}

.tbl-card{background:#fff;border:1.5px solid #f0e8a0;border-radius:14px;padding:18px;margin-bottom:20px;box-shadow:0 1px 4px rgba(0,0,0,.04);}
.tbl-title{font-size:.78rem;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.09em;margin-bottom:14px;display:flex;align-items:center;gap:7px;}
.tbl-title-icon{width:24px;height:24px;border-radius:7px;background:var(--y2);display:flex;align-items:center;justify-content:center;color:var(--ydark);}

.tbl-container{overflow-x:auto;}
.tbl-main{width:100%;border-collapse:collapse;font-size:.82rem;}
.tbl-main thead{background:var(--ybg);border-bottom:2px solid var(--y1);}
.tbl-main th{padding:12px 14px;text-align:left;font-weight:700;color:var(--ydark);letter-spacing:.03em;font-size:.75rem;text-transform:uppercase;}
.tbl-main tbody tr{border-bottom:1px solid #f4f4f5;transition:.12s;}
.tbl-main tbody tr:hover{background:#fafaf5;}
.tbl-main td{padding:11px 14px;color:#52525b;}
.tbl-main .tbl-date{color:#a1a1aa;font-size:.77rem;}
.tbl-main .tbl-qty{font-weight:700;color:var(--ydark);}
.tbl-main .tbl-price{color:#16a34a;font-weight:600;}
.tbl-main .tbl-name{font-weight:600;color:#18181b;}
.tbl-main .tbl-status{padding:4px 10px;border-radius:6px;font-size:.72rem;font-weight:700;display:inline-block;}
.status-received{background:#dcfce7;color:#166534;}
.status-pending{background:#fef3c7;color:#92400e;}
.status-partial{background:#fed7aa;color:#92400e;}
.status-cancelled{background:#fee2e2;color:#991b1b;}

.btn-tbl-act{padding:4px 10px;border-radius:7px;font-size:.75rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:4px;border:none;font-family:'Outfit',sans-serif;transition:.12s;}
.btn-tbl-edit{background:var(--y2);color:var(--ydark);border:1px solid var(--y1);}
.btn-tbl-edit:hover{background:var(--y1);}
.btn-tbl-del{background:#fef2f2;color:#dc2626;border:1px solid #fecaca;margin-left:4px;}
.btn-tbl-del:hover{background:#fee2e2;}

.modal-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.45);backdrop-filter:blur(3px);display:none;align-items:center;justify-content:center;z-index:99999;padding:16px;}
.modal-card{background:#fff;border-radius:16px;max-width:460px;width:100%;padding:24px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1),0 8px 10px -6px rgba(0,0,0,0.1);border:1.5px solid #f0e8a0;animation:modalIn .15s ease-out;}
@keyframes modalIn{from{opacity:0;transform:scale(.95);}to{opacity:1;transform:scale(1);}}
.modal-hdr{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;}
.modal-title{font-size:1.1rem;font-weight:800;color:#18181b;}
.modal-close{background:none;border:none;font-size:1.4rem;color:#71717a;cursor:pointer;line-height:1;}
.modal-close:hover{color:#18181b;}
.mform-group{margin-bottom:14px;}
.mform-lbl{display:block;font-size:.78rem;font-weight:700;color:#52525b;margin-bottom:5px;text-transform:uppercase;letter-spacing:.03em;}
.mform-inp{width:100%;padding:9px 12px;border-radius:9px;border:1.5px solid #e4e4e7;font-size:.85rem;outline:none;transition:.15s;font-family:inherit;}
.mform-inp:focus{border-color:var(--ydark);box-shadow:0 0 0 3px rgba(247,223,121,.3);}
.modal-ftr{display:flex;justify-content:flex-end;gap:10px;margin-top:20px;}
.btn-sec{padding:8px 16px;background:#f4f4f5;color:#52525b;border:1px solid #e4e4e7;border-radius:9px;font-weight:700;font-size:.82rem;cursor:pointer;}
.btn-sec:hover{background:#e4e4e7;}
.btn-pri{padding:8px 16px;background:#18181b;color:#fff;border:none;border-radius:9px;font-weight:700;font-size:.82rem;cursor:pointer;}
.btn-pri:hover{background:#3f3f46;}

.empty-state{text-align:center;padding:40px 20px;color:#a1a1aa;}
.empty-icon{font-size:2rem;margin-bottom:10px;opacity:.5;}
</style>

<div class="pg-header">
    <div>
        <div class="pg-title">{{ $product->name }}</div>
        <div class="pg-sub">Product Details &amp; Inventory</div>
    </div>
    <div class="hdr-actions">
        <a href="{{ route('products.index') }}" class="btn-back">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
        <a href="{{ route('products.edit',$product) }}" class="btn-edit">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit
        </a>
        <a href="{{ route('products.adjust-stock.form',$product) }}" class="btn-adjust">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Adjust Stock
        </a>
        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('WARNING: Are you sure you want to delete {{ addslashes($product->name) }}?');" style="margin:0;">
            @csrf @method('DELETE')
            <button type="submit" class="btn-back" style="border:1px solid #fca5a5; color:#dc2626; background:#fef2f2;">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                Delete
            </button>
        </form>
    </div>
</div>

<div class="status-row">
    <span class="sbadge {{ $product->product_type==='retail'?'sb-retail':'sb-service' }}">
        {{ $product->product_type==='retail' ? 'For Sale' : 'Shop Use' }}
    </span>
    @if($product->current_stock<=0)
        <span class="sbadge sb-out">Out of Stock</span>
    @elseif($product->isLowStock())
        <span class="sbadge sb-low">Low Stock</span>
    @else
        <span class="sbadge sb-good">In Stock</span>
    @endif
</div>

<div class="detail-grid">
    <div class="dcard">
        <div class="dcard-title">
            <div class="dcard-icon"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
            Product Info
        </div>
        <div class="drow"><span class="dlbl">SKU</span><span class="dval">{{ $product->sku ?: '—' }}</span></div>
        <div class="drow"><span class="dlbl">Type</span><span class="dval">{{ $product->product_type==='retail' ? 'For Sale' : 'Shop Use' }}</span></div>
        <div class="drow"><span class="dlbl">Supplier</span><span class="dval">{{ $product->supplier?->name ?? '—' }}</span></div>
        <div class="drow"><span class="dlbl">Description</span><span class="dval" style="max-width:180px;text-align:right;font-size:.78rem;">{{ $product->description ?: '—' }}</span></div>
    </div>

    <div class="dcard">
        <div class="dcard-title">
            <div class="dcard-icon"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
            Pricing
        </div>
        @if($product->product_type === 'retail')
            <div class="drow"><span class="dlbl">Selling Price</span><span class="dval" style="color:var(--ydark);">PKR {{ number_format($product->selling_price,2) }}</span></div>
        @endif
        <div class="drow"><span class="dlbl">Cost Price</span><span class="dval">{{ $product->cost_price ? 'PKR '.number_format($product->cost_price,2) : '—' }}</span></div>
        @if($product->product_type === 'retail')
            <div class="drow">
                <span class="dlbl">Profit Margin</span>
                <span class="dval">
                    @if($product->cost_price && $product->selling_price)
                        {{ number_format((($product->selling_price-$product->cost_price)/$product->cost_price)*100,1) }}%
                    @else —
                    @endif
                </span>
            </div>
        @endif
    </div>
</div>

<div class="inv-card">
    <div class="inv-title">Inventory Status</div>
    <div class="inv-stats">
        <div class="istat">
            <div class="istat-val">{{ $product->current_stock }}</div>
            <div class="istat-lbl">Current Stock</div>
        </div>
        <div class="istat">
            <div class="istat-val">{{ $product->min_stock_level ?: 0 }}</div>
            <div class="istat-lbl">Min Level</div>
        </div>
        <div class="istat">
            <div class="istat-val">{{ $product->productUsages()->sum('quantity_used') }}</div>
            <div class="istat-lbl">Total Used</div>
        </div>
        <div class="istat">
            <div class="istat-val" style="font-size:1rem;">{{ $product->track_inventory?'Active':'Off' }}</div>
            <div class="istat-lbl">Tracking</div>
        </div>
    </div>
</div>

<!-- USAGE HISTORY TABLE -->
<div class="tbl-card">
    <div class="tbl-title">
        <div class="tbl-title-icon"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg></div>
        Usage History (Shop Use)
    </div>
    @if($product->productUsages->count() > 0)
        <div class="tbl-container">
            <table class="tbl-main">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Service</th>
                        <th>Customer</th>
                        <th>Mobile Number</th>
                        <th>Qty Used</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->productUsages()->latest()->get() as $usage)
                    <tr>
                        <td class="tbl-date">{{ $usage->usage_date?->format('M d, Y') ?: '—' }}</td>
                        <td class="tbl-name">{{ $usage->service?->name ?? 'Direct Shop Issue' }}</td>
                        <td>{{ $usage->invoice?->customer_name ?? '—' }}</td>
                        <td>{{ $usage->invoice?->customer?->phone ?: '—' }}</td>
                        <td class="tbl-qty">{{ $usage->quantity_used }}</td>
                        <td>{{ $usage->notes ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">📊</div>
            <p>No usage history yet</p>
        </div>
    @endif
</div>
@if($product->product_type === 'retail')
<!-- SALES/CUSTOMER USAGE HISTORY FOR RETAIL PRODUCTS -->
<div class="tbl-card">
    <div class="tbl-title">
        <div class="tbl-title-icon"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M16 11a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
        Customer Purchases
    </div>
    @php
        $sales = $product->invoiceItems()
            ->with('invoice', 'invoice.customer')
            ->latest('created_at')
            ->get();
    @endphp
    @if($sales->count() > 0)
        <div class="tbl-container">
            <table class="tbl-main">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Mobile Number</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $item)
                    @php
                        $invoice = $item->invoice ?? null;
                    @endphp
                    <tr>
                        <td class="tbl-date">{{ $invoice?->created_at?->format('M d, Y') ?: '—' }}</td>
                        <td class="tbl-name">{{ $invoice?->customer_name ?? '—' }}</td>
                        <td>{{ $invoice?->customer?->phone ?: '—' }}</td>
                        <td class="tbl-qty">{{ $item->quantity }}</td>
                        <td class="tbl-price">PKR {{ number_format($product->selling_price, 2) }}</td>
                        <td class="tbl-price"><strong>PKR {{ number_format($product->selling_price * $item->quantity, 2) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">🛒</div>
            <p>No customer purchases yet</p>
        </div>
    @endif
</div>
@endif

<!-- PURCHASE/INVENTORY HISTORY TABLE -->
<div class="tbl-card">
    <div class="tbl-title">
        <div class="tbl-title-icon"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        Purchase & Inventory Adjustments
    </div>
    @php
        $purchases = $product->purchaseItems()
            ->with('purchase', 'purchase.supplier')
            ->latest('created_at')
            ->get();
    @endphp
    @if($purchases->count() > 0)
        <div class="tbl-container">
            <table class="tbl-main">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Quantity</th>
                        <th>Unit Cost</th>
                        <th>Selling Price</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases as $item)
                    @php
                        $purchase = $item->purchase;
                        $status = $purchase->status ?? 'pending';
                        $statusClass = $status === 'received' ? 'status-received' : ($status === 'partial' ? 'status-partial' : ($status === 'cancelled' || $status === 'deleted' ? 'status-cancelled' : 'status-pending'));
                    @endphp
                    <tr>
                        <td class="tbl-date">{{ $purchase->order_date?->format('M d, Y') ?: '—' }}</td>
                        <td class="tbl-name">{{ $purchase->supplier?->name ?? '—' }}</td>
                        <td class="tbl-qty">
                            @if($item->quantity_ordered > 0)
                                <span style="color:#16a34a; font-weight:800; display:inline-flex; align-items:center; gap:2px;">
                                    +{{ $item->quantity_ordered }}
                                </span>
                            @elseif($item->quantity_ordered < 0)
                                <span style="color:#dc2626; font-weight:800; display:inline-flex; align-items:center; gap:2px;">
                                    {{ $item->quantity_ordered }}
                                </span>
                            @else
                                <span>0</span>
                            @endif
                        </td>
                        <td class="tbl-price">PKR {{ number_format($item->unit_cost, 2) }}</td>
                        <td class="tbl-price" style="color:var(--ydark);">PKR {{ number_format($item->unit_selling_price ?? $product->selling_price, 2) }}</td>
                        <td class="tbl-price" style="{{ $item->quantity_ordered < 0 ? 'color:#dc2626;' : '' }}">
                            <strong>PKR {{ number_format(abs($item->unit_cost * $item->quantity_ordered), 2) }}</strong>
                            @if($item->quantity_ordered < 0)
                                <span style="font-size:0.68rem; color:#dc2626; font-weight:700; display:block;">(Removed)</span>
                            @endif
                        </td>
                        <td><span class="tbl-status {{ $statusClass }}">{{ ucfirst($status) }}</span></td>
                        <td style="text-align:right; white-space:nowrap;">
                            <button type="button" class="btn-tbl-act btn-tbl-edit" onclick="openAdjModal('{{ route('products.adjustments.update', $item) }}', '{{ $item->quantity_ordered }}', '{{ $item->unit_cost }}', '{{ $item->unit_selling_price ?? $product->selling_price }}', '{{ addslashes($purchase->notes ?? '') }}', '{{ $purchase->supplier_id }}')">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
                            </button>
                            <form method="POST" action="{{ route('products.adjustments.destroy', $item) }}" onsubmit="return confirm('Are you sure you want to delete/cancel this purchase/adjustment record? Stock will be updated accordingly.');" style="display:inline-block; margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-tbl-act btn-tbl-del">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">📦</div>
            <p>No purchase history yet</p>
        </div>
    @endif
</div>

<!-- STOCK & PRICE CHANGE HISTORY TABLE -->
<div class="tbl-card">
    <div class="tbl-title">
        <div class="tbl-title-icon"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v-2m-9-1h18"/></svg></div>
        Stock &amp; Price Change History
    </div>
    @if($product->priceHistories->count() > 0)
        <div class="tbl-container">
            <table class="tbl-main">
                <thead>
                    <tr>
                        <th>Date &amp; Time</th>
                        <th>Action / Reason</th>
                        <th>Stock Change</th>
                        <th>Selling Price</th>
                        <th>Cost Price</th>
                        <th>Changed By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->priceHistories()->latest()->get() as $ph)
                    <tr>
                        <td class="tbl-date">{{ $ph->created_at?->format('M d, Y h:i A') }}</td>
                        <td class="tbl-name" style="font-size:0.85rem;">{{ $ph->reason ?: 'Stock / Price Adjustment' }}</td>
                        <td style="font-weight:700;">
                            @if($ph->old_stock !== null || $ph->new_stock !== null)
                                @php
                                    $diff = ($ph->new_stock ?? 0) - ($ph->old_stock ?? 0);
                                @endphp
                                <span style="{{ $diff > 0 ? 'color:#16a34a;' : ($diff < 0 ? 'color:#dc2626;' : 'color:#52525b;') }}">
                                    {{ $ph->old_stock ?? 0 }} &rarr; {{ $ph->new_stock ?? 0 }}
                                    ({{ $diff > 0 ? '+'.$diff : $diff }})
                                </span>
                            @else
                                <span style="color:#a1a1aa;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($ph->old_selling_price != $ph->new_selling_price && $ph->old_selling_price !== null)
                                <span style="font-size:0.8rem; color:#71717a; text-decoration:line-through;">PKR {{ number_format($ph->old_selling_price, 2) }}</span>
                                <strong style="color:var(--ydark); margin-left:4px;">PKR {{ number_format($ph->new_selling_price, 2) }}</strong>
                            @else
                                <span style="color:#18181b;">PKR {{ number_format($ph->new_selling_price, 2) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($ph->old_cost_price != $ph->new_cost_price && $ph->old_cost_price !== null)
                                <span style="font-size:0.8rem; color:#71717a; text-decoration:line-through;">PKR {{ number_format($ph->old_cost_price, 2) }}</span>
                                <strong style="color:#18181b; margin-left:4px;">PKR {{ number_format($ph->new_cost_price, 2) }}</strong>
                            @else
                                <span style="color:#18181b;">PKR {{ number_format($ph->new_cost_price, 2) }}</span>
                            @endif
                        </td>
                        <td class="tbl-name">{{ $ph->user?->name ?? 'System' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">🏷️</div>
            <p>No stock or price history recorded yet</p>
        </div>
    @endif
</div>

<!-- EDIT ADJUSTMENT / PURCHASE MODAL -->
<div id="modal-edit-adj" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-hdr">
            <div class="modal-title">Edit Purchase / Adjustment</div>
            <button type="button" class="modal-close" onclick="closeAdjModal()">&times;</button>
        </div>
        <form id="form-edit-adj" method="POST">
            @csrf @method('PUT')
            <div class="mform-group">
                <label class="mform-lbl">Quantity (Positive for Add, Negative for Remove)</label>
                <input type="number" step="1" name="quantity" id="adj-qty" class="mform-inp" required>
            </div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="mform-group">
                    <label class="mform-lbl">Unit Cost Price (PKR)</label>
                    <input type="number" step="0.01" min="0" name="unit_cost" id="adj-cost" class="mform-inp">
                </div>
                <div class="mform-group">
                    <label class="mform-lbl">Selling Price (PKR)</label>
                    <input type="number" step="0.01" min="0" name="selling_price" id="adj-selling-price" class="mform-inp">
                </div>
            </div>
            @if(isset($suppliers) && $suppliers->count() > 0)
            <div class="mform-group">
                <label class="mform-lbl">Supplier</label>
                <select name="supplier_id" id="adj-supplier" class="mform-inp">
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="mform-group">
                <label class="mform-lbl">Notes</label>
                <input type="text" name="notes" id="adj-notes" class="mform-inp" placeholder="Reason or notes">
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn-sec" onclick="closeAdjModal()">Cancel</button>
                <button type="submit" class="btn-pri">Update Adjustment</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAdjModal(actionUrl, qty, cost, sell, notes, supplierId) {
    document.getElementById('form-edit-adj').action = actionUrl;
    document.getElementById('adj-qty').value = qty;
    document.getElementById('adj-cost').value = cost;
    if (document.getElementById('adj-selling-price')) {
        document.getElementById('adj-selling-price').value = sell || '';
    }
    document.getElementById('adj-notes').value = notes;
    if (document.getElementById('adj-supplier')) {
        document.getElementById('adj-supplier').value = supplierId || '';
    }
    document.getElementById('modal-edit-adj').style.display = 'flex';
}
function closeAdjModal() {
    document.getElementById('modal-edit-adj').style.display = 'none';
}

window.onclick = function(event) {
    var modalAdj = document.getElementById('modal-edit-adj');
    if (event.target == modalAdj) {
        closeAdjModal();
    }
};
</script>
@endsection
