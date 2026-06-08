@extends('layouts.app')
@section('title', 'Add Employee')

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
            padding: 14px 0;
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
    </style>

    <div class="form-wrap">
        <div class="page-header">
            <a href="{{ route('staff.index') }}" class="back-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
            </a>
            <div>
                <div class="page-title">Add Employee</div>
                <div class="page-sub">Create a new team member profile</div>
            </div>
        </div>

        @if($errors->any())
            <div class="error-box">
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('staff.store') }}" method="POST">
            @csrf

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
                            <input type="text" name="name" value="{{ old('name') }}" required class="f-input"
                                placeholder="e.g. Ahmed Khan">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Email Address <span>(optional)</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" class="f-input"
                                placeholder="staff@example.com">
                        </div>
                    </div>
                    <div class="f-grid-2">
                        <div class="f-group">
                            <label class="f-label">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="f-input"
                                placeholder="+92 300 0000000">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Bio <span>(optional)</span></label>
                            <input type="text" name="bio" value="{{ old('bio') }}" class="f-input"
                                placeholder="Short description...">
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
                            <label class="f-label">Login Password <span>(optional)</span></label>
                            <input type="password" name="password" class="f-input" placeholder="Min 8 characters">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Confirm Password <span>(optional)</span></label>
                            <input type="password" name="password_confirmation" class="f-input"
                                placeholder="Re-enter password">
                        </div>
                    </div>
                    <div class="f-grid-2">
                        <div class="f-group">
                            <label class="f-label">Assign Role <span>(optional)</span></label>
                            <div class="role-row">
                                <select name="staff_role_id" class="f-select">
                                    <option value="">Select a Role...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
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
                            <div class="toggle-row">
                                <input type="checkbox" name="status" value="1" id="status-toggle" {{ old('status', true) ? 'checked' : '' }}>
                                <label for="status-toggle" class="toggle-row" style="padding:0;cursor:pointer;"><span>Active
                                        Employee</span></label>
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
                    <div class="f-grid-3">
                        <div class="f-group">
                            <label class="f-label">Joining Date</label>
                            <input type="date" name="hiring_date" value="{{ old('hiring_date', date('Y-m-d')) }}"
                                class="f-input">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Shift Start <span>*</span></label>
                            <input type="time" name="shift_start" value="{{ old('shift_start', $currentBranch?->opening_time ? \Carbon\Carbon::parse($currentBranch->opening_time)->format('H:i') : '') }}" required class="f-input">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Shift End <span>*</span></label>
                            <input type="time" name="shift_end" value="{{ old('shift_end', $currentBranch?->closing_time ? \Carbon\Carbon::parse($currentBranch->closing_time)->format('H:i') : '') }}" required class="f-input">
                        </div>
                    </div>
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
                    <div class="f-grid-3">
                        <div class="f-group">
                            <label class="f-label">Base Salary <span>(PKR / month)</span></label>
                            <input type="number" name="base_salary" value="{{ old('base_salary', 0) }}" min="0" step="0.01" class="f-input" placeholder="e.g. 25000">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Comm. per Customer <span>(%)</span></label>
                            <input type="number" name="commission_per_customer" value="{{ old('commission_per_customer', 0) }}" min="0" max="100" step="0.01" class="f-input" placeholder="e.g. 5">
                        </div>
                        <div class="f-group">
                            <label class="f-label">Comm. per Service <span>(%)</span></label>
                            <input type="number" name="commission_per_service" value="{{ old('commission_per_service', 0) }}" min="0" max="100" step="0.01" class="f-input" placeholder="e.g. 10">
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
                                    <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" class="individual-service-check">
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
                    Add Employee
                </button>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all-services');
            const serviceChecks = document.querySelectorAll('.individual-service-check');
            
            if (selectAll) {
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
