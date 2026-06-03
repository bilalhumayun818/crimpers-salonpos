@extends('layouts.app')
@section('title', 'Staff HRMS')
@section('content')
<style>
.hrms-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
.staff-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
.staff-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.staff-avatar { width: 48px; height: 48px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #64748b; }
.staff-name { font-size: 1rem; font-weight: 700; color: #1e293b; }
.staff-role { font-size: 0.75rem; color: #64748b; }

.stat-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }
.stat-label { color: #64748b; }
.stat-value { font-weight: 600; color: #1e293b; }

.action-row { display: flex; gap: 8px; margin-top: 16px; }
.btn-hrms { flex: 1; padding: 8px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer; border: 1px solid #e2e8f0; background: #fff; transition: 0.2s; }
.btn-hrms:hover { background: #f8fafc; }
.btn-present { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
.btn-absent { background: #fee2e2; color: #991b1b; border-color: #fecaca; }

.rating-stars { color: #fbbf24; font-size: 0.9rem; }
.hrms-search-box { background: #fff; border: 1.5px solid #f0e8a0; border-radius: 16px; padding: 6px 6px 6px 24px; margin-bottom: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 12px; max-width: 100%; }
.hrms-search-input { flex: 1; border: none; font-size: 1rem; font-family: inherit; color: #1e293b; outline: none; background: transparent; height: 48px; }
.hrms-search-input::placeholder { color: #94a3b8; }
.hrms-search-icon { color: #c9a800; flex-shrink: 0; }
.btn-search-trigger { background: #c9a800; color: #fff; border: none; padding: 0 24px; height: 44px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s; }
.btn-search-trigger:hover { background: #b09400; transform: translateY(-1px); }
</style>

<div class="hrms-search-box">
    <div class="hrms-search-icon">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
    </div>
    <input type="text" id="staff-search" class="hrms-search-input" placeholder="Search employee by name or phone number…">
    <button type="button" class="btn-search-trigger" onclick="triggerSearch()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        Search
    </button>
</div>

<div class="hrms-grid">
    @foreach($staff as $s)
    @php $attendance = $attendances->get($s->id); @endphp
    <div class="staff-card" id="staff-{{ $s->id }}" data-name="{{ strtolower($s->name) }}" data-phone="{{ $s->phone }}">
        <div class="staff-header">
            <div class="staff-avatar">{{ strtoupper(substr($s->name, 0, 1)) }}</div>
            <div>
                <div class="staff-name">{{ $s->name }}</div>
                <div class="staff-role">
                    ID: {{ $s->id }}
                </div>
                @if($s->phone)
                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 2px;">
                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle; margin-right:2px;"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l2.27-2.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                    {{ $s->phone }}
                </div>
                @endif
            </div>
        </div>

        <div class="action-row">
            <button class="btn-hrms btn-attendance {{ optional($attendance)->status === 'present' ? 'btn-present' : '' }}" onclick="markAttendance({{ $s->id }}, 'present', this)">Present</button>
            <button class="btn-hrms btn-attendance {{ optional($attendance)->status === 'absent' ? 'btn-absent' : '' }}" onclick="markAttendance({{ $s->id }}, 'absent', this)">Absent</button>
        </div>
    </div>
    @endforeach
</div>

{{-- Quick Shift Modal --}}
<div class="modal-overlay" id="shift-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center;">
    <div style="background:#fff; padding:24px; border-radius:16px; width:100%; max-width:350px;">
        <h3 style="margin-bottom:16px; font-size:1.1rem; font-weight:700;">Set Working Hours</h3>
        <input type="hidden" id="shift-staff-id">
        <div style="display:flex; gap:10px; margin-bottom:20px;">
            <div style="flex:1;">
                <label style="display:block; font-size:0.75rem; font-weight:700; color:#64748b; margin-bottom:4px;">Shift Start</label>
                <input type="time" id="quick-shift-start" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
            </div>
            <div style="flex:1;">
                <label style="display:block; font-size:0.75rem; font-weight:700; color:#64748b; margin-bottom:4px;">Shift End</label>
                <input type="time" id="quick-shift-end" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <button class="btn-hrms" style="background:#f1f5f9;" onclick="document.getElementById('shift-modal').style.display='none'">Cancel</button>
            <button class="btn-hrms" style="background:#c9a800; color:#fff; border:none;" onclick="saveQuickShift()">Apply Shift</button>
        </div>
    </div>
</div>

{{-- Salary Modal --}}
<div class="modal-overlay" id="salary-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center;">
    <div style="background:#fff; padding:24px; border-radius:16px; width:100%; max-width:400px;">
        <h3 style="margin-bottom:16px; font-size:1.1rem; font-weight:700;">Update Salary Settings</h3>
        <input type="hidden" id="edit-staff-id">
        <div style="margin-bottom:12px;">
            <label style="display:block; font-size:0.75rem; font-weight:700; color:#64748b; margin-bottom:4px;">Base Salary (PKR)</label>
            <input type="number" id="edit-base-salary" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
        </div>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom:12px;">
            <div>
                <label style="display:block; font-size:0.75rem; font-weight:700; color:#64748b; margin-bottom:4px;">Comm. Customer (%)</label>
                <input type="number" id="edit-commission-customer" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
            </div>
            <div>
                <label style="display:block; font-size:0.75rem; font-weight:700; color:#64748b; margin-bottom:4px;">Comm. Service (%)</label>
                <input type="number" id="edit-commission-service" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
            </div>
        </div>
        <div style="display:flex; gap:10px; margin-bottom:20px;">
            <div style="flex:1;">
                <label style="display:block; font-size:0.75rem; font-weight:700; color:#64748b; margin-bottom:4px;">Shift Start</label>
                <input type="time" id="edit-shift-start" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
            </div>
            <div style="flex:1;">
                <label style="display:block; font-size:0.75rem; font-weight:700; color:#64748b; margin-bottom:4px;">Shift End</label>
                <input type="time" id="edit-shift-end" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <button class="btn-hrms" style="background:#f1f5f9;" onclick="document.getElementById('salary-modal').style.display='none'">Cancel</button>
            <button class="btn-hrms" style="background:#c9a800; color:#fff; border:none;" onclick="saveSalary()">Save Changes</button>
        </div>
    </div>
</div>

<script>
function markAttendance(staffId, status, btn) {
    fetch("{{ route('staff.hrms.attendance') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ staff_id: staffId, status: status })
    }).then(res => res.json()).then(data => {
        if(data.success) {
            const card = document.getElementById('staff-' + staffId);
            card.querySelectorAll('.btn-attendance').forEach(b => {
                b.classList.remove('btn-present', 'btn-absent');
            });
            btn.classList.add(status === 'present' ? 'btn-present' : 'btn-absent');
        }
    });
}

function openShiftModal(staffId, start, end) {
    document.getElementById('shift-staff-id').value = staffId;
    document.getElementById('quick-shift-start').value = start || '';
    document.getElementById('quick-shift-end').value = end || '';
    document.getElementById('shift-modal').style.display = 'flex';
}

function saveQuickShift() {
    const id = document.getElementById('shift-staff-id').value;
    const start = document.getElementById('quick-shift-start').value;
    const end = document.getElementById('quick-shift-end').value;

    fetch("{{ route('staff.hrms.shift') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ staff_id: id, shift_start: start, shift_end: end })
    }).then(res => res.json()).then(data => {
        if(data.success) {
            location.reload();
        }
    });
}

function editSalary(id, base, commCustomer, commService, start, end) {
    document.getElementById('edit-staff-id').value = id;
    document.getElementById('edit-base-salary').value = base;
    document.getElementById('edit-commission-customer').value = commCustomer;
    document.getElementById('edit-commission-service').value = commService;
    document.getElementById('edit-shift-start').value = start || '';
    document.getElementById('edit-shift-end').value = end || '';
    document.getElementById('salary-modal').style.display = 'flex';
}

function saveSalary() {
    const id = document.getElementById('edit-staff-id').value;
    const base = document.getElementById('edit-base-salary').value;
    const commCustomer = document.getElementById('edit-commission-customer').value;
    const commService = document.getElementById('edit-commission-service').value;
    const start = document.getElementById('edit-shift-start').value;
    const end = document.getElementById('edit-shift-end').value;

    fetch("{{ route('staff.hrms.salary-update') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ 
            staff_id: id, 
            base_salary: base, 
            commission_per_customer: commCustomer,
            commission_per_service: commService,
            shift_start: start,
            shift_end: end
        })
    }).then(res => res.json()).then(data => {
        if(data.success) {
            location.reload();
        }
    });
}

// Live Search logic
function triggerSearch() {
    const term = document.getElementById('staff-search').value.toLowerCase();
    const cards = document.querySelectorAll('.staff-card');
    
    cards.forEach(card => {
        const name = card.dataset.name;
        const phone = card.dataset.phone;
        
        if (name.includes(term) || phone.includes(term)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

document.getElementById('staff-search').addEventListener('input', triggerSearch);
</script>
@endsection
