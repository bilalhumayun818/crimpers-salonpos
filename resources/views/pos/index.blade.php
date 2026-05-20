@extends('layouts.app')
@section('title', 'POS Terminal')
@section('content')
  <style>
    .pos-wrap {
      display: flex;
      gap: 18px;
      min-height: calc(100vh - 115px);
      margin-top: 0;
      align-items: flex-start;
    }

    .pos-left {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 12px;
      min-width: 0;
      position: sticky;
      top: 66px;
      height: calc(100vh - 130px);
    }

    /* Search bar */
    .pos-search-bar {
      background: #fff;
      border: 1px solid #f0e8a0;
      border-radius: 14px;
      padding: 12px 16px;
      box-shadow: 0 1px 4px rgba(247, 223, 121, .12);
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .search-row {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .search-wrap {
      position: relative;
      flex: 1;
    }

    .search-wrap svg {
      position: absolute;
      left: 13px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      pointer-events: none;
    }

    .pos-search {
      width: 100%;
      padding: 10px 14px 10px 40px;
      border: 1.5px solid #f0e8a0;
      border-radius: 11px;
      background: #fffdf8;
      font-size: .9rem;
      color: #1e293b;
      font-family: 'Outfit', sans-serif;
      outline: none;
      transition: .2s;
    }

    .pos-search:focus {
      border-color: #F7DF79;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(247, 223, 121, .1);
    }

    .search-clear {
      width: 34px;
      height: 34px;
      border-radius: 99px;
      border: 1.5px solid #e2e8f0;
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: #94a3b8;
      transition: .2s;
      flex-shrink: 0;
    }

    .search-clear:hover {
      border-color: #fca5a5;
      color: #ef4444;
      background: #fef2f2;
    }

    /* Filter tabs */
    .filter-row {
      display: flex;
      gap: 7px;
      overflow-x: auto;
      padding-bottom: 2px;
    }

    .filter-row::-webkit-scrollbar {
      height: 0;
    }

    .cat-tab {
      padding: 8px 16px;
      border-radius: 99px;
      font-size: .8rem;
      font-weight: 700;
      border: 1.5px solid #e2e8f0;
      background: #fff;
      color: #64748b;
      cursor: pointer;
      white-space: nowrap;
      transition: .2s;
      font-family: 'Outfit', sans-serif;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .cat-tab:hover {
      border-color: #F7DF79;
      color: #c9a800;
      background: #fffdf0;
    }

    .cat-tab.active {
      background: linear-gradient(135deg, #F7DF79, #c9a800);
      border-color: #c9a800;
      color: #18181b;
      box-shadow: 0 2px 8px rgba(201, 168, 0, .2);
    }

    .cat-tab.pkg-tab.active {
      background: linear-gradient(135deg, #334155, #1e293b);
      border-color: #1e293b;
      color: #fff;
      box-shadow: 0 2px 8px rgba(30, 41, 59, .2);
    }

    .cat-tab.prod-tab.active {
      background: linear-gradient(135deg, #fef08a, #ca8a04);
      border-color: #ca8a04;
      color: #18181b;
      box-shadow: 0 2px 8px rgba(202, 138, 4, .2);
    }

    .tab-count {
      font-size: .65rem;
      opacity: .75;
      font-weight: 700;
    }

    /* Items grid */
    .items-scroll {
      flex: 1;
      overflow-y: auto;
      padding-right: 4px;
    }

    .items-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
      gap: 11px;
    }

    .item-card {
      background: #fff;
      border: 1.5px solid #f0e8a0;
      border-radius: 14px;
      padding: 14px 13px;
      cursor: pointer;
      transition: all .2s;
      position: relative;
      box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
    }

    .item-card:hover {
      border-color: #F7DF79;
      box-shadow: 0 4px 14px rgba(247, 223, 121, .25);
      transform: translateY(-1px);
    }

    .item-card.pkg-card {
      border-color: #cbd5e1;
      background: #f8fafc;
    }

    .item-card.pkg-card:hover {
      border-color: #94a3b8;
      box-shadow: 0 4px 14px rgba(0, 0, 0, .08);
      background: #f1f5f9;
    }
    
    .item-card.prod-card {
      border-color: #fef08a;
      background: #fefce8;
    }
    
    .item-card.prod-card:hover {
      border-color: #ca8a04;
      box-shadow: 0 4px 14px rgba(202, 138, 4, .2);
    }

    .item-icon {
      width: 44px;
      height: 44px;
      border-radius: 11px;
      background: #fffdf0;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 10px;
      color: #c9a800;
    }

    .item-card.pkg-card .item-icon {
      background: #e2e8f0;
      color: #1e293b;
    }
    
    .item-card.prod-card .item-icon {
      background: #fef08a;
      color: #ca8a04;
    }

    .item-badge {
      position: absolute;
      top: 9px;
      right: 9px;
      font-size: .58rem;
      font-weight: 700;
      padding: 2px 6px;
      border-radius: 99px;
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    .badge-hot {
      background: #F7DF79;
      color: #1e293b;
    }

    .badge-deal {
      background: #1e293b;
      color: #fff;
    }

    .item-name {
      font-size: .85rem;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 2px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .item-card.pkg-card .item-name {
      color: #18181b;
    }

    .item-meta {
      font-size: .72rem;
      color: #94a3b8;
      margin-bottom: 7px;
    }

    .item-card.pkg-card .item-meta {
      color: #5C6370;
    }

    .item-price {
      font-size: 1rem;
      font-weight: 700;
      color: #c9a800;
    }

    .item-card.pkg-card .item-price {
      color: #1e293b;
    }
    
    .item-card.prod-card .item-price {
      color: #ca8a04;
    }

    /* Cart */
    .pos-right {
      width: 400px;
      min-width: 400px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .cart-header {
      padding: 14px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-shrink: 0;
    }

    .cart-title {
      font-size: .9rem;
      font-weight: 800;
      color: #1e293b;
      display: flex;
      align-items: center;
      gap: 8px;
      text-transform: uppercase;
      letter-spacing: .03em;
    }

    .cart-clear {
      font-size: .65rem;
      font-weight: 800;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: .08em;
      cursor: pointer;
      border: none;
      background: none;
      transition: .2s;
      font-family: inherit;
    }

    .cart-clear:hover {
      color: #ef4444;
    }

    .cart-body {
      flex: 1;
      padding: 12px;
      overflow-y: auto;
    }

    .cart-empty {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100%;
      color: #cbd5e1;
      padding: 32px 0;
      opacity: .6;
    }

    .cart-empty svg {
      margin-bottom: 10px;
    }

    .cart-empty p {
      font-size: .8rem;
      font-weight: 600;
    }

    .cart-row {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 16px 15px;
      border-radius: 14px;
      margin-bottom: 10px;
      background: #fff;
      border: 1px solid #f1f5f9;
      box-shadow: 0 2px 5px rgba(0, 0, 0, .03);
      transition: transform .2s;
    }

    .cart-row:hover {
      transform: translateX(2px);
      border-color: #F7DF79;
    }

    .cart-row-info {
      flex: 1;
      min-width: 0;
    }

    .cart-row-name {
      font-size: .92rem;
      font-weight: 700;
      color: #1e293b;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .cart-row-type {
      font-size: .65rem;
      font-weight: 600;
      color: #94a3b8;
      text-transform: uppercase;
    }

    .qty-ctrl {
      display: flex;
      align-items: center;
      border: 1.5px solid #f1f5f9;
      border-radius: 10px;
      overflow: hidden;
      background: #f8fafc;
    }

    .qty-btn {
      width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: none;
      border: none;
      cursor: pointer;
      font-size: 1rem;
      color: #64748b;
      transition: .2s;
    }

    .qty-btn:hover {
      background: var(--ybg);
      color: var(--yk);
    }

    .qty-num {
      width: 26px;
      text-align: center;
      font-size: .85rem;
      font-weight: 800;
      color: #1e293b;
    }

    .cart-row-price {
      font-size: .85rem;
      font-weight: 800;
      color: var(--yk);
      min-width: 80px;
      text-align: right;
    }

    .totals-row {
      display: flex;
      justify-content: space-between;
      font-size: .8rem;
      font-weight: 600;
      color: #64748b;
      margin-bottom: 6px;
    }

    .totals-total {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: var(--ybg);
      border: 1.5px solid #F7DF79;
      border-radius: 14px;
      padding: 12px 16px;
    }

    .totals-total-label {
      font-size: .85rem;
      font-weight: 800;
      color: #1e293b;
      text-transform: uppercase;
    }

    .totals-total-val {
      font-size: 1.3rem;
      font-weight: 900;
      color: var(--yk);
    }

    .pay-section-label {
      font-size: .65rem;
      font-weight: 800;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: .08em;
      margin-bottom: 8px;
    }

    .split-input {
      padding: 10px 14px;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      font-size: .88rem;
      font-weight: 600;
      font-family: inherit;
      color: #1e293b;
      background: #f8fafc;
      outline: none;
      transition: .2s;
    }

    .split-input:focus {
      border-color: var(--yd);
      background: #fff;
      box-shadow: 0 0 0 4px rgba(199, 168, 0, .1);
    }

    .checkout-btn {
      width: 100%;
      padding: 16px;
      border-radius: 14px;
      border: none;
      cursor: pointer;
      background: linear-gradient(135deg, #fef9c3, #F7DF79);
      color: #854d0e;
      font-size: 1rem;
      font-weight: 800;
      font-family: inherit;
      transition: all .25s;
      box-shadow: 0 4px 15px rgba(247, 223, 121, .3);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .checkout-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 25px rgba(199, 168, 0, .35);
    }

    .checkout-btn:disabled {
      opacity: .5;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .5);
      backdrop-filter: blur(6px);
      z-index: 1000;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .modal-box {
      background: #fff;
      border-radius: 24px;
      padding: 32px;
      max-width: 420px;
      width: 100%;
      text-align: center;
      box-shadow: 0 30px 70px rgba(0, 0, 0, .2);
    }

    .modal-icon {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: #FBEFBC;
      color: #c9a800;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 18px;
    }

    .modal-title {
      font-size: 1.3rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 5px;
    }

    .modal-sub {
      font-size: .85rem;
      color: #64748b;
      margin-bottom: 22px;
    }

    .modal-actions {
      display: flex;
      flex-direction: column;
      gap: 9px;
      margin-bottom: 18px;
    }

    .modal-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 11px 18px;
      border-radius: 11px;
      font-size: .85rem;
      font-weight: 600;
      cursor: pointer;
      border: none;
      font-family: 'Outfit', sans-serif;
      transition: .2s;
    }

    .modal-btn-print {
      background: #fffdf0;
      color: #c9a800;
      border: 1px solid #F7DF79 !important;
    }

    .modal-btn-print:hover {
      background: #FBEFBC;
    }

    .modal-btn-wa {
      background: #fffdf0;
      color: #c9a800;
      border: 1px solid #F7DF79 !important;
    }

    .modal-btn-wa:hover {
      background: #FBEFBC;
    }

    .modal-done {
      background: none;
      border: none;
      font-size: .72rem;
      font-weight: 700;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: .08em;
      cursor: pointer;
      font-family: 'Outfit', sans-serif;
    }

    .modal-done:hover {
      color: #64748b;
    }
  </style>

  <div class="pos-wrap">

    {{-- LEFT: Items --}}
    <div class="pos-left">

      {{-- Search + Filters --}}
      <div class="pos-search-bar">
        <div class="search-row">
          <div class="search-wrap">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8" />
              <path d="m21 21-4.35-4.35" />
            </svg>
            <input type="text" id="item-search" class="pos-search"
              placeholder="Search services, products or scan barcode…" autofocus>
          </div>

          @if(auth()->user()->hasPermission('reconciliation', 'access') || auth()->user()->hasPermission('pos', 'access'))
            <button class="search-clear" id="reconciliation-btn" title="Cash Reconciliation"
              onclick="window.location.href='{{ url('reconciliation') }}'" style="border-color:#f0e8a0; color:#c9a800;">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
              </svg>
            </button>
          @endif

          <button class="search-clear" id="search-clear-btn" title="Clear search">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>

        {{-- Expandable filter tabs --}}
        <div class="filter-row-container" style="display:flex; align-items:flex-start; gap:8px; margin-top:8px;">
          <div id="filter-row"
            style="display:flex; flex-wrap:wrap; gap:8px; overflow:hidden; max-height:42px; transition:max-height 0.3s ease; flex:1;">
            <button class="cat-tab main-cat-tab active" data-main-type="service">All Services</button>
            <button class="cat-tab main-cat-tab pkg-tab" data-main-type="package">Packages</button>
            <button class="cat-tab main-cat-tab prod-tab" data-main-type="product">Products</button>
            
            <div style="width:1.5px; height:24px; background:#e2e8f0; margin: 4px; border-radius: 2px;"></div>
            
            @foreach($categories as $cat)
              <button class="cat-tab sub-cat-tab" data-filter="cat-{{ $cat->id }}" data-cat-type="{{ strtolower($cat->type) }}">{{ $cat->name }}</button>
            @endforeach
          </div>
          <button id="toggle-more-cat" class="cat-tab"
            style="padding:0; width:34px; height:34px; display:flex; align-items:center; justify-content:center; flex-shrink:0; background:#f8fafc;">
            <svg style="transition:0.3s" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
              viewBox="0 0 24 24">
              <path d="M6 9l6 6 6-6" />
            </svg>
          </button>
        </div>
      </div>

      {{-- Items grid --}}
      <div class="items-scroll">
        <div class="items-grid" id="items-grid">

          {{-- Packages --}}
          @if(isset($packages))
            @foreach($packages as $pkg)
              <div class="item-card pkg-card" data-id="{{ $pkg->id }}" data-type="package" data-name="{{ $pkg->name }}"
                data-price="{{ $pkg->price }}" data-filter="type-package" data-peak-enabled="{{ $pkg->peak_pricing_enabled }}"
                data-peak-price="{{ $pkg->peak_price }}" data-peak-start="{{ $pkg->peak_start }}"
                data-peak-end="{{ $pkg->peak_end }}" data-levels-enabled="{{ $pkg->pricing_levels_enabled ? '1' : '0' }}"
                data-levels="{{ json_encode($pkg->pricing_levels ?? ['junior' => $pkg->price, 'senior' => $pkg->price, 'master' => $pkg->price]) }}">
                <span class="item-badge badge-deal">Bundle</span>
                <div class="item-icon">
                  <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                  </svg>
                </div>
                <div class="item-name">{{ $pkg->name }}</div>
                <div class="item-meta">{{ $pkg->duration }} mins · {{ $pkg->services->count() }} services</div>
                <div class="item-price" id="price-pkg-{{ $pkg->id }}">PKR {{ number_format($pkg->price, 2) }}</div>
              </div>
            @endforeach
          @endif

          {{-- Services --}}
          @foreach($services as $service)
            <div class="item-card service-card" data-id="{{ $service->id }}" data-type="service"
              data-name="{{ $service->name }}" data-price="{{ $service->price }}"
              data-category="{{ $service->category_id }}" data-filter="cat-{{ $service->category_id }}"
              data-peak-enabled="{{ $service->peak_pricing_enabled }}" data-peak-price="{{ $service->peak_price }}"
              data-peak-start="{{ $service->peak_start }}" data-peak-end="{{ $service->peak_end }}"
              data-levels-enabled="{{ $service->pricing_levels_enabled ? '1' : '0' }}"
              data-levels="{{ json_encode($service->pricing_levels ?? ['junior' => $service->price, 'senior' => $service->price, 'master' => $service->price]) }}"
              data-barcode="">
              @if($service->is_popular)<span class="item-badge badge-hot">Popular</span>@endif
              <div class="item-icon">
                @if($service->image)
                  <img src="{{ asset('storage/' . $service->image) }}"
                    style="width:100%;height:100%;object-fit:cover;border-radius:11px;">
                @else
                  <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path
                      d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                  </svg>
                @endif
              </div>
              <div class="item-name">{{ $service->name }}</div>
              <div class="item-meta">{{ $service->duration }} mins</div>
              <div class="item-price" id="price-svc-{{ $service->id }}">PKR {{ number_format($service->price, 2) }}</div>
            </div>
          @endforeach

          {{-- Products --}}
          @foreach($products as $product)
            <div class="item-card prod-card" data-id="{{ $product->id }}" data-type="product"
              data-name="{{ $product->name }}" data-price="{{ $product->selling_price }}"
              data-category="{{ $product->category_id }}" data-filter="cat-{{ $product->category_id }}"
              data-barcode="{{ $product->barcode ?? '' }}">
              <div class="item-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                  <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
              </div>
              <div class="item-name">{{ $product->name }}</div>
              <div class="item-meta">Stock: {{ $product->current_stock }}</div>
              <div class="item-price">PKR {{ number_format($product->selling_price, 2) }}</div>
            </div>
          @endforeach

        </div>
      </div>
    </div>

    {{-- RIGHT: Cart & Checkout --}}
    <div class="pos-right">

      {{-- Part 1: Order Summary Box --}}
      <div class="cart-main-box"
        style="flex:1; display:flex; flex-direction:column; background:#fff; border-radius:18px; border:1px solid #f0e8a0; margin-bottom:12px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,.03);">
        <div class="cart-header" style="background:#fffdf8; border-bottom:1px solid #f0e8a0;">
          <div class="cart-title">
            <svg width="18" height="18" fill="none" stroke="#c9a800" stroke-width="2.5" viewBox="0 0 24 24">
              <path d="M6 2 3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
              <line x1="3" y1="6" x2="21" y2="6" />
              <path d="M16 10a4 4 0 01-8 0" />
            </svg>
            Current Order Items
          </div>
          <button class="cart-clear" id="clear-cart">Clear</button>
        </div>

        <div class="cart-body" id="cart-items"
          style="height: 210px; max-height: 210px; overflow-y: auto; padding: 12px; background: #fafbfb;">
          <div class="cart-empty" id="empty-cart-msg">
            <svg width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
              <path d="M6 2 3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
              <path d="M3 6h18m-8 4a2 2 0 11-4 0" />
            </svg>
            <p style="margin-top:8px;">No items selected yet</p>
          </div>
        </div>

        <div class="cart-totals-area" style="padding:12px 18px; background:#fff; border-top:1px solid #f1f5f9;">
          <div class="totals-row" style="margin-bottom:4px;"><span>Subtotal</span><span id="subtotal-val"
              style="color:#1e293b;">PKR 0.00</span></div>
          <div class="totals-row" style="margin-bottom:8px;"><span>Tax (5%)</span><span id="tax-val"
              style="color:#1e293b;">PKR 0.00</span></div>
          <div class="totals-total"
            style="padding:10px 14px; border-radius:12px; border-width:2px; display:flex; align-items:center; justify-content:space-between;">
            <span class="totals-total-label" style="font-size:.75rem; color:#475569;">Total Payable</span>
            <span class="totals-total-val" id="total-val" style="font-size:1.15rem; letter-spacing:-0.02em;">PKR
              0.00</span>
          </div>
        </div>
      </div>

      {{-- Part 2: Customer & Finalize Box --}}
      <div class="cart-action-box"
        style="background:#fff; border-radius:18px; border:1px solid #f0e8a0; padding:16px; box-shadow:0 2px 10px rgba(0,0,0,.03);">
        <div class="pay-section-label" style="margin-bottom:10px; display:flex; align-items:center; gap:5px;">
          <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
            <path
              d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
          </svg>
          Client Identifications
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:10px;">
          <div>
            <input type="text" id="pos_customer_name" class="split-input"
              style="width:100%; border:1.5px solid #e2e8f0; font-size:.8rem; padding:8px 10px;" placeholder="Name">
          </div>
          <div>
            <input type="text" id="pos_customer_phone" class="split-input"
              style="width:100%; border:1.5px solid #e2e8f0; font-size:.8rem; padding:8px 10px;" placeholder="Phone">
          </div>
        </div>

        <button type="button" id="lookup-cust-btn"
          style="width:100%; padding:9px; background:#fffdf0; border:1.5px solid #f0e8a0; border-radius:10px; color:#c9a800; font-weight:800; font-size:.72rem; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px;">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          Verify Client Profile
        </button>

        <div id="cust-quick-stats"
          style="display:none; margin-top:12px; padding:10px; background:#f0f9ff; border:1px solid #bae6fd; border-radius:12px;">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <span id="cust-membership"
              style="font-size:.6rem; background:#0369a1; color:#fff; padding:2px 6px; border-radius:99px; font-weight:800; text-transform:uppercase;">Standard</span>
            <span id="cust-last-visit" style="font-size:.65rem; color:#0369a1; font-weight:700;"></span>
          </div>
          <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
            <div style="font-size:.78rem; font-weight:800; color:#0c4a6e;">
              Spent: <span id="cust-total-spent" style="color:#059669;">PKR 0</span>
            </div>
            <a href="#" id="cust-detail-link" target="_blank" style="font-size:.65rem; font-weight:700; color:#fff; background:#0c4a6e; padding:4px 8px; border-radius:6px; text-decoration:none; transition:.2s;">View Detail</a>
          </div>
        </div>

        <button class="checkout-btn" id="checkout-btn" style="margin-top:12px; padding:14px; font-size:.95rem;"
          disabled>Proceed to Payment</button>
      </div>

    </div>
  </div>

  {{-- Staff Selection Modal --}}
  <div class="modal-overlay" id="staff-tier-modal">
    <div class="modal-box" style="max-width:500px;">
      <div class="modal-icon" style="background:#fffdf0; color:#c9a800;">
        <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
          <circle cx="8.5" cy="7" r="4" />
          <path d="M20 8v6M23 11h-6" />
        </svg>
      </div>
      <div class="modal-title">Select Expertise Level</div>
      <div class="modal-sub">Choose a staff tier for this service</div>

      <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:24px;">
        <button class="tier-option" data-tier="junior"
          style="padding:16px 10px; border:2px solid #e2e8f0; border-radius:16px; background:#fff; cursor:pointer; transition:.2s; display:flex; flex-direction:column; align-items:center; gap:8px;">
          <div style="font-size:.65rem; font-weight:800; color:#94a3b8; text-transform:uppercase;">Junior</div>
          <div class="tier-price" id="price-junior" style="font-size:.9rem; font-weight:800; color:#1e293b;">PKR 0</div>
        </button>
        <button class="tier-option" data-tier="senior"
          style="padding:16px 10px; border:2px solid #e2e8f0; border-radius:16px; background:#fff; cursor:pointer; transition:.2s; display:flex; flex-direction:column; align-items:center; gap:8px;">
          <div style="font-size:.65rem; font-weight:800; color:#94a3b8; text-transform:uppercase;">Senior</div>
          <div class="tier-price" id="price-senior" style="font-size:.9rem; font-weight:800; color:#1e293b;">PKR 0</div>
        </button>
        <button class="tier-option" data-tier="master"
          style="padding:16px 10px; border:2px solid #e2e8f0; border-radius:16px; background:#fff; cursor:pointer; transition:.2s; display:flex; flex-direction:column; align-items:center; gap:8px;">
          <div style="font-size:.65rem; font-weight:800; color:#94a3b8; text-transform:uppercase;">Master</div>
          <div class="tier-price" id="price-master" style="font-size:.9rem; font-weight:800; color:#1e293b;">PKR 0</div>
        </button>
      </div>

      <button class="modal-done"
        onclick="document.getElementById('staff-tier-modal').style.display='none'">Cancel</button>
    </div>
  </div>

  <style>
    .tier-option:hover {
      border-color: #F7DF79;
      background: #fffdf0;
    }

    .tier-option.selected {
      border-color: #c9a800;
      background: #fffdf0;
      box-shadow: 0 4px 15px rgba(199, 168, 0, .15);
    }
  </style>

@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      let cart = [];
      let activeCustomer = null;

      const cartEl = document.getElementById('cart-items');
      const emptyMsg = document.getElementById('empty-cart-msg');
      const checkBtn = document.getElementById('checkout-btn');

      // Customer Inputs
      const custNameInput = document.getElementById('pos_customer_name');
      const custPhoneInput = document.getElementById('pos_customer_phone');
      const quickStatsWrap = document.getElementById('cust-quick-stats');
      const quickSpent = document.getElementById('cust-total-spent');
      const quickLastVisit = document.getElementById('cust-last-visit');
      const quickMembership = document.getElementById('cust-membership');
      const lookupBtn = document.getElementById('lookup-cust-btn');

      // Load existing checkout session if returning from payment screen
      const savedSessionRaw = localStorage.getItem('pos_checkout_session');
      if (savedSessionRaw) {
        try {
          const savedSession = JSON.parse(savedSessionRaw);
          if (savedSession && savedSession.cart && savedSession.cart.length > 0) {
            cart = savedSession.cart;
            custNameInput.value = savedSession.customerName || '';
            custPhoneInput.value = savedSession.customerPhone || '';
            if (savedSession.customer_id) {
              activeCustomer = { id: savedSession.customer_id, name: savedSession.customerName, phone: savedSession.customerPhone };
              lookupCustomerQuietly(savedSession.customer_id);
            }
            setTimeout(() => {
              renderCart();
            }, 100);
          }
        } catch (e) {
          console.error("Failed to restore checkout session:", e);
        }
      }

      function lookupCustomerQuietly(customerId) {
        let searchUrl = window.location.pathname;
        if (!searchUrl.endsWith('/')) searchUrl += '/';
        searchUrl += 'search-customer';

        fetch(`${searchUrl}?customer_id=${customerId}`)
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              activeCustomer = data.customer;
              quickMembership.textContent = data.customer.membership_type || 'Standard';
              quickSpent.textContent = `PKR ${parseFloat(data.customer.total_spent || 0).toLocaleString()}`;
              quickLastVisit.textContent = data.customer.last_visit ? `Last Visit: ${data.customer.last_visit}` : 'First Visit';
              
              const detailLink = document.getElementById('cust-detail-link');
              if (detailLink) {
                  let baseUrl = "{{ url('customers') }}";
                  if(baseUrl.endsWith('/')) baseUrl = baseUrl.slice(0, -1);
                  detailLink.href = `${baseUrl}/${data.customer.id}`;
              }
              quickStatsWrap.style.display = 'block';
            }
          });
      }

      // ── Customer Lookup ───────────────────────────────────────────
      function lookupCustomer() {
        const name = custNameInput.value.trim();
        const phone = custPhoneInput.value.trim();

        const cleanPhone = phone.replace(/[^0-9]/g, '');
        const query = cleanPhone || name;

        // Robust URL detection for VPS/Subdirectory
        let searchUrl = window.location.pathname;
        if (!searchUrl.endsWith('/')) searchUrl += '/';
        searchUrl += 'search-customer';

        fetch(`${searchUrl}?q=${encodeURIComponent(query)}&name=${encodeURIComponent(name)}&phone=${encodeURIComponent(phone)}`)
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              activeCustomer = data.customer;
              custNameInput.value = data.customer.name;
              custPhoneInput.value = data.customer.phone;

              quickMembership.textContent = data.customer.membership_type || 'Standard';
              quickSpent.textContent = `PKR ${parseFloat(data.customer.total_spent || 0).toLocaleString()}`;
              quickLastVisit.textContent = data.customer.last_visit ? `Last Visit: ${data.customer.last_visit}` : 'First Visit';
              
              const detailLink = document.getElementById('cust-detail-link');
              if (detailLink) {
                  // Make sure there's no trailing slash before appending ID
                  let baseUrl = "{{ url('customers') }}";
                  if(baseUrl.endsWith('/')) baseUrl = baseUrl.slice(0, -1);
                  detailLink.href = `${baseUrl}/${data.customer.id}`;
              }

              quickStatsWrap.style.display = 'block';
            } else {
              activeCustomer = null;
              quickStatsWrap.style.display = 'none';
              alert('No exact match found. Proceeding will create a new client profile.');
            }
          });
      }

      lookupBtn.addEventListener('click', lookupCustomer);

      // ── Peak Time Detection ──────────────────────────────────────
      function isPeakTime(start, end) {
        if (!start || !end) return false;
        const now = new Date();
        const current = now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();

        const parseTime = (t) => {
          const [h, m, s] = t.split(':').map(Number);
          return h * 3600 + (m || 0) * 60 + (s || 0);
        };

        const s = parseTime(start);
        const e = parseTime(end);

        if (s < e) return current >= s && current <= e;
        return current >= s || current <= e; // Handles overnight peak (e.g. 22:00 to 02:00)
      }

      // ── Update Item Card Prices based on Peak ─────────────────────
      function updatePeakPrices() {
        document.querySelectorAll('.item-card').forEach(card => {
          const { peakEnabled, peakPrice, peakStart, peakEnd, price } = card.dataset;
          const displayPriceEl = card.querySelector('.item-price');

          if (peakEnabled == "1" && isPeakTime(peakStart, peakEnd)) {
            const finalPrice = parseFloat(price) + parseFloat(peakPrice);
            displayPriceEl.innerHTML = `<span style="text-decoration:line-through; font-size:0.7em; opacity:0.6;">PKR ${parseFloat(price).toFixed(2)}</span> <span style="color:#ef4444;">PKR ${finalPrice.toFixed(2)}</span> <span style="font-size:0.5em; background:#fee2e2; color:#ef4444; padding:2px 4px; border-radius:4px; vertical-align:middle;">PEAK</span>`;
            card.dataset.currentPrice = finalPrice;
            card.dataset.peakActive = "1";
          } else {
            displayPriceEl.textContent = `PKR ${parseFloat(price).toFixed(2)}`;
            card.dataset.currentPrice = price;
            card.dataset.peakActive = "0";
          }
        });
      }
      setInterval(updatePeakPrices, 30000); // Check every 30s
      updatePeakPrices();

      // ── Add item ──────────────────────────────────────────────────
      let pendingItem = null;

      document.querySelectorAll('.item-card').forEach(card => {
        card.addEventListener('click', () => {
          const { id, type, name, currentPrice, levels, levelsEnabled } = card.dataset;

          if ((type === 'service' || type === 'package') && levelsEnabled === '1') {
            pendingItem = {
              id, type, name,
              basePrice: parseFloat(currentPrice),
              levels: JSON.parse(levels),
              peakActive: card.dataset.peakActive === "1",
              peakSurcharge: parseFloat(card.dataset.peakPrice || 0)
            };
            showTierModal(pendingItem);
          } else {
            addToCart(id, type, name, parseFloat(currentPrice));
          }
        });
      });

      function showTierModal(item) {
        const modal = document.getElementById('staff-tier-modal');
        const surcharge = item.peakActive ? item.peakSurcharge : 0;

        const pJunior = parseFloat(item.levels.junior || 0) + surcharge;
        const pSenior = parseFloat(item.levels.senior || 0) + surcharge;
        const pMaster = parseFloat(item.levels.master || 0) + surcharge;

        document.getElementById('price-junior').textContent = `PKR ${pJunior.toFixed(2)}`;
        document.getElementById('price-senior').textContent = `PKR ${pSenior.toFixed(2)}`;
        document.getElementById('price-master').textContent = `PKR ${pMaster.toFixed(2)}`;

        // Update data-prices on buttons for the click handler
        modal.querySelectorAll('.tier-option').forEach(btn => {
          const tier = btn.dataset.tier;
          btn.dataset.calculatedPrice = parseFloat(item.levels[tier] || 0) + surcharge;
        });

        modal.style.display = 'flex';

        modal.querySelectorAll('.tier-option').forEach(btn => {
          btn.onclick = () => {
            const tier = btn.dataset.tier;
            const price = btn.dataset.calculatedPrice;
            addToCart(item.id, item.type, `${item.name} (${tier.charAt(0).toUpperCase() + tier.slice(1)})`, price);
            modal.style.display = 'none';
          };
        });
      }

      function addToCart(id, type, name, price) {
        const ex = cart.find(i => i.id === id && i.type === type && i.name === name);
        ex ? (ex.qty++, ex.sub = ex.qty * ex.price)
          : cart.push({ id, type, name, price: parseFloat(price), qty: 1, sub: parseFloat(price) });
        renderCart();
      }

      // ── Render cart ───────────────────────────────────────────────
      function renderCart() {
        cartEl.querySelectorAll('.cart-row').forEach(r => r.remove());
        if (!cart.length) {
          emptyMsg.style.display = 'flex';
          checkBtn.disabled = true;
          setTotals(0); return;
        }
        emptyMsg.style.display = 'none';
        cart.forEach((item, idx) => {
          const row = document.createElement('div');
          row.className = 'cart-row';
          row.innerHTML = `
            <div class="cart-row-info">
              <div class="cart-row-name">${item.name}</div>
              <div class="cart-row-type">${item.type === 'package' ? 'Package' : item.type === 'service' ? 'Service' : 'Product'}</div>
            </div>
            <div class="qty-ctrl">
              <button class="qty-btn minus" data-idx="${idx}">−</button>
              <input type="number" class="qty-num" data-idx="${idx}" value="${item.qty}" min="1" style="width:40px; border:none; background:transparent; text-align:center; font-family:inherit; font-size:.85rem; font-weight:800; outline:none; -moz-appearance: textfield;">
              <button class="qty-btn plus" data-idx="${idx}">+</button>
            </div>
            <div class="cart-row-price">PKR ${item.sub.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>`;
          cartEl.appendChild(row);
        });
        checkBtn.disabled = false;
        setTotals(cart.reduce((s, i) => s + i.sub, 0));

        cartEl.querySelectorAll('.qty-num').forEach(input => {
          input.onchange = e => {
            const i = parseInt(e.target.dataset.idx);
            let newQty = parseInt(e.target.value);
            if (isNaN(newQty) || newQty < 1) newQty = 1;
            cart[i].qty = newQty;
            cart[i].sub = cart[i].qty * cart[i].price;
            renderCart();
          };
          // Prevent scrolling from changing value
          input.onwheel = e => e.preventDefault();
        });

        cartEl.querySelectorAll('.minus').forEach(b => b.onclick = e => {
          const i = parseInt(e.currentTarget.dataset.idx);
          if (cart[i].qty > 1) { cart[i].qty--; cart[i].sub = cart[i].qty * cart[i].price; }
          else cart.splice(i, 1);
          renderCart();
        });
        cartEl.querySelectorAll('.plus').forEach(b => b.onclick = e => {
          const i = parseInt(e.currentTarget.dataset.idx);
          cart[i].qty++; cart[i].sub = cart[i].qty * cart[i].price;
          renderCart();
        });
      }

      function setTotals(sub) {
        const tax = sub * 0.05;
        document.getElementById('subtotal-val').textContent = `PKR ${sub.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
        document.getElementById('tax-val').textContent = `PKR ${tax.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
        document.getElementById('total-val').textContent = `PKR ${(sub + tax).toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
      }

      // ── Checkout ──────────────────────────────────────────────────
      checkBtn.addEventListener('click', () => {
        if (!cart.length) return;

        const sub = cart.reduce((s, i) => s + i.sub, 0);
        const tax = sub * .05;
        const finalTotal = sub + tax;

        const checkoutData = {
          cart: cart,
          subtotal: sub,
          tax: tax,
          discount: 0,
          payable_amount: finalTotal,
          customerName: custNameInput.value || 'Walk-in Customer',
          customerPhone: custPhoneInput.value || '',
          customer_id: activeCustomer ? activeCustomer.id : null
        };

        localStorage.setItem('pos_checkout_session', JSON.stringify(checkoutData));

        checkBtn.disabled = true;
        checkBtn.textContent = 'Redirecting...';
        window.location.href = "{{ route('pos.payment') }}";
      });

      document.getElementById('clear-cart').onclick = () => {
        if (confirm('Clear the entire order?')) { cart = []; renderCart(); }
      };

      // ── Search & Filters ────────────────────
      const searchEl = document.getElementById('item-search');
      searchEl.addEventListener('input', e => {
        const val = e.target.value.toLowerCase();
        if (val.trim() === '') {
           document.querySelector('.main-cat-tab.active').click();
           return;
        }
        document.querySelectorAll('.item-card').forEach(c => {
          const match = c.dataset.name.toLowerCase().includes(val) || (c.dataset.barcode && c.dataset.barcode.includes(val));
          c.style.display = match ? '' : 'none';
        });
        const bc = Array.from(document.querySelectorAll('.item-card')).find(c => c.dataset.barcode === e.target.value);
        if (bc) { bc.click(); e.target.value = ''; }
      });
      document.getElementById('search-clear-btn').onclick = () => { searchEl.value = ''; document.querySelector('.main-cat-tab.active').click(); searchEl.focus(); };

      const filterRow = document.getElementById('filter-row');
      const toggleMoreCat = document.getElementById('toggle-more-cat');
      toggleMoreCat.addEventListener('click', () => {
        const isCol = filterRow.style.maxHeight === '42px' || !filterRow.style.maxHeight;
        filterRow.style.maxHeight = isCol ? '500px' : '42px';
        toggleMoreCat.querySelector('svg').style.transform = isCol ? 'rotate(180deg)' : 'rotate(0deg)';
      });

      document.querySelectorAll('.main-cat-tab').forEach(btn => {
        btn.addEventListener('click', () => {
          document.querySelectorAll('.cat-tab').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          const type = btn.dataset.mainType;
          
          document.querySelectorAll('.sub-cat-tab').forEach(sub => {
             // Show subcategory if its type matches, or if it's service and has no type defined
             sub.style.display = (sub.dataset.catType === type || (type === 'service' && !sub.dataset.catType)) ? 'inline-flex' : 'none';
          });

          document.querySelectorAll('.item-card').forEach(c => {
             c.style.display = (c.dataset.type === type) ? '' : 'none';
          });
        });
      });

      document.querySelectorAll('.sub-cat-tab').forEach(btn => {
        btn.addEventListener('click', () => {
          document.querySelectorAll('.sub-cat-tab').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          const filter = btn.dataset.filter;
          const type = btn.dataset.catType || 'service';
          
          document.querySelectorAll('.item-card').forEach(c => {
             // Only show cards that match the category filter AND are of the current main type
             c.style.display = (c.dataset.filter === filter && c.dataset.type === type) ? '' : 'none';
          });
        });
      });

      // trigger initial filter to only show services
      document.querySelector('.main-cat-tab.active').click();
    });
  </script>

@endpush