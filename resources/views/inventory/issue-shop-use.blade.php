@extends('layouts.app')
@section('title', 'Issue Shop Use Product')
@section('content')
<style>
:root{--y1:#F7DF79;--y2:#FBEFBC;--yd:#c9a800;--ydark:#a07800;--ybg:#fffdf0;}
.pg-header{display:flex;align-items:center;gap:12px;margin-bottom:22px;}
.back-btn{width:36px;height:36px;border-radius:9px;border:1.5px solid #e4e4e7;background:#fff;display:flex;align-items:center;justify-content:center;color:#71717a;text-decoration:none;transition:.2s;flex-shrink:0;}
.back-btn:hover{border-color:var(--y1);color:var(--ydark);background:var(--ybg);}
.pg-title{font-size:1.4rem;font-weight:800;color:#18181b;letter-spacing:-.02em;margin-bottom:3px;}
.form-card{background:#fff;border:1.5px solid #f0e8a0;border-radius:16px;padding:24px;box-shadow:0 1px 6px rgba(0,0,0,.04);max-width:600px;}
.f-label{display:block;font-size:.75rem;font-weight:600;color:#374151;margin-bottom:6px;}
.f-input,.f-select{width:100%;padding:9px 12px;border:1.5px solid #f0e8a0;border-radius:9px;font-size:.875rem;color:#18181b;background:var(--ybg);margin-bottom:16px;}
.f-input:focus,.f-select:focus{border-color:var(--y1);background:#fff;outline:none;}
.btn-save{padding:9px 22px;border:none;background:linear-gradient(135deg,var(--y1),var(--yd));border-radius:9px;color:#18181b;font-size:.875rem;font-weight:800;cursor:pointer;width:100%;}
</style>

<div class="pg-header">
    <a href="{{ route('inventory.dashboard') }}" class="back-btn">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <div class="pg-title">Issue Shop Use Product</div>
    </div>
</div>

<div class="form-card">
    <form method="POST" action="{{ route('inventory.store-shop-use') }}">
        @csrf
        <label class="f-label">Select Product</label>
        <select name="product_id" class="f-select" required>
            @foreach($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->current_stock }})</option>
            @endforeach
        </select>

        <label class="f-label">Quantity to Issue</label>
        <input type="number" name="quantity" min="1" value="1" required class="f-input">

        <label class="f-label">Notes</label>
        <input type="text" name="notes" placeholder="e.g. For general cleaning" class="f-input">

        <button type="submit" class="btn-save">Issue & Add to Expense</button>
    </form>
</div>
@endsection
