@extends('layouts.app')
@section('title', 'Inventory Dashboard')
@section('content')
<style>
:root{--y1:#F7DF79;--y2:#FBEFBC;--yd:#c9a800;--ydark:#a07800;--ybg:#fffdf0;}
.pg-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;gap:16px;flex-wrap:wrap;}
.pg-title{font-size:1.4rem;font-weight:800;color:#18181b;letter-spacing:-.02em;margin-bottom:3px;}
.pg-sub{font-size:.85rem;color:#71717a;}
.hdr-actions{display:flex;gap:8px;}
.btn-link{padding:8px 14px;background:var(--y2);color:var(--ydark);border:1.5px solid var(--y1);border-radius:9px;text-decoration:none;font-weight:700;font-size:.82rem;display:inline-flex;align-items:center;gap:5px;transition:.15s;}
.btn-link:hover{background:var(--y1);}

/* Stats */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin-bottom:22px;}
.stat-card{background:#fff;border:1.5px solid #f0e8a0;border-radius:14px;padding:18px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 4px rgba(0,0,0,.04);transition:.2s;}
.stat-card:hover{box-shadow:0 4px 14px rgba(247,223,121,.2);border-color:var(--y1);}
.stat-icon{width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.si-yellow{background:var(--y2);color:var(--ydark);}
.si-dark{background:#18181b;color:#fff;}
.si-amber{background:#fef3c7;color:#92400e;}
.si-red{background:#fee2e2;color:#dc2626;}
.stat-val{font-size:1.5rem;font-weight:800;color:#18181b;line-height:1;margin-bottom:3px;}
.stat-lbl{font-size:.72rem;font-weight:600;color:#a1a1aa;text-transform:uppercase;letter-spacing:.07em;}
.stat-note{font-size:.7rem;color:#a1a1aa;margin-top:2px;}

/* Alert sections */
.alert-card{background:#fff;border:1.5px solid #f0e8a0;border-radius:14px;padding:18px;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.04);}
.alert-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;}
.alert-title{font-size:.9rem;font-weight:800;color:#18181b;display:flex;align-items:center;gap:8px;}
.alert-icon{width:26px;height:26px;border-radius:7px;background:var(--y2);display:flex;align-items:center;justify-content:center;color:var(--ydark);}
.alert-link{font-size:.78rem;font-weight:700;color:var(--ydark);text-decoration:none;transition:.15s;}
.alert-link:hover{color:var(--ydark);text-decoration:underline;}

.stock-list{display:flex;flex-direction:column;gap:8px;}
.stock-item{display:flex;align-items:center;justify-content:space-between;padding:10px 13px;background:var(--ybg);border:1px solid #f0e8a0;border-radius:10px;}
.stock-item.out{background:#fef2f2;border-color:#fecaca;}
.stock-name{font-size:.85rem;font-weight:700;color:#18181b;margin-bottom:2px;}
.stock-detail{font-size:.72rem;color:#a1a1aa;}
.stock-badge{padding:3px 9px;border-radius:99px;font-size:.68rem;font-weight:700;white-space:nowrap;}
.sb-low{background:var(--y2);color:var(--ydark);}
.sb-out{background:#fee2e2;color:#991b1b;}

/* Bottom grid */
.bottom-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;}
.panel{background:#fff;border:1.5px solid #f0e8a0;border-radius:14px;padding:18px;box-shadow:0 1px 4px rgba(0,0,0,.04);}
.panel-title{font-size:.85rem;font-weight:800;color:#18181b;margin-bottom:14px;}
.ptable{width:100%;border-collapse:collapse;}
.ptable thead tr{background:var(--ybg);}
.ptable thead th{padding:9px 12px;font-size:.65rem;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.08em;text-align:left;border-bottom:1px solid #f0e8a0;}
.ptable tbody tr{border-bottom:1px solid #f4f4f5;transition:.15s;}
.ptable tbody tr:hover{background:var(--ybg);}
.ptable tbody tr:last-child{border-bottom:none;}
.ptable td{padding:10px 12px;font-size:.82rem;color:#374151;}
.ptable a{color:var(--ydark);text-decoration:none;font-weight:700;}
.ptable a:hover{text-decoration:underline;}
.status-pill{padding:2px 8px;border-radius:99px;font-size:.65rem;font-weight:700;}
.sp-received{background:var(--y2);color:var(--ydark);}
.sp-ordered{background:#dbeafe;color:#1e40af;}
.sp-pending{background:#fef3c7;color:#92400e;}
.empty-note{text-align:center;color:#a1a1aa;font-size:.85rem;padding:20px 0;}
</style>

<div class="pg-header">
    <div>
        <div class="pg-title">Inventory Dashboard</div>
        <div class="pg-sub">Stock levels, alerts, and purchase activity</div>
    </div>
    <div class="hdr-actions">
        <a href="{{ route('products.index') }}" class="btn-link">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            All Products
        <a href="{{ route('inventory.issue-shop-use') }}" class="btn-link" style="background:#18181b;color:#fff;border-color:#18181b;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            Issue Shop Use
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon si-yellow">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div>
            <div class="stat-val">{{ number_format($totalProducts) }}</div>
            <div class="stat-lbl">Total Products</div>
            <div class="stat-note">Inventory tracked</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-dark">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div>
            <div class="stat-val" style="font-size:1rem;">PKR {{ number_format($forSaleRetailValue,0) }}</div>
            <div class="stat-lbl">For Sale Retail Value</div>
            <div class="stat-note">Potential retail revenue</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-amber">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div>
            <div class="stat-val" style="font-size:1rem;">PKR {{ number_format($forSaleCostValue,0) }}</div>
            <div class="stat-lbl">For Sale Cost</div>
            <div class="stat-note">Cost of retail goods</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#e0e7ff;color:#4338ca;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div>
            <div class="stat-val" style="font-size:1rem;">PKR {{ number_format($shopUseCostValue,0) }}</div>
            <div class="stat-lbl">Shop Use Cost</div>
            <div class="stat-note">Cost of supplies</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-red">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        </div>
        <div>
            <div class="stat-val">{{ $lowStockProducts->count() }}</div>
            <div class="stat-lbl">Low Stock</div>
            <div class="stat-note">Need attention</div>
        </div>
    </div>
</div>

{{-- Low stock alerts --}}
@if($lowStockProducts->count())
<div class="alert-card">
    <div class="alert-head">
        <div class="alert-title">
            <div class="alert-icon"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg></div>
            Low Stock Alerts
        </div>
        <a href="{{ route('inventory.low-stock') }}" class="alert-link">View All →</a>
    </div>
    <div class="stock-list">
        @foreach($lowStockProducts->take(5) as $product)
        <div class="stock-item {{ $product->current_stock<=0?'out':'' }}">
            <div>
                <div class="stock-name">{{ $product->name }}</div>
                <div class="stock-detail">Stock: {{ $product->current_stock }} · Min: {{ $product->min_stock_level }}{{ $product->supplier?' · '.$product->supplier->name:'' }}</div>
            </div>
            <span class="stock-badge {{ $product->current_stock<=0?'sb-out':'sb-low' }}">
                {{ $product->current_stock<=0?'Out of Stock':'Low Stock' }}
            </span>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Out of stock --}}
@if($outOfStockProducts->count())
<div class="alert-card">
    <div class="alert-head">
        <div class="alert-title">
            <div class="alert-icon" style="background:#fee2e2;color:#dc2626;"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></div>
            Out of Stock
        </div>
        <a href="{{ route('inventory.stock-report',['status'=>'out_of_stock']) }}" class="alert-link">View All →</a>
    </div>
    <div class="stock-list">
        @foreach($outOfStockProducts->take(3) as $product)
        <div class="stock-item out">
            <div>
                <div class="stock-name">{{ $product->name }}</div>
                <div class="stock-detail">Min level: {{ $product->min_stock_level }}{{ $product->supplier?' · '.$product->supplier->name:'' }}</div>
            </div>
            <span class="stock-badge sb-out">Out of Stock</span>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Bottom: recent purchases + pending deliveries --}}
<div class="bottom-grid">
    <div class="panel">
        <div class="panel-title">Recent Purchases</div>
        @if($recentPurchases->count())
        <table class="ptable">
            <thead><tr><th>PO #</th><th>Supplier</th><th>Status</th><th>Amount</th></tr></thead>
            <tbody>
            @foreach($recentPurchases as $p)
            <tr>
                <td><a href="{{ route('purchases.show',$p) }}">{{ $p->purchase_order_number }}</a></td>
                <td>{{ $p->supplier->name }}</td>
                <td><span class="status-pill {{ $p->status==='received'?'sp-received':($p->status==='ordered'?'sp-ordered':'sp-pending') }}">{{ ucfirst(str_replace('_',' ',$p->status)) }}</span></td>
                <td style="font-weight:700;color:var(--ydark);">PKR {{ number_format($p->total_amount,0) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-note">No recent purchases</div>
        @endif
    </div>

    <div class="panel">
        <div class="panel-title">Pending Deliveries</div>
        @if($pendingDeliveries->count())
        <table class="ptable">
            <thead><tr><th>PO #</th><th>Supplier</th><th>Expected</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($pendingDeliveries as $p)
            <tr>
                <td><a href="{{ route('purchases.show',$p) }}">{{ $p->purchase_order_number }}</a></td>
                <td>{{ $p->supplier->name }}</td>
                <td>{{ $p->expected_delivery_date?$p->expected_delivery_date->format('M d'):'—' }}</td>
                <td><span class="status-pill {{ $p->status==='ordered'?'sp-ordered':'sp-pending' }}">{{ ucfirst(str_replace('_',' ',$p->status)) }}</span></td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-note">No pending deliveries</div>
        @endif
    </div>
</div>

@if($topUsedProducts->count())
<div class="panel" style="margin-top:16px;">
    <div class="panel-title">Top Used Products (This Month)</div>
    <table class="ptable">
        <thead><tr><th>Product</th><th>Used</th><th>Remaining</th></tr></thead>
        <tbody>
        @foreach($topUsedProducts as $u)
        <tr>
            <td style="font-weight:600;">{{ $u->product->name }}</td>
            <td>{{ $u->total_used }}</td>
            <td style="{{ $u->product->current_stock<=$u->product->min_stock_level?'color:#dc2626;font-weight:700;':'' }}">{{ $u->product->current_stock }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
