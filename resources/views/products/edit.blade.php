@extends('layouts.app')
@section('title', 'Edit Product')
@section('content')
<style>
:root{--y1:#F7DF79;--y2:#FBEFBC;--yd:#c9a800;--ydark:#a07800;--ybg:#fffdf0;}
.pg-header{display:flex;align-items:center;gap:12px;margin-bottom:22px;}
.back-btn{width:36px;height:36px;border-radius:9px;border:1.5px solid #e4e4e7;background:#fff;display:flex;align-items:center;justify-content:center;color:#71717a;text-decoration:none;transition:.2s;flex-shrink:0;}
.back-btn:hover{border-color:var(--y1);color:var(--ydark);background:var(--ybg);}
.pg-title{font-size:1.4rem;font-weight:800;color:#18181b;letter-spacing:-.02em;margin-bottom:3px;}
.pg-sub{font-size:.85rem;color:#71717a;}
.form-card{background:#fff;border:1.5px solid #f0e8a0;border-radius:16px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.04);margin-bottom:16px;}
.form-section{padding:20px 24px;border-bottom:1px solid #f4f4f5;}
.section-title{font-size:.72rem;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;display:flex;align-items:center;gap:7px;}
.section-icon{width:22px;height:22px;border-radius:6px;background:var(--y2);display:flex;align-items:center;justify-content:center;color:var(--ydark);}
.f-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;}
.f-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px;}
.f-grid-4{display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:14px;margin-bottom:14px;}
.f-row{margin-bottom:14px;}
.f-label{display:block;font-size:.75rem;font-weight:600;color:#374151;margin-bottom:6px;}
.f-input,.f-select,.f-textarea{width:100%;padding:9px 12px;border:1.5px solid #f0e8a0;border-radius:9px;font-size:.875rem;font-family:'Outfit',sans-serif;color:#18181b;background:var(--ybg);outline:none;transition:.2s;box-sizing:border-box;}
.f-input:focus,.f-select:focus,.f-textarea:focus{border-color:var(--y1);background:#fff;box-shadow:0 0 0 3px rgba(247,223,121,.15);}
.f-textarea{resize:vertical;min-height:80px;}
.check-row{display:flex;align-items:center;gap:8px;cursor:pointer;}
.check-row input[type=checkbox]{width:15px;height:15px;accent-color:var(--ydark);cursor:pointer;}
.check-lbl{font-size:.85rem;font-weight:600;color:#374151;}
.form-footer{padding:16px 24px;display:flex;gap:10px;justify-content:flex-end;}
.btn-cancel{padding:9px 20px;border:1.5px solid #e4e4e7;background:#fff;border-radius:9px;color:#71717a;font-size:.875rem;font-weight:600;cursor:pointer;text-decoration:none;font-family:'Outfit',sans-serif;transition:.2s;}
.btn-cancel:hover{border-color:#fca5a5;color:#dc2626;background:#fef2f2;}
.btn-save{padding:9px 22px;border:none;background:linear-gradient(135deg,var(--y1),var(--yd));border-radius:9px;color:#18181b;font-size:.875rem;font-weight:800;cursor:pointer;font-family:'Outfit',sans-serif;transition:.2s;box-shadow:0 3px 10px rgba(247,223,121,.3);}
.btn-save:hover{transform:translateY(-1px);}
.danger-zone{background:#fff;border:1px solid #fecaca;border-radius:14px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;}
.danger-title{font-size:.875rem;font-weight:700;color:#b91c1c;margin-bottom:2px;}
.danger-sub{font-size:.75rem;color:#a1a1aa;}
.btn-danger{padding:8px 16px;border:1.5px solid #fca5a5;background:#fef2f2;border-radius:9px;color:#dc2626;font-size:.8rem;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;transition:.2s;}
.btn-danger:hover{background:#fee2e2;}
</style>

<div class="pg-header">
    <a href="{{ route('products.show',$product) }}" class="back-btn">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <div class="pg-title">Edit Product</div>
        <div class="pg-sub">{{ $product->name }}</div>
    </div>
</div>

<form method="POST" action="{{ route('products.update',$product) }}">
    @csrf @method('PUT')

    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px;margin-bottom:16px;">
        <ul style="margin:0;padding-left:20px;color:#ef4444;font-size:0.85rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="form-card">

        <div class="form-section">
            <div class="section-title">
                <div class="section-icon"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
                Basic Information
            </div>
            <div class="f-grid-4">
                <div>
                    <label class="f-label">Product Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" value="{{ $product->name }}" required class="f-input">
                </div>
                <div>
                    <label class="f-label">SKU</label>
                    <input type="text" name="sku" value="{{ $product->sku }}" class="f-input" placeholder="Auto-generated if blank">
                </div>
                <div>
                    <label class="f-label">Product Type <span style="color:#ef4444;">*</span></label>
                    <select name="product_type" id="product_type" required class="f-select">
                        <option value="retail" {{ $product->product_type==='retail'?'selected':'' }}>For Sale</option>
                        <option value="service_supply" {{ $product->product_type==='service_supply'?'selected':'' }}>Shop Use</option>
                    </select>
                </div>
                <div>
                    <label class="f-label">Category</label>
                    <select name="category_id" class="f-select">
                        <option value="">No Category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $product->category_id==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="f-row">
                <label class="f-label">Description</label>
                <textarea name="description" class="f-textarea">{{ $product->description }}</textarea>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title">
                <div class="section-icon"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
                Pricing &amp; Inventory
            </div>
            <div class="f-grid-3" style="margin-bottom:14px;">
                <div id="selling_price_wrapper">
                    <label class="f-label">Selling Price (PKR) <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="selling_price" id="selling_price" min="0" step="0.01" value="{{ $product->selling_price }}" required class="f-input">
                </div>
                <div>
                    <label class="f-label">Cost Price (PKR)</label>
                    <input type="number" name="cost_price" min="0" step="0.01" value="{{ $product->cost_price }}" class="f-input" placeholder="Purchase cost">
                </div>
                <div>
                    <label class="f-label">Current Stock <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="current_stock" min="0" step="1" value="{{ $product->current_stock }}" required class="f-input">
                </div>
            </div>
            <div class="f-grid-2" style="margin-bottom:14px;">
                <div>
                    <label class="f-label">Min Stock Level</label>
                    <input type="number" name="min_stock_level" min="0" step="1" value="{{ $product->min_stock_level }}" class="f-input" placeholder="Low stock threshold">
                </div>
                <div>
                    <label class="f-label">Supplier</label>
                    <select name="supplier_id" class="f-select">
                        <option value="">No Supplier</option>
                        @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ $product->supplier_id==$sup->id?'selected':'' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <label class="check-row">
                <input type="checkbox" name="track_inventory" {{ $product->track_inventory?'checked':'' }}>
                <span class="check-lbl">Track inventory for this product</span>
            </label>
        </div>

        <div class="form-footer">
            <a href="{{ route('products.show',$product) }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-save">Save Changes</button>
        </div>
    </div>
</form>

<div class="danger-zone">
    <div>
        <div class="danger-title">Delete Product</div>
        <div class="danger-sub">This action cannot be undone</div>
    </div>
    <form method="POST" action="{{ route('products.destroy',$product) }}" onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn-danger">Delete Product</button>
    </form>
</div>

<script>
    const productType = document.getElementById('product_type');
    const sellingPriceWrapper = document.getElementById('selling_price_wrapper');
    const sellingPriceInput = document.getElementById('selling_price');

    productType.addEventListener('change', function() {
        if(this.value === 'service_supply') {
            sellingPriceWrapper.style.display = 'none';
            sellingPriceInput.required = false;
            if (!sellingPriceInput.value) sellingPriceInput.value = '0';
        } else {
            sellingPriceWrapper.style.display = 'block';
            sellingPriceInput.required = true;
        }
    });
    
    productType.dispatchEvent(new Event('change'));
</script>
@endsection
