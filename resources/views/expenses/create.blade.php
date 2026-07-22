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
                        <select name="staff_id" class="f-select" id="staff-select">
                            <option value="">-- Choose Staff --</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                        <div style="color:#ef4444;font-size:.75rem;margin-top:6px;font-weight:600;display:none;" class="err-staff-id"></div>
                    </div>

                    <div class="f-row">
                        <label class="f-label">Expense Type *</label>
                        <select name="staff_category" class="f-select" id="staff-cat">
                            <option value="">-- Choose Type --</option>
                            <option value="salary">💰 Full Salary Payment (Resets Commission Cycle)</option>
                            <option value="salary_advance">Salary Advance (Resets Commission Cycle)</option>
                            <option value="bonus">Bonus</option>
                            <option value="allowance">Allowance</option>
                            <option value="deduction">Deduction</option>
                            <option value="incentive">Incentive</option>
                            <option value="other">Other</option>
                        </select>
                        <div style="color:#ef4444;font-size:.75rem;margin-top:6px;font-weight:600;display:none;" class="err-staff-cat"></div>
                    </div>

                    <div class="f-row">
                        <label class="f-label">Description</label>
                        <textarea name="description_staff" class="f-input" placeholder="e.g., Advance payment for emergency..." id="desc-staff"></textarea>
                        <div style="color:#ef4444;font-size:.75rem;margin-top:6px;font-weight:600;display:none;" class="err-staff-desc"></div>
                    </div>

                    <div class="f-row">
                        <label class="f-label">Amount (PKR)</label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:16px;top:50%;transform:translateY(-50%);font-weight:700;color:#94a3b8;">Rs.</span>
                            <input type="number" step="0.01" name="amount_staff" class="f-input" style="padding-left:46px;" placeholder="0.00" id="amt-staff">
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
        if (!amt || amt <= 0) { document.querySelector('.err-amt-staff').textContent = 'Valid amount required'; document.querySelector('.err-amt-staff').style.display = 'block'; return false; }
        
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
