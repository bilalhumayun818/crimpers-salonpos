@extends('layouts.app')
@section('title', 'Products')
@section('content')
<style>
:root{--y1:#F7DF79;--y2:#FBEFBC;--yd:#c9a800;--ydark:#a07800;--ybg:#fffdf0;}

/* ── Page header ── */
.pg-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;gap:16px;flex-wrap:wrap;}
.pg-title{font-size:1.35rem;font-weight:800;color:#18181b;letter-spacing:-.02em;margin-bottom:3px;}
.pg-sub{font-size:.82rem;color:#71717a;}

/* ── Filters bar ── */
.filters-bar{background:#fff;border:1px solid var(--y1);border-radius:14px;padding:14px 16px;margin-bottom:18px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;}
.fg{display:flex;flex-direction:column;gap:5px;flex:1;min-width:160px;}
.fg label{font-size:.62rem;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.1em;}
.fg select,.fg input{padding:8px 10px;border:1.5px solid #f0e8a0;border-radius:9px;font-size:.84rem;font-family:'Outfit',sans-serif;background:var(--ybg);color:#18181b;outline:none;transition:.2s;}
.fg select:focus,.fg input:focus{border-color:var(--y1);background:#fff;box-shadow:0 0 0 3px rgba(247,223,121,.15);}
.search-row{display:flex;gap:7px;}
.btn-search{padding:8px 14px;background:#18181b;color:#fff;border:none;border-radius:9px;font-weight:700;font-size:.82rem;cursor:pointer;display:flex;align-items:center;gap:5px;font-family:'Outfit',sans-serif;transition:.2s;white-space:nowrap;}
.btn-search:hover{background:#3f3f46;}

/* ── Summary chips ── */
.summary-chips{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;}
.chip{padding:6px 14px;border-radius:99px;font-size:.75rem;font-weight:700;border:1.5px solid;display:inline-flex;align-items:center;gap:6px;}
.chip-all{background:#f4f4f5;border-color:#e4e4e7;color:#52525b;}
.chip-good{background:var(--y2);border-color:var(--y1);color:var(--ydark);}
.chip-low{background:#fef3c7;border-color:#fde68a;color:#92400e;}
.chip-out{background:#fee2e2;border-color:#fca5a5;color:#991b1b;}

/* ── Table wrapper ── */
.tbl-wrap{background:#fff;border:1.5px solid var(--y1);border-radius:16px;overflow:hidden;box-shadow:0 2px 12px rgba(247,223,121,.12);}
.tbl-scroll{overflow-x:auto;}

/* ── Excel-style table ── */
.products-tbl{width:100%;border-collapse:collapse;min-width:860px;}
.products-tbl thead tr{background:linear-gradient(90deg,#18181b 0%,#27272a 100%);}
.products-tbl thead th{padding:11px 14px;font-size:.65rem;font-weight:800;color:#d4d4d8;text-transform:uppercase;letter-spacing:.09em;text-align:left;white-space:nowrap;border-right:1px solid #3f3f46;}
.products-tbl thead th:last-child{border-right:none;}
.products-tbl thead th.th-center{text-align:center;}

/* Row styles */
.products-tbl tbody tr{border-bottom:1px solid #fef9e7;transition:background .13s;}
.products-tbl tbody tr:nth-child(even){background:#fffdf8;}
.products-tbl tbody tr:hover{background:#fffbeb;}
.products-tbl tbody tr:last-child{border-bottom:none;}
.products-tbl td{padding:10px 14px;font-size:.84rem;color:#27272a;vertical-align:middle;border-right:1px solid #f0e8a0;}
.products-tbl td:last-child{border-right:none;}
.products-tbl td.td-center{text-align:center;}

/* Row number column */
.col-num{width:42px;color:#a1a1aa;font-size:.72rem;font-weight:700;text-align:center;}

/* Product name cell */
.pname{font-weight:700;color:#18181b;font-size:.88rem;line-height:1.3;}
.psku{font-size:.68rem;color:#a1a1aa;font-weight:600;margin-top:1px;display:flex;align-items:center;gap:3px;}

/* Type badge */
.type-badge{display:inline-flex;padding:2px 8px;border-radius:99px;font-size:.62rem;font-weight:800;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap;}
.type-retail{background:var(--y2);color:var(--ydark);}
.type-service{background:#f3e8ff;color:#7c3aed;}

/* Price / stock cells */
.price-cell{font-weight:700;color:#18181b;font-size:.88rem;}
.cost-cell{font-size:.82rem;color:#52525b;}
.stock-val{font-size:.95rem;font-weight:800;color:#18181b;}

/* Stock status badge */
.stock-badge{display:inline-flex;padding:3px 9px;border-radius:8px;font-size:.68rem;font-weight:700;white-space:nowrap;}
.s-good{background:var(--y2);color:var(--ydark);}
.s-low{background:#fef3c7;color:#92400e;}
.s-out{background:#fee2e2;color:#991b1b;}

/* Actions column */
.actions-cell{display:flex;align-items:center;gap:5px;flex-wrap:nowrap;}
.btn-act{padding:5px 10px;border-radius:7px;text-align:center;text-decoration:none;font-size:.72rem;font-weight:700;transition:.15s;border:1.5px solid transparent;cursor:pointer;white-space:nowrap;font-family:'Outfit',sans-serif;display:inline-flex;align-items:center;gap:4px;}
.btn-view{background:#f4f4f5;color:#52525b;border-color:#e4e4e7;}
.btn-view:hover{background:#e4e4e7;color:#18181b;}
.btn-edit{background:var(--y2);color:var(--ydark);border-color:var(--y1);}
.btn-edit:hover{background:var(--y1);}
.btn-stock{background:linear-gradient(135deg,#18181b,#3f3f46);color:#F7DF79;border-color:#18181b;}
.btn-stock:hover{background:linear-gradient(135deg,#3f3f46,#52525b);box-shadow:0 3px 8px rgba(0,0,0,.18);}
.btn-issue{background:#f3e8ff;color:#7c3aed;border-color:#e9d5ff;}
.btn-issue:hover{background:#e9d5ff;}
.btn-del{background:transparent;color:#dc2626;border-color:#fca5a5;}
.btn-del:hover{background:#fee2e2;}

/* ── Modals ── */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);display:none;align-items:center;justify-content:center;z-index:999;padding:20px;}
.modal-box{background:#fff;border-radius:18px;width:100%;max-width:420px;padding:26px;box-shadow:0 20px 50px rgba(0,0,0,.2);}
.modal-title{font-size:1.15rem;font-weight:800;color:#18181b;margin-bottom:16px;}
.modal-form-group{margin-bottom:14px;text-align:left;}
.modal-form-group label{display:block;font-size:.75rem;font-weight:700;color:#52525b;margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em;}
.modal-form-group input, .modal-form-group textarea {width:100%;padding:10px 14px;border:1.5px solid #e4e4e7;border-radius:9px;font-size:.9rem;font-family:'Outfit',sans-serif;box-sizing:border-box;}
.modal-form-group input:focus, .modal-form-group textarea:focus {border-color:var(--y1);outline:none;box-shadow:0 0 0 3px rgba(247,223,121,.15);}
.modal-footer{display:flex;gap:10px;margin-top:20px;}
.btn-m{flex:1;padding:12px;border-radius:9px;font-size:.9rem;font-weight:700;cursor:pointer;transition:.2s;border:none;font-family:'Outfit',sans-serif;}
.btn-m-cancel{background:#f4f4f5;color:#52525b;}
.btn-m-cancel:hover{background:#e4e4e7;}
.btn-m-confirm{background:var(--y2);color:var(--ydark);border:1.5px solid var(--y1);}
.btn-m-confirm:hover{background:var(--y1);}

/* Empty state */
.empty-state{padding:70px 20px;text-align:center;color:#a1a1aa;}
.empty-state svg{margin:0 auto 14px;display:block;opacity:.3;}
.empty-state p{font-size:.9rem;font-weight:500;}

/* Pagination */
.pag-wrap{padding:12px 16px;border-top:1px solid #f0e8a0;}

/* Responsive hint */
@media(max-width:768px){
  .pg-header{flex-direction:column;}
  .chip-label{display:none;}
}
</style>

{{-- Header --}}
<div class="pg-header">
    <div>
        <div class="pg-title">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" style="display:inline;vertical-align:-.2em;margin-right:6px;"><path d="M3 3h18v4H3zM3 10h18v4H3zM3 17h18v4H3z"/></svg>
            Product Management
        </div>
        <div class="pg-sub">All products in spreadsheet view — click <strong>Adjust Stock</strong> on any row to update quantity</div>
    </div>
</div>

{{-- Filters --}}
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
        <label>Category</label>
        <select name="category_id" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
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
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                Search
            </button>
        </div>
    </div>
</form>

{{-- Summary chips --}}
@php
    $total   = $products->total() ?? $products->count();
    $allProd = \App\Models\Product::query();
    $good    = (clone $allProd)->whereRaw('current_stock > min_stock_level')->count();
    $low     = (clone $allProd)->whereRaw('current_stock > 0 AND current_stock <= min_stock_level')->count();
    $out     = (clone $allProd)->where('current_stock', '<=', 0)->count();
@endphp
<div class="summary-chips">
    <span class="chip chip-all">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
        {{ $total }} Products
    </span>
    <span class="chip chip-good">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        {{ $good }} In Stock
    </span>
    <span class="chip chip-low">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        {{ $low }} Low Stock
    </span>
    <span class="chip chip-out">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        {{ $out }} Out of Stock
    </span>
</div>

{{-- Table --}}
<div class="tbl-wrap">
    <div class="tbl-scroll">
        <table class="products-tbl">
            <thead>
                <tr>
                    <th class="col-num th-center">#</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Supplier</th>
                    <th class="th-center">Sell Price</th>
                    <th class="th-center">Cost Price</th>
                    <th class="th-center">Stock</th>
                    <th class="th-center">Status</th>
                    <th class="th-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $i => $product)
                <tr>
                    {{-- Row number --}}
                    <td class="col-num td-center">{{ $products->firstItem() + $i }}</td>

                    {{-- Product name + SKU --}}
                    <td>
                        <div class="pname">{{ $product->name }}</div>
                        <div class="psku">
                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                            {{ $product->sku ?: 'No SKU' }}
                        </div>
                    </td>

                    {{-- Type --}}
                    <td>
                        <span class="type-badge {{ $product->product_type==='retail'?'type-retail':'type-service' }}">
                            {{ $product->product_type==='retail' ? 'For Sale' : 'Shop Use' }}
                        </span>
                    </td>

                    {{-- Supplier --}}
                    <td style="font-size:.8rem;color:#52525b;">{{ $product->supplier?->name ?? 'Generic' }}</td>

                    {{-- Sell Price --}}
                    <td class="td-center price-cell">PKR {{ number_format($product->selling_price, 0) }}</td>

                    {{-- Cost Price --}}
                    <td class="td-center cost-cell">{{ $product->cost_price ? 'PKR '.number_format($product->cost_price,0) : '—' }}</td>

                    {{-- Stock qty --}}
                    <td class="td-center">
                        <span class="stock-val">{{ $product->current_stock }}</span>
                        <span style="font-size:.65rem;color:#a1a1aa;margin-left:2px;">units</span>
                    </td>

                    {{-- Stock status --}}
                    <td class="td-center">
                        @if($product->track_inventory)
                            <span class="stock-badge {{ $product->current_stock<=0?'s-out':($product->current_stock<=$product->min_stock_level?'s-low':'s-good') }}">
                                {{ $product->current_stock<=0?'Out of Stock':($product->current_stock<=$product->min_stock_level?'Low Stock':'In Stock') }}
                            </span>
                        @else
                            <span style="font-size:.72rem;color:#a1a1aa;font-weight:600;">No Tracking</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="td-center">
                        <div class="actions-cell" style="justify-content:center;">
                            {{-- Adjust Stock --}}
                            <a href="{{ route('products.adjust-stock.form', $product) }}" class="btn-act btn-stock">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                                Adjust Stock
                            </a>
                            {{-- Issue --}}
                            <button type="button" class="btn-act btn-issue" onclick="openIssueModal({{ $product->id }}, '{{ htmlspecialchars(addslashes($product->name)) }}', {{ $product->current_stock }})">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                                Issue for Shop
                            </button>
                            {{-- View --}}
                            <a href="{{ route('products.show', $product) }}" class="btn-act btn-view">View</a>
                            {{-- Edit --}}
                            <a href="{{ route('products.edit', $product) }}" class="btn-act btn-edit">Edit</a>
                            {{-- Delete --}}
                            <form method="POST" action="{{ route('products.destroy', $product) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($product->name) }}? This cannot be undone.');"
                                  style="margin:0;display:inline-flex;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-act btn-del">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <svg width="52" height="52" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p>No products found — try adjusting your filters</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pag-wrap">
        {{ $products->links() }}
    </div>
</div>

{{-- Issue Shop Use Modal --}}
<div class="modal-overlay" id="issueModal">
    <div class="modal-box">
        <div class="modal-title" id="issueModalTitle">Issue Product</div>
        <form method="POST" action="{{ route('inventory.store-shop-use') }}">
            @csrf
            <input type="hidden" name="product_id" id="issueProductId">
            
            <div class="modal-form-group">
                <label>Available Stock</label>
                <div id="issueStockText" style="font-weight:800;color:#18181b;font-size:1.1rem;">0 units</div>
            </div>

            <div class="modal-form-group">
                <label>Quantity to Issue</label>
                <input type="number" name="quantity" min="1" step="0.01" required placeholder="e.g. 1">
            </div>

            <div class="modal-form-group">
                <label>Notes / Reason</label>
                <textarea name="notes" rows="2" placeholder="e.g. Used for hair coloring station 2..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-m btn-m-cancel" onclick="closeIssueModal()">Cancel</button>
                <button type="submit" class="btn-m btn-m-confirm">Confirm Issue</button>
            </div>
        </form>
    </div>
</div>

<script>
function openIssueModal(id, name, stock) {
    document.getElementById('issueProductId').value = id;
    document.getElementById('issueModalTitle').textContent = 'Issue: ' + name;
    document.getElementById('issueStockText').textContent = stock + ' units';
    document.getElementById('issueModal').style.display = 'flex';
}
function closeIssueModal() {
    document.getElementById('issueModal').style.display = 'none';
}
</script>
@endsection
