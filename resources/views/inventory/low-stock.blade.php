@extends('layouts.app')
@section('title', 'Low Stock Alerts')

@section('content')
<style>
.page-header{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px;padding:16px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;}
.page-header h2{font-size:1.25rem;font-weight:700;color:#1e293b;margin:0;}

.table-wrap{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:24px;}
.table-head{background:#f8fafc;padding:12px 16px;border-bottom:1px solid #e2e8f0;display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr 1fr 1fr;gap:12px;font-size:.8rem;font-weight:700;color:#64748b;text-transform:uppercase;}
.table-row{padding:12px 16px;border-bottom:1px solid #f1f5f9;display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr 1fr 1fr;gap:12px;align-items:center;}
.table-row:last-child{border-bottom:none;}

.product-name{font-weight:600;color:#1e293b;}
.supplier-name{font-size:.85rem;color:#64748b;}
.stock-badge{display:inline-block;padding:4px 10px;border-radius:99px;font-size:.75rem;font-weight:600;}
.stock-low{background:#fef3c7;color:#b45309;}
.stock-out{background:#fee2e2;color:#991b1b;}

.action-btns{display:flex;gap:6px;}
.btn-restock{padding:5px 10px;background:#22c55e;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600;font-size:.75rem;font-family:'Outfit',sans-serif;}
.btn-view{padding:5px 10px;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:6px;text-decoration:none;font-weight:600;font-size:.75rem;font-family:'Outfit',sans-serif;}

.empty-msg{text-align:center;padding:40px 20px;color:#94a3b8;font-size:.9rem;}
</style>

<div class="page-header">
    <h2>Low Stock Alerts</h2>
</div>

@if($lowStockProducts->count())
<div class="table-wrap">
    <div class="table-head">
        <span>Product</span>
        <span>Current Stock</span>
        <span>Min Level</span>
        <span>Supplier</span>
        <span>Type</span>
        <span>Status</span>
        <span>Actions</span>
    </div>
    @foreach($lowStockProducts as $product)
    <div class="table-row">
        <div>
            <div class="product-name">{{ $product->name }}</div>
            @if($product->sku)
            <div style="font-size:.75rem;color:#94a3b8;">SKU: {{ $product->sku }}</div>
            @endif
        </div>
        <span style="font-weight:600;{{ $product->current_stock <= 0 ? 'color:#dc2626;' : 'color:#d97706;' }}">{{ $product->current_stock }}</span>
        <span>{{ $product->min_stock_level }}</span>
        <span class="supplier-name">{{ $product->supplier ? $product->supplier->name : '—' }}</span>
        <span>{{ ucfirst(str_replace('_', ' ', $product->product_type)) }}</span>
        <span>
            <span class="stock-badge {{ $product->current_stock <= 0 ? 'stock-out' : 'stock-low' }}">
                {{ $product->current_stock <= 0 ? 'Out of Stock' : 'Low Stock' }}
            </span>
        </span>
        <span class="action-btns">
            <a href="{{ route('products.show', $product) }}" class="btn-view">View</a>
            <a href="{{ route('products.adjust-stock.form', $product) }}" class="btn-restock" style="background:#f59e0b;">Adjust Stock</a>
        </span>
    </div>
    @endforeach
</div>

{{ $lowStockProducts->links() }}
@else
<div class="empty-msg">
    <svg width="52" height="52" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 16px;opacity:.3;"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    All products are sufficiently stocked!
</div>
@endif
@endsection