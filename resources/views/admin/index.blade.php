@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<style>
:root{--y1:#F7DF79;--y2:#FBEFBC;--yd:#c9a800;--yk:#a07800;--ybg:#fffdf0;}

/* ── Hero Banner ── */
.dash-hero{background:linear-gradient(135deg,#1a1a1a 0%,#2d2d2d 100%);border-radius:20px;padding:28px 32px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;gap:20px;overflow:hidden;position:relative;}
.dash-hero::before{content:'';position:absolute;top:-40px;right:-40px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(247,223,121,.18) 0%,transparent 70%);}
.dash-hero::after{content:'';position:absolute;bottom:-60px;left:30%;width:160px;height:160px;border-radius:50%;background:radial-gradient(circle,rgba(247,223,121,.08) 0%,transparent 70%);}
.dash-hero-left{position:relative;z-index:1;}
.dash-hero-greeting{font-size:.78rem;font-weight:700;color:var(--yd);text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px;}
.dash-hero-title{font-size:1.7rem;font-weight:800;color:#fff;letter-spacing:-.02em;margin:0 0 6px;}
.dash-hero-sub{font-size:.88rem;color:#a1a1aa;margin:0;}
.dash-hero-right{position:relative;z-index:1;text-align:right;flex-shrink:0;}
.dash-hero-date{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:14px 20px;backdrop-filter:blur(8px);}
.dash-hero-date-day{font-size:1.8rem;font-weight:800;color:var(--y1);line-height:1;}
.dash-hero-date-rest{font-size:.78rem;color:#a1a1aa;margin-top:3px;}

/* ── Stat Cards ── */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:24px;}
.stat-card{background:#fff;border:1.5px solid #e9e0c0;border-radius:18px;padding:20px;box-shadow:0 2px 10px rgba(199,168,0,.07);transition:.25s;position:relative;overflow:hidden;}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:18px 18px 0 0;}
.stat-card.yellow::before{background:linear-gradient(90deg,var(--y1),var(--yd));}
.stat-card.blue::before{background:linear-gradient(90deg,#60a5fa,#3b82f6);}
.stat-card.purple::before{background:linear-gradient(90deg,#a78bfa,#7c3aed);}
.stat-card.orange::before{background:linear-gradient(90deg,#fb923c,#ea580c);}
.stat-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(199,168,0,.14);}
.stat-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;}
.stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.stat-icon.yellow{background:var(--y2);color:var(--yk);}
.stat-icon.blue{background:#dbeafe;color:#2563eb;}
.stat-icon.purple{background:#ede9fe;color:#6d28d9;}
.stat-icon.orange{background:#ffedd5;color:#c2410c;}
.stat-label{font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;}
.stat-val{font-size:1.65rem;font-weight:800;color:#0f172a;line-height:1;margin-bottom:4px;}
.stat-sub{font-size:.75rem;color:#94a3b8;}

/* ── Main Grid ── */
.dash-grid{display:grid;grid-template-columns:1fr 320px;gap:20px;}
@media(max-width:1100px){.dash-grid{grid-template-columns:1fr;}}

/* ── Panel ── */
.panel{background:#fff;border:1.5px solid #e9e0c0;border-radius:18px;overflow:hidden;margin-bottom:20px;box-shadow:0 2px 10px rgba(199,168,0,.07);}
.panel-head{padding:16px 22px;background:var(--ybg);border-bottom:1.5px solid #f0e8b0;display:flex;align-items:center;justify-content:space-between;}
.panel-head h3{font-size:.9rem;font-weight:700;color:#18181b;margin:0;}
.panel-link{font-size:.75rem;font-weight:700;color:var(--yk);text-decoration:none;transition:.15s;}
.panel-link:hover{color:var(--yd);}
.panel-body{padding:20px;}

/* ── Sales Table ── */
.dash-table{width:100%;border-collapse:collapse;}
.dash-table th{padding:11px 16px;font-size:.7rem;font-weight:700;color:var(--yk);text-transform:uppercase;letter-spacing:.06em;text-align:left;background:var(--ybg);border-bottom:1.5px solid #f0e8b0;}
.dash-table td{padding:13px 16px;border-bottom:1px solid #faf6e8;font-size:.85rem;color:#374151;}
.dash-table tr:last-child td{border-bottom:none;}
.dash-table tr:hover td{background:#fffdf5;}
.inv-amount{font-weight:800;color:var(--yk);}

/* ── Badges ── */
.badge{padding:3px 9px;border-radius:99px;font-size:.65rem;font-weight:700;text-transform:uppercase;}
.badge-cash{background:var(--y2);color:var(--yk);}
.badge-card{background:#dbeafe;color:#1d4ed8;}
.badge-split{background:#ede9fe;color:#5b21b6;}
.badge-done{background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;}
.badge-pending{background:#fef3c7;color:#92400e;}
.badge-info{background:#dbeafe;color:#1e40af;}
.badge-success{background:var(--y2);color:var(--yk);}
.badge-warn{background:#fef3c7;color:#92400e;}
.badge-danger{background:#fee2e2;color:#991b1b;}

/* ── Appointment Rows ── */
.appt-row{display:flex;align-items:center;gap:12px;padding:11px 0;border-bottom:1px solid #faf6e8;}
.appt-row:last-child{border-bottom:none;}
.appt-avatar{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--y1),var(--yd));display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.85rem;color:#18181b;flex-shrink:0;}
.appt-name{font-weight:700;color:#1e293b;font-size:.875rem;}
.appt-meta{font-size:.72rem;color:#94a3b8;margin-top:1px;}

/* ── Inventory Bar ── */
.inv-item{margin-bottom:14px;}
.inv-item:last-child{margin-bottom:0;}
.inv-top{display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:5px;}
.inv-name{font-weight:600;color:#1e293b;}
.inv-count{color:#94a3b8;}
.inv-bar{height:7px;background:#f1f5f9;border-radius:4px;overflow:hidden;}
.inv-fill{height:100%;border-radius:4px;transition:width .4s;}

/* ── Status Panel ── */
.status-panel{background:linear-gradient(160deg,#1a1a1a,#2a2a2a);border:none;border-radius:18px;overflow:hidden;margin-bottom:20px;box-shadow:0 4px 20px rgba(0,0,0,.2);}
.status-panel .panel-head{background:rgba(255,255,255,.05);border-bottom:1px solid rgba(255,255,255,.08);}
.status-panel .panel-head h3{color:#fff;}
.status-row{display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid rgba(255,255,255,.06);font-size:.85rem;color:#d4d4d8;}
.status-row:last-child{border-bottom:none;}
.status-val{font-weight:700;color:#fff;}

/* ── Quick Actions ── */
.qa-link{display:flex;align-items:center;gap:12px;padding:13px 16px;border-radius:14px;background:#fff;border:1.5px solid #e9e0c0;text-decoration:none;margin-bottom:10px;transition:.2s;}
.qa-link:hover{border-color:var(--yd);background:var(--ybg);transform:translateX(4px);}
.qa-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.qa-icon.yellow{background:var(--y2);color:var(--yk);}
.qa-icon.blue{background:#dbeafe;color:#2563eb;}
.qa-icon.orange{background:#ffedd5;color:#c2410c;}
.qa-icon.slate{background:#f1f5f9;color:#475569;}
.qa-text strong{display:block;font-size:.85rem;font-weight:700;color:#1e293b;}
.qa-text span{font-size:.7rem;color:#94a3b8;}
.qa-arrow{margin-left:auto;color:#d4d4d8;flex-shrink:0;}

/* ── Analytics Chart ── */
.chart-panel{background:#fff;border:1.5px solid #e9e0c0;border-radius:20px;padding:24px;margin-top:24px;box-shadow:0 4px 20px rgba(199,168,0,.08);}
.chart-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:16px;}
.chart-tabs{display:flex;background:#f8fafc;padding:4px;border-radius:12px;border:1px solid #e2e8f0;}
.chart-tab{padding:8px 16px;font-size:.78rem;font-weight:700;color:#64748b;border-radius:10px;cursor:pointer;transition:.2s;border:none;background:transparent;}
.chart-tab.active{background:#fff;color:var(--yk);box-shadow:0 2px 8px rgba(0,0,0,.05);border:1px solid #f0e8b0;}
.chart-peak{font-size:.72rem;font-weight:700;color:#16a34a;background:#dcfce7;padding:4px 10px;border-radius:99px;display:inline-flex;align-items:center;gap:4px;}

/* ══ MOBILE RESPONSIVE ══════════════════════════════════════════ */
@media(max-width:640px){
    /* Hero: stack vertically, hide date box to save space */
    .dash-hero{flex-direction:column;align-items:flex-start;padding:20px;border-radius:16px;margin-bottom:16px;gap:12px;}
    .dash-hero-right{display:none;}
    .dash-hero-title{font-size:1.3rem;}

    /* Stat cards: 2 per row */
    .stats-grid{grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;}
    .stat-val{font-size:1.3rem;}
    .stat-card{padding:14px;}

    /* Two-col panel sub-grid: stack */
    .dash-two-col{grid-template-columns:1fr !important;}

    /* Dash table: hide less-important columns on very small screens */
    .dash-table th:nth-child(3),
    .dash-table td:nth-child(3){display:none;}
    .dash-table th, .dash-table td{padding:10px 10px;font-size:.78rem;}

    /* Quick actions: display as 2-col grid */
    .qa-pos-link { display: none; }  /* hide POS on mobile */
    .qa-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .qa-link{margin-bottom:0;border-radius:12px;padding:12px 10px;gap:8px;flex-direction:column;align-items:center;text-align:center;}
    .qa-link:hover{transform:none;}
    .qa-arrow{display:none;}
    .qa-icon{width:44px;height:44px;}
    .qa-text strong{font-size:.76rem;}
    .qa-text span{display:none;}

    /* Charts */
    .chart-panel{padding:16px;margin-top:16px;border-radius:14px;}
    .chart-head{flex-direction:column;align-items:flex-start;gap:10px;margin-bottom:16px;}
    .chart-tabs .chart-tab{padding:6px 10px;font-size:.7rem;}
}

/* ── Mobile Quick Action disabled state ── */
@media(max-width: 768px) {
    .qa-mobile-disabled {
        opacity: 0.4;
        cursor: default;
        pointer-events: none;
    }
}

@keyframes heartbeat {
  0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
  25% { transform: scale(1.04); box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
  50% { transform: scale(1); }
  75% { transform: scale(1.04); box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
  100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}
</style>

{{-- Hero Banner --}}
<div class="dash-hero">
    <div class="dash-hero-left">
        <div class="dash-hero-greeting" style="display:flex;align-items:center;gap:6px;">
            @if(now()->hour < 12)
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color:#f59e0b;"><path d="M12 4V2M12 22v-2M4 12H2M22 12h-2M17.657 6.343l-1.414 1.414M7.757 16.243l-1.414 1.414M17.657 17.657l-1.414-1.414M7.757 7.757L6.343 6.343"/><circle cx="12" cy="12" r="5"/></svg> Good Morning
            @elseif(now()->hour < 17)
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color:#f59e0b;"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg> Good Afternoon
            @else
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color:#60a5fa;"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg> Good Evening
            @endif
        </div>
        <h1 class="dash-hero-title">Dashboard Overview</h1>
        <p class="dash-hero-sub">Here's what's happening at your salon today.</p>
    </div>
    <div class="dash-hero-right">
        <div class="dash-hero-date">
            <div class="dash-hero-date-day">{{ now()->format('d') }}</div>
            <div class="dash-hero-date-rest">{{ now()->format('M Y') }} &nbsp;·&nbsp; {{ now()->format('l') }}</div>
        </div>
    </div>
</div>

@if(count($lateAppointments) > 0)
<div style="background:#fee2e2; border:1.5px solid #fca5a5; border-radius:18px; padding:18px 24px; margin-bottom:24px; display:flex; align-items:center; gap:16px; animation: pulse-red 2s infinite;">
    <div style="width:42px; height:42px; background:#fff; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#dc2626; flex-shrink:0; box-shadow:0 4px 12px rgba(220,38,38,0.1);">
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div style="flex:1;">
        <h4 style="margin:0; font-size:.9rem; font-weight:800; color:#991b1b;">Late Appointment Alert</h4>
        <p style="margin:2px 0 0; font-size:.8rem; color:#b91c1c;">{{ count($lateAppointments) }} appointment(s) discarded today due to 10+ minute delay.</p>
    </div>
    <a href="{{ route('appointments.index') }}" style="padding:8px 16px; background:#dc2626; color:#fff; border-radius:10px; font-size:.75rem; font-weight:700; text-decoration:none; transition:.2s;">Review All</a>
</div>
<style>
@keyframes pulse-red { 0% { box-shadow:0 0 0 0 rgba(220,38,38,0.2); } 70% { box-shadow:0 0 0 10px rgba(220,38,38,0); } 100% { box-shadow:0 0 0 0 rgba(220,38,38,0); } }
</style>
@endif

{{-- Stat Cards --}}
<div class="stats-grid">
    <a href="{{ route('invoices.index', ['period' => 'today', 'tab' => 'sales']) }}" class="stat-card yellow" style="text-decoration:none; color:inherit; display:block;">
        <div class="stat-top">
            <div>
                <div class="stat-label">Today's Revenue</div>
                <div class="stat-val">PKR {{ number_format($totalSalesToday, 0) }}</div>
                <div class="stat-sub">Total sales today</div>
            </div>
            <div class="stat-icon yellow">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
        </div>
    </a>
    <a href="{{ route('appointments.index') }}" class="stat-card blue" style="text-decoration:none; color:inherit; display:block;">
        <div class="stat-top">
            <div>
                <div class="stat-label">Appointments</div>
                <div class="stat-val">{{ $totalAppointmentsToday }}</div>
                <div class="stat-sub">Scheduled today</div>
            </div>
            <div class="stat-icon blue">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
        </div>
    </a>
    <a href="{{ route('staff.index') }}" class="stat-card purple" style="text-decoration:none; color:inherit; display:block;">
        <div class="stat-top">
            <div>
                <div class="stat-label">Staff Present</div>
                <div class="stat-val">{{ $staffPresentToday }}<span style="font-size:1rem;color:#94a3b8;font-weight:600;"> / {{ $totalStaff }}</span></div>
                <div class="stat-sub">On duty today</div>
            </div>
            <div class="stat-icon purple">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"/></svg>
            </div>
        </div>
    </a>
    <a href="{{ route('products.index') }}" class="stat-card orange" style="text-decoration:none; color:inherit; display:block;">
        <div class="stat-top">
            <div>
                <div class="stat-label">Low Stock Items</div>
                <div class="stat-val" style="{{ $lowStockProducts > 0 ? 'color:#c2410c;' : '' }}">{{ $lowStockProducts }}</div>
                <div class="stat-sub">{{ $lowStockProducts > 0 ? 'Need restocking' : 'All stocked up' }}</div>
            </div>
            <div class="stat-icon orange">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
    </a>
</div>

{{-- Main Grid --}}
<div class="dash-grid">
    <div>
        {{-- Recent Sales --}}
        <div class="panel">
            <div class="panel-head">
                <h3>
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:6px;color:var(--yk);"><path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Recent Sales
                </h3>
                <a href="{{ route('invoices.index') }}" class="panel-link">View All →</a>
            </div>
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentInvoices as $inv)
                    <tr>
                        <td><strong style="color:#18181b;">{{ $inv->invoice_no }}</strong></td>
                        <td>{{ $inv->customer_name ?? 'Walk-in' }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($inv->payment_method) == 'cash' ? 'cash' : (strtolower($inv->payment_method) == 'card' ? 'card' : 'split') }}">
                                {{ strtoupper($inv->payment_method) }}
                            </span>
                        </td>
                        <td><span class="inv-amount">PKR {{ number_format($inv->payable_amount, 0) }}</span></td>
                        <td style="color:#94a3b8;font-size:.8rem;">{{ $inv->created_at->format('g:i A') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:32px;color:#94a3b8;">No sales recorded today.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="dash-two-col" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            {{-- Inventory Health --}}
            <div class="panel">
                <div class="panel-head">
                    <h3>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:5px;color:var(--yk);"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Inventory Health
                    </h3>
                    <span class="badge {{ $lowStockProducts > 0 ? 'badge-danger' : 'badge-done' }}">{{ $lowStockProducts }} Low Stock</span>
                </div>
                <div class="panel-body" style="max-height: 280px; overflow-y: auto;">
                    @forelse($lowStockList as $p)
                    <div class="inv-item">
                        <div class="inv-top">
                            <span class="inv-name">{{ $p->name }}</span>
                            <span class="inv-count">{{ $p->current_stock }} / {{ $p->min_stock_level }}</span>
                        </div>
                        <div class="inv-bar">
                            @php $pct = min(100, ($p->current_stock / max(1, $p->min_stock_level)) * 100); @endphp
                            <div class="inv-fill" style="width:{{ $pct }}%;background:{{ $pct < 50 ? '#ef4444' : '#f59e0b' }};"></div>
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:24px;color:var(--yk);font-weight:700;font-size:.85rem;">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:block;margin:0 auto 8px;"><polyline points="20 6 9 17 4 12"/></svg>
                        All stock levels are healthy!
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Appointments --}}
            <div class="panel">
                <div class="panel-head">
                    <h3>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:5px;color:var(--yk);"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Appointments
                    </h3>
                    <a href="{{ route('appointments.index') }}" class="panel-link">View →</a>
                </div>
                <div class="panel-body" style="padding-top:12px;">
                    @forelse($recentAppointments as $appt)
                    <div class="appt-row row-{{ $appt->status }}" id="appt-row-{{ $appt->id }}">
                        <div class="appt-avatar">{{ strtoupper(substr($appt->customer_name, 0, 1)) }}</div>
                        <div style="flex:1;min-width:0;">
                            <div class="appt-name">{{ $appt->customer_name }}</div>
                            <div class="appt-meta">{{ $appt->service->name ?? 'Unknown Service' }} · {{ $appt->staff->name ?? 'Unassigned' }} · {{ $appt->appointment_date->format('M j') }}</div>
                        </div>
                        <span class="badge {{ $appt->status == 'completed' ? 'badge-done' : 'badge-info' }}">{{ $appt->status }}</span>
                    </div>
                    @empty
                    <div style="text-align:center;padding:24px;color:#94a3b8;font-size:.85rem;">No appointments today.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Right Sidebar --}}
    <div>
        {{-- Operational Status --}}
        <div class="status-panel">
            <div class="panel-head">
                <h3>
                    <svg width="14" height="14" fill="none" stroke="#F7DF79" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:6px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Operational Status
                </h3>
            </div>
            <div class="panel-body">
                <div class="status-row">
                    <span>Reconciliation</span>
                    <span class="badge {{ $reconciliationDone ? 'badge-done' : 'badge-warn' }}">{{ $reconciliationDone ? 'Done' : 'Pending' }}</span>
                </div>
                <div class="status-row">
                    <span>Discounts Today</span>
                    <a href="{{ route('invoices.index', ['period' => 'today']) }}" class="status-val" style="text-decoration:none; color:var(--y1); transition:.15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--y1)'" title="View Invoices">PKR {{ number_format($todayDiscounts, 0) }} →</a>
                </div>
                <div class="status-row">
                    <span>Expenses Today</span>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('expenses.create') }}" class="status-val" style="text-decoration:none; color:#f87171; transition:.15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#f87171'" title="Expenses & Payouts">PKR {{ number_format($todayExpenses, 0) }} →</a>
                    @else
                        <span class="status-val" style="color:#f87171;">PKR {{ number_format($todayExpenses, 0) }}</span>
                    @endif
                </div>
                <div style="margin-top:18px; {{ $needsReconciliation ? 'animation: heartbeat ' . max(0.5, 2 - ($hoursPastMidnight * 0.2)) . 's infinite;' : '' }}">
                    @if(!$reconciliationDone)
                    <a href="{{ route('reconciliation.index') }}" style="display:block;padding:11px;background:{{ $needsReconciliation ? 'linear-gradient(135deg, #ef4444, #991b1b)' : 'linear-gradient(135deg,var(--y1),var(--yd))' }};border-radius:11px;text-align:center;text-decoration:none;font-weight:700;font-size:.85rem;color:{{ $needsReconciliation ? '#fff' : '#18181b' }};box-shadow:{{ $needsReconciliation ? '0 3px 15px rgba(239, 68, 68, 0.5)' : '0 3px 12px rgba(199,168,0,.3)' }}; {{ $needsReconciliation ? 'border: 2px solid #fca5a5;' : '' }}">
                        {{ $needsReconciliation ? 'CLOSE YESTERDAY NOW!' : 'Close Day Now' }}
                    </a>
                    @else
                    <div style="text-align:center;color:var(--y1);font-size:.8rem;font-weight:700;padding:8px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:4px;"><polyline points="20 6 9 17 4 12"/></svg>
                        Day reconciled successfully
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div style="font-size:.68rem;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin:0 0 12px;">Quick Actions</div>

        @php
            $isMobileView = request()->header('Sec-CH-UA-Mobile') === '?1'
                || (bool) preg_match(
                    '/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile Safari/i',
                    request()->userAgent() ?? ''
                );
        @endphp

        <div class="qa-grid">
        <a href="{{ route('pos.index') }}" class="qa-link qa-pos-link">
            <div class="qa-icon yellow">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div class="qa-text"><strong>POS Terminal</strong><span>New client checkout</span></div>
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="qa-arrow"><polyline points="9 18 15 12 9 6"/></svg>
        </a>

        @if($isMobileView)
        <span class="qa-link qa-mobile-disabled">
        @else
        <a href="{{ route('appointments.index') }}" class="qa-link">
        @endif
            <div class="qa-icon blue">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="qa-text"><strong>Appointments</strong><span>Schedule & manage</span></div>
            @if(!$isMobileView)
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="qa-arrow"><polyline points="9 18 15 12 9 6"/></svg>
            @endif
        @if($isMobileView)
        </span>
        @else
        </a>
        @endif

        @if($isMobileView)
        <span class="qa-link qa-mobile-disabled">
        @else
        <a href="{{ route('staff.index') }}" class="qa-link">
        @endif
            <div class="qa-icon slate">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"/></svg>
            </div>
            <div class="qa-text"><strong>Staff Management</strong><span>Attendance & performance</span></div>
            @if(!$isMobileView)
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="qa-arrow"><polyline points="9 18 15 12 9 6"/></svg>
            @endif
        @if($isMobileView)
        </span>
        @else
        </a>
        @endif

        @if($isMobileView)
        <span class="qa-link qa-mobile-disabled">
        @else
        <a href="{{ route('products.index') }}" class="qa-link">
        @endif
            <div class="qa-icon orange">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="qa-text"><strong>Inventory</strong><span>Products & stock levels</span></div>
            @if(!$isMobileView)
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="qa-arrow"><polyline points="9 18 15 12 9 6"/></svg>
            @endif
        @if($isMobileView)
        </span>
        @else
        </a>
        @endif

        @if($isMobileView)
        <span class="qa-link qa-mobile-disabled">
        @else
        <a href="{{ route('reports.index') }}" class="qa-link">
        @endif
            <div class="qa-icon yellow">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div class="qa-text"><strong>Reports</strong><span>Sales & analytics</span></div>
            @if(!$isMobileView)
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="qa-arrow"><polyline points="9 18 15 12 9 6"/></svg>
            @endif
        @if($isMobileView)
        </span>
        @else
        </a>
        @endif
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     LIVE APPOINTMENT REMINDER NOTIFICATION SYSTEM
     ═══════════════════════════════════════════════ --}}

<!-- Notification stack container -->
<div id="appt-notif-stack" style="position:fixed;top:24px;right:24px;z-index:99999;display:flex;flex-direction:column;gap:14px;pointer-events:none;"></div>

<style>
@keyframes notif-in  { from { opacity:0; transform:translateX(60px) scale(.95); } to { opacity:1; transform:translateX(0) scale(1); } }
@keyframes notif-out { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(60px); } }
@keyframes notif-pulse { 0%,100%{box-shadow:0 0 0 0 rgba(247,223,121,.5);} 70%{box-shadow:0 0 0 14px rgba(247,223,121,0);} }
.appt-notif {
    pointer-events:auto;
    width:340px;
    background:#fff;
    border-radius:18px;
    border:2px solid #F7DF79;
    box-shadow:0 8px 32px rgba(0,0,0,.18);
    padding:20px 22px 18px;
    animation: notif-in .4s cubic-bezier(.22,1,.36,1) both, notif-pulse 2s 1s infinite;
    position:relative;
    overflow:hidden;
}
.appt-notif::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#F7DF79,#c9a800);}
.appt-notif-head{display:flex;align-items:center;gap:10px;margin-bottom:12px;}
.appt-notif-icon{width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#F7DF79,#c9a800);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.appt-notif-title{font-weight:800;font-size:.95rem;color:#0f172a;}
.appt-notif-sub{font-size:.75rem;color:#64748b;margin-top:1px;}
.appt-notif-details{background:#fffdf0;border-radius:10px;padding:10px 14px;margin-bottom:14px;font-size:.82rem;color:#374151;line-height:1.7;}
.appt-notif-details strong{color:#0f172a;}
.appt-notif-timer{font-size:.72rem;font-weight:700;color:#dc2626;margin-bottom:12px;display:flex;align-items:center;gap:6px;}
.appt-notif-timer svg{flex-shrink:0;}
.appt-notif-actions{display:flex;gap:10px;}
.appt-notif-btn-yes{flex:1;padding:9px;background:linear-gradient(135deg,#F7DF79,#c9a800);border:none;border-radius:10px;font-weight:800;font-size:.8rem;color:#18181b;cursor:pointer;transition:.2s;}
.appt-notif-btn-yes:hover{transform:translateY(-1px);}
.appt-notif-btn-no{flex:1;padding:9px;background:#fff;border:1.5px solid #fca5a5;border-radius:10px;font-weight:700;font-size:.8rem;color:#dc2626;cursor:pointer;transition:.2s;}
.appt-notif-btn-no:hover{background:#fef2f2;}
.appt-notif-dismiss{position:absolute;top:10px;right:12px;background:none;border:none;font-size:1rem;color:#94a3b8;cursor:pointer;line-height:1;}
</style>

<script>
const _seenNotifs = new Set();
const _csrfToken  = document.querySelector('meta[name="csrf-token"]')?.content || '';

function createNotification(appt) {
    const stack = document.getElementById('appt-notif-stack');
    const el = document.createElement('div');
    el.className = 'appt-notif';
    el.id = 'notif-' + appt.id;

    // 10-minute countdown (600 seconds)
    let secondsLeft = 600;

    el.innerHTML = `
        <button class="appt-notif-dismiss" onclick="dismissNotif(${appt.id})" title="Dismiss">✕</button>
        <div class="appt-notif-head">
            <div class="appt-notif-icon">
                <svg width="20" height="20" fill="none" stroke="#18181b" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
                <div class="appt-notif-title">⏰ Appointment Starting Now!</div>
                <div class="appt-notif-sub">Customer is expected at reception</div>
            </div>
        </div>
        <div class="appt-notif-details">
            <div><strong>👤 Customer:</strong> ${appt.customer_name}</div>
            <div><strong>✂️ Service:</strong> ${appt.service}</div>
            <div><strong>👨‍💼 Staff:</strong> ${appt.staff}</div>
            <div><strong>🕐 Time:</strong> ${appt.start_time}</div>
        </div>
        <div class="appt-notif-timer" id="notif-timer-${appt.id}">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Auto-discards in <span id="notif-countdown-${appt.id}">10:00</span>
        </div>
        <div class="appt-notif-actions">
            <button class="appt-notif-btn-yes" onclick="markArrived(${appt.id})">✅ Yes, Customer Arrived!</button>
            <button class="appt-notif-btn-no" onclick="dismissNotif(${appt.id})">❌ Not Here</button>
        </div>
    `;

    stack.appendChild(el);

    // Countdown timer
    const interval = setInterval(() => {
        secondsLeft--;
        const mins = String(Math.floor(secondsLeft / 60)).padStart(2,'0');
        const secs = String(secondsLeft % 60).padStart(2,'0');
        const cdEl = document.getElementById('notif-countdown-' + appt.id);
        if (cdEl) cdEl.textContent = mins + ':' + secs;

        if (secondsLeft <= 0) {
            clearInterval(interval);
            dismissNotif(appt.id);
        }
    }, 1000);

    // Store interval reference for cleanup
    el.dataset.interval = interval;
}

function dismissNotif(id) {
    const el = document.getElementById('notif-' + id);
    if (!el) return;
    clearInterval(el.dataset.interval);
    el.style.animation = 'notif-out .3s ease forwards';
    setTimeout(() => el.remove(), 320);
}

function markArrived(id) {
    fetch('{{ url("/appointments") }}/' + id + '/mark-arrived', {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': _csrfToken, 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(() => dismissNotif(id))
    .catch(() => dismissNotif(id));
}

function pollDueAppointments() {
    fetch('{{ url("/appointments/due-now") }}')
    .then(r => r.json())
    .then(appointments => {
        appointments.forEach(appt => {
            if (!_seenNotifs.has(appt.id)) {
                _seenNotifs.add(appt.id);
                createNotification(appt);
            }
        });
    })
    .catch(e => console.warn('Appointment poll failed:', e));
}

// Poll immediately then every 60 seconds
pollDueAppointments();
setInterval(pollDueAppointments, 60000);
</script>

{{-- Analytics Section --}}
<div class="chart-panel">
    <div class="chart-head">
        <div>
            <h3 style="font-size:1.1rem; font-weight:800; color:#1e293b; margin:0;">Revenue Performance</h3>
            <p style="font-size:.8rem; color:#94a3b8; margin:2px 0 0;">Visualizing sales growth and peak activity</p>
        </div>
        <div class="chart-tabs">
            <button class="chart-tab active" onclick="updateChart('daily', this)">Daily</button>
            <button class="chart-tab" onclick="updateChart('weekly', this)">Weekly</button>
            <button class="chart-tab" onclick="updateChart('monthly', this)">Monthly</button>
        </div>
    </div>
    
    <div style="position:relative; height:320px;">
        <canvas id="salesChart"></canvas>
    </div>
    
    <div style="margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between;">
        <div id="peak-info" class="chart-peak">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            Peak Sales: <span id="peak-val">PKR 0</span> (<span id="peak-label">-</span>)
        </div>
        <div style="font-size:.75rem; color:#94a3b8; font-weight:600;">Updated real-time from POS sales</div>
    </div>
</div>

{{-- Sales vs Expenses Analysis Section --}}
<div class="chart-panel" style="margin-top:24px;">
    <div class="chart-head" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; border-bottom:1px solid #f1f5f9; padding-bottom:16px; margin-bottom:20px;">
        <div>
            <h3 style="font-size:1.15rem; font-weight:800; color:#1e293b; margin:0; display:flex; align-items:center; gap:8px;">
                <svg width="20" height="20" fill="none" stroke="#ef4444" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h-2a2 2 0 00-2-2z"/></svg>
                Sales vs Expenses Analysis
            </h3>
            <p style="font-size:.8rem; color:#94a3b8; margin:2px 0 0;">Compare revenue against operational expenses and net profits</p>
        </div>
        
        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            {{-- Interval Toggle --}}
            <div class="chart-tabs" id="comp-interval-tabs" style="display:flex; background:#f8fafc; padding:3px; border-radius:10px; border:1px solid #e2e8f0; height:36px; align-items:center;">
                <button type="button" class="chart-tab active" style="padding:6px 12px; font-size:0.75rem;" onclick="setCompInterval('daily', this)">Daily</button>
                <button type="button" class="chart-tab" style="padding:6px 12px; font-size:0.75rem;" onclick="setCompInterval('weekly', this)">Weekly</button>
                <button type="button" class="chart-tab" style="padding:6px 12px; font-size:0.75rem;" onclick="setCompInterval('monthly', this)">Monthly</button>
                <button type="button" class="chart-tab" style="padding:6px 12px; font-size:0.75rem;" onclick="setCompInterval('custom', this)">Custom</button>
            </div>

            {{-- Custom Date Inputs (hidden initially) --}}
            <div id="comp-custom-dates" style="display:none; align-items:center; gap:6px;">
                <input type="date" id="comp-date-from" class="f-input" style="padding:6px 10px; font-size:0.75rem; border-radius:8px; border:1px solid #e2e8f0; width:125px; height:36px;" onchange="fetchCompChartData()">
                <span style="font-size:0.75rem; color:#94a3b8;">to</span>
                <input type="date" id="comp-date-to" class="f-input" style="padding:6px 10px; font-size:0.75rem; border-radius:8px; border:1px solid #e2e8f0; width:125px; height:36px;" onchange="fetchCompChartData()">
            </div>

            {{-- Drawer Selector --}}
            <div>
                <select id="comp-drawer-mode" class="f-input" style="padding:6px 28px 6px 10px; font-size:0.75rem; border-radius:8px; border:1px solid #e2e8f0; height:36px; background:#fff; font-weight:700; color:#475569;" onchange="fetchCompChartData()">
                    <option value="all">All Expenses</option>
                    <option value="drawer">Only Drawer Expenses</option>
                    <option value="non_drawer">Non Drawer Expenses</option>
                </select>
            </div>
        </div>
    </div>

    <div style="position:relative; height:340px;">
        <canvas id="compChart"></canvas>
    </div>

    <div style="margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; gap:16px;">
            <span style="font-size:0.75rem; font-weight:700; color:#c9a800; display:inline-flex; align-items:center; gap:5px;">
                <span style="width:10px; height:10px; border-radius:50%; background:#c9a800; display:inline-block;"></span> Sales Revenue
            </span>
            <span style="font-size:0.75rem; font-weight:700; color:#ef4444; display:inline-flex; align-items:center; gap:5px;">
                <span style="width:10px; height:10px; border-radius:50%; background:#ef4444; display:inline-block;"></span> Operational Expenses
            </span>
            <span style="font-size:0.75rem; font-weight:700; color:#10b981; display:inline-flex; align-items:center; gap:5px;">
                <span style="width:10px; height:10px; border-radius:50%; background:#10b981; display:inline-block;"></span> Net Profit
            </span>
        </div>
        <div style="font-size:.72rem; color:#94a3b8; font-weight:600;">Data updates automatically on filter changes</div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const dailyData = @json($dailySales);
const weeklyData = @json($weeklySales);
const monthlyData = @json($monthlySales);

let currentChart = null;
let compChartInstance = null;
let currentCompInterval = 'daily';

function initChart() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(247, 223, 121, 0.4)');
    gradient.addColorStop(1, 'rgba(247, 223, 121, 0)');

    currentChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyData.map(d => d.label),
            datasets: [{
                label: 'Sales Revenue',
                data: dailyData.map(d => d.value),
                borderColor: '#c9a800',
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#c9a800',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'PKR ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: {
                        font: { size: 11, weight: '600' },
                        color: '#94a3b8',
                        callback: value => 'PKR ' + value.toLocaleString()
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: {
                        font: { size: 11, weight: '700' },
                        color: '#64748b'
                    }
                }
            }
        }
    });
    
    updatePeakInfo(dailyData);
}

function updateChart(type, btn) {
    // Update tabs
    document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');

    let data;
    if (type === 'daily') data = dailyData;
    else if (type === 'weekly') data = weeklyData;
    else data = monthlyData;

    currentChart.data.labels = data.map(d => d.label);
    currentChart.data.datasets[0].data = data.map(d => d.value);
    currentChart.update();
    
    updatePeakInfo(data);
}

function updatePeakInfo(data) {
    if (!data || !data.length) return;
    const max = data.reduce((prev, current) => (prev.value > current.value) ? prev : current);
    document.getElementById('peak-val').textContent = 'PKR ' + max.value.toLocaleString();
    document.getElementById('peak-label').textContent = max.label;
}

function initCompChart() {
    const ctx = document.getElementById('compChart').getContext('2d');
    compChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Sales Revenue',
                    data: [],
                    borderColor: '#c9a800',
                    borderWidth: 3,
                    backgroundColor: 'rgba(247, 223, 121, 0.03)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#c9a800',
                    pointBorderWidth: 2,
                    pointRadius: 4
                },
                {
                    label: 'Operational Expenses',
                    data: [],
                    borderColor: '#ef4444',
                    borderWidth: 3,
                    backgroundColor: 'rgba(239, 68, 68, 0.02)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#ef4444',
                    pointBorderWidth: 2,
                    pointRadius: 4
                },
                {
                    label: 'Net Profit',
                    data: [],
                    borderColor: '#10b981',
                    borderWidth: 2.5,
                    backgroundColor: 'rgba(16, 185, 129, 0.02)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 1.5,
                    pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 11 },
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': PKR ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: {
                        font: { size: 10, weight: '600' },
                        color: '#94a3b8',
                        callback: value => 'PKR ' + value.toLocaleString()
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: {
                        font: { size: 10, weight: '700' },
                        color: '#64748b'
                    }
                }
            }
        }
    });

    fetchCompChartData();
}

function setCompInterval(interval, btn) {
    currentCompInterval = interval;
    document.querySelectorAll('#comp-interval-tabs .chart-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');

    const customDatesEl = document.getElementById('comp-custom-dates');
    if (interval === 'custom') {
        customDatesEl.style.display = 'inline-flex';
        const today = new Date().toISOString().split('T')[0];
        const thirtyDaysAgo = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
        document.getElementById('comp-date-from').value = thirtyDaysAgo;
        document.getElementById('comp-date-to').value = today;
    } else {
        customDatesEl.style.display = 'none';
    }

    fetchCompChartData();
}

function fetchCompChartData() {
    if (!compChartInstance) return;

    const drawerMode = document.getElementById('comp-drawer-mode').value;
    const dateFrom = document.getElementById('comp-date-from').value;
    const dateTo = document.getElementById('comp-date-to').value;

    let url = `/admin/chart-data?type=${currentCompInterval}&drawer_mode=${drawerMode}`;
    if (currentCompInterval === 'custom' && dateFrom && dateTo) {
        url += `&date_from=${dateFrom}&date_to=${dateTo}`;
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            compChartInstance.data.labels = data.labels;
            compChartInstance.data.datasets[0].data = data.sales;
            compChartInstance.data.datasets[1].data = data.expenses;
            compChartInstance.data.datasets[2].data = data.profit;
            compChartInstance.update();
        })
        .catch(err => console.warn('Failed to load comparison chart data:', err));
}

document.addEventListener('DOMContentLoaded', () => {
    initChart();
    initCompChart();
});
</script>
@endsection
