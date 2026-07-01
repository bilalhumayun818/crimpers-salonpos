@extends('layouts.app')
@section('title', $product->name . ' — Adjust Stock')
@section('content')
<style>
:root{--y1:#F7DF79;--y2:#FBEFBC;--yd:#c9a800;--ydark:#a07800;--ybg:#fffdf0;}

/* ── Page layout ── */
.adj-page{max-width:620px;margin:0 auto;}

/* ── Back button ── */
.btn-back{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#f4f4f5;color:#52525b;border:1.5px solid #e4e4e7;border-radius:9px;text-decoration:none;font-weight:600;font-size:.8rem;font-family:'Outfit',sans-serif;transition:.15s;margin-bottom:20px;}
.btn-back:hover{background:#e4e4e7;color:#18181b;}

/* ── Hero header ── */
.adj-hero{background:#fff;border:1.5px solid var(--y1);border-radius:18px;padding:24px 26px 20px;margin-bottom:20px;position:relative;overflow:hidden;}
.adj-hero::before{content:'';position:absolute;top:-30px;right:-30px;width:140px;height:140px;background:radial-gradient(circle,rgba(247,223,121,.22) 0%,transparent 70%);pointer-events:none;}
.adj-label{font-size:.65rem;font-weight:800;color:#a1a1aa;text-transform:uppercase;letter-spacing:.12em;margin-bottom:6px;display:flex;align-items:center;gap:5px;}
.adj-label svg{opacity:.6;}
.adj-product-name{font-size:1.75rem;font-weight:900;color:#18181b;line-height:1.15;letter-spacing:-.03em;margin-bottom:8px;}
.adj-meta{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.meta-pill{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:99px;font-size:.7rem;font-weight:700;}
.meta-sku{background:#f4f4f5;color:#52525b;border:1px solid #e4e4e7;}
.meta-type-retail{background:var(--y2);color:var(--ydark);border:1px solid var(--y1);}
.meta-type-service{background:#f3e8ff;color:#7c3aed;border:1px solid #e9d5ff;}

/* ── Stock display bar ── */
.stock-bar{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;}
.stock-tile{background:#fff;border-radius:14px;padding:16px 18px;border:1.5px solid;}
.stock-tile.current{border-color:var(--y1);background:var(--ybg);}
.stock-tile.minimum{border-color:#e4e4e7;background:#fafafa;}
.tile-lbl{font-size:.62rem;font-weight:800;color:#a1a1aa;text-transform:uppercase;letter-spacing:.09em;margin-bottom:4px;}
.tile-val{font-size:2.2rem;font-weight:900;color:#18181b;line-height:1;letter-spacing:-.04em;}
.tile-unit{font-size:.72rem;color:#a1a1aa;font-weight:600;margin-top:2px;}

/* ── Alerts ── */
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-weight:600;font-size:.88rem;display:flex;align-items:center;gap:8px;}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-weight:600;font-size:.88rem;display:flex;align-items:center;gap:8px;}

/* ── Form card ── */
.adj-card{background:#fff;border:1.5px solid #e4e4e7;border-radius:16px;padding:26px;}
.card-title{font-size:.82rem;font-weight:800;color:#18181b;text-transform:uppercase;letter-spacing:.07em;margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid #f0e8a0;display:flex;align-items:center;gap:7px;}

/* ── Type selector ── */
.section-lbl{font-size:.65rem;font-weight:800;color:#a1a1aa;text-transform:uppercase;letter-spacing:.09em;margin-bottom:10px;}
.type-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:20px;}
.type-btn{padding:13px 8px;border-radius:12px;border:1.5px solid #e4e4e7;background:#f8fafc;font-size:.8rem;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;color:#64748b;transition:.18s;display:flex;flex-direction:column;align-items:center;gap:6px;}
.type-btn svg{width:22px;height:22px;stroke-width:1.8;}
.type-btn:hover{border-color:var(--y1);color:var(--ydark);background:var(--ybg);}
.type-btn.active{background:var(--y2);border-color:var(--yd);color:var(--ydark);box-shadow:0 2px 8px rgba(247,223,121,.35);}

/* ── Field ── */
.field-group{margin-bottom:18px;}
.field-label{display:block;font-size:.65rem;font-weight:800;color:#a1a1aa;text-transform:uppercase;letter-spacing:.09em;margin-bottom:8px;}
.field-input{width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid #e4e4e7;font-size:.95rem;font-family:'Outfit',sans-serif;color:#18181b;background:#f8fafc;outline:none;transition:.2s;box-sizing:border-box;}
.field-input:focus{border-color:var(--y1);background:#fff;box-shadow:0 0 0 3px rgba(247,223,121,.18);}

/* ── Live preview ── */
.preview-box{background:linear-gradient(135deg,#fffdf0,#fff);border:1.5px solid var(--y1);border-radius:12px;padding:14px 18px;margin-bottom:18px;display:none;align-items:center;justify-content:space-between;}
.preview-box.show{display:flex;}
.preview-old{font-size:.82rem;color:#71717a;}
.preview-old strong{color:#18181b;}
.preview-arrow{color:#d4d4d8;font-size:1.2rem;}
.preview-new-lbl{font-size:.6rem;color:#a1a1aa;text-transform:uppercase;letter-spacing:.08em;}
.preview-new-val{font-size:1.5rem;font-weight:900;letter-spacing:-.03em;}

/* ── Submit ── */
.btn-submit{width:100%;padding:14px;border-radius:12px;border:none;cursor:pointer;background:linear-gradient(135deg,#18181b,#3f3f46);color:var(--y1);font-size:.95rem;font-weight:800;font-family:'Outfit',sans-serif;transition:.22s;display:flex;align-items:center;justify-content:center;gap:8px;letter-spacing:.02em;}
.btn-submit:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,0,0,.25);}
</style>

<div class="adj-page">

    {{-- Back --}}
    <a href="{{ route('products.index') }}" class="btn-back">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Back to Products
    </a>

    {{-- Hero: product identity --}}
    <div class="adj-hero">
        <div class="adj-label">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Adjust Stock
        </div>
        <div class="adj-product-name">{{ $product->name }}</div>
        <div class="adj-meta">
            @if($product->sku)
            <span class="meta-pill meta-sku">
                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                {{ $product->sku }}
            </span>
            @endif
            <span class="meta-pill {{ $product->product_type==='retail' ? 'meta-type-retail' : 'meta-type-service' }}">
                {{ $product->product_type==='retail' ? 'For Sale' : 'Shop Use' }}
            </span>
            @if($product->supplier)
            <span class="meta-pill meta-sku">{{ $product->supplier->name }}</span>
            @endif
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Stock tiles --}}
    <div class="stock-bar">
        <div class="stock-tile current">
            <div class="tile-lbl">Current Stock</div>
            <div class="tile-val">{{ $product->current_stock }}</div>
            <div class="tile-unit">units available</div>
        </div>
        <div class="stock-tile minimum">
            <div class="tile-lbl">Minimum Level</div>
            <div class="tile-val">{{ $product->min_stock_level ?? '—' }}</div>
            <div class="tile-unit">reorder threshold</div>
        </div>
    </div>

    {{-- Form --}}
    <div class="adj-card">
        <div class="card-title">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Stock Adjustment
        </div>

        <form method="POST" action="{{ route('products.adjust-stock', $product) }}" id="adj-form">
            @csrf

            {{-- Adjustment type --}}
            <div class="section-lbl">Adjustment Type</div>
            <div class="type-grid">
                <button type="button" class="type-btn active" data-type="add" onclick="selectType(this)">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    Add Stock
                </button>
                <button type="button" class="type-btn" data-type="subtract" onclick="selectType(this)">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    Remove
                </button>
                <button type="button" class="type-btn" data-type="set" onclick="selectType(this)">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Set Value
                </button>
            </div>
            <input type="hidden" name="adjustment_type" id="adjustment_type" value="add">

            {{-- Quantity --}}
            <div class="field-group">
                <label class="field-label" for="quantity">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="field-input" min="0"
                       value="{{ old('quantity', 0) }}" required placeholder="Enter quantity">
            </div>

            {{-- Live preview --}}
            <div class="preview-box" id="preview-box">
                <div>
                    <div class="preview-old">Current: <strong>{{ $product->current_stock }}</strong> units</div>
                </div>
                <span class="preview-arrow">→</span>
                <div style="text-align:right;">
                    <div class="preview-new-lbl">New Stock</div>
                    <div class="preview-new-val" id="preview-new">{{ $product->current_stock }}</div>
                </div>
            </div>

            {{-- Reason --}}
            <div class="field-group">
                <label class="field-label" for="reason">Reason / Notes</label>
                <input type="text" name="reason" id="reason" class="field-input"
                       value="{{ old('reason') }}" required
                       placeholder="e.g. New delivery, Damaged goods, Manual correction…">
            </div>

            <button type="submit" class="btn-submit">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                Apply Stock Adjustment
            </button>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
var currentStock = {{ $product->current_stock }};
var selectedType = 'add';

function selectType(btn) {
    document.querySelectorAll('.type-btn').forEach(function(b){ b.classList.remove('active'); });
    btn.classList.add('active');
    selectedType = btn.getAttribute('data-type');
    document.getElementById('adjustment_type').value = selectedType;
    updatePreview();
}

function updatePreview() {
    var qty    = parseInt(document.getElementById('quantity').value) || 0;
    var newVal = currentStock;
    if (selectedType === 'add')      newVal = currentStock + qty;
    if (selectedType === 'subtract') newVal = Math.max(0, currentStock - qty);
    if (selectedType === 'set')      newVal = qty;

    var box   = document.getElementById('preview-box');
    var numEl = document.getElementById('preview-new');

    if (qty > 0 || selectedType === 'set') {
        box.classList.add('show');
        numEl.textContent = newVal;
        numEl.style.color = newVal > currentStock ? '#16a34a'
                          : newVal < currentStock ? '#ef4444'
                          : '#18181b';
    } else {
        box.classList.remove('show');
    }
}

document.getElementById('quantity').addEventListener('input', updatePreview);
</script>
@endpush