@extends('layouts.app')
@section('title', 'Staff Attendance Report')

@section('content')
<style>
.page-header{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px;padding:16px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;}
.page-header h2{font-size:1.25rem;font-weight:700;color:#1e293b;margin:0;}

.filters{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:4px;}
.filter-label{font-size:.8rem;font-weight:600;color:#64748b;text-transform:uppercase;}
.filter-input{padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.9rem;font-family:'Outfit',sans-serif;outline:none;}
.filter-input:focus{border-color:#F7DF79;box-shadow:0 0 0 3px rgba(247,223,121,0.2);}

.btn-primary{background:#18181b;color:#fff;border:none;padding:9px 18px;border-radius:10px;font-weight:600;cursor:pointer;font-family:'Outfit',sans-serif;}
.btn-primary:hover{background:#27272a;}

.btn-export-csv {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: #ffffff;
    border: none;
    border-radius: 10px;
    padding: 9px 18px;
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
    padding: 9px 18px;
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
.badge{padding:4px 8px;border-radius:99px;font-size:0.75rem;font-weight:600;}
.badge-present{background:#dcfce7;color:#166534;}
.badge-absent{background:#fee2e2;color:#991b1b;}
.badge-late{background:#fef3c7;color:#92400e;}
.badge-leave{background:#e0e7ff;color:#3730a3;}
</style>

<div class="page-header">
    <h2>Staff Attendance Report</h2>
</div>

<form action="{{ route('reports.attendance') }}" method="GET" class="filters">
    <div class="filter-group" style="flex:1; max-width:200px;">
        <label class="filter-label">Date From</label>
        <input type="date" name="date_from" class="filter-input" value="{{ $dateFrom }}">
    </div>
    <div class="filter-group" style="flex:1; max-width:200px;">
        <label class="filter-label">Date To</label>
        <input type="date" name="date_to" class="filter-input" value="{{ $dateTo }}">
    </div>
    <div class="filter-group" style="flex:1; max-width:200px;">
        <label class="filter-label">Staff Name</label>
        <input type="text" name="staff_search" class="filter-input" placeholder="e.g. Ahmed..." value="{{ $staffSearch }}">
    </div>
    <div class="filter-group" style="flex:1; max-width:160px;">
        <label class="filter-label">Status</label>
        <select name="status_filter" class="filter-input" style="background:#fff;">
            <option value="">All Statuses</option>
            <option value="present" {{ $statusFilter === 'present' ? 'selected' : '' }}>Present</option>
            <option value="absent"  {{ $statusFilter === 'absent'  ? 'selected' : '' }}>Absent</option>
            <option value="late"    {{ $statusFilter === 'late'    ? 'selected' : '' }}>Late</option>
            <option value="leave"   {{ $statusFilter === 'leave'   ? 'selected' : '' }}>Leave</option>
        </select>
    </div>
    <button type="submit" class="btn-primary" style="height:37px;">Filter</button>
    <a href="{{ route('reports.attendance') }}" style="color:#64748b;font-size:0.9rem;font-weight:500;text-decoration:none;margin-bottom:8px;">Clear</a>

    <div style="margin-left:auto; display:flex; gap:12px;">
        <a href="{{ route('reports.attendance.export', ['date_from' => $dateFrom, 'date_to' => $dateTo, 'staff_search' => $staffSearch, 'status_filter' => $statusFilter, 'format' => 'csv']) }}" class="btn-export-csv">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Export CSV
        </a>
        <a href="{{ route('reports.attendance.export', ['date_from' => $dateFrom, 'date_to' => $dateTo, 'staff_search' => $staffSearch, 'status_filter' => $statusFilter, 'format' => 'xls']) }}" class="btn-export-xls">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/><path d="M14 2v6h6M8 13h8M8 17h8M10 9h4"/></svg>
            Export XLS
        </a>
    </div>
</form>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Staff Name</th>
                <th>Status</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Hours Worked</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
            <tr>
                <td style="font-weight:500; color:#1e293b;">{{ $attendance->attendance_date ? $attendance->attendance_date->format('M d, Y') : 'N/A' }}</td>
                <td class="name-cell">{{ $attendance->staff->name ?? 'N/A' }}</td>
                <td>
                    @php
                        $statusClass = 'badge-present';
                        if($attendance->status == 'absent') $statusClass = 'badge-absent';
                        if($attendance->status == 'late') $statusClass = 'badge-late';
                        if($attendance->status == 'leave') $statusClass = 'badge-leave';
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ ucfirst($attendance->status) }}</span>
                </td>
                <td>{{ $attendance->check_in_time ? $attendance->check_in_time->format('h:i A') : '--:--' }}</td>
                <td>{{ $attendance->check_out_time ? $attendance->check_out_time->format('h:i A') : '--:--' }}</td>
                <td style="font-weight:600;">{{ number_format($attendance->hours_worked, 1) }} hrs</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:40px; color:#94a3b8;">No attendance records found for this period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($attendances->count())
    <div style="padding:12px 16px;border-top:1px solid #f1f5f9;font-size:.8rem;color:#64748b;">
        Showing {{ $attendances->count() }} {{ Str::plural('record', $attendances->count()) }}
    </div>
    @endif
</div>

@endsection
