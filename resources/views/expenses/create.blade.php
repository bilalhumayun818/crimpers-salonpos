@extends('layouts.app')
@section('title', 'Add Expense')

@section('content')
<style>
:root{--y1:#F7DF79;--y2:#FBEFBC;--yd:#c9a800;--ybg:#fffdf0;}
.exp-wrap{max-width:640px;margin:20px auto 0;}
.exp-card{background:#fff;border:1.5px solid #e9e0c0;border-radius:20px;box-shadow:0 4px 20px rgba(199,168,0,.08);overflow:hidden;}
.exp-head{background:linear-gradient(135deg,var(--y1),var(--yd));padding:24px;text-align:center;color:#18181b;}
.exp-icon{width:56px;height:56px;border-radius:16px;background:rgba(255,255,255,.3);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;box-shadow:0 4px 12px rgba(0,0,0,.05);}
.exp-title{font-size:1.4rem;font-weight:800;margin:0;}
.exp-sub{font-size:.85rem;font-weight:600;opacity:.8;margin-top:4px;}

.tab-nav{display:flex;gap:8px;padding:16px 32px;background:#f8fafc;border-bottom:1.5px solid #e9e0c0;flex-wrap:wrap;justify-content:center;}
.tab-btn{padding:10px 20px;border:2px solid #e2e8f0;background:#fff;color:#64748b;border-radius:10px;font-weight:700;font-size:.85rem;cursor:pointer;transition:.2s;text-transform:uppercase;letter-spacing:.02em;display:flex;align-items:center;gap:6px;}
.tab-btn svg{width:15px;height:15px;stroke-width:2.5;}
.tab-btn:hover{border-color:var(--y1);color:var(--yd);}
.tab-btn.active{background:var(--y1);border-color:var(--yd);color:var(--yd);}
.tab-btn.active svg{stroke:var(--yd);}

.exp-body{padding:32px;}
.tab-content{display:none;}
.tab-content.active{display:block;}

.f-row{margin-bottom:24px;}
.f-label{display:block;font-size:.85rem;font-weight:700;color:#1e293b;margin-bottom:8px;}
.f-input,.f-select{width:100%;padding:14px 16px;border:1.5px solid #e2e8f0;border-radius:12px;font-family:'Outfit',sans-serif;font-size:.95rem;transition:.2s;background:#f8fafc;}
.f-input:focus,.f-select:focus{border-color:var(--y1);background:#fff;box-shadow:0 0 0 4px rgba(247,223,121,.15);outline:none;}
textarea.f-input{resize:vertical;min-height:90px;}
.f-select{cursor:pointer;}

.toggle-wrap{display:flex;align-items:center;justify-content:space-between;background:#f8fafc;border:1.5px solid #e2e8f0;padding:16px;border-radius:12px;}
.toggle-info strong{display:block;color:#1e293b;font-weight:700;font-size:.9rem;margin-bottom:2px;}
.toggle-info span{color:#64748b;font-size:.75rem;}

.toggle-switch{position:relative;display:inline-block;width:56px;height:30px;}
.toggle-switch input{opacity:0;width:0;height:0;}
.slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background-color:#cbd5e1;transition:.3s;border-radius:34px;}
.slider:before{position:absolute;content:"";height:22px;width:22px;left:4px;bottom:4px;background-color:white;transition:.3s;border-radius:50%;box-shadow:0 2px 4px rgba(0,0,0,.1);}
input:checked + .slider{background-color:#10b981;}
input:checked + .slider:before{transform:translateX(26px);}

.exp-footer{padding:0 32px 32px;}
.btn-submit{width:100%;padding:16px;background:#18181b;color:var(--y1);border:none;border-radius:12px;font-size:1rem;font-weight:800;cursor:pointer;font-family:'Outfit',sans-serif;transition:.2s;}
.btn-submit:hover{background:#27272a;transform:translateY(-2px);box-shadow:0 6px 16px rgba(0,0,0,.1);}

.category-opts{display:grid;grid-template-columns:repeat(auto-fit,minmax(110px,1fr));gap:10px;margin-top:8px;}
.cat-btn{padding:10px 12px;border:2px solid #e2e8f0;background:#fff;color:#64748b;border-radius:10px;font-weight:600;font-size:.8rem;cursor:pointer;transition:.2s;text-align:center;display:flex;flex-direction:column;align-items:center;gap:5px;}
.cat-btn svg{width:18px;height:18px;stroke-width:2;}
.cat-btn span{display:block;}
.cat-btn:hover,.cat-btn.selected{background:var(--y1);border-color:var(--yd);color:var(--yd);}
.cat-btn:hover svg,.cat-btn.selected svg{stroke:var(--yd);}
.cat-btn.selected{font-weight:700;}

.success-msg{background:#dcfce7;border:1px solid #86efac;color:#166534;padding:16px;border-radius:12px;margin-bottom:20px;font-weight:700;text-align:center;display:flex;align-items:center;justify-content:center;gap:8px;}
</style>

<div class="exp-wrap">
    @if(session('success'))
    <div class="success-msg">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="exp-card">
        <div class="exp-head">
            <div class="exp-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <h2 class="exp-title">Record Expense</h2>
            <div class="exp-sub">Track salon expenditures</div>
        </div>

        <!-- TAB NAVIGATION -->
        <div class="tab-nav">
            <button type="button" class="tab-btn active" onclick="switchTab('daily')">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                Daily
            </button>
            <button type="button" class="tab-btn" onclick="switchTab('fixed')">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 1 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                Fixed
            </button>
            <button type="button" class="tab-btn" onclick="switchTab('staff')">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/></svg>
                Staff
            </button>
        </div>

        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf
            <input type="hidden" name="expense_type" id="expense_type" value="daily">
            <input type="hidden" name="category" id="category" value="">
            <input type="hidden" name="staff_id" id="form_staff_id" value="">
            <input type="hidden" name="description" id="form_description" value="">
            <input type="hidden" name="amount" id="form_amount" value="">
            <input type="hidden" name="deducted_from_drawer" id="form_deduct" value="0">

            <div class="exp-body">

                <!-- DAILY EXPENSE TAB -->
                <div class="tab-content active" id="daily-tab">
                    <div class="f-row">
                        <label class="f-label">Description</label>
                        <textarea name="description_daily" class="f-input" placeholder="e.g., Tea and snacks for staff, salon cleaning supplies..." id="desc-daily"></textarea>
                        <div style="color:#ef4444;font-size:.75rem;margin-top:6px;font-weight:600;display:none;" class="err-daily"></div>
                    </div>

                    <div class="f-row">
                        <label class="f-label">Amount (PKR)</label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:16px;top:50%;transform:translateY(-50%);font-weight:700;color:#94a3b8;">Rs.</span>
                            <input type="number" step="0.01" name="amount_daily" class="f-input" style="padding-left:46px;" placeholder="0.00" id="amt-daily">
                        </div>
                        <div style="color:#ef4444;font-size:.75rem;margin-top:6px;font-weight:600;display:none;" class="err-amt-daily"></div>
                    </div>

                    <div class="toggle-wrap">
                        <div class="toggle-info">
                            <strong>Deduct from Drawer?</strong>
                            <span>Did you take cash out of the register for this?</span>
                        </div>
                        <label class="toggle-switch">
                            <input type="hidden" name="deducted_from_drawer_daily" value="0">
                            <input type="checkbox" name="deducted_from_drawer_daily" value="1" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <!-- FIXED EXPENSE TAB -->
                <div class="tab-content" id="fixed-tab">
                    <div class="f-row">
                        <label class="f-label">Category *</label>
                        <div class="category-opts">
                            <button type="button" class="cat-btn selected" onclick="selectCategory('water')" title="Water">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.32 0z"/></svg>
                                <span>Water</span>
                            </button>
                            <button type="button" class="cat-btn" onclick="selectCategory('electricity')" title="Electricity">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                <span>Electricity</span>
                            </button>
                            <button type="button" class="cat-btn" onclick="selectCategory('gas')" title="Gas">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><circle cx="6" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="18" cy="12" r="2"/><path d="M6 10v4M12 10v4M18 10v4" stroke="currentColor" stroke-width="2" fill="none"/></svg>
                                <span>Gas</span>
                            </button>
                            <button type="button" class="cat-btn" onclick="selectCategory('internet')" title="Internet">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                <span>Internet</span>
                            </button>
                            <button type="button" class="cat-btn" onclick="selectCategory('rent')" title="Rent">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                <span>Rent</span>
                            </button>
                            <button type="button" class="cat-btn" onclick="selectCategory('maintenance')" title="Maintenance">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6V4m6.364 1.636l1.414-1.414M18 12h2m-1.636 6.364l1.414 1.414M12 18v2m-6.364-1.636l-1.414 1.414M6 12H4m1.636-6.364L4.222 4.222"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>Maintenance</span>
                            </button>
                            <button type="button" class="cat-btn" onclick="selectCategory('insurance')" title="Insurance">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                <span>Insurance</span>
                            </button>
                            <button type="button" class="cat-btn" onclick="selectCategory('other')" title="Other">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/><path d="M9 12h6M9 16h6"/></svg>
                                <span>Other</span>
                            </button>
                        </div>
                    </div>

                    <div class="f-row">
                        <label class="f-label">Description</label>
                        <textarea name="description_fixed" class="f-input" placeholder="e.g., Monthly water bill for salon..." id="desc-fixed"></textarea>
                        <div style="color:#ef4444;font-size:.75rem;margin-top:6px;font-weight:600;display:none;" class="err-fixed"></div>
                    </div>

                    <div class="f-row">
                        <label class="f-label">Amount (PKR)</label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:16px;top:50%;transform:translateY(-50%);font-weight:700;color:#94a3b8;">Rs.</span>
                            <input type="number" step="0.01" name="amount_fixed" class="f-input" style="padding-left:46px;" placeholder="0.00" id="amt-fixed">
                        </div>
                        <div style="color:#ef4444;font-size:.75rem;margin-top:6px;font-weight:600;display:none;" class="err-amt-fixed"></div>
                    </div>

                    <div class="toggle-wrap">
                        <div class="toggle-info">
                            <strong>Deduct from Drawer?</strong>
                            <span>Did you take cash out of the register for this?</span>
                        </div>
                        <label class="toggle-switch">
                            <input type="hidden" name="deducted_from_drawer_fixed" value="0">
                            <input type="checkbox" name="deducted_from_drawer_fixed" value="1" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <!-- STAFF EXPENSE TAB -->
                <div class="tab-content" id="staff-tab">
                    <div class="f-row">
                        <label class="f-label">Select Staff Member *</label>
                        <select name="staff_id" class="f-select" id="staff-select" onchange="updateStaffCalculations()">
                            <option value="">-- Choose Staff --</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}"
                                        data-base="{{ number_format($member->base_salary ?? 0, 2, '.', '') }}"
                                        data-daily-base="{{ number_format($member->daily_base_salary ?? 0, 2, '.', '') }}"
                                        data-today-comm="{{ number_format($member->today_earned_commission ?? 0, 2, '.', '') }}"
                                        data-comm="{{ number_format($member->total_earned_commission ?? 0, 2, '.', '') }}"
                                        data-adv="{{ number_format($member->current_cycle_advances ?? 0, 2, '.', '') }}"
                                        data-ded="{{ number_format($member->current_cycle_deductions ?? 0, 2, '.', '') }}"
                                        data-daily-paid="{{ number_format($member->current_cycle_daily_salaries ?? 0, 2, '.', '') }}"
                                        data-daily-base-paid="{{ number_format($member->current_cycle_daily_base_salaries ?? 0, 2, '.', '') }}"
                                        data-daily-count="{{ $member->current_cycle_daily_salaries_count ?? 0 }}"
                                        data-absent-days="{{ $member->current_cycle_absent_days ?? 0 }}"
                                        data-absent-ded="{{ number_format($member->current_cycle_absent_deductions ?? 0, 2, '.', '') }}"
                                        data-expected-daily="{{ number_format($member->expected_daily_salary ?? 0, 2, '.', '') }}"
                                        data-net="{{ number_format($member->net_salary_payable ?? 0, 2, '.', '') }}">
                                    {{ $member->name }}
                                </option>
                            @endforeach
                        </select>
                        <div style="color:#ef4444;font-size:.75rem;margin-top:6px;font-weight:600;display:none;" class="err-staff-id"></div>
                    </div>

                    <div class="f-row">
                        <label class="f-label">Expense Type *</label>
                        <input type="hidden" name="staff_category" id="staff-cat" value="full_salary">
                        <div class="category-opts">
                            <button type="button" class="staff-cat-btn cat-btn selected" onclick="selectStaffCategory('full_salary', this)" title="Full Salary">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                                <span>Full Salary</span>
                            </button>
                            <button type="button" class="staff-cat-btn cat-btn" onclick="selectStaffCategory('daily_salary', this)" title="Daily Salary">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Daily Salary</span>
                            </button>
                            <button type="button" class="staff-cat-btn cat-btn" onclick="selectStaffCategory('advance', this)" title="Salary Advance">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Advance</span>
                            </button>
                            <button type="button" class="staff-cat-btn cat-btn" onclick="selectStaffCategory('deduction', this)" title="Deduction">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M8 12h8"/></svg>
                                <span>Deduction</span>
                            </button>
                            <button type="button" class="staff-cat-btn cat-btn" onclick="selectStaffCategory('other', this)" title="Other">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span>Other</span>
                            </button>
                        </div>
                        <div style="color:#ef4444;font-size:.75rem;margin-top:6px;font-weight:600;display:none;" class="err-staff-cat"></div>
                    </div>

                    <div id="salary-breakdown-info" style="display:none; margin-bottom:20px; padding:14px 16px; background:#fffdf0; border:1.5px solid #f0e8a0; border-radius:12px; font-size:0.85rem; color:#1e293b;">
                        <div style="font-weight:800; color:#a07800; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span id="sb-title">Salary Breakdown Summary</span>
                        </div>
                        
                        <!-- FULL SALARY SUMMARY -->
                        <div id="full-salary-summary" style="display:none;">
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px; font-size:0.8rem; color:#475569;">
                                <div>Base Salary: <strong style="color:#1e293b;" id="sb-base">Rs. 0.00</strong></div>
                                <div>Earned Comm: <strong style="color:#0284c7;" id="sb-comm">Rs. 0.00</strong></div>
                                <div>Daily Base Paid: <strong style="color:#8b5cf6;" id="sb-daily-paid">Rs. 0.00</strong> <span id="sb-daily-count" style="font-size:0.75rem; color:#94a3b8;">(0 days)</span></div>
                                <div>Absent Deductions: <strong style="color:#ef4444;" id="sb-absent-ded">Rs. 0.00</strong> <span id="sb-absent-days" style="font-size:0.75rem; color:#94a3b8;">(0 days)</span></div>
                                <div>Advances Taken: <strong style="color:#eab308;" id="sb-adv">Rs. 0.00</strong></div>
                                <div>Other Deductions: <strong style="color:#dc2626;" id="sb-ded">Rs. 0.00</strong></div>
                            </div>
                                <div>Advances Taken: <strong style="color:#eab308;" id="sb-adv">Rs. 0.00</strong></div>
                                <div>Other Deductions: <strong style="color:#dc2626;" id="sb-ded">Rs. 0.00</strong></div>
                            </div>
                            <div style="border-top:1px dashed #cbd5e1; margin-top:8px; padding-top:6px; font-weight:800; color:#16a34a; font-size:0.9rem;">
                                Net Payable: <span id="sb-net">Rs. 0.00</span>
                            </div>
                        </div>

                        <!-- DAILY SALARY SUMMARY -->
                        <div id="daily-salary-summary" style="display:none;">
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px; font-size:0.8rem; color:#475569;">
                                <div>Daily Base (Base/30): <strong style="color:#1e293b;" id="dsb-base">Rs. 0.00</strong></div>
                                <div>Today's Commission: <strong style="color:#0284c7;" id="dsb-comm">Rs. 0.00</strong></div>
                            </div>
                            <div style="border-top:1px dashed #cbd5e1; margin-top:8px; padding-top:6px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:6px;">
                                <div style="font-weight:800; color:#0f172a; font-size:0.85rem;">
                                    Expected Daily Pay: <span id="dsb-expected" style="color:#2563eb;">Rs. 0.00</span>
                                </div>
                                <div id="dsb-bonus-badge" style="font-weight:800; font-size:0.82rem; color:#16a34a;"></div>
                            </div>
                        </div>

                        <!-- ADVANCE / DEDUCTION / OTHER SUMMARY -->
                        <div id="other-staff-summary" style="display:none;">
                            <div style="font-size:0.8rem; color:#475569;">
                                Net Payable Remaining in Cycle: <strong style="color:#16a34a;" id="osb-net">Rs. 0.00</strong>
                            </div>
                        </div>
                    </div>

                    <div class="f-row">
                        <label class="f-label">Description</label>
                        <textarea name="description_staff" class="f-input" placeholder="e.g., Monthly salary payment or advance note..." id="desc-staff"></textarea>
                        <div style="color:#ef4444;font-size:.75rem;margin-top:6px;font-weight:600;display:none;" class="err-staff-desc"></div>
                    </div>

                    <div class="f-row">
                        <label class="f-label">Amount (PKR)</label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:16px;top:50%;transform:translateY(-50%);font-weight:700;color:#94a3b8;">Rs.</span>
                            <input type="number" step="0.01" name="amount_staff" class="f-input" style="padding-left:46px;" placeholder="0.00" id="amt-staff" oninput="onAmountInputChanged()">
                        </div>
                        <div style="color:#ef4444;font-size:.75rem;margin-top:6px;font-weight:600;display:none;" class="err-amt-staff"></div>
                    </div>

                    <div class="toggle-wrap">
                        <div class="toggle-info">
                            <strong>Deduct from Drawer?</strong>
                            <span>Did you take cash out of the register for this?</span>
                        </div>
                        <label class="toggle-switch">
                            <input type="hidden" name="deducted_from_drawer_staff" value="0">
                            <input type="checkbox" name="deducted_from_drawer_staff" value="1" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

            </div>

            <div class="exp-footer">
                <button type="submit" class="btn-submit" onclick="return prepareSubmit()">Save Expense</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentTab = 'daily';
let selectedCategory = 'water';

function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    
    document.getElementById(tab + '-tab').classList.add('active');
    event.target.classList.add('active');
    currentTab = tab;
}

function selectCategory(cat) {
    document.querySelectorAll('.cat-btn').forEach(el => el.classList.remove('selected'));
    event.target.classList.add('selected');
    selectedCategory = cat;
    document.getElementById('category').value = cat;
}

function selectStaffCategory(cat, btn) {
    document.querySelectorAll('.staff-cat-btn').forEach(el => el.classList.remove('selected'));
    if (btn) {
        btn.classList.add('selected');
    }
    document.getElementById('staff-cat').value = cat;
    updateStaffCalculations();
}

function updateStaffCalculations() {
    const staffSelect = document.getElementById('staff-select');
    const staffCat = document.getElementById('staff-cat').value;
    const amtInput = document.getElementById('amt-staff');
    const infoBox = document.getElementById('salary-breakdown-info');

    const fullSum  = document.getElementById('full-salary-summary');
    const dailySum = document.getElementById('daily-salary-summary');
    const otherSum = document.getElementById('other-staff-summary');

    const selectedOption = staffSelect.options[staffSelect.selectedIndex];

    if (!selectedOption || !selectedOption.value) {
        infoBox.style.display = 'none';
        amtInput.readOnly = false;
        amtInput.style.background = '#f8fafc';
        return;
    }

    const base           = parseFloat(selectedOption.getAttribute('data-base') || 0);
    const dailyBase      = parseFloat(selectedOption.getAttribute('data-daily-base') || 0);
    const todayComm      = parseFloat(selectedOption.getAttribute('data-today-comm') || 0);
    const comm           = parseFloat(selectedOption.getAttribute('data-comm') || 0);
    const adv            = parseFloat(selectedOption.getAttribute('data-adv') || 0);
    const ded            = parseFloat(selectedOption.getAttribute('data-ded') || 0);
    const dailyBasePaid  = parseFloat(selectedOption.getAttribute('data-daily-base-paid') || 0);
    const dailyCount     = parseInt(selectedOption.getAttribute('data-daily-count') || 0);
    const absentDays     = parseInt(selectedOption.getAttribute('data-absent-days') || 0);
    const absentDed      = parseFloat(selectedOption.getAttribute('data-absent-ded') || 0);
    const expectedDaily  = parseFloat(selectedOption.getAttribute('data-expected-daily') || 0);
    const net            = parseFloat(selectedOption.getAttribute('data-net') || 0);

    infoBox.style.display = 'block';
    fullSum.style.display = 'none';
    dailySum.style.display = 'none';
    otherSum.style.display = 'none';

    if (staffCat === 'full_salary' || staffCat === 'salary') {
        document.getElementById('sb-title').textContent = 'Full Salary Breakdown';
        fullSum.style.display = 'block';

        document.getElementById('sb-base').textContent       = 'Rs. ' + base.toLocaleString('en-US', {minimumFractionDigits:2});
        document.getElementById('sb-comm').textContent       = 'Rs. ' + comm.toLocaleString('en-US', {minimumFractionDigits:2});
        document.getElementById('sb-daily-paid').textContent = 'Rs. ' + dailyBasePaid.toLocaleString('en-US', {minimumFractionDigits:2});
        document.getElementById('sb-daily-count').textContent= '(' + dailyCount + ' day' + (dailyCount === 1 ? '' : 's') + ')';
        document.getElementById('sb-absent-ded').textContent  = 'Rs. ' + absentDed.toLocaleString('en-US', {minimumFractionDigits:2});
        document.getElementById('sb-absent-days').textContent = '(' + absentDays + ' day' + (absentDays === 1 ? '' : 's') + ')';
        document.getElementById('sb-adv').textContent        = 'Rs. ' + adv.toLocaleString('en-US', {minimumFractionDigits:2});
        document.getElementById('sb-ded').textContent        = 'Rs. ' + ded.toLocaleString('en-US', {minimumFractionDigits:2});
        document.getElementById('sb-net').textContent        = 'Rs. ' + net.toLocaleString('en-US', {minimumFractionDigits:2});

        amtInput.value = net.toFixed(2);
        amtInput.readOnly = true;
        amtInput.style.background = '#e2e8f0';

    } else if (staffCat === 'daily_salary') {
        document.getElementById('sb-title').textContent = "Daily Salary Breakdown";
        dailySum.style.display = 'block';

        document.getElementById('dsb-base').textContent     = 'Rs. ' + dailyBase.toLocaleString('en-US', {minimumFractionDigits:2});
        document.getElementById('dsb-comm').textContent     = 'Rs. ' + todayComm.toLocaleString('en-US', {minimumFractionDigits:2});
        document.getElementById('dsb-expected').textContent = 'Rs. ' + expectedDaily.toLocaleString('en-US', {minimumFractionDigits:2});

        amtInput.readOnly = false;
        amtInput.style.background = '#f8fafc';
        if (!amtInput.value || parseFloat(amtInput.value) === 0 || amtInput.getAttribute('data-last-cat') !== 'daily_salary') {
            amtInput.value = expectedDaily.toFixed(2);
        }
        onAmountInputChanged();

    } else {
        document.getElementById('sb-title').textContent = staffCat.charAt(0).toUpperCase() + staffCat.slice(1) + ' Record';
        otherSum.style.display = 'block';
        document.getElementById('osb-net').textContent = 'Rs. ' + net.toLocaleString('en-US', {minimumFractionDigits:2});

        amtInput.readOnly = false;
        amtInput.style.background = '#f8fafc';
    }

    amtInput.setAttribute('data-last-cat', staffCat);
}

function onAmountInputChanged() {
    const staffSelect = document.getElementById('staff-select');
    const staffCat    = document.getElementById('staff-cat').value;
    const amtInput    = document.getElementById('amt-staff');
    const badge       = document.getElementById('dsb-bonus-badge');

    if (staffCat !== 'daily_salary' || !badge) return;

    const selectedOption = staffSelect.options[staffSelect.selectedIndex];
    if (!selectedOption || !selectedOption.value) {
        badge.innerHTML = '';
        return;
    }

    const expectedDaily = parseFloat(selectedOption.getAttribute('data-expected-daily') || 0);
    const typedAmount   = parseFloat(amtInput.value || 0);

    if (typedAmount > expectedDaily) {
        const bonus = typedAmount - expectedDaily;
        badge.innerHTML = `<span style="background:#dcfce7; color:#15803d; padding:4px 10px; border-radius:8px; font-weight:800; border:1px solid #bbf7d0;">🎁 Bonus Added: +Rs. ${bonus.toLocaleString('en-US', {minimumFractionDigits:2})}</span>`;
    } else if (typedAmount === expectedDaily && expectedDaily > 0) {
        badge.innerHTML = `<span style="color:#2563eb; font-weight:700;">Standard Daily Salary</span>`;
    } else if (typedAmount > 0 && typedAmount < expectedDaily) {
        badge.innerHTML = `<span style="color:#d97706; font-weight:700;">Partial Daily Salary</span>`;
    } else {
        badge.innerHTML = '';
    }
}

function prepareSubmit() {
    // Clear all error messages
    document.querySelectorAll('[class*="err-"]').forEach(el => el.style.display = 'none');
    
    let desc, amt, deduct, staffId;
    
    if (currentTab === 'daily') {
        desc = document.getElementById('desc-daily').value.trim();
        amt = document.getElementById('amt-daily').value;
        deduct = document.querySelector('input[name="deducted_from_drawer_daily"]:checked') ? 1 : 0;
        
        if (!desc) { document.querySelector('.err-daily').textContent = 'Description required'; document.querySelector('.err-daily').style.display = 'block'; return false; }
        if (!amt || amt <= 0) { document.querySelector('.err-amt-daily').textContent = 'Valid amount required'; document.querySelector('.err-amt-daily').style.display = 'block'; return false; }
        
    } else if (currentTab === 'fixed') {
        if (!selectedCategory) { alert('Please select a category'); return false; }
        desc = document.getElementById('desc-fixed').value.trim();
        amt = document.getElementById('amt-fixed').value;
        deduct = document.querySelector('input[name="deducted_from_drawer_fixed"]:checked') ? 1 : 0;
        
        if (!desc) { document.querySelector('.err-fixed').textContent = 'Description required'; document.querySelector('.err-fixed').style.display = 'block'; return false; }
        if (!amt || amt <= 0) { document.querySelector('.err-amt-fixed').textContent = 'Valid amount required'; document.querySelector('.err-amt-fixed').style.display = 'block'; return false; }
        
    } else if (currentTab === 'staff') {
        staffId = document.getElementById('staff-select').value;
        let staffCat = document.getElementById('staff-cat').value;
        desc = document.getElementById('desc-staff').value.trim();
        amt = document.getElementById('amt-staff').value;
        deduct = document.querySelector('input[name="deducted_from_drawer_staff"]:checked') ? 1 : 0;
        
        if (!staffId) { document.querySelector('.err-staff-id').textContent = 'Staff member required'; document.querySelector('.err-staff-id').style.display = 'block'; return false; }
        if (!staffCat) { document.querySelector('.err-staff-cat').textContent = 'Expense type required'; document.querySelector('.err-staff-cat').style.display = 'block'; return false; }
        if (amt === '' || amt === null || parseFloat(amt) < 0) { document.querySelector('.err-amt-staff').textContent = 'Valid amount required'; document.querySelector('.err-amt-staff').style.display = 'block'; return false; }
        
        document.getElementById('category').value = staffCat;
        document.getElementById('form_staff_id').value = staffId;
    }
    
    // Set hidden fields for submission
    document.getElementById('expense_type').value = currentTab;
    document.getElementById('form_description').value = desc;
    document.getElementById('form_amount').value = amt;
    document.getElementById('form_deduct').value = deduct;
    
    return true;
}
</script>
@endsection
