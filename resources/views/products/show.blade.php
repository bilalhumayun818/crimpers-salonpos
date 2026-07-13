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
@if($product->product_type !== 'retail')
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
                        <th>Qty Used</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->productUsages()->latest()->get() as $usage)
                    <tr>
                        <td class="tbl-date">{{ $usage->usage_date?->format('M d, Y') ?: '—' }}</td>
                        <td class="tbl-name">{{ $usage->service?->name ?? '—' }}</td>
                        <td>{{ $usage->invoice?->customer_name ?? '—' }}</td>
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
@else
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
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases as $item)
                    @php
                        $purchase = $item->purchase;
                        $status = $purchase->status ?? 'pending';
                        $statusClass = $status === 'received' ? 'status-received' : ($status === 'partial' ? 'status-partial' : 'status-pending');
                    @endphp
                    <tr>
                        <td class="tbl-date">{{ $purchase->order_date?->format('M d, Y') ?: '—' }}</td>
                        <td class="tbl-name">{{ $purchase->supplier?->name ?? '—' }}</td>
                        <td class="tbl-qty">{{ $item->quantity_ordered }}</td>
                        <td class="tbl-price">PKR {{ number_format($item->unit_cost, 2) }}</td>
                        <td class="tbl-price"><strong>PKR {{ number_format($item->unit_cost * $item->quantity_ordered, 2) }}</strong></td>
                        <td><span class="tbl-status {{ $statusClass }}">{{ ucfirst($status) }}</span></td>
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
@endsection
