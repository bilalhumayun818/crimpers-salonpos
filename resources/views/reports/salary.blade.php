@extends('layouts.app')
@section('title', 'Staff Salary Report')

@section('content')
<style>
.page-header{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px;padding:16px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;}
.page-header h2{font-size:1.25rem;font-weight:700;color:#1e293b;margin:0;}

.filters{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:4px;}
.filter-label{font-size:.8rem;font-weight:600;color:#64748b;text-transform:uppercase;}
.filter-input{padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.9rem;font-family:'Outfit',sans-serif;outline:none;min-width:220px;}
.filter-input:focus{border-color:#F7DF79;box-shadow:0 0 0 3px rgba(247,223,121,0.2);}
.btn-primary{background:#18181b;color:#fff;border:none;padding:9px 18px;border-radius:10px;font-weight:600;cursor:pointer;font-family:'Outfit',sans-serif;}
.btn-primary:hover{background:#27272a;}

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
.btn-export-csv:hover { transform: translateY(-2px); box-shadow: 0 6px 14px rgba(59, 130, 246, 0.4); color: #ffffff; }

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
.btn-export-xls:hover { transform: translateY(-2px); box-shadow: 0 6px 14px rgba(16, 185, 129, 0.4); color: #ffffff; }

.table-wrap{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow-x:auto;margin-bottom:24px;}
.table{width:100%;border-collapse:collapse;min-width:800px;}
.table th{background:#f8fafc;padding:12px 16px;border-bottom:1px solid #e2e8f0;text-align:left;font-size:.8rem;font-weight:700;color:#64748b;text-transform:uppercase;white-space:nowrap;}
.table td{padding:12px 16px;border-bottom:1px solid #f1f5f9;color:#475569;font-size:0.9rem;}
.table tr:last-child td{border-bottom:none;}
.name-cell{font-weight:600;color:#1e293b;}
.amount{font-weight:700;color:#166534;}
.commission{font-weight:600;color:#0ea5e9;}
</style>

<div class="page-header">
    <h2>Staff Salary Report</h2>
    <div style="display:flex;gap:12px;">
        <a href="{{ route('reports.salary.export', array_merge(request()->only(['search']), ['format' => 'csv'])) }}" class="btn-export-csv">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Export CSV
        </a>
        <a href="{{ route('reports.salary.export', array_merge(request()->only(['search']), ['format' => 'xls'])) }}" class="btn-export-xls">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/><path d="M14 2v6h6M8 13h8M8 17h8M10 9h4"/></svg>
            Export XLS
        </a>
    </div>
</div>

<form action="{{ route('reports.salary') }}" method="GET" class="filters">
    <div class="filter-group" style="flex:2; min-width:220px;">
        <label class="filter-label">Search by Staff Name</label>
        <input type="text" name="search" class="filter-input" placeholder="e.g. Ahmed..." value="{{ $search }}">
    </div>
    <button type="submit" class="btn-primary">Filter</button>
    <a href="{{ route('reports.salary') }}" style="color:#64748b;font-size:0.9rem;font-weight:500;text-decoration:none;margin-bottom:8px;">Clear</a>
</form>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Staff Name</th>
                <th>Base Salary</th>
                <th>Earned Commission</th>
                <th>Advances</th>
                <th>Deductions</th>
                <th>Net Due Payable</th>
                <th>Last Paid Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffs as $staff)
            <tr>
                <td class="name-cell">{{ $staff->name }}</td>
                <td>Rs {{ number_format($staff->base_salary, 2) }}</td>
                <td class="commission">Rs {{ number_format($staff->total_earned_commission, 2) }}</td>
                <td style="color:#a16207; font-weight:600;">Rs {{ number_format($staff->current_cycle_advances, 2) }}</td>
                <td style="color:#b91c1c; font-weight:600;">Rs {{ number_format($staff->current_cycle_deductions, 2) }}</td>
                <td class="amount">Rs {{ number_format($staff->net_salary_payable, 2) }}</td>
                <td>
                    @if($staff->last_paid_at)
                        {{ $staff->last_paid_at->format('M d, Y') }}
                    @else
                        <span style="color:#ef4444; font-size:0.8rem; font-weight:600;">Never Paid</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">No salary records match your search.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($staffs->count())
    <div style="padding:12px 16px;border-top:1px solid #f1f5f9;font-size:.8rem;color:#64748b;">
        Showing {{ $staffs->count() }} {{ Str::plural('record', $staffs->count()) }}
    </div>
    @endif
</div>

@endsection
