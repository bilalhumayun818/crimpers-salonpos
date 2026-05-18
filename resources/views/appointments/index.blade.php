@extends('layouts.app')
@section('title', 'Appointments')

@section('content')
<style>
.appt-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
.appt-title{font-size:1.5rem;font-weight:800;color:#0f172a;letter-spacing:-.02em;margin-bottom:3px;}
.appt-sub{font-size:.85rem;color:#64748b;}
.header-actions{display:flex;gap:10px;}
.btn-outline{padding:9px 18px;border:1.5px solid #F7DF79;background:#fff;border-radius:10px;color:#c9a800;font-size:.85rem;font-weight:600;cursor:pointer;text-decoration:none;font-family:'Outfit',sans-serif;transition:.2s;display:inline-flex;align-items:center;gap:6px;}
.btn-outline:hover{background:#fffdf0;}
.btn-solid{padding:9px 18px;border:none;background:linear-gradient(135deg,#F7DF79,#c9a800);border-radius:10px;color:#18181b;font-size:.85rem;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;transition:.2s;box-shadow:0 3px 10px rgba(247,223,121,.25);display:inline-flex;align-items:center;gap:6px;}
.btn-solid:hover{transform:translateY(-1px);box-shadow:0 5px 16px rgba(247,223,121,.35);}

.appt-table-wrap{background:#fff;border:1px solid #f0e8a0;border-radius:16px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.04);}
.appt-table{width:100%;border-collapse:collapse;}
.appt-table thead tr{background:#f8fafc;}
.appt-table thead th{padding:13px 18px;font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.07em;text-align:left;border-bottom:1px solid #f1f5f9;}
.appt-table tbody tr{border-bottom:1px solid #f8fafc;transition:background .15s;}
.appt-table tbody tr:hover{background:#fafffe;}
.appt-table tbody tr:last-child{border-bottom:none;}
.appt-table td{padding:14px 18px;font-size:.875rem;color:#374151;vertical-align:middle;}

.customer-cell{display:flex;align-items:center;gap:10px;}
.customer-avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#F7DF79,#c9a800);color:#fff;font-size:.8rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.customer-name{font-weight:600;color:#1e293b;}
.customer-phone{font-size:.75rem;color:#94a3b8;}

.datetime-main{font-weight:600;color:#1e293b;margin-bottom:2px;}
.datetime-time{font-size:.78rem;color:#64748b;}

.status-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:.72rem;font-weight:700;text-transform:capitalize;}
.status-scheduled{background:#dbeafe;color:#1d4ed8;}
.status-confirmed{background:#FBEFBC;color:#a07800;}
.status-completed{background:#f3e8ff;color:#7c3aed;}
.status-cancelled{background:#fee2e2;color:#b91c1c;}
.status-arrived{background:#dcfce7;color:#166534;}
.status-late{background:#fef9c3;color:#854d0e;}
.status-discarded{background:#fee2e2;color:#991b1b; animation: pulse-red-badge 2s infinite;}
@keyframes pulse-red-badge { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); } 70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }

.row-scheduled{background:#f8fafc !important;}
.row-arrived{background:#f0fdf4 !important;}
.row-late{background:#fffbeb !important;}
.row-completed{background:#faf5ff !important;}
.row-cancelled{background:#f1f5f9 !important;}
.row-discarded { animation: pulse-red-row 2s infinite; background:#fef2f2 !important; }
@keyframes pulse-red-row { 0% { box-shadow: inset 0 0 0 0 rgba(239, 68, 68, 0.2); } 70% { box-shadow: inset 0 0 100px 0 rgba(239, 68, 68, 0); } 100% { box-shadow: inset 0 0 0 0 rgba(239, 68, 68, 0); } }

.action-link{color:#18181b;font-size:.82rem;font-weight:700;text-decoration:none;padding:5px 10px;border-radius:7px;transition:.15s;background:#f4f4f5;border:1.5px solid #e4e4e7;}
.action-link:hover{background:#e4e4e7;color:#18181b;}
.action-del{color:#ef4444;font-size:.82rem;font-weight:600;background:none;border:none;cursor:pointer;padding:5px 10px;border-radius:7px;font-family:'Outfit',sans-serif;transition:.15s;}
.action-del:hover{background:#fef2f2;}

.empty-state{padding:60px 20px;text-align:center;color:#cbd5e1;}
.empty-state svg{margin:0 auto 14px;display:block;opacity:.35;}
.empty-state p{font-size:.9rem;font-weight:500;}

.pagination-wrap{padding:16px 18px;border-top:1px solid #f1f5f9;}
</style>

<div class="appt-header">
    <div>
        <div class="appt-title">Appointments</div>
        <div class="appt-sub">All scheduled and upcoming appointments</div>
    </div>
    <div class="header-actions">
        <a href="{{ route('appointments.calendar') }}" class="btn-outline">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Calendar View
        </a>
        <a href="{{ route('appointments.create') }}" class="btn-solid">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Appointment
        </a>
    </div>
</div>

<div class="appt-table-wrap">
    <table class="appt-table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Service</th>
                <th>Staff</th>
                <th>Date &amp; Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appt)
            <tr id="appt-row-{{ $appt->id }}" class="row-{{ $appt->status }}">
                <td>
                    <div class="customer-cell">
                        <div class="customer-avatar">{{ strtoupper(substr($appt->customer_name,0,1)) }}</div>
                        <div>
                            <div class="customer-name">{{ $appt->customer_name }}</div>
                            @if($appt->customer_phone)
                            <div class="customer-phone">{{ $appt->customer_phone }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td style="font-weight:500;color:#1e293b;">{{ $appt->service ? $appt->service->name : ($appt->servicePackage ? $appt->servicePackage->name : 'Unassigned') }}</td>
                <td style="color:#64748b;">{{ $appt->staff->name ?? 'Unassigned' }}</td>
                <td>
                    <div class="datetime-main">{{ $appt->appointment_date->format('M j, Y') }}</div>
                    <div class="datetime-time">{{ $appt->start_time->format('g:i A') }} — {{ $appt->end_time->format('g:i A') }}</div>
                </td>
                <td>
                    <span class="status-badge status-{{ $appt->status }}">{{ $appt->status }}</span>
                </td>
                <td>
                    <div style="display:flex;gap:6px;">
                        <a href="{{ route('appointments.edit', $appt) }}" class="action-link">Edit</a>
                        <form method="POST" action="{{ route('appointments.destroy', $appt) }}" onsubmit="return confirm('Delete this appointment?');" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-del">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <p>No appointments found</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($appointments->hasPages())
    <div class="pagination-wrap">
        {{ $appointments->links() }}
    </div>
    @endif
</div>
@endsection
