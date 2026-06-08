@extends('layouts.app')
@section('title', 'Employee Management')

@section('content')
    <style>
        :root {
            --y1: #F7DF79;
            --y2: #FBEFBC;
            --yd: #c9a800;
            --yk: #a07800;
            --ybg: #fffdf0;
        }

        .staff-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .staff-title {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -.02em;
            margin-bottom: 4px;
        }

        .staff-sub {
            font-size: .85rem;
            color: #64748b;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
            flex-wrap: wrap;
        }

        .btn-primary {
            padding: 9px 18px;
            border: none;
            background: linear-gradient(135deg, var(--y1), var(--yd));
            border-radius: 10px;
            color: #18181b;
            font-size: .85rem;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            transition: .2s;
            box-shadow: 0 3px 10px rgba(199, 168, 0, .2);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 16px rgba(199, 168, 0, .3);
            color: #18181b;
        }

        .btn-dark {
            padding: 9px 18px;
            border: none;
            background: #18181b;
            border-radius: 10px;
            color: #fff;
            font-size: .85rem;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            transition: .2s;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .15);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-dark:hover {
            transform: translateY(-1px);
            background: #27272a;
            color: #fff;
        }

        .staff-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 16px;
        }

        .staff-card {
            background: #fff;
            border: 1px solid #e9e0c0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(199, 168, 0, .08);
            transition: .2s;
        }

        .staff-card:hover {
            box-shadow: 0 6px 20px rgba(199, 168, 0, .15);
            transform: translateY(-2px);
        }

        .staff-card-head {
            padding: 16px;
            border-bottom: 1px solid #f5efc8;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .staff-avatar {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--y1), var(--yd));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #18181b;
            font-weight: 800;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .staff-info h3 {
            font-size: .95rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .staff-info p {
            font-size: .8rem;
            color: #94a3b8;
            margin: 2px 0 0;
        }

        .staff-card-body {
            padding: 12px 16px;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .8rem;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #faf6e8;
        }

        .stat-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .stat-label {
            color: #64748b;
        }

        .stat-value {
            font-weight: 700;
            color: #1e293b;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: .7rem;
            font-weight: 700;
        }

        .status-active {
            background: var(--y2);
            color: var(--yk);
        }

        .status-inactive {
            background: #fecaca;
            color: #7f1d1d;
        }

        .staff-card-foot {
            padding: 12px 16px;
            border-top: 1px solid #f5efc8;
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            flex: 1;
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            background: #f4f4f5;
            border-radius: 8px;
            color: #18181b;
            font-size: .75rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            text-align: center;
            transition: .2s;
            font-family: 'Outfit', sans-serif;
        }

        .btn-sm:hover {
            background: #e4e4e7;
            border-color: #d4d4d8;
            color: #18181b;
        }

        .btn-sm-del {
            background: #fff;
            border-color: #fca5a5;
            color: #ef4444;
        }

        .btn-sm-del:hover {
            background: #fef2f2;
            border-color: #f87171;
            color: #dc2626;
        }

        .empty-state {
            background: #fff;
            border: 1px solid #e9e0c0;
            border-radius: 16px;
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state p {
            color: #94a3b8;
            font-size: .9rem;
            margin: 8px 0 0;
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

    <div class="staff-header">
        <div>
            <div class="staff-title">Employee Management</div>
            <div class="staff-sub">Manage team members, shifts, attendance &amp; performance</div>
        </div>
        <div class="header-actions">
            <a href="{{ route('staff-roles.index') }}" class="btn-dark">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" />
                </svg>
                Roles &amp; Settings
            </a>
            <a href="{{ route('staff.create') }}" class="btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Add Employee
            </a>
        </div>
    </div>

    @if($staff->count())
        <div class="staff-grid">
            @foreach($staff as $member)
                <div class="staff-card">
                    <div class="staff-card-head">
                        <div class="staff-avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                        <div class="staff-info">
                            <h3>{{ $member->name }}</h3>
                            <p>{{ $member->role->name ?? 'General Staff' }}</p>
                        </div>
                    </div>

                    <div class="staff-card-body">
                        <div class="stat-row">
                            <span class="stat-label">Hired</span>
                            <span class="stat-value">{{ $member->hiring_date->format('M Y') }}</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Base Salary</span>
                            <span class="stat-value">PKR {{ number_format($member->base_salary, 0) }}/mo</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Comm. / Customer</span>
                            <span class="stat-value">{{ number_format($member->commission_per_customer, 1) }}%</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Comm. / Service</span>
                            <span class="stat-value">{{ number_format($member->commission_per_service, 1) }}%</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">This Month Hours</span>
                            <span
                                class="stat-value">{{ rtrim(rtrim(number_format($member->attendances()->whereYear('attendance_date', now()->year)->whereMonth('attendance_date', now()->month)->count() * $member->shift_duration_hours, 2), '0'), '.') }}h</span>
                        </div>
                        @if($member->upsellPerformance)
                            <div class="stat-row">
                                <span class="stat-label">Upsells</span>
                                <span class="stat-value">{{ $member->upsellPerformance->total_upsells }}</span>
                            </div>
                        @endif
                        <div class="stat-row">
                            <span class="stat-label">Status</span>
                            <span class="status-badge {{ $member->status ? 'status-active' : 'status-inactive' }}">
                                {{ $member->status ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <div class="staff-card-foot">
                        <a href="{{ route('staff.edit', $member) }}" class="btn-sm">Edit</a>
                        <button type="button" class="btn-sm btn-sm-del"
                            onclick="openDeleteModal('{{ route('staff.destroy', $member) }}', '{{ addslashes($member->name) }}')">Delete</button>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top:20px;">{{ $staff->links() }}</div>
    @else
        <div class="empty-state">
            <svg width="52" height="52" fill="none" stroke="#c9a800" stroke-width="1.5" viewBox="0 0 24 24"
                style="margin:0 auto;opacity:.5;">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
            </svg>
            <p>No employees yet — add your first team member</p>
        </div>
    @endif

    <!-- Delete Form -->
    <form id="delete-form" method="POST" style="display:none;">@csrf @method('DELETE')</form>

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
            <p style="margin:0 0 24px;color:#64748b;font-size:.9rem;line-height:1.6;">You are about to delete <strong
                    id="delete-target-name" style="color:#0f172a;"></strong>.<br>This action <strong>cannot be
                    undone</strong>.</p>
            <div style="display:flex;gap:12px;">
                <button type="button" onclick="closeDeleteModal()"
                    style="flex:1;padding:11px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;font-size:.9rem;transition:.2s;">Cancel</button>
                <button type="button" onclick="document.getElementById('delete-form').submit()"
                    style="flex:1;padding:11px;border:none;border-radius:10px;background:#ef4444;color:#fff;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;font-size:.9rem;box-shadow:0 4px 14px rgba(239,68,68,.3);transition:.2s;">Yes,
                    Delete</button>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(url, name) {
            document.getElementById('delete-target-name').innerText = name;
            document.getElementById('delete-form').action = url;
            document.getElementById('deleteModal').style.display = 'flex';
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
    </script>
@endsection
