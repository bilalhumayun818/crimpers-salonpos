@extends('layouts.app')
@section('title', 'Edit Employee')

@section('content')
    <style>
        :root {
            --y1: #F7DF79;
            --y2: #FBEFBC;
            --yd: #c9a800;
            --yk: #a07800;
            --ybg: #fffdf0;
        }

        .page-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
        }

        .back-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #f4f4f5;
            border: 1.5px solid #e4e4e7;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #52525b;
            text-decoration: none;
            transition: .2s;
            flex-shrink: 0;
        }

        .back-btn:hover {
            background: #e4e4e7;
            color: #18181b;
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 3px;
        }

        .page-sub {
            font-size: .85rem;
            color: #64748b;
            margin: 0;
        }

        .form-wrap {
            max-width: 740px;
        }

        .form-card {
            background: #fff;
            border: 1.5px solid #e9e0c0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(199, 168, 0, .08);
            margin-bottom: 16px;
        }

        .form-card-head {
            padding: 16px 22px;
            background: var(--ybg);
            border-bottom: 1.5px solid #f0e8b0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-card-head-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--y1), var(--yd));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #18181b;
            flex-shrink: 0;
        }

        .form-card-head-title {
            font-size: .9rem;
            font-weight: 700;
            color: #18181b;
        }

        .form-card-body {
            padding: 22px;
        }

        .f-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .f-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }

        @media(max-width:600px) {

            .f-grid-2,
            .f-grid-3 {
                grid-template-columns: 1fr;
            }
        }

        .f-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .f-label {
            font-size: .85rem;
            font-weight: 600;
            color: #334155;
        }

        .f-label span {
            font-weight: 400;
            color: #94a3b8;
        }

        .f-input,
        .f-select,
        .f-textarea {
            width: 100%;
            padding: 10px 13px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: .9rem;
            color: #18181b;
            background: #fff;
            outline: none;
            transition: .2s;
            box-sizing: border-box;
        }

        .f-input:focus,
        .f-select:focus,
        .f-textarea:focus {
            border-color: var(--yd);
            box-shadow: 0 0 0 3px rgba(199, 168, 0, .12);
        }

        .f-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 8px;
        }

        .service-check {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 13px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            transition: .2s;
        }

        .service-check:hover {
            border-color: var(--yd);
            background: var(--ybg);
        }

        .service-check input[type=checkbox] {
            width: 16px;
            height: 16px;
            accent-color: var(--yd);
            cursor: pointer;
            flex-shrink: 0;
        }

        .service-check span {
            font-size: .83rem;
            color: #374151;
        }

        .toggle-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toggle-row input[type=checkbox] {
            width: 18px;
            height: 18px;
            accent-color: var(--yd);
            cursor: pointer;
        }

        .toggle-row span {
            font-size: .9rem;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
        }

        .error-box {
            background: #fef2f2;
            border: 1.5px solid #fecaca;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .error-box ul {
            margin: 0;
            padding-left: 18px;
            color: #ef4444;
            font-size: .85rem;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            padding-top: 4px;
        }

        .btn-cancel {
            flex: 1;
            padding: 11px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            text-align: center;
            color: #64748b;
            font-weight: 700;
            text-decoration: none;
            font-family: 'Outfit', sans-serif;
            font-size: .9rem;
            transition: .2s;
            background: #fff;
        }

        .btn-cancel:hover {
            background: #f4f4f5;
            border-color: #d4d4d8;
            color: #18181b;
        }

        .btn-save {
            flex: 1;
            padding: 11px;
            background: linear-gradient(135deg, var(--y1), var(--yd));
            border: none;
            border-radius: 10px;
            color: #18181b;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            font-size: .9rem;
            box-shadow: 0 3px 12px rgba(199, 168, 0, .25);
            transition: .2s;
        }

        .btn-save:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 18px rgba(199, 168, 0, .35);
        }

        .role-row {
            display: flex;
            gap: 8px;
            align-items: stretch;
        }

        .role-row .f-select {
            flex: 1;
        }

        .role-add-btn {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--ybg);
            border: 1.5px solid #e9e0c0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--yk);
            text-decoration: none;
            transition: .2s;
            flex-shrink: 0;
        }

        .role-add-btn:hover {
            background: var(--y2);
            border-color: var(--yd);
        }

        /* Danger Zone */
        .danger-zone {
            background: #fff;
            border: 1.5px solid #fca5a5;
            border-radius: 18px;
            overflow: hidden;
            margin-top: 8px;
        }

        .danger-zone-head {
            padding: 16px 22px;
            background: #fef2f2;
            border-bottom: 1.5px solid #fecaca;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .danger-zone-head-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ef4444;
            flex-shrink: 0;
        }

        .danger-zone-head-title {
            font-size: .9rem;
            font-weight: 700;
            color: #dc2626;
        }

        .danger-zone-body {
            padding: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .danger-zone-desc {
            font-size: .875rem;
            color: #64748b;
            line-height: 1.5;
        }

        .btn-delete {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            background: #ef4444;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            font-size: .875rem;
            box-shadow: 0 3px 10px rgba(239, 68, 68, .25);
            transition: .2s;
            white-space: nowrap;
        }

        .btn-delete:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: translateY(12px) scale(.97)
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1)
            }
        }
    </style>

    <div class="form-wrap">
        <div class="page-header">
            <a href="{{ route('staff.index') }}" class="back-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
            </a>
            <div>
                <div class="page-title">Edit Employee</div>
                <div class="page-sub">{{ $staff->name }} &mdash; {{ $staff->role->name ?? 'General Staff' }}</div>
            </div>
        </div>

        @if($errors->any())
            <div class="error-box">
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('staff.update', $staff) }}" method="POST">
            @csrf @method('PUT')

            {{-- Personal Info --}}
            <div class="form-card">
                <div class="form-card-head">
                    <div class="form-card-head-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </div>
                    <span class="form-card-head-title">Personal Information</span>
                </div>
                <div class="form-card-body">
                    <div class="f-grid-2" style="margin-bottom:16px;">
                        <div class="f-group">
                            <label class="f-label">Full Name <span>*</span></label>
                            <input type="text" name="name" value="{{ old('name', $staff->name) }}" required class="f-input">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Email Address <span>*</span></label>
                            <input type="email" name="email" value="{{ old('email', $staff->email) }}" required
                                class="f-input">
                        </div>
                    </div>
                    <div class="f-grid-2">
                        <div class="f-group">
                            <label class="f-label">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}" class="f-input">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Bio</label>
                            <input type="text" name="bio" value="{{ old('bio', $staff->bio) }}" class="f-input">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Account & Role --}}
            <div class="form-card">
                <div class="form-card-head">
                    <div class="form-card-head-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0110 0v4" />
                        </svg>
                    </div>
                    <span class="form-card-head-title">Account &amp; Role</span>
                </div>
                <div class="form-card-body">
                    <div class="f-grid-2" style="margin-bottom:16px;">
                        <div class="f-group">
                            <label class="f-label">New Password <span>(leave blank to keep current)</span></label>
                            <input type="password" name="password" class="f-input" placeholder="Min 8 characters">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="f-input"
                                placeholder="Re-enter new password">
                        </div>
                    </div>
                    <div class="f-grid-2">
                        <div class="f-group">
                            <label class="f-label">Assign Role <span>(optional)</span></label>
                            <div class="role-row">
                                <select name="staff_role_id" class="f-select">
                                    <option value="">Select a Role...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('staff_role_id', $staff->staff_role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('staff-roles.create') }}" class="role-add-btn" title="Create New Role">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"
                                        viewBox="0 0 24 24">
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="f-group">
                            <label class="f-label">Status</label>
                            <div class="toggle-row" style="margin-top:10px;">
                                <input type="checkbox" name="status" value="1" id="status-toggle" {{ old('status', $staff->status) ? 'checked' : '' }}>
                                <label for="status-toggle"
                                    style="font-size:.9rem;font-weight:600;color:#334155;cursor:pointer;">Active
                                    Employee</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Employment Details --}}
            <div class="form-card">
                <div class="form-card-head">
                    <div class="form-card-head-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <rect x="2" y="7" width="20" height="14" rx="2" />
                            <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                        </svg>
                    </div>
                    <span class="form-card-head-title">Employment Details</span>
                </div>
                <div class="form-card-body">
                    <div class="f-group">
                        <label class="f-label">Joining Date</label>
                        <input type="date" name="hiring_date"
                            value="{{ old('hiring_date', $staff->hiring_date->format('Y-m-d')) }}" class="f-input">
                    </div>
                </div>
            </div>

            {{-- Shift Settings --}}
            <div class="form-card">
                <div class="form-card-head">
                    <div class="form-card-head-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <span class="form-card-head-title">Shift Settings</span>
                </div>
                <div class="form-card-body">
                    <div class="f-grid-2">
                        <div class="f-group">
                            <label class="f-label">Shift Start <span>*</span></label>
                            <input type="time" name="shift_start" value="{{ old('shift_start', $staff->shift_start) }}" required class="f-input">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Shift End <span>*</span></label>
                            <input type="time" name="shift_end" value="{{ old('shift_end', $staff->shift_end) }}" required class="f-input">
                        </div>
                    </div>
                    <p style="margin-top:10px; font-size:.78rem; color:#94a3b8;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        These define the employee&apos;s default working hours used for attendance tracking.
                    </p>
                </div>
            </div>

            {{-- Compensation --}}
            <div class="form-card">
                <div class="form-card-head">
                    <div class="form-card-head-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                        </svg>
                    </div>
                    <span class="form-card-head-title">Compensation & Commission</span>
                </div>
                <div class="form-card-body">
                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                        <div class="f-group">
                            <label class="f-label">Base Salary <span>(PKR / month)</span></label>
                            <input type="number" name="base_salary" value="{{ old('base_salary', $staff->base_salary) }}" min="0" step="0.01" class="f-input" placeholder="e.g. 25000">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Comm. per Customer <span>(%)</span></label>
                            <input type="number" name="commission_per_customer" value="{{ old('commission_per_customer', $staff->commission_per_customer) }}" min="0" max="100" step="0.01" class="f-input" placeholder="e.g. 5">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Comm. per Service <span>(%)</span></label>
                            <input type="number" name="commission_per_service" value="{{ old('commission_per_service', $staff->commission_per_service) }}" min="0" max="100" step="0.01" class="f-input" placeholder="e.g. 10">
                        </div>
                    </div>
                    <p style="margin-top:10px; font-size:.78rem; color:#94a3b8;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Total pay = Base Salary + (% Commission per Customer served) + (% Commission per Service performed).
                    </p>
                </div>
            </div>

            {{-- Services --}}
            @if($services->count())
                <div class="form-card">
                    <div class="form-card-head" style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="form-card-head-icon">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" />
                                </svg>
                            </div>
                            <span class="form-card-head-title">Assigned Services</span>
                        </div>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 700; color: #1e293b;">
                            <input type="checkbox" id="select-all-services" style="width: 16px; height: 16px; accent-color: var(--yd); cursor: pointer;">
                            Select All
                        </label>
                    </div>
                    <div class="form-card-body">
                        <div class="services-grid">
                            @foreach($services as $service)
                                <label class="service-check">
                                    <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" class="individual-service-check" {{ $staff->services->contains($service->id) ? 'checked' : '' }}>
                                    <span>{{ $service->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="form-actions">
                <a href="{{ route('staff.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-save">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                        style="display:inline;vertical-align:middle;margin-right:5px;">
                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" />
                        <polyline points="17 21 17 13 7 13 7 21" />
                        <polyline points="7 3 7 8 15 8" />
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>

        {{-- Danger Zone --}}
        <div class="danger-zone">
            <div class="danger-zone-head">
                <div class="danger-zone-head-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <span class="danger-zone-head-title">Danger Zone</span>
            </div>
            <div class="danger-zone-body">
                <div class="danger-zone-desc">
                    <strong style="color:#dc2626;">Delete this employee</strong><br>
                    Permanently removes {{ $staff->name }} and all associated records. This cannot be undone.
                </div>
                <button type="button" class="btn-delete" onclick="openDeleteModal()">Delete Employee</button>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="delete-form" method="POST" action="{{ route('staff.destroy', $staff) }}" style="display:none;">
        @csrf @method('DELETE')
    </form>

    <!-- Delete Modal -->
    <div id="deleteModal"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
        <div
            style="background:#fff;padding:32px 28px;border-radius:20px;width:100%;max-width:400px;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.15);animation:modalIn .2s ease-out;margin:16px;">
            <div
                style="width:56px;height:56px;border-radius:50%;background:#fee2e2;color:#ef4444;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
                <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="3 6 5 6 21 6" />
                    <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" />
                    <path d="M10 11v6M14 11v6" />
                    <path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2" />
                </svg>
            </div>
            <h3 style="margin:0 0 8px;font-size:1.25rem;font-weight:800;color:#0f172a;">Delete Employee?</h3>
            <p style="margin:0 0 24px;color:#64748b;font-size:.9rem;line-height:1.6;">You are about to permanently delete
                <strong style="color:#0f172a;">{{ $staff->name }}</strong> and all their records.<br>This <strong>cannot be
                    undone</strong>.
            </p>
            <div style="display:flex;gap:12px;">
                <button type="button" onclick="closeDeleteModal()"
                    style="flex:1;padding:11px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;font-size:.9rem;">Cancel</button>
                <button type="button" onclick="document.getElementById('delete-form').submit()"
                    style="flex:1;padding:11px;border:none;border-radius:10px;background:#ef4444;color:#fff;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;font-size:.9rem;box-shadow:0 4px 14px rgba(239,68,68,.3);">Yes,
                    Delete</button>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal() { document.getElementById('deleteModal').style.display = 'flex'; }
        function closeDeleteModal() { document.getElementById('deleteModal').style.display = 'none'; }
        
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all-services');
            const serviceChecks = document.querySelectorAll('.individual-service-check');
            
            if (selectAll) {
                // Initialize "Select All" state on load
                const allChecked = Array.from(serviceChecks).every(c => c.checked);
                if (serviceChecks.length > 0) selectAll.checked = allChecked;

                // When "Select All" is clicked
                selectAll.addEventListener('change', function() {
                    serviceChecks.forEach(cb => {
                        cb.checked = selectAll.checked;
                    });
                });

                // Update "Select All" state if user manually toggles individual checkboxes
                serviceChecks.forEach(cb => {
                    cb.addEventListener('change', function() {
                        const allChecked = Array.from(serviceChecks).every(c => c.checked);
                        selectAll.checked = allChecked;
                    });
                });
            }
        });
    </script>
@endsection