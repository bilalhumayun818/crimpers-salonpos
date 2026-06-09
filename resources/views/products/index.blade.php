@extends('layouts.app')
@section('title', 'Products')
@section('content')
<style>
:root{--y1:#F7DF79;--y2:#FBEFBC;--yd:#c9a800;--ydark:#a07800;--ybg:#fffdf0;}
.pg-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;gap:16px;}
.pg-title{font-size:1.4rem;font-weight:800;color:#18181b;letter-spacing:-.02em;margin-bottom:3px;}
.pg-sub{font-size:.85rem;color:#71717a;}
.btn-add{padding:9px 18px;background:linear-gradient(135deg,var(--y1),var(--yd));color:#18181b;border:none;border-radius:10px;text-decoration:none;font-weight:700;font-size:.85rem;cursor:pointer;font-family:'Outfit',sans-serif;display:inline-flex;align-items:center;gap:7px;box-shadow:0 3px 10px rgba(247,223,121,.35);transition:.2s;}
.btn-add:hover{transform:translateY(-1px);box-shadow:0 5px 16px rgba(247,223,121,.45);}

.filters-bar{background:#fff;border:1px solid var(--y1);border-radius:14px;padding:14px 16px;margin-bottom:20px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;}
.fg{display:flex;flex-direction:column;gap:5px;flex:1;min-width:180px;}
.fg label{font-size:.65rem;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.1em;}
.fg select,.fg input{padding:8px 11px;border:1.5px solid #f0e8a0;border-radius:9px;font-size:.85rem;font-family:'Outfit',sans-serif;background:var(--ybg);color:#18181b;outline:none;transition:.2s;}
.fg select:focus,.fg input:focus{border-color:var(--y1);background:#fff;box-shadow:0 0 0 3px rgba(247,223,121,.15);}
.search-row{display:flex;gap:7px;}
.btn-search{padding:8px 14px;background:#18181b;color:#fff;border:none;border-radius:9px;font-weight:700;font-size:.82rem;cursor:pointer;display:flex;align-items:center;gap:5px;font-family:'Outfit',sans-serif;transition:.2s;}
.btn-search:hover{background:#3f3f46;}

.products-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:16px;margin-bottom:24px;}
.product-card{background:#fff;border:1.5px solid #f0e8a0;border-radius:16px;padding:18px;position:relative;transition:all .2s;display:flex;flex-direction:column;box-shadow:0 1px 4px rgba(0,0,0,.04);}
.product-card:hover{transform:translateY(-3px);border-color:var(--y1);box-shadow:0 8px 24px rgba(247,223,121,.2);}

.type-badge{position:absolute;top:16px;right:16px;padding:3px 9px;border-radius:99px;font-size:.62rem;font-weight:800;text-transform:uppercase;letter-spacing:.05em;}
.type-retail{background:var(--y2);color:var(--ydark);}
.type-service{background:#f3e8ff;color:#7c3aed;}

.p-name{font-size:1rem;font-weight:700;color:#18181b;margin-bottom:3px;line-height:1.3;padding-right:60px;}
.p-sku{font-size:.7rem;font-weight:600;color:#a1a1aa;margin-bottom:14px;display:flex;align-items:center;gap:4px;}

.card-stats{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;padding:10px;background:var(--ybg);border-radius:11px;border:1px solid #f0e8a0;}
.stat-box .lbl{font-size:.6rem;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.07em;margin-bottom:2px;}
.stat-box .val{font-size:.88rem;font-weight:700;color:#18181b;}

.price-box{margin-bottom:14px;}
.price-lbl{font-size:.62rem;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.07em;margin-bottom:2px;}
.price-val{font-size:1.3rem;font-weight:800;color:var(--ydark);letter-spacing:-.02em;}

.stock-row{display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:12px;border-top:1px solid #f4f4f5;}
.stock-num{font-size:.9rem;font-weight:800;color:#18181b;}
.stock-lbl{font-size:.62rem;color:#a1a1aa;font-weight:600;}
.stock-badge{padding:4px 10px;border-radius:8px;font-size:.7rem;font-weight:700;}
.s-good{background:var(--y2);color:var(--ydark);}
.s-low{background:#fef3c7;color:#92400e;}
.s-out{background:#fee2e2;color:#991b1b;}

.card-actions{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:12px;}
.btn-act{padding:8px;border-radius:9px;text-align:center;text-decoration:none;font-size:.78rem;font-weight:700;transition:.15s;border:1.5px solid transparent;}
.btn-view{background:#f4f4f5;color:#52525b;border-color:#e4e4e7;}
.btn-view:hover{background:#e4e4e7;color:#18181b;}
.btn-edit{background:var(--y2);color:var(--ydark);border-color:var(--y1);}
.btn-edit:hover{background:var(--y1);}

.empty-state{grid-column:1/-1;text-align:center;padding:60px 20px;background:#fff;border:1.5px dashed #f0e8a0;border-radius:16px;color:#a1a1aa;}
.empty-state svg{margin:0 auto 14px;display:block;opacity:.3;}
.empty-state p{font-size:.9rem;font-weight:500;}
</style>

<div class="pg-header">
    <div>
        <div class="pg-title">Product Management</div>
        <div class="pg-sub">Manage your retail products and service supplies</div>
    </div>
    {{-- 
    <a href="{{ route('products.create') }}" class="btn-add">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Product
    </a>
    --}}
</div>

<form method="GET" class="filters-bar">
    <div class="fg">
        <label>Product Type</label>
        <select name="type" onchange="this.form.submit()">
            <option value="all" {{ request('type','all')==='all'?'selected':'' }}>All Types</option>
            <option value="retail" {{ request('type')==='retail'?'selected':'' }}>For Sale</option>
            <option value="service_supply" {{ request('type')==='service_supply'?'selected':'' }}>Shop Use</option>
        </select>
    </div>
    <div class="fg">
        <label>Stock Status</label>
        <select name="stock_status" onchange="this.form.submit()">
            <option value="all" {{ request('stock_status','all')==='all'?'selected':'' }}>All Status</option>
            <option value="in_stock" {{ request('stock_status')==='in_stock'?'selected':'' }}>In Stock</option>
            <option value="low_stock" {{ request('stock_status')==='low_stock'?'selected':'' }}>Low Stock</option>
            <option value="out_of_stock" {{ request('stock_status')==='out_of_stock'?'selected':'' }}>Out of Stock</option>
        </select>
    </div>
    <div class="fg">
        <label>Search</label>
        <div class="search-row">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, SKU or barcode…" style="flex:1;">
            <button type="submit" class="btn-search">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                Search
            </button>
        </div>
    </div>
</form>

<div class="products-grid">
    @forelse($products as $product)
    <div class="product-card">
        <span class="type-badge {{ $product->product_type==='retail'?'type-retail':'type-service' }}">
            {{ $product->product_type==='retail' ? 'For Sale' : 'Shop Use' }}
        </span>
        <div class="p-name">{{ $product->name }}</div>
        <div class="p-sku">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            {{ $product->sku ?: 'No SKU' }}
        </div>
        <div class="price-box">
            <div class="price-lbl">Selling Price</div>
            <div class="price-val">
                PKR {{ number_format($product->selling_price, 2) }}
            </div>
        </div>
        <div class="card-stats">
            <div class="stat-box">
                <div class="lbl">Cost Price</div>
                <div class="val">{{ $product->cost_price ? 'PKR '.number_format($product->cost_price,0) : '—' }}</div>
            </div>
            <div class="stat-box">
                <div class="lbl">Supplier</div>
                <div class="val" style="font-size:.72rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $product->supplier?->name }}">
                    {{ $product->supplier?->name ?? 'Generic' }}
                </div>
            </div>
        </div>
        <div class="stock-row">
            <div>
                <div class="stock-num">{{ $product->current_stock }} units</div>
                <div class="stock-lbl">Available</div>
            </div>
            @if($product->track_inventory)
            <span class="stock-badge {{ $product->current_stock<=0?'s-out':($product->current_stock<=$product->min_stock_level?'s-low':'s-good') }}">
                {{ $product->current_stock<=0?'Out of Stock':($product->current_stock<=$product->min_stock_level?'Low Stock':'In Stock') }}
            </span>
            @endif
        </div>
        <div class="card-actions">
            <a href="{{ route('products.show',$product) }}" class="btn-act btn-view">View</a>
            <a href="{{ route('products.edit',$product) }}" class="btn-act btn-edit">Edit</a>
            <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('WARNING: Are you sure you want to delete {{ addslashes($product->name) }}?');" style="margin:0; flex:1; display:flex;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-act" style="border:1.5px solid #fca5a5; color:#dc2626; width:100%; background:transparent;">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <svg width="52" height="52" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        <p>No products found — try adjusting your filters</p>
    </div>
    @endforelse
</div>

{{ $products->links() }}
@endsection
