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
.stat-card{background:#fff;border:1.5px solid #e9e0c0;border-radius:16px;padding:20px;box-shadow:0 2px 10px rgba(199,168,0,.07);display:flex;align-items:center;gap:14px;justify-content:space-between;}
.stat-card-left{display:flex;align-items:center;gap:14px;}
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

/* ── Modal Pop-up ── */
.modal-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:9999;padding:20px;}
.modal-card{background:#ffffff;border-radius:20px;width:100%;max-width:480px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.2),0 10px 10px -5px rgba(0,0,0,0.04);border:1.5px solid #e9e0c0;overflow:hidden;animation:modalSlideUp 0.25s ease-out;}
@keyframes modalSlideUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.modal-header{padding:20px 24px;background:var(--ybg);border-bottom:1.5px solid #f0e8b0;display:flex;align-items:center;gap:14px;position:relative;}
.modal-icon{width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,var(--y1),var(--yd));display:flex;align-items:center;justify-content:center;color:#18181b;flex-shrink:0;}
.modal-title{font-size:1.1rem;font-weight:800;color:#18181b;margin:0;}
.modal-subtitle{font-size:0.78rem;color:#64748b;margin:2px 0 0 0;}
.btn-close-modal{position:absolute;right:18px;top:18px;background:none;border:none;font-size:1.5rem;color:#94a3b8;cursor:pointer;border-radius:50%;width:30px;height:30px;display:flex;align-items:center;justify-content:center;}
.btn-close-modal:hover{background:#e2e8f0;color:#0f172a;}
.modal-body{padding:24px;}
.modal-footer{padding:16px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:12px;}
.btn-cancel{padding:10px 18px;background:#e2e8f0;color:#475569;border:none;border-radius:10px;font-weight:700;font-size:0.88rem;cursor:pointer;}
.btn-cancel:hover{background:#cbd5e1;}
.minus-badge{font-size:0.65rem;background:#fee2e2;color:#991b1b;padding:2px 6px;border-radius:4px;font-weight:800;margin-left:6px;text-transform:uppercase;}
.btn-sm-edit{background:#fef3c7;color:#92400e;border:1px solid #fde68a;border-radius:8px;font-size:0.75rem;font-weight:700;padding:5px 12px;cursor:pointer;transition:0.2s;}
.btn-sm-edit:hover{background:#fde68a;transform:translateY(-1px);}
</style>

@if(session('success'))
<div class="alert-success">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- Opening Balance Modal Pop-up --}}
<div id="openingBalanceModal" class="modal-overlay" style="display: {{ $showOpeningBalanceModal ? 'flex' : 'none' }};">
    <div class="modal-card">
        <div class="modal-header">
            <div class="modal-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <div>
                <h3 class="modal-title">Shift Opening Cash Balance</h3>
                <p class="modal-subtitle">Enter float cash present in drawer for {{ \Carbon\Carbon::parse($businessDate)->format('M j, Y') }}</p>
            </div>
            <button type="button" class="btn-close-modal" onclick="closeOpeningModal()">&times;</button>
        </div>
        <form action="{{ route('reconciliation.opening_balance') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ \Carbon\Carbon::parse($businessDate)->format('Y-m-d') }}">
            <div class="modal-body">
                <label class="form-label" style="font-weight: 700; color: #1e293b; margin-bottom: 6px; display: block;">Opening Balance Amount (PKR):</label>
                <div class="input-wrap" style="margin-bottom: 14px;">
                    <span class="input-prefix">PKR</span>
                    <input type="number" step="0.01" min="0" name="opening_balance" id="modal_opening_balance" value="{{ $openingBalance }}" class="form-input big" required placeholder="0.00">
                </div>
                <p style="font-size: 0.78rem; color: #64748b; margin: 0; line-height: 1.5;">
                    💡 This is the starting cash present in your drawer before processing sales or expenses for this shift.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeOpeningModal()">Cancel</button>
                <button type="submit" class="btn-submit" style="width: auto; padding: 10px 24px;">Save Opening Balance</button>
            </div>
        </form>
    </div>
</div>

{{-- Hero --}}
<div class="recon-hero">
    <div>
        <div class="recon-hero-title">Cash Reconciliation</div>
        <div class="recon-hero-sub">Verify and close your daily cash drawer</div>
    </div>
    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <form method="GET" action="{{ route('reconciliation.index') }}" style="display: flex; align-items: center; gap: 6px;">
            <label style="color: #a1a1aa; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Date:</label>
            <input type="date" name="date" value="{{ is_string($businessDate) ? $businessDate : $businessDate->format('Y-m-d') }}" style="padding: 6px 12px; border-radius: 8px; font-weight: 700; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.1); color: #fff;" onchange="this.form.submit()">
        </form>

        <a href="{{ route('reports.daily-history') }}" style="background: var(--y1); color: #18181b; padding: 8px 14px; border-radius: 8px; font-weight: 800; font-size: 0.8rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Daily Sheet History
        </a>

        <div class="recon-hero-date">
            <div class="recon-hero-date-label">Shift Date</div>
            <div class="recon-hero-date-val">{{ \Carbon\Carbon::parse($businessDate)->format('M j, Y') }}</div>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-card-left">
            <div class="stat-icon stat-icon-yellow">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <div>
                <div class="stat-label">Opening Balance</div>
                <div class="stat-val">PKR {{ number_format($openingBalance, 2) }}</div>
            </div>
        </div>
        <button type="button" class="btn-sm-edit" onclick="openOpeningModal()">✏️ Set / Edit</button>
    </div>
    <div class="stat-card">
        <div class="stat-card-left">
            <div class="stat-icon stat-icon-green">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div>
                <div class="stat-label">Expected Cash in Drawer</div>
                <div class="stat-val green">PKR {{ number_format($expectedCash, 2) }}</div>
            </div>
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
                <div class="fig-val">PKR {{ number_format($openingBalance, 2) }}</div>
            </div>

            <div class="fig-row">
                <div class="fig-label"><span class="fig-dot fig-dot-blue"></span>Gross Total Sales (All Methods)</div>
                <div class="fig-val" style="color: #2563eb;">PKR {{ number_format($grossSales, 2) }}</div>
            </div>

            @if($cardSales > 0)
            <div class="fig-row">
                <div class="fig-label">
                    <span class="fig-dot" style="background:#dc2626;"></span>Card Sales
                    <span class="minus-badge">Not in Drawer</span>
                </div>
                <div class="fig-val" style="color:#dc2626;">- PKR {{ number_format($cardSales, 2) }}</div>
            </div>
            @endif

            @if(($bankSales + $splitBankSales) > 0)
            <div class="fig-row">
                <div class="fig-label">
                    <span class="fig-dot" style="background:#ea580c;"></span>Bank / Online Sales (EasyPaisa/JazzCash/HBL)
                    <span class="minus-badge">Not in Drawer</span>
                </div>
                <div class="fig-val" style="color:#ea580c;">- PKR {{ number_format($bankSales + $splitBankSales, 2) }}</div>
            </div>
            @endif

            <div class="fig-row" style="background:#f0fdf4; padding: 8px 12px; border-radius: 8px; margin: 4px 0; border: 1px solid #bbf7d0;">
                <div class="fig-label" style="font-weight:800; color:#166534;">
                    <span class="fig-dot" style="background:#16a34a;"></span>Net Cash Sales (Physical Cash in Drawer)
                </div>
                <div class="fig-val" style="color:#166534; font-size:0.95rem;">PKR {{ number_format($cashSales, 2) }}</div>
            </div>

            <div class="fig-row">
                <div class="fig-label"><span class="fig-dot" style="background:#ef4444;"></span>Drawer Expenses (Deducted)</div>
                <div class="fig-val" style="color:#ef4444;">- PKR {{ number_format($totalExpenses, 2) }}</div>
            </div>

            <div class="fig-total">
                <div class="fig-total-label">Total Expected Cash in Drawer</div>
                <div class="fig-total-val">PKR {{ number_format($expectedCash, 2) }}</div>
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
                <input type="hidden" name="date" value="{{ $businessDate->format('Y-m-d') }}">
                <input type="hidden" name="expected_cash" value="{{ $expectedCash }}">

                <div class="form-field">
                    <label class="form-label">Opening Balance <span>(PKR)</span></label>
                    <div class="input-wrap">
                        <span class="input-prefix">PKR</span>
                        <input type="number" step="0.01" name="opening_balance"
                               value="{{ $openingBalance }}"
                               min="0" class="form-input">
                    </div>
                </div>

                <div class="form-field">
                    <label class="form-label">Actual Cash Counted in Drawer <span>(required)</span></label>
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
                between the expected cash (PKR {{ number_format($expectedCash, 2) }})
                and the actual cash counted (PKR {{ number_format($reconciliation->actual_cash, 2) }}).
                Please recount and verify.
            @endif
        </p>
    </div>
</div>
@endif

<script>
    function openOpeningModal() {
        document.getElementById('openingBalanceModal').style.display = 'flex';
        document.getElementById('modal_opening_balance').focus();
    }
    function closeOpeningModal() {
        document.getElementById('openingBalanceModal').style.display = 'none';
    }
</script>

@endsection

