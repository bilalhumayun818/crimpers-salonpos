@extends('layouts.app')
@section('title', 'Product Usage Report')

@section('content')
<style>
.page-header{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px;padding:16px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;}
.page-header h2{font-size:1.25rem;font-weight:700;color:#1e293b;margin:0;}

.filters{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:4px;}
.filter-label{font-size:.8rem;font-weight:600;color:#64748b;text-transform:uppercase;}
.filter-input{padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.9rem;font-family:'Outfit',sans-serif;}

.btn-primary{background:#18181b;color:#fff;border:none;padding:10px 20px;border-radius:10px;font-weight:600;cursor:pointer;font-family:'Outfit',sans-serif;}
.btn-primary:hover{background:#27272a;}

.table-wrap{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:24px;}
.table-head{background:#f8fafc;padding:12px 16px;border-bottom:1px solid #e2e8f0;display:grid;grid-template-columns:2fr 2fr 1fr;gap:12px;font-size:.8rem;font-weight:700;color:#64748b;text-transform:uppercase;}
.table-row{padding:12px 16px;border-bottom:1px solid #f1f5f9;display:grid;grid-template-columns:2fr 2fr 1fr;gap:12px;align-items:center;}
.table-row:last-child{border-bottom:none;}

.product-name{font-weight:600;color:#1e293b;}
.sku{font-size:.75rem;color:#94a3b8;}
    
.btn-export-csv {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: #ffffff;
    border: none;
    border-radius: 10px;
    padding: 8px 16px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.25s ease;
    box-shadow: 0 4px 10px rgba(59, 130, 246, 0.25);
}
.btn-export-csv:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(59, 130, 246, 0.4);
    color: #ffffff;
}

.btn-export-xls {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    border: none;
    border-radius: 10px;
    padding: 8px 16px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.25s ease;
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);
}
.btn-export-xls:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(16, 185, 129, 0.4);
    color: #ffffff;
}
</style>

<div class="page-header">
    <h2>Product Usage Report</h2>
    <div style="display:flex;gap:12px;">
        <a href="{{ route('inventory.usage-report.export', request()->all() + ['format' => 'csv']) }}" class="btn-export-csv">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Export CSV
        </a>
        <a href="{{ route('inventory.usage-report.export', request()->all() + ['format' => 'xls']) }}" class="btn-export-xls">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/><path d="M14 2v6h6M8 13h8M8 17h8M10 9h4"/></svg>
            Export XXL
        </a>

    </div>
</div>

<form method="GET" class="filters">
    <div class="filter-group">
        <label class="filter-label">Start Date</label>
        <input type="date" name="start_date" class="filter-input" value="{{ $startDate instanceof \Carbon\Carbon ? $startDate->format('Y-m-d') : $startDate }}">
    </div>
    <div class="filter-group">
        <label class="filter-label">End Date</label>
        <input type="date" name="end_date" class="filter-input" value="{{ $endDate instanceof \Carbon\Carbon ? $endDate->format('Y-m-d') : $endDate }}">
    </div>
    <button type="submit" class="btn-primary" style="height: 40px; margin-left: 8px;">Filter</button>
</form>

@if($usageData->count())
<div class="table-wrap">
    <div class="table-head">
        <span>Product Consumed</span>
        <span>Used In Service</span>
        <span style="text-align: right;">Total Quantity Used</span>
    </div>
    @foreach($usageData as $usage)
    <div class="table-row">
        <div>
            <div class="product-name">{{ $usage->product->name ?? 'Unknown Product' }}</div>
            <div class="sku">{{ $usage->product->sku ?? 'No SKU' }}</div>
        </div>
        <div>
            <div style="font-weight:500; color:#475569;">{{ $usage->service->name ?? 'Direct Usage / Unknown' }}</div>
        </div>
        <div style="text-align: right; font-weight:700; color:#ef4444; font-size: 1.1rem;">
            {{ number_format($usage->total_used, 2) }}
        </div>
    </div>
    @endforeach
</div>
@else
<div style="text-align:center;padding:40px;color:#94a3b8;background:#fff;border-radius:12px;border:1px solid #e2e8f0;">
    No product usage recorded for the selected date range.
</div>
@endif

@endsection
