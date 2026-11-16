@extends('layouts.app')
@section('title', $staff->name)

@section('content')
    <style>
        :root {
            --y1: #F7DF79;
            --y2: #FBEFBC;
            --yd: #c9a800;
            --yk: #a07800;
            --ybg: #fffdf0;
        }

        /* ── Layout ── */
        .detail-wrap {
            display: grid;
            grid-template-columns: 290px 1fr;
            gap: 20px;
            align-items: start;
        }

        @media(max-width:900px) {
            .detail-wrap {
                grid-template-columns: 1fr;
            }
        }

        /* ── Profile Card ── */
        .profile-card {
            background: #fff;
            border: 1.5px solid #e9e0c0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(199, 168, 0, .1);
            position: sticky;
            top: 20px;
        }

        .profile-banner {
            width: 100%;
            background: linear-gradient(135deg, var(--y1), var(--yd));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 0 24px;
            flex-direction: column;
            gap: 10px;
        }

        .profile-avatar-lg {
            width: 72px;
            height: 72px;
            border-radius: 18px;
            background: rgba(255, 255, 255, .35);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #18181b;
            font-size: 2rem;
            font-weight: 800;
            border: 3px solid rgba(255, 255, 255, .6);
        }

        .profile-banner-name {
            font-size: 1.1rem;
            font-weight: 800;
            color: #18181b;
            margin: 0;
        }

        .profile-banner-role {
            font-size: .8rem;
            color: #5a4200;
            margin: 0;
            font-weight: 600;
        }

        .profile-body {
            padding: 16px 18px;
        }

        .p-stat {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #faf6e8;
            font-size: .82rem;
        }

        .p-stat:last-child {
            border-bottom: none;
        }

        .p-lbl {
            color: #64748b;
        }

        .p-val {
            font-weight: 700;
            color: #1e293b;
            text-align: right;
            max-width: 60%;
            word-break: break-word;
        }

        .p-val-sm {
            font-size: .75rem;
        }

        .badge-active {
            background: var(--y2);
            color: var(--yk);
            padding: 3px 10px;
            border-radius: 99px;
            font-size: .7rem;
            font-weight: 700;
        }

        .badge-inactive {
            background: #fecaca;
            color: #7f1d1d;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: .7rem;
            font-weight: 700;
        }

        .profile-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 14px 18px;
            border-top: 1px solid #f5efc8;
        }

        .btn-edit-profile {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 14px;
            border-radius: 10px;
            font-size: .83rem;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            text-decoration: none;
            border: none;
            transition: .2s;
            background: linear-gradient(135deg, var(--y1), var(--yd));
            color: #18181b;
            box-shadow: 0 3px 10px rgba(199, 168, 0, .2);
        }

        .btn-edit-profile:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 16px rgba(199, 168, 0, .3);
            color: #18181b;
        }

        .btn-del-profile {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 14px;
            border-radius: 10px;
            font-size: .83rem;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            text-decoration: none;
            border: 1.5px solid #fca5a5;
            background: #fef2f2;
            color: #dc2626;
            transition: .2s;
        }

        .btn-del-profile:hover {
            background: #fee2e2;
            border-color: #f87171;
        }

        /* ── Right Panel ── */
        .right-panel {
            min-width: 0;
        }

        /* ── Tabs ── */
        .tabs-wrap {
            display: flex;
            gap: 2px;
            margin-bottom: 18px;
            background: #f8f8f8;
            border-radius: 12px;
            padding: 4px;
            overflow-x: auto;
        }

        .tab-btn {
            padding: 9px 18px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: .85rem;
            font-weight: 600;
            color: #64748b;
            border-radius: 9px;
            font-family: 'Outfit', sans-serif;
            transition: .2s;
            white-space: nowrap;
        }

        .tab-btn.active {
            background: #fff;
            color: #18181b;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
        }

        .tab-btn:hover:not(.active) {
            background: rgba(255, 255, 255, .6);
            color: #374151;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* ── Section Cards ── */
        .sec-card {
            background: #fff;
            border: 1.5px solid #e9e0c0;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 16px;
            box-shadow: 0 1px 4px rgba(199, 168, 0, .06);
        }

        .sec-head {
            padding: 14px 18px;
            border-bottom: 1px solid #f5efc8;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--ybg);
        }

        .sec-title {
            font-size: .78rem;
            font-weight: 700;
            color: #a07800;
            text-transform: uppercase;
            letter-spacing: .07em;
        }

        .btn-toggle {
            padding: 6px 14px;
            border: 1.5px solid #e9e0c0;
            border-radius: 8px;
            background: #fff;
            color: #64748b;
            font-size: .78rem;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            transition: .2s;
        }

        .btn-toggle:hover {
            border-color: var(--yd);
            color: var(--yk);
        }

        .btn-toggle.active {
            background: var(--y2);
            border-color: var(--yd);
            color: var(--yk);
        }

        /* ── Inline Forms ── */
        .inline-form {
            padding: 18px;
            border-bottom: 1px solid #f5efc8;
            background: #fffdf5;
            display: none;
        }

        .inline-form.open {
            display: block;
        }

        .f-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        .f-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        @media(max-width:600px) {

            .f-grid-2,
            .f-grid-3 {
                grid-template-columns: 1fr;
            }
        }

        .f-label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }

        .f-label span {
            font-weight: 400;
            color: #94a3b8;
        }

        .f-input,
        .f-select {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            font-size: .875rem;
            font-family: 'Outfit', sans-serif;
            color: #18181b;
            background: #fff;
            outline: none;
            transition: .2s;
            box-sizing: border-box;
        }

        .f-input:focus,
        .f-select:focus {
            border-color: var(--yd);
            box-shadow: 0 0 0 3px rgba(199, 168, 0, .1);
        }

        .btn-submit {
            padding: 9px 22px;
            border: none;
            background: linear-gradient(135deg, var(--y1), var(--yd));
            border-radius: 9px;
            color: #18181b;
            font-size: .85rem;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            transition: .2s;
            box-shadow: 0 3px 10px rgba(199, 168, 0, .2);
        }

        .btn-submit:hover {
            transform: translateY(-1px);
        }

        /* ── Table ── */
        .rec-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rec-table th {
            padding: 11px 16px;
            font-size: .72rem;
            font-weight: 700;
            color: #a07800;
            text-transform: uppercase;
            letter-spacing: .05em;
            text-align: left;
            background: #fffdf0;
            border-bottom: 1px solid #f5efc8;
        }

        .rec-table td {
            padding: 11px 16px;
            font-size: .85rem;
            color: #374151;
            border-bottom: 1px solid #faf6e8;
        }

        .rec-table tr:last-child td {
            border-bottom: none;
        }

        .rec-table tr:hover td {
            background: #fffdf5;
        }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: .7rem;
            font-weight: 700;
        }

        .badge-present {
            background: var(--y2);
            color: var(--yk);
        }

        .badge-absent {
            background: #fecaca;
            color: #7f1d1d;
        }

        .badge-late {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-halfday,
        .badge-halfdayday {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-leave {
            background: #f3e8ff;
            color: #6b21a8;
        }

        .badge-morning {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-afternoon {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-evening {
            background: #f3e8ff;
            color: #6b21a8;
        }

        .badge-fullday {
            background: var(--y2);
            color: var(--yk);
        }

        /* ── Empty State ── */
        .empty-tbl {
            text-align: center;
            padding: 36px;
            color: #94a3b8;
            font-size: .875rem;
        }

        /* ── Star Rating ── */
        .star-rating {
            display: flex;
            gap: 4px;
            margin: 10px 0;
        }

        .star-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 2rem;
            line-height: 1;
            padding: 2px;
            transition: .15s;
            color: #e2d48a;
        }

        .star-btn.lit {
            color: #f59e0b;
        }

        .star-btn:hover {
            transform: scale(1.15);
        }

        /* ── Stats Bar ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 12px;
            padding: 16px;
        }

        .stat-box {
            text-align: center;
            background: var(--ybg);
            border: 1.5px solid #f0e8b0;
            border-radius: 12px;
            padding: 14px 10px;
        }

        .stat-num {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--yd);
        }

        .stat-sub {
            font-size: .7rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-top: 3px;
        }

        /* ── Alert ── */
        .alert-success {
            background: var(--ybg);
            border: 1.5px solid #f0e8b0;
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--yk);
            font-size: .875rem;
            margin-bottom: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
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

    @if(session('success'))
        <div class="alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="detail-wrap">

        {{-- ── Left: Profile Card ── --}}
        <div class="profile-card">
            <div class="profile-banner">
                <div class="profile-avatar-lg">{{ strtoupper(substr($staff->name, 0, 1)) }}</div>
                <p class="profile-banner-name">{{ $staff->name }}</p>
            </div>

            <div class="profile-body">
                <div class="p-stat">
                    <span class="p-lbl">Email</span>
                    <span class="p-val p-val-sm">{{ $staff->email }}</span>
                </div>
                <div class="p-stat">
                    <span class="p-lbl">Phone</span>
                    <span class="p-val">{{ $staff->phone ?? '—' }}</span>
                </div>
                <div class="p-stat">
                    <span class="p-lbl">Hired</span>
                    <span class="p-val">{{ $staff->hiring_date->format('M d, Y') }}</span>
                </div>
                <div class="p-stat">
                    <span class="p-lbl">Rate</span>
                    <span class="p-val">PKR {{ number_format($staff->hourly_rate, 0) }}/hr</span>
                </div>
                <div class="p-stat">
                    <span class="p-lbl">Status</span>
                    <span
                        class="{{ $staff->status ? 'badge-active' : 'badge-inactive' }}">{{ $staff->status ? 'Active' : 'Inactive' }}</span>
                </div>
                <div class="p-stat">
                    <span class="p-lbl">Rating</span>
                    <span class="p-val">
                        @for($i = 1; $i <= 5; $i++)
                            <span style="color:{{ $i <= $staff->rating ? '#f59e0b' : '#e2d48a' }};font-size:.95rem;">★</span>
                        @endfor
                    </span>
                </div>
            </div>

            <div class="profile-actions">
                <a href="{{ route('staff.edit', $staff) }}" class="btn-edit-profile">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                    Edit Profile
                </a>
                <button type="button" class="btn-del-profile" onclick="openDeleteModal()">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" />
                        <path d="M10 11v6M14 11v6" />
                    </svg>
                    Delete Staff
                </button>
            </div>
        </div>

        {{-- ── Right: Tabs ── --}}
        <div class="right-panel">
            <div class="tabs-wrap">
                <button class="tab-btn active" onclick="switchTab(this,'attendance')">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                        style="display:inline;vertical-align:middle;margin-right:4px;">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    Attendance
                </button>
                <button class="tab-btn" onclick="switchTab(this,'shifts')">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                        style="display:inline;vertical-align:middle;margin-right:4px;">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    Shifts
                </button>
                <button class="tab-btn" onclick="switchTab(this,'performance')">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                        style="display:inline;vertical-align:middle;margin-right:4px;">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>
                    Performance
                </button>
            </div>

            {{-- ═══ ATTENDANCE TAB ═══ --}}
            <div id="attendance" class="tab-content active">
                <div class="sec-card">
                    <div class="sec-head">
                        <span class="sec-title">Attendance Log</span>
                        <button class="btn-toggle" id="att-toggle" onclick="toggleForm('att-form','att-toggle','Record')">+
                            Record</button>
                    </div>

                    <div id="att-form" class="inline-form">
                        <form method="POST" action="{{ route('staff.record-attendance') }}">
                            @csrf
                            <input type="hidden" name="staff_id" value="{{ $staff->id }}">
                            <div class="f-grid-2">
                                <div>
                                    <label class="f-label">Date <span>(leave blank for today)</span></label>
                                    <input type="date" name="attendance_date" class="f-input">
                                </div>
                                <div>
                                    <label class="f-label">Status *</label>
                                    <select name="status" required class="f-select">
                                        <option value="present">Present</option>
                                        <option value="absent">Absent</option>
                                        <option value="late">Late</option>
                                        <option value="half_day">Half Day</option>
                                        <option value="leave">Leave</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn-submit">Save Attendance</button>
                        </form>
                    </div>

                    @if($attendances->count())
                        <table class="rec-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $rec)
                                    <tr>
                                        <td>{{ $rec->attendance_date->format('M d, Y') }}</td>
                                        <td><span
                                                class="badge badge-{{ str_replace('_', '', $rec->status) }}">{{ ucfirst(str_replace('_', ' ', $rec->status)) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div style="padding:12px 16px;">{{ $attendances->links() }}</div>
                    @else
                        <p class="empty-tbl">No attendance records yet — click "+ Record" to add one.</p>
                    @endif
                </div>
            </div>

            {{-- ═══ SHIFTS TAB ═══ --}}
            <div id="shifts" class="tab-content">
                <div class="sec-card">
                    <div class="sec-head">
                        <span class="sec-title">Shift Schedule</span>
                        <button class="btn-toggle" id="shift-toggle"
                            onclick="toggleForm('shift-form','shift-toggle','Schedule')">+ Schedule</button>
                    </div>

                    <div id="shift-form" class="inline-form">
                        <form method="POST" action="{{ route('staff.shifts.store-inline', $staff) }}">
                            @csrf
                            <div class="f-grid-2">
                                <div>
                                    <label class="f-label">Date *</label>
                                    <input type="date" name="shift_date" class="f-input" required
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div>
                                    <label class="f-label">Shift Type *</label>
                                    <select name="shift_type" class="f-select" required>
                                        <option value="morning">Morning</option>
                                        <option value="afternoon">Afternoon</option>
                                        <option value="evening">Evening</option>
                                        <option value="full_day" selected>Full Day</option>
                                    </select>
                                </div>
                            </div>
                            <div class="f-grid-3">
                                <div>
                                    <label class="f-label">Start Time *</label>
                                    <input type="time" name="start_time" class="f-input" required value="09:00">
                                </div>
                                <div>
                                    <label class="f-label">End Time *</label>
                                    <input type="time" name="end_time" class="f-input" required value="18:00">
                                </div>
                                <div>
                                    <label class="f-label">Break (mins)</label>
                                    <input type="number" name="break_duration" class="f-input" min="0"
                                        placeholder="e.g. 30">
                                </div>
                            </div>
                            <button type="submit" class="btn-submit">Save Shift</button>
                        </form>
                    </div>

                    @if($shifts->count())
                        <table class="rec-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Start</th>
                                    <th>End</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shifts as $shift)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($shift->shift_date)->format('M d, Y') }}</td>
                                        <td><span
                                                class="badge badge-{{ $shift->shift_type }}">{{ ucfirst(str_replace('_', ' ', $shift->shift_type)) }}</span>
                                        </td>
                                        <td>{{ $shift->start_time }}</td>
                                        <td>{{ $shift->end_time }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div style="padding:12px 16px;">{{ $shifts->links() }}</div>
                    @else
                        <p class="empty-tbl">No shifts scheduled yet — click "+ Schedule" to add one.</p>
                    @endif
                </div>
            </div>

            {{-- ═══ PERFORMANCE TAB ═══ --}}
            <div id="performance" class="tab-content">

                {{-- Star Rating Card --}}
                <div class="sec-card" style="margin-bottom:16px;">
                    <div class="sec-head">
                        <span class="sec-title">Admin Rating</span>
                        <span style="font-size:.78rem;color:#94a3b8;">1 = Poor &nbsp;·&nbsp; 5 = Excellent</span>
                    </div>
                    <div style="padding:18px;">
                        <form method="POST" action="{{ route('staff.rate', $staff) }}" id="rating-form">
                            @csrf
                            <input type="hidden" name="rating" id="rating-value" value="{{ $staff->rating }}">
                            <div class="star-rating" id="star-row">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" class="star-btn {{ $i <= $staff->rating ? 'lit' : '' }}"
                                        data-val="{{ $i }}" onclick="setRating({{ $i }})"
                                        title="{{ $i }} star{{ $i > 1 ? 's' : '' }}">★</button>
                                @endfor
                            </div>
                            <p id="rating-label" style="font-size:.85rem;color:#64748b;margin:4px 0 14px;">
                                {{ $staff->rating > 0 ? ['', '★ Poor', '★★ Fair', '★★★ Good', '★★★★ Very Good', '★★★★★ Excellent'][$staff->rating] : 'No rating yet — click a star' }}
                            </p>
                            <button type="submit" class="btn-submit">Save Rating</button>
                        </form>
                    </div>
                </div>

                {{-- Upsell Stats --}}
                @if($upsellPerformance)
                    <div class="sec-card">
                        <div class="sec-head"><span class="sec-title">Upsell Performance</span></div>
                        <div class="stats-row">
                            <div class="stat-box">
                                <div class="stat-num">{{ $upsellPerformance->total_upsells }}</div>
                                <div class="stat-sub">Total Upsells</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-num" style="font-size:1.1rem;">PKR
                                    {{ number_format($upsellPerformance->upsell_revenue, 0) }}</div>
                                <div class="stat-sub">Revenue</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-num">{{ $upsellPerformance->conversion_rate }}%</div>
                                <div class="stat-sub">Conversion</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-num" style="font-size:1.1rem;">PKR
                                    {{ number_format($upsellPerformance->average_upsell_value, 0) }}</div>
                                <div class="stat-sub">Avg Value</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="sec-card">
                        <p class="empty-tbl">No upsell performance data recorded yet.</p>
                    </div>
                @endif
            </div>

        </div>{{-- end right-panel --}}
    </div>{{-- end detail-wrap --}}

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
                <strong style="color:#0f172a;">{{ $staff->name }}</strong> and ALL their records.<br>This <strong>cannot be
                    undone</strong>.</p>
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
        /* ── Tab switching ── */
        function switchTab(btn, tab) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(tab).classList.add('active');
        }

        /* ── Toggle inline forms ── */
        function toggleForm(formId, btnId, label) {
            const form = document.getElementById(formId);
            const btn = document.getElementById(btnId);
            const open = form.classList.toggle('open');
            btn.classList.toggle('active', open);
            btn.textContent = open ? '✕ Close' : '+ ' + label;
        }

        /* ── Star rating ── */
        const labels = ['', '★ Poor', '★★ Fair', '★★★ Good', '★★★★ Very Good', '★★★★★ Excellent'];
        function setRating(val) {
            document.getElementById('rating-value').value = val;
            document.querySelectorAll('.star-btn').forEach((btn, idx) => {
                btn.classList.toggle('lit', idx < val);
            });
            document.getElementById('rating-label').textContent = labels[val];
        }

        /* ── Delete modal ── */
        function openDeleteModal() { document.getElementById('deleteModal').style.display = 'flex'; }
        function closeDeleteModal() { document.getElementById('deleteModal').style.display = 'none'; }
    </script>
@endsection