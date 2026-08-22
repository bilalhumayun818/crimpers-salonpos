@extends('layouts.app')
@section('title', 'New Appointment')
@section('content')
<style>
.edit-wrap{max-width:680px;margin:0 auto;}
.edit-header{display:flex;align-items:center;gap:14px;margin-bottom:28px;}
.back-btn{width:38px;height:38px;border-radius:10px;border:1.5px solid #e2e8f0;background:#fff;display:flex;align-items:center;justify-content:center;color:#64748b;text-decoration:none;transition:.2s;flex-shrink:0;}
.back-btn:hover{border-color:#F7DF79;color:#c9a800;background:#fffdf0;}
.edit-title{font-size:1.4rem;font-weight:800;color:#0f172a;letter-spacing:-.02em;margin-bottom:3px;}
.edit-sub{font-size:.85rem;color:#64748b;}
.edit-card{background:#fff;border:1px solid #f0e8a0;border-radius:18px;box-shadow:0 2px 12px rgba(0,0,0,.05);overflow:hidden;}
.edit-card-head{padding:20px 28px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px;}
.edit-card-icon{width:36px;height:36px;border-radius:10px;background:#fffdf0;display:flex;align-items:center;justify-content:center;color:#F7DF79;}
.edit-card-title{font-size:.95rem;font-weight:700;color:#1e293b;}
.edit-card-body{padding:28px;}
.f-row{margin-bottom:20px;}
.f-row-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;}
.f-label{display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:8px;}
.f-input{width:100%;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:11px;font-size:.9rem;font-family:'Outfit',sans-serif;color:#1e293b;background:#fafafa;outline:none;transition:.2s;}
.f-input:focus{border-color:#F7DF79;background:#fff;box-shadow:0 0 0 3px rgba(247,223,121,.1);}
textarea.f-input{resize:vertical;min-height:90px;}
.form-footer{display:flex;gap:12px;justify-content:flex-end;padding-top:8px;border-top:1px solid #f1f5f9;margin-top:8px;}
.btn-cancel{padding:10px 22px;border:1.5px solid #e2e8f0;background:#fff;border-radius:10px;color:#64748b;font-size:.875rem;font-weight:600;cursor:pointer;text-decoration:none;font-family:'Outfit',sans-serif;transition:.2s;}
.btn-cancel:hover{border-color:#fca5a5;color:#dc2626;background:#fef2f2;}
.btn-save{padding:10px 22px;border:none;background:linear-gradient(135deg,#F7DF79,#c9a800);border-radius:10px;color:#18181b;font-size:.875rem;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;transition:.2s;box-shadow:0 3px 10px rgba(247,223,121,.25);}
.btn-save:hover{transform:translateY(-1px);}
</style>

<div class="edit-wrap">
  <div class="edit-header">
    <a href="{{ route('appointments.index') }}" class="back-btn">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
      <div class="edit-title">New Appointment</div>
      <div class="edit-sub">Schedule a new client visit</div>
    </div>
  </div>
  <div class="edit-card">
    <div class="edit-card-head">
      <div class="edit-card-icon">
        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
      <div class="edit-card-title">Appointment Details</div>
    </div>
    <div class="edit-card-body">
      @if ($errors->any())
        <div style="background:#fef2f2; border:1px solid #fca5a5; border-radius:10px; padding:14px; margin-bottom:20px;">
          <h4 style="color:#dc2626; margin-top:0; margin-bottom:8px; font-size:.9rem;">There were some problems with your input:</h4>
          <ul style="color:#b91c1c; font-size:.85rem; margin:0; padding-left:20px;">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      <form method="POST" action="{{ route('appointments.store') }}">
        @csrf
        <div class="f-row" style="position:relative;">
          <label class="f-label">Customer Name</label>
          <div style="display:flex;gap:8px;">
            <input type="text" id="custNameInput" name="customer_name" required class="f-input" placeholder="Enter customer name" autocomplete="off">
            <button type="button" id="scanBtn" style="padding:0 18px;border:1.5px solid #F7DF79;background:#fffdf0;border-radius:11px;color:#c9a800;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;transition:.2s;"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg> Scan</button>
          </div>
          <div id="custSuggestions" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1);max-height:200px;overflow-y:auto;z-index:1000;margin-top:4px;"></div>
        </div>
        <div class="f-row-2">
          <div>
            <label class="f-label">Service</label>
            <select id="serviceSelect" name="service_id" class="f-input">
              <option value="">Select service…</option>
              @foreach($services as $s)
              <option value="{{ $s->id }}">{{ $s->name }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="f-label">Or Choose Package</label>
            <select id="packageSelect" name="service_package_id" class="f-input">
              <option value="">Select package…</option>
              @foreach($packages as $p)
              <option value="{{ $p->id }}">{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="f-row">
          <label class="f-label">Staff Member (Optional)</label>
          <select name="staff_id" class="f-input">
            <option value="">No staff assigned…</option>
            @foreach($staffMembers as $s)
            <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="f-row">
          <label class="f-label">Appointment Date</label>
          <input type="date" name="appointment_date" required class="f-input">
        </div>
        <div class="f-row-2">
          <div>
            <label class="f-label">Start Time</label>
            <input type="time" name="start_time" required class="f-input" min="{{ $openingTime }}" max="{{ $closingTime }}">
          </div>
          <div>
            <label class="f-label">End Time</label>
            <input type="time" name="end_time" required class="f-input" min="{{ $openingTime }}" max="{{ $closingTime }}">
          </div>
        </div>
        <div class="f-row">
          <label class="f-label">Phone (optional)</label>
          <input type="tel" id="custPhoneInput" name="customer_phone" class="f-input" placeholder="+1 234 567 890" autocomplete="off">
          <div id="phoneStatus" style="display:none;margin-top:6px;padding:7px 12px;border-radius:8px;font-size:.78rem;font-weight:600;"></div>
        </div>
        <div class="f-row" style="margin-bottom:0;">
          <label class="f-label">Notes (optional)</label>
          <textarea name="notes" class="f-input" placeholder="Any special requests…"></textarea>
        </div>
        <div class="form-footer">
          <a href="{{ route('appointments.index') }}" class="btn-cancel">Cancel</a>
          <button type="submit" class="btn-save">Create Appointment</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// ─── Customer Smart Lookup ───────────────────────────────────────────────────
const custNameInput   = document.getElementById('custNameInput');
const custPhoneInput  = document.getElementById('custPhoneInput');
const custSuggestions = document.getElementById('custSuggestions');
const phoneStatus     = document.getElementById('phoneStatus');
let custTimeout, phoneTimeout;

function showPhoneStatus(msg, type) {
    if (type === 'hide') { phoneStatus.style.display = 'none'; return; }
    const styles = type === 'present'
        ? 'background:#fef3c7;color:#92400e;border:1px solid #fcd34d;'
        : 'background:#dbeafe;color:#1e40af;border:1px solid #93c5fd;';
    phoneStatus.style.cssText = 'display:block;margin-top:6px;padding:7px 12px;border-radius:8px;font-size:.78rem;font-weight:600;' + styles;
    phoneStatus.innerHTML = msg;
}

// Scan button logic
document.getElementById('scanBtn').addEventListener('click', function() {
    clearTimeout(custTimeout);
    showPhoneStatus('', 'hide');
    const term = custNameInput.value.trim();
    if (term.length < 2) { 
        custSuggestions.innerHTML = '<div style="padding:10px 12px;color:#94a3b8;font-size:.83rem;">Please enter at least 2 characters to scan.</div>';
        custSuggestions.style.display = 'block';
        return; 
    }

    custSuggestions.innerHTML = '<div style="padding:10px 12px;color:#64748b;font-size:.83rem;">Scanning...</div>';
    custSuggestions.style.display = 'block';

    fetch('{{ url("/customers/search") }}?term=' + encodeURIComponent(term))
    .then(r => r.json())
    .then(data => {
        custSuggestions.innerHTML = '';
        if (data.length > 0) {
            data.forEach(cust => {
                const div = document.createElement('div');
                div.style.cssText = 'padding:10px 12px;cursor:pointer;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;';
                div.innerHTML = `<span style="font-weight:600;color:#1e293b;">${cust.name}</span><span style="font-size:.78rem;color:#64748b;">${cust.phone || ''}</span>`;
                div.onmouseover = () => div.style.background = '#fffdf0';
                div.onmouseout  = () => div.style.background = '#fff';
                div.onclick = () => {
                    custNameInput.value = cust.name;
                    if (cust.phone) {
                        custPhoneInput.value = cust.phone;
                        showPhoneStatus('✅ Already Present — existing customer found', 'present');
                    }
                    custSuggestions.style.display = 'none';
                };
                custSuggestions.appendChild(div);
            });
            custSuggestions.style.display = 'block';
        } else {
            custSuggestions.innerHTML = '<div style="padding:10px 12px;color:#94a3b8;font-size:.83rem;">No customer found — a new profile will be created on submit.</div>';
            custSuggestions.style.display = 'block';
        }
    })
    .catch(() => {
        custSuggestions.innerHTML = '<div style="padding:10px 12px;color:#dc2626;font-size:.83rem;">Error scanning. Please try again.</div>';
    });
});

// Name autocomplete (optional: clear suggestions on typing if previously scanned)
custNameInput.addEventListener('input', function() {
    custSuggestions.style.display = 'none';
    showPhoneStatus('', 'hide');
});

// Phone number lookup on blur / typing
custPhoneInput.addEventListener('input', function() {
    clearTimeout(phoneTimeout);
    showPhoneStatus('', 'hide');
    const phone = this.value.trim();
    if (phone.length < 4) return;

    phoneTimeout = setTimeout(() => {
        fetch('{{ url("/customers/search") }}?term=' + encodeURIComponent(phone))
        .then(r => r.json())
        .then(data => {
            // Filter strictly by phone match
            const match = data.find(c => c.phone && c.phone.replace(/\s/g,'') === phone.replace(/\s/g,''));
            if (match) {
                showPhoneStatus('⚠️ Already Present — this number belongs to <strong>' + match.name + '</strong>', 'present');
                // Also fill name if name field is empty
                if (!custNameInput.value.trim()) custNameInput.value = match.name;
            } else {
                showPhoneStatus('➕ New Client — a new customer profile will be created', 'new');
            }
        });
    }, 400);
});

// Close suggestions on outside click
document.addEventListener('click', function(e) {
    if (e.target !== custNameInput && !custSuggestions.contains(e.target)) {
        custSuggestions.style.display = 'none';
    }
});

// Mutual exclusion for service vs package selection
const serviceSelect = document.getElementById('serviceSelect');
const packageSelect = document.getElementById('packageSelect');

serviceSelect.addEventListener('change', function() {
    if (this.value) {
        packageSelect.value = '';
    }
});

packageSelect.addEventListener('change', function() {
    if (this.value) {
        serviceSelect.value = '';
    }
});
</script>
@endsection
