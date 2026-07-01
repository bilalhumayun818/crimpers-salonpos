@extends('layouts.app')
@section('title', 'Business Settings')

@section('content')
<style>
.perm-card{background:#fff;border:1px solid #e2e8f0;border-radius:20px;padding:32px;box-shadow:0 1px 3px rgba(0,0,0,.03);}
.perm-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;margin-top:24px;}
.perm-group{padding:20px;border:1.5px solid #f1f5f9;border-radius:16px;background:#fcfcfc;transition:.2s;}
.perm-group:hover{border-color:#F7DF79;background:#fff;}
.perm-group-title{font-size:.92rem;font-weight:700;color:#1e293b;margin-bottom:14px;display:flex;align-items:center;gap:8px;padding-bottom:10px;border-bottom:1px solid #f1f5f9;}
.perm-group-title svg{color:#a07800;}
.perm-item{display:flex;align-items:center;justify-content:space-between;padding:6px 0;}
.perm-label{font-size:.85rem;color:#475569;font-weight:500;}
.perm-toggle{position:relative;display:inline-block;width:38px;height:20px;}
.perm-toggle input{opacity:0;width:0;height:0;}
.slider{position:absolute;cursor:pointer;inset:0;background-color:#e2e8f0;transition:.3s;border-radius:34px;}
.slider:before{position:absolute;content:"";height:14px;width:14px;left:3px;bottom:3px;background-color:white;transition:.3s;border-radius:50%;}
input:checked + .slider{background-color:#c9a800;}
input:checked + .slider:before{transform:translateX(18px);}

.role-selector{background:#fff;border:1px solid #e2e8f0;border-radius:15px;padding:20px;margin-bottom:24px;display:flex;align-items:center;gap:15px;}
.role-select{flex:1;padding:12px;border:1.5px solid #f0e8a0;border-radius:12px;font-family:'Outfit',sans-serif;font-size:1rem;background:var(--ybg);outline:none;}
.btn-load{padding:12px 24px;background:#18181b;color:#fff;border:none;border-radius:12px;font-weight:700;cursor:pointer;transition:.2s;}
.btn-load:hover{background:#3f3f46;}
</style>

<div style="max-width:960px;margin:0 auto;padding:24px 0;">
    <div style="margin-bottom:28px;">
        <h1 style="font-size:1.6rem;font-weight:800;color:#0f172a;margin:0 0 4px;letter-spacing:-.02em;">Business Settings</h1>
        <p style="font-size:.9rem;color:#64748b;margin:0;">Configure global operating hours and role permissions</p>
    </div>

    {{-- Operating Hours Settings --}}
    <div class="perm-card" style="margin-bottom: 24px; padding: 24px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,var(--y1),var(--yd));display:flex;align-items:center;justify-content:center;color:#18181b;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <h2 style="font-size:1.1rem;font-weight:800;color:#1e293b;margin:0;">Branch Operating Hours</h2>
        </div>
        
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;">
            @foreach(\App\Models\Branch::all() as $branch)
            <form action="{{ route('branches.update-hours', $branch) }}" method="POST" style="background:#fcfcfc;border:1.5px solid #f1f5f9;border-radius:16px;padding:20px;display:flex;flex-direction:column;gap:12px;">
                @csrf @method('PUT')
                <div style="font-weight:800;color:#334155;font-size:.95rem;"> {{ $branch->name }}</div>
                <div style="display:flex;gap:10px;align-items:center;">
                    <div style="flex:1;">
                        <label style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;">Opening Time</label>
                        <input type="time" name="opening_time" value="{{ \Carbon\Carbon::parse($branch->opening_time)->format('H:i') }}" style="width:100%;padding:10px;border:1.5px solid #e2e8f0;border-radius:8px;font-family:'Outfit',sans-serif;outline:none;">
                    </div>
                    <div style="flex:1;">
                        <label style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;">Closing Time</label>
                        <input type="time" name="closing_time" value="{{ \Carbon\Carbon::parse($branch->closing_time)->format('H:i') }}" style="width:100%;padding:10px;border:1.5px solid #e2e8f0;border-radius:8px;font-family:'Outfit',sans-serif;outline:none;">
                    </div>
                </div>
                <button type="submit" class="btn-load" style="padding:10px;font-size:.85rem;margin-top:4px;">Update Hours</button>
            </form>
            @endforeach
        </div>
    </div>

    {{-- Role Selector --}}
    <form action="{{ route('business-settings.index') }}" method="GET" class="role-selector">
        <div style="font-weight:700;color:#1e293b;font-size:.9rem;white-space:nowrap;">Select Role to Configure:</div>
        <select name="role_id" class="role-select" onchange="this.form.submit()">
            <option value="">— Choose a Role —</option>
            @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ (request('role_id') == $role->id) ? 'selected' : '' }}>{{ $role->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-load">Load Permissions</button>
    </form>

    @if($selectedRole)
    <form action="{{ route('business-settings.update', $selectedRole) }}" method="POST">
        @csrf
        <div class="perm-card">
            <div style="display:flex;align-items:center;justify-content:space-between;padding-bottom:18px;border-bottom:1px solid #f1f5f9;margin-bottom:24px;">
                <div>
                    <span style="font-size:.75rem;text-transform:uppercase;font-weight:800;color:#a1a1aa;letter-spacing:.05em;">Currently Modifying</span>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <h2 style="font-size:1.25rem;font-weight:800;color:#1e293b;margin:0;">{{ $selectedRole->name }}</h2>
                        <span style="display:inline-flex; align-items:center; gap:5px; font-size:0.6rem; font-weight:800; padding:2px 10px; border-radius:99px; background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; text-transform:uppercase;">
                            @if(is_null($selectedRole->branch_id))
                                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                Global
                            @else
                                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $selectedRole->branch->name }}
                            @endif
                        </span>
                    </div>
                </div>
                
                {{-- Branch Assignment --}}
                <div style="flex:1; max-width:240px; margin-left:40px;">
                    <label style="display:flex; align-items:center; gap:6px; font-size:0.65rem; font-weight:800; color:#94a3b8; text-transform:uppercase; margin-bottom:6px;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Operational Scope
                    </label>
                    <select name="role_branch_id" style="width:100%; padding:10px 14px; border:1.5px solid #f0e8a0; border-radius:12px; font-size:0.85rem; font-weight:700; background:#fffdf8; outline:none; font-family:inherit; color:#1e293b; cursor:pointer;">
                        <option value="all" {{ is_null($selectedRole->branch_id) ? 'selected' : '' }}>All Branches (Global)</option>
                        @foreach(\App\Models\Branch::all() as $br)
                            <option value="{{ $br->id }}" {{ $selectedRole->branch_id == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" style="padding:10px 24px;background:linear-gradient(135deg,#c9a800,#a07800);border:none;border-radius:11px;color:#fff;font-weight:700;font-size:.85rem;cursor:pointer;box-shadow:0 4px 12px rgba(160,120,0,.25);">
                    Save Sanctions
                </button>
            </div>

            @php
                $perms = $selectedRole->permissions ?? [];
                $isSet = function($mod, $act) use ($perms) {
                    return isset($perms[$mod][$act]) && $perms[$mod][$act] == '1';
                };
            @endphp

            <div class="perm-grid">
                 {{-- Dashboard --}}
                 <div class="perm-group">
                    <div class="perm-group-title">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </div>
                    <div class="perm-item">
                        <span class="perm-label">View Statistics & Charts</span>
                        <label class="perm-toggle">
                            <input type="checkbox" name="permissions[dashboard][view]" value="1" {{ $isSet('dashboard','view') ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                {{-- Reports --}}
                <div class="perm-group">
                    <div class="perm-group-title">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Reports
                    </div>
                    <div class="perm-item">
                        <span class="perm-label">Access Analytics Reports</span>
                        <label class="perm-toggle">
                            <input type="checkbox" name="permissions[reports][view]" value="1" {{ $isSet('reports','view') ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                 {{-- Customers --}}
                 <div class="perm-group">
                    <div class="perm-group-title">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"/></svg>
                        Customers
                    </div>
                    @foreach(['view','create','edit','delete'] as $act)
                    <div class="perm-item">
                        <span class="perm-label">{{ ucfirst($act) }} Customers</span>
                        <label class="perm-toggle">
                            <input type="checkbox" name="permissions[customers][{{$act}}]" value="1" {{ $isSet('customers',$act) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    @endforeach
                </div>

                {{-- Suppliers --}}
                <div class="perm-group">
                    <div class="perm-group-title">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                        Suppliers
                    </div>
                    @foreach(['view','create','edit','delete'] as $act)
                    <div class="perm-item">
                        <span class="perm-label">{{ ucfirst($act) }} Suppliers</span>
                        <label class="perm-toggle">
                            <input type="checkbox" name="permissions[suppliers][{{$act}}]" value="1" {{ $isSet('suppliers',$act) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    @endforeach
                </div>

                {{-- POS --}}
                <div class="perm-group">
                    <div class="perm-group-title">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Point of Sale
                    </div>

                    {{-- POS sub-label --}}
                    <div style="font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#a07800;margin:4px 0 8px;padding-bottom:4px;border-bottom:1px dashed #fde68a;">
                        ⚡ Point of Sale
                    </div>
                    <div class="perm-item">
                        <span class="perm-label">Access POS Terminal</span>
                        <label class="perm-toggle">
                            <input type="checkbox" name="permissions[pos][access]" value="1" {{ $isSet('pos','access') ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="perm-item">
                        <span class="perm-label">Cash Reconciliation</span>
                        <label class="perm-toggle">
                            <input type="checkbox" name="permissions[reconciliation][access]" value="1" {{ $isSet('reconciliation','access') ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                {{-- Expenses --}}
                <div class="perm-group">
                    <div class="perm-group-title">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        Expenses
                    </div>
                    <div class="perm-item">
                        <span class="perm-label">Record Expenses</span>
                        <label class="perm-toggle">
                            <input type="checkbox" name="permissions[expenses][create]" value="1" {{ $isSet('expenses','create') ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                {{-- Enterprise History --}}
                <div class="perm-group" style="background:#f8fafc; border-color:#e2e8f0;">
                    <div class="perm-group-title" style="color:#475569; border-color:#e2e8f0;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Enterprise History
                    </div>
                    
                    <div class="perm-item" style="border-bottom: 1px dashed #e2e8f0; padding-bottom: 12px; margin-bottom: 12px;">
                        <span class="perm-label" style="font-weight:800; color:#1e293b;">Master History Access</span>
                        <label class="perm-toggle">
                            <input type="checkbox" name="permissions[history][access]" value="1" {{ $isSet('history','access') ? 'checked' : '' }} onchange="toggleSubPerms(this, 'history-subs')">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div id="history-subs" style="display: {{ $isSet('history','access') ? 'block' : 'none' }}; padding-left: 10px; border-left: 2px solid #f1f5f9; margin-left: 5px;">
                        <div class="perm-item">
                            <span class="perm-label" style="font-size:0.75rem;">View Sales History</span>
                            <label class="perm-toggle">
                                <input type="checkbox" name="permissions[sales][view]" value="1" {{ $isSet('sales','view') ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="perm-item">
                            <span class="perm-label" style="font-size:0.75rem;">View Purchase History</span>
                            <label class="perm-toggle">
                                <input type="checkbox" name="permissions[purchases][view]" value="1" {{ $isSet('purchases','view') ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="perm-item">
                            <span class="perm-label" style="font-size:0.75rem;">View Reconciliation History</span>
                            <label class="perm-toggle">
                                <input type="checkbox" name="permissions[reconciliation][view]" value="1" {{ $isSet('reconciliation','view') ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="perm-item">
                            <span class="perm-label" style="font-size:0.75rem;">View Expense History</span>
                            <label class="perm-toggle">
                                <input type="checkbox" name="permissions[expenses][view]" value="1" {{ $isSet('expenses','view') ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <script>
                function toggleSubPerms(master, subId) {
                    document.getElementById(subId).style.display = master.checked ? 'block' : 'none';
                }
                </script>

                {{-- Inventory --}}
                <div class="perm-group">
                    <div class="perm-group-title">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Inventory Management
                    </div>
                    @foreach(['view','create','edit','delete'] as $act)
                    <div class="perm-item">
                        <span class="perm-label">{{ ucfirst($act) }} Products</span>
                        <label class="perm-toggle">
                            <input type="checkbox" name="permissions[inventory][{{$act}}]" value="1" {{ $isSet('inventory',$act) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    @endforeach
                </div>

                {{-- Employees --}}
                <div class="perm-group">
                    <div class="perm-group-title">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                        Employee Management
                    </div>
                    @foreach(['view','create','edit','delete'] as $act)
                    <div class="perm-item">
                        <span class="perm-label">{{ ucfirst($act) }} Employees</span>
                        <label class="perm-toggle">
                            <input type="checkbox" name="permissions[staff][{{$act}}]" value="1" {{ $isSet('staff',$act) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                    @endforeach
                    <div class="perm-item">
                        <span class="perm-label">Manage Attendance</span>
                        <label class="perm-toggle">
                            <input type="checkbox" name="permissions[staff][attendance]" value="1" {{ $isSet('staff','attendance') ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                {{-- Business --}}
                <div class="perm-group" style="background:#f0f9ff;border-color:#bae6fd;">
                    <div class="perm-group-title" style="color:#0369a1;border-color:#bae6fd;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Business Settings
                    </div>
                    <div class="perm-item">
                        <span class="perm-label">Manage Roles & Permissions</span>
                        <label class="perm-toggle">
                            <input type="checkbox" name="permissions[business][view]" value="1" {{ $isSet('business','view') ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                {{-- Security --}}
                <div class="perm-group" style="background:#fff6f6;border-color:#fee2e2;">
                    <div class="perm-group-title" style="color:#ef4444;border-color:#fee2e2;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                        Security & Root
                    </div>
                    <div class="perm-item">
                        <span class="perm-label" style="color:#ef4444;font-weight:700;">Full Settings Access</span>
                        <label class="perm-toggle">
                            <input type="checkbox" name="permissions[admin][all]" value="1" {{ $isSet('admin','all') ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Role Users & Logins --}}
        <div class="perm-card" style="margin-top: 30px;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding-bottom:18px;border-bottom:1px solid #f1f5f9;margin-bottom:24px;">
                <div>
                    <h2 style="font-size:1.1rem;font-weight:800;color:#1e293b;margin:0;">Authorized Logins</h2>
                    <p style="font-size:0.8rem; color:#64748b;">Manage email and passwords for all {{ $selectedRole->name }}s</p>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:2px solid #f1f5f9;">
                            <th style="padding:12px; text-align:left; font-size:0.8rem; color:#64748b; font-weight:700; text-transform:uppercase;">User Name</th>
                            <th style="padding:12px; text-align:left; font-size:0.8rem; color:#64748b; font-weight:700; text-transform:uppercase;">Login Email</th>
                            <th style="padding:12px; text-align:left; font-size:0.8rem; color:#64748b; font-weight:700; text-transform:uppercase;">Password</th>
                            <th style="padding:12px; text-align:right; font-size:0.8rem; color:#64748b; font-weight:700; text-transform:uppercase;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Master Role Login (Generic Role Login) --}}
                        @if($selectedRole->email)
                        <tr style="border-bottom:1px solid #f1f5f9; background:#fffcf0;">
                            <td style="padding:16px 12px; font-weight:800; color:#854d0e;">
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 15V3m0 12l-4-4m4 4l4-4"/></svg>
                                    Master Role Account
                                </div>
                            </td>
                            <td style="padding:16px 12px;">
                                <input type="email" name="role_email" value="{{ $selectedRole->email }}" style="padding:8px 12px; border:1.5px solid #fde047; border-radius:8px; font-size:0.85rem; width:220px; font-weight:700; color:#854d0e;">
                            </td>
                            <td style="padding:16px 12px;">
                                <input type="password" name="role_password" placeholder="••••••••" style="padding:8px 12px; border:1.5px solid #fde047; border-radius:8px; font-size:0.85rem; width:180px;">
                            </td>
                            <td style="padding:16px 12px; text-align:right;">
                                <span style="font-size:0.7rem; color:#a16207; background:#fefce8; padding:4px 10px; border-radius:30px; font-weight:800;">SHARED TERMINAL</span>
                            </td>
                        </tr>
                        @endif

                        @php
                            $roleUsers = \App\Models\User::where('staff_role_id', $selectedRole->id)->get();
                        @endphp
                        @forelse($roleUsers as $u)
                        {{-- Skip if this is the master user account we just showed --}}
                        @if($selectedRole->email && $u->email === $selectedRole->email) @continue @endif
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:16px 12px; font-weight:700; color:#1e293b;">
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/></svg>
                                    {{ $u->name }}
                                </div>
                            </td>
                            <td style="padding:16px 12px;">
                                <input type="email" name="user_emails[{{ $u->id }}]" value="{{ $u->email }}" style="padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; width:220px; font-family:'Outfit',sans-serif;">
                            </td>
                            <td style="padding:16px 12px;">
                                <input type="password" name="user_passwords[{{ $u->id }}]" placeholder="••••••••" style="padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.85rem; width:180px;">
                            </td>
                            <td style="padding:16px 12px; text-align:right;">
                                <span style="font-size:0.75rem; color:#94a3b8; font-weight:600;">Individual Account</span>
                            </td>
                        </tr>
                        @empty
                        @if(!$selectedRole->email)
                        <tr>
                            <td colspan="4" style="padding:30px; text-align:center; color:#94a3b8; font-style:italic;">No logins assigned to this role yet.</td>
                        </tr>
                        @endif
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    @else
    <div style="padding:60px;background:#fff;border-radius:20px;border:2px dashed #e2e8f0;text-align:center;">
        <svg width="48" height="48" fill="none" stroke="#64748b" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:16px;opacity:.4;"><path d="M12 15V3m0 12l-4-4m4 4l4-4M2 17l.621 2.485A2 2 0 004.561 21h14.878a2 2 0 001.94-1.515L22 17"/></svg>
        <p style="color:#64748b;font-weight:600;">Please select a role from the dropdown above to manage its permissions.</p>
    </div>
    @endif
</div>
@endsection
