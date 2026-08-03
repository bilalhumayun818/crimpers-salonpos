@extends('layouts.app')
@section('title', 'Cash Reconciliation')

@section('content')
<style>
:root{--y1:#F7DF79;--y2:#FBEFBC;--yd:#c9a800;--yk:#a07800;--ybg:#fffdf0;}

/* ── Hero ── */
.recon-hero{background:linear-gradient(135deg,#1a1a1a,#2d2d2d);border-radius:20px;padding:26px 30px;margin-bottom:22px;display:flex;align-items:center;justify-content:space-between;gap:16px;position:relative;overflow:hidden;}
.recon-hero::before{content:'';position:absolute;top:-50px;right:-50px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(247,223,121,.15) 0%,transparent 70%);}
.recon-hero-title{font-size:1.5rem;font-weight:800;color:#fff;margin:0 0 5px;letter-spacing:-.02em;}
.recon-hero-sub{font-size:.85rem;color:#a1a1aa;margin:0;}
.recon-hero-date{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px 18px;text-align:right;flex-shrink:0;}
.recon-hero-date-label{font-size:.65rem;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.08em;}
.recon-hero-date-val{font-size:1rem;font-weight:800;color:var(--y1);margin-top:2px;}

/* ── Stat Cards ── */
.stat-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:22px;}
.stat-card{background:#fff;border:1.5px solid #e9e0c0;border-radius:16px;padding:20px;box-shadow:0 2px 10px rgba(199,168,0,.07);display:flex;align-items:center;gap:14px;}
.stat-icon{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.stat-icon-yellow{background:var(--y2);color:var(--yk);}
.stat-icon-green{background:#dcfce7;color:#16a34a;}
.stat-label{font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;}
.stat-val{font-size:1.3rem;font-weight:800;color:#0f172a;}
.stat-val.green{color:#16a34a;}

/* ── Main Grid ── */
.recon-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
@media(max-width:900px){.recon-grid{grid-template-columns:1fr;}}

/* ── Panel ── */
.panel{background:#fff;border:1.5px solid #e9e0c0;border-radius:18px;overflow:hidden;box-shadow:0 2px 10px rgba(199,168,0,.07);}
.panel-head{padding:16px 22px;background:var(--ybg);border-bottom:1.5px solid #f0e8b0;display:flex;align-items:center;gap:10px;}
.panel-head-icon{width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,var(--y1),var(--yd));display:flex;align-items:center;justify-content:center;color:#18181b;flex-shrink:0;}
.panel-head h3{font-size:.9rem;font-weight:700;color:#18181b;margin:0;}
.panel-body{padding:22px;}

/* ── Figures ── */
.fig-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #faf6e8;font-size:.875rem;}
.fig-row:last-child{border-bottom:none;}
.fig-label{display:flex;align-items:center;gap:8px;color:#64748b;}
.fig-dot{width:8px;height:8px;border-radius:50%;background:var(--yd);flex-shrink:0;}
.fig-dot-blue{background:#3b82f6;}
.fig-val{font-weight:700;color:#1e293b;}
.fig-total{display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:var(--ybg);border-radius:12px;margin-top:14px;border:1.5px solid #f0e8b0;}
.fig-total-label{font-size:.82rem;font-weight:700;color:var(--yk);}
.fig-total-val{font-size:1.1rem;font-weight:800;color:var(--yk);}

/* ── Form ── */
.form-field{margin-bottom:18px;}
.form-label{font-size:.82rem;font-weight:700;color:#334155;margin-bottom:7px;display:block;}
.form-label span{font-weight:400;color:#94a3b8;}
.input-wrap{display:flex;align-items:center;border:1.5px solid #e2e8f0;border-radius:11px;overflow:hidden;background:#fff;transition:.2s;}
.input-wrap:focus-within{border-color:var(--yd);box-shadow:0 0 0 3px rgba(199,168,0,.12);}
.input-prefix{padding:0 14px;font-size:.85rem;font-weight:700;color:var(--yk);background:var(--ybg);border-right:1.5px solid #f0e8b0;height:44px;display:flex;align-items:center;flex-shrink:0;}
.form-input{flex:1;padding:11px 14px;border:none;outline:none;font-family:'Outfit',sans-serif;font-size:.95rem;color:#18181b;background:transparent;}
.form-input.big{font-size:1.3rem;font-weight:800;color:#0f172a;}
.btn-submit{width:100%;padding:13px;background:linear-gradient(135deg,var(--y1),var(--yd));border:none;border-radius:11px;color:#18181b;font-weight:700;font-size:.95rem;cursor:pointer;font-family:'Outfit',sans-serif;box-shadow:0 3px 12px rgba(199,168,0,.25);transition:.2s;}
.btn-submit:hover{transform:translateY(-1px);box-shadow:0 5px 18px rgba(199,168,0,.35);}

/* ── Status Banner ── */
.status-banner{border-radius:16px;padding:22px 24px;margin-top:20px;display:flex;align-items:flex-start;gap:16px;}
.status-banner.matched{background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;}
.status-banner.mismatch{background:linear-gradient(135deg,#fef2f2,#fee2e2);border:1.5px solid #fca5a5;}
.status-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.status-icon.matched{background:#16a34a;color:#fff;}
.status-icon.mismatch{background:#ef4444;color:#fff;}
.status-title{font-size:1rem;font-weight:800;margin:0 0 5px;}
.status-banner.matched .status-title{color:#14532d;}
.status-banner.mismatch .status-title{color:#7f1d1d;}
.status-desc{font-size:.85rem;line-height:1.6;margin:0;}
.status-banner.matched .status-desc{color:#166534;}
.status-banner.mismatch .status-desc{color:#991b1b;}
.status-diff{font-weight:800;font-size:1rem;}

/* ── Alert ── */
.alert-success{background:var(--ybg);border:1.5px solid #f0e8b0;border-radius:10px;padding:12px 16px;color:var(--yk);font-size:.875rem;margin-bottom:18px;font-weight:600;display:flex;align-items:center;gap:8px;}
</style>

@if(session('success'))
<div class="alert-success">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- Hero --}}
<div class="recon-hero">
    <div>
        <div class="recon-hero-title">Cash Reconciliation</div>
        <div class="recon-hero-sub">Verify and close your daily cash drawer</div>
    </div>
    <div class="recon-hero-date">
        <div class="recon-hero-date-label">Shift Date</div>
        <div class="recon-hero-date-val">{{ $businessDate->format('M j, Y') }}</div>
        @if($businessDate->isYesterday())
            <div style="font-size:0.7rem; color:#ef4444; font-weight:800; margin-top:4px;">(Previous Day Unclosed)</div>
        @endif
    </div>
</div>

{{-- Stat Cards --}}
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div>
            <div class="stat-label">Opening Balance</div>
            <div class="stat-val">PKR {{ number_format($reconciliation->opening_balance ?? 0, 2) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div>
            <div class="stat-label">Expected in Drawer</div>
            <div class="stat-val green">PKR {{ number_format(($reconciliation->opening_balance ?? 0) + $totalSales - ($totalExpenses ?? 0), 2) }}</div>
        </div>
    </div>
</div>

{{-- Main Grid --}}
<div class="recon-grid">

    {{-- Left: Breakdown --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-head-icon">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <h3>Cash Breakdown</h3>
        </div>
        <div class="panel-body">
            <div class="fig-row">
                <div class="fig-label"><span class="fig-dot"></span>Opening Balance</div>
                <div class="fig-val">PKR {{ number_format($reconciliation->opening_balance ?? 0, 2) }}</div>
            </div>
            <div class="fig-row">
                <div class="fig-label"><span class="fig-dot fig-dot-blue"></span>Today's Sales</div>
                <div class="fig-val">PKR {{ number_format($totalSales, 2) }}</div>
            </div>
            <div class="fig-row">
                <div class="fig-label"><span class="fig-dot" style="background:#ef4444;"></span>Drawer Expenses</div>
                <div class="fig-val" style="color:#ef4444;">- PKR {{ number_format($totalExpenses ?? 0, 2) }}</div>
            </div>
            <div class="fig-total">
                <div class="fig-total-label">Total Expected Cash</div>
                <div class="fig-total-val">PKR {{ number_format(($reconciliation->opening_balance ?? 0) + $totalSales - ($totalExpenses ?? 0), 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Right: Form --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-head-icon">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </div>
            <h3>Close Day</h3>
        </div>
        <div class="panel-body">
            <form action="{{ route('reconciliation.store') }}" method="POST">
                @csrf
                <input type="hidden" name="expected_cash" value="{{ ($reconciliation->opening_balance ?? 0) + $totalSales - ($totalExpenses ?? 0) }}">

                <div class="form-field">
                    <label class="form-label">Opening Balance <span>(PKR)</span></label>
                    <div class="input-wrap">
                        <span class="input-prefix">PKR</span>
                        <input type="number" step="0.01" name="opening_balance"
                               value="{{ $reconciliation->opening_balance ?? 0 }}"
                               min="0" class="form-input">
                    </div>
                </div>

                <div class="form-field">
                    <label class="form-label">Actual Cash Counted <span>(required)</span></label>
                    <div class="input-wrap">
                        <span class="input-prefix">PKR</span>
                        <input type="number" step="0.01" name="actual_cash" required
                               value="{{ $reconciliation->actual_cash ?? '' }}"
                               placeholder="0.00"
                               class="form-input big">
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:5px;"><polyline points="20 6 9 17 4 12"/></svg>
                    Submit Reconciliation
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Status Result --}}
@if($reconciliation && $reconciliation->actual_cash !== null)
@php $matched = $reconciliation->status === 'matched'; @endphp
<div class="status-banner {{ $matched ? 'matched' : 'mismatch' }}">
    <div class="status-icon {{ $matched ? 'matched' : 'mismatch' }}">
        @if($matched)
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        @else
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        @endif
    </div>
    <div>
        <p class="status-title">{{ $matched ? 'Cash Drawer Balanced!' : 'Cash Mismatch Detected' }}</p>
        <p class="status-desc">
            @if($matched)
                Your cash drawer matches the expected total (or is within the acceptable 1 RS range). Day closed successfully.
            @else
                There is a difference of <span class="status-diff">PKR {{ number_format(abs($reconciliation->difference ?? 0), 2) }}</span>
                between the expected cash (PKR {{ number_format(($reconciliation->opening_balance ?? 0) + $totalSales - ($totalExpenses ?? 0), 2) }})
                and the actual cash counted (PKR {{ number_format($reconciliation->actual_cash, 2) }}).
                Please recount and verify.
            @endif
        </p>
    </div>
</div>
@endif

@endsection
