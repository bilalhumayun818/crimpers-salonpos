@extends('layouts.app')
@section('title', 'Edit Appointment')
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
.danger-zone{margin-top:20px;background:#fff;border:1px solid #fecaca;border-radius:14px;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px;}
.danger-title{font-size:.9rem;font-weight:700;color:#b91c1c;margin-bottom:3px;}
.danger-sub{font-size:.8rem;color:#94a3b8;}
.btn-danger{padding:9px 18px;border:1.5px solid #fca5a5;background:#fef2f2;border-radius:10px;color:#dc2626;font-size:.82rem;font-weight:700;cursor:pointer;font-family:'Outfit',sans-serif;transition:.2s;}
.btn-danger:hover{background:#fee2e2;}
</style>
<div class="edit-wrap">
  <div class="edit-header">
    <a href="{{ route('appointments.index') }}" class="back-btn">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
      <div class="edit-title">Edit Appointment</div>
      <div class="edit-sub">{{ $appointment->customer_name }} &mdash; {{ $appointment->appointment_date->format('M j, Y') }}</div>
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
      <form method="POST" action="{{ route('appointments.update', $appointment) }}">
        @csrf @method('PUT')
        <div class="f-row">
          <label class="f-label">Customer Name</label>
          <input type="text" name="customer_name" value="{{ $appointment->customer_name }}" required class="f-input">
        </div>
        <div class="f-row-2">
          <div>
            <label class="f-label">Service</label>
            <select id="serviceSelect" name="service_id" class="f-input">
              <option value="">Select service…</option>
              @foreach($services as $s)
              <option value="{{ $s->id }}" {{ $appointment->service_id==$s->id?'selected':'' }}>{{ $s->name }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="f-label">Or Choose Package</label>
            <select id="packageSelect" name="service_package_id" class="f-input">
              <option value="">Select package…</option>
              @foreach($packages as $p)
              <option value="{{ $p->id }}" {{ $appointment->service_package_id==$p->id?'selected':'' }}>{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="f-row">
          <label class="f-label">Staff Member (Optional)</label>
          <select name="staff_id" class="f-input">
            <option value="">No staff assigned…</option>
            @foreach($staffMembers as $s)
            <option value="{{ $s->id }}" {{ $appointment->staff_id==$s->id?'selected':'' }}>{{ $s->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="f-row">
          <label class="f-label">Appointment Date</label>
          <input type="date" name="appointment_date" value="{{ $appointment->appointment_date->format('Y-m-d') }}" required class="f-input">
        </div>
        <div class="f-row-2">
          <div>
            <label class="f-label">Start Time</label>
            <input type="time" name="start_time" value="{{ $appointment->start_time->format('H:i') }}" required class="f-input" min="{{ $openingTime }}" max="{{ $closingTime }}">
          </div>
          <div>
            <label class="f-label">End Time</label>
            <input type="time" name="end_time" value="{{ $appointment->end_time->format('H:i') }}" required class="f-input" min="{{ $openingTime }}" max="{{ $closingTime }}">
          </div>
        </div>
        <div class="f-row-2">
          <div>
            <label class="f-label">Phone (optional)</label>
            <input type="tel" name="customer_phone" value="{{ $appointment->customer_phone }}" class="f-input" placeholder="+1 234 567 890">
          </div>
          <div>
            <label class="f-label">Status</label>
            <select name="status" class="f-input">
              @foreach(['scheduled','confirmed','completed','cancelled'] as $st)
              <option value="{{ $st }}" {{ $appointment->status==$st?'selected':'' }}>{{ ucfirst($st) }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="f-row">
          <label class="f-label">Notes (optional)</label>
          <textarea name="notes" class="f-input">{{ $appointment->notes }}</textarea>
        </div>
        <div class="form-footer">
          <a href="{{ route('appointments.index') }}" class="btn-cancel">Cancel</a>
          <button type="submit" class="btn-save">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
  <div class="danger-zone">
    <div>
      <div class="danger-title">Delete Appointment</div>
      <div class="danger-sub">This action cannot be undone</div>
    </div>
    <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" onsubmit="return confirm('Permanently delete this appointment?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn-danger">Delete Appointment</button>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endsection
